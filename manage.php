<?php
session_start();
require 'db_connect.php'; 
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$search = $_GET['search'] ?? '';
$sort_col = $_GET['sort'] ?? 'eoi_id';
$sort_dir = $_GET['dir'] ?? 'ASC';

$query_params = ['search' => $search, 'sort' => $sort_col, 'dir' => $sort_dir];
$query_string = http_build_query($query_params);

if (isset($_GET['delete'])) {
    $eoi_id_to_delete = $_GET['delete']; 
    
    $stmt = $conn->prepare("DELETE FROM eoi WHERE eoi_id = ?");
    $stmt->bind_param("i", $eoi_id_to_delete); 
    $stmt->execute();
    
    unset($query_params['delete']); 
    header("Location: manage.php?" . http_build_query($query_params));
    exit;
}

if (isset($_POST['update_status'])) {
    $eoi_id = $_POST['eoi_id'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE eoi SET status = ? WHERE eoi_id = ?");
    $stmt->bind_param("si", $status, $eoi_id); 
    $stmt->execute();
    
    header("Location: manage.php?" . $query_string);
    exit;
}

$sql = "SELECT * FROM eoi";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " WHERE job_ref LIKE ? OR CONCAT(first_name, ' ', last_name) LIKE ?";
    $likeSearch = "%$search%";
    $params[] = $likeSearch;
    $params[] = $likeSearch;
    $types = "ss";
}

$allowed_columns = ['eoi_id', 'job_ref', 'first_name', 'last_name', 'email', 'phone', 'status'];
$allowed_dirs = ['ASC', 'DESC'];

if (in_array($sort_col, $allowed_columns)) {
    $sql .= " ORDER BY $sort_col"; 
    if (in_array($sort_dir, $allowed_dirs)) {
        $sql .= " $sort_dir"; 
    }
} else {
    $sql .= " ORDER BY eoi_id ASC";
}

$stmt = $conn->prepare($sql);
if (count($params) > 0) {
    $stmt->bind_param($types, ...$params); 
}
$stmt->execute();
$result = $stmt->get_result();

?>

<h2>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</h2>
<a href="logout.php">Logout</a>

<h3>Search EOI</h3>
<form method="GET" action="manage.php">
    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort_col); ?>">
    <input type="hidden" name="dir" value="<?= htmlspecialchars($sort_dir); ?>">
    
    <input type="text" name="search" placeholder="Job Ref or Name" value="<?= htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
    <a href="manage.php">Clear Search</a>
</form>

<h3>EOI List</h3>
<table border="1" cellpadding="8" style="border-collapse: collapse; width: 100%;">
<tr>
    <?php
    $cols = [
        'eoi_id' => 'ID',
        'job_ref' => 'Job Ref',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'status' => 'Status',
        'actions' => 'Actions'
    ];

    foreach ($cols as $key => $label) {
        if ($key != 'actions') {
            $dir = ($sort_col == $key && $sort_dir == 'ASC') ? 'DESC' : 'ASC';
            
            $sort_params = ['search' => $search, 'sort' => $key, 'dir' => $dir];
            $sort_url = http_build_query($sort_params);
            
            echo "<th><a href='?{$sort_url}'>$label</a></th>";
        } else {
            echo "<th>$label</th>";
        }
    }
    ?>
</tr>

<?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['eoi_id']; ?></td>
        <td><?= htmlspecialchars($row['job_ref']); ?></td>
        <td><?= htmlspecialchars($row['first_name']); ?></td>
        <td><?= htmlspecialchars($row['last_name']); ?></td>
        <td><?= htmlspecialchars($row['email']); ?></td>
        <td><?= htmlspecialchars($row['phone']); ?></td>
        <td>
            <form method="POST" action="?<?= $query_string; ?>" style="margin:0;">
                <input type="hidden" name="eoi_id" value="<?= $row['eoi_id']; ?>">
                <select name="status">
                    <?php
                    $statuses = ['New', 'In Progress', 'Closed', 'Hired', 'Interviewing', 'Rejected'];
                    foreach ($statuses as $s) {
                        $selected = ($s == $row['status']) ? "selected" : "";
                        echo "<option value=\"$s\" $selected>$s</option>";
                    }
                    ?>
                </select>
                <button type="submit" name="update_status">Update</button>
            </form>
        </td>
        <td>
            <?php
            $delete_params = array_merge($query_params, ['delete' => $row['eoi_id']]);
            $delete_url = http_build_query($delete_params);
            ?>
            <a href="?<?= $delete_url; ?>" onclick="return confirm('Are you sure you want to delete EOI #<?= $row['eoi_id']; ?>?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="<?= count($cols); ?>" style="text-align: center;">No EOIs found.</td>
    </tr>
<?php endif; ?>
</table>
