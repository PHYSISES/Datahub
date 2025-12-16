<?php
include 'db_connection.php';
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Pagination settings
$limit = 5; // rows per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Sorting
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'submitted_at';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';
$allowed_sort = ['name', 'email', 'submitted_at'];
if (!in_array($sort_column, $allowed_sort)) $sort_column = 'submitted_at';

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_sql = "";
$params = [];
$types = "";

if ($search !== "") {
    $search_sql = "WHERE name LIKE ? OR email LIKE ? OR message LIKE ?";
    $search_term = "%$search%";
    $params = [$search_term, $search_term, $search_term];
    $types = "sss";
}

// Count total rows for pagination
$count_sql = "SELECT COUNT(*) FROM feedback $search_sql";
$stmt_count = $conn->prepare($count_sql);
if ($search !== "") {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$stmt_count->bind_result($total_rows);
$stmt_count->fetch();
$stmt_count->close();
$total_pages = ceil($total_rows / $limit);

// Fetch messages
$sql = "SELECT id, name, contact_no, email, message, submitted_at, done 
        FROM feedback $search_sql 
        ORDER BY $sort_column $sort_order 
        LIMIT ?, ?";

$stmt = $conn->prepare($sql);

// Fix PHP 8+ issue: merge params with limit & offset
if ($search !== "") {
    $all_params = array_merge($params, [$offset, $limit]);
    $all_types = $types . "ii";

    $bind_names[] = $all_types;
    foreach ($all_params as $key => $value) {
        $bind_names[] = & $all_params[$key];
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_names);
} else {
    $stmt->bind_param("ii", $offset, $limit);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Messages - CharterProject</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Times New Roman, sans-serif; padding: 20px; background-color: #f0f2f5; }
        .container { max-width: 1000px; margin: auto; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { cursor: pointer; background-color: #1e3a8a; color: white; }
        th a { color: white; text-decoration: none; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .done-btn { background-color: #162f6f; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; }
        .done-btn.done { background-color: #4caf50; }
        .pagination { margin-top: 15px; text-align: center; }
        .pagination a { display: inline-block; margin: 0 5px; padding: 8px 12px; background-color: #1e3a8a; color: white; border-radius: 5px; text-decoration: none; }
        .pagination a.active { background-color: #162f6f; }
        .search-bar { margin-bottom: 20px; text-align: right; }
        .search-bar input[type="text"] { padding: 8px; width: 250px; border-radius: 5px; border: 1px solid #ccc; }
        .return-home { display: inline-block; margin-bottom: 20px; padding: 10px 15px; background-color: #1e3a8a; color: white; border-radius: 5px; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="return-home">Return Home</a>
    <h1>Customer Messages</h1>

    <div class="search-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <table>
        <tr>
            <th><a href="?sort=name&order=<?php echo $sort_order === 'ASC' ? 'desc' : 'asc'; ?>">Full Name</a></th>
            <th>Contact No.</th>
            <th><a href="?sort=email&order=<?php echo $sort_order === 'ASC' ? 'desc' : 'asc'; ?>">Email</a></th>
            <th>Message</th>
            <th><a href="?sort=submitted_at&order=<?php echo $sort_order === 'ASC' ? 'desc' : 'asc'; ?>">Submitted At</a></th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['message']); ?></td>
            <td><?php echo $row['submitted_at']; ?></td>
            <td>
                <?php if ($row['done']): ?>
                    <button class="done-btn done" disabled>Done</button>
                <?php else: ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="done_id" value="<?php echo $row['id']; ?>">
                        <button class="done-btn" type="submit" name="mark_done">Mark Done</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a class="<?php echo $i === $page ? 'active' : ''; ?>" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort_column; ?>&order=<?php echo strtolower($sort_order); ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</div>

<?php
// Handle marking as done
if (isset($_POST['mark_done'])) {
    $done_id = intval($_POST['done_id']);
    $update = $conn->prepare("UPDATE feedback SET done = 1 WHERE id = ?");
    $update->bind_param("i", $done_id);
    $update->execute();
    header("Location: admin_messages.php"); // refresh page
    exit();
}
?>
</body>
</html>
