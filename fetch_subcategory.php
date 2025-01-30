<?php
session_start();
include 'db_connect.php';
$id = $_SESSION['user_id'];

// Get parameters from DataTables
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$order_column_index = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0;
$order_dir = isset($_POST['order'][0]['dir']) && ($_POST['order'][0]['dir'] === 'asc' || $_POST['order'][0]['dir'] === 'desc') ? $_POST['order'][0]['dir'] : 'asc';

// Define columns for ordering
$columns = ['subcategory_id', 'subcategory_name', 'category_name', 'user_name'];
$order_column = isset($columns[$order_column_index]) ? $columns[$order_column_index] : 'subcategory_name';

// Base query with join to get user name and category name
$sql = "SELECT s.subcategory_id, s.subcategory_name, c.category_name, u.user_name
        FROM tbl_subcategories s
        LEFT JOIN tbl_category c ON s.category_id = c.category_id
        LEFT JOIN tbl_user u ON s.user_id = u.user_id
        WHERE s.user_id = ? AND s.subcategory_name LIKE ?
        ORDER BY $order_column $order_dir LIMIT ?, ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => "Failed to prepare statement"]);
    exit();
}

// Bind parameters
$search_param = "%" . $search . "%";
$stmt->bind_param("ssii", $id, $search_param, $start, $length);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Get total records for DataTables
$sql_count = "SELECT COUNT(*) as total
              FROM tbl_subcategories s
              LEFT JOIN tbl_category c ON s.category_id = c.category_id
              LEFT JOIN tbl_user u ON s.user_id = u.user_id
              WHERE s.subcategory_name LIKE ? AND s.user_id = ?";
$count_stmt = $conn->prepare($sql_count);
if ($count_stmt === false) {
    echo json_encode(["error" => "Failed to prepare statement for count"]);
    exit();
}
$count_stmt->bind_param("ss", $search_param, $id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$row_count = $count_result->fetch_assoc();
$total_records = $row_count['total'];

$sql_filtered_count = "SELECT COUNT(*) as count
                       FROM tbl_subcategories s
                       LEFT JOIN tbl_category c ON s.category_id = c.category_id
                       LEFT JOIN tbl_user u ON s.user_id = u.user_id
                       WHERE s.subcategory_name LIKE ? AND s.user_id = ?";
$filtered_stmt = $conn->prepare($sql_filtered_count);
$filtered_stmt->bind_param("ss", $search_param, $id);
$filtered_stmt->execute();
$filtered_result = $filtered_stmt->get_result();
$filtered_count = $filtered_result->fetch_assoc()['count'];

// Return JSON response
echo json_encode([
    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    "recordsTotal" => $total_records,
    "recordsFiltered" => $filtered_count,
    "data" => $users
]);

$stmt->close();
$count_stmt->close();
$conn->close();
?>
