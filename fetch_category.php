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
$columns = ['category_id', 'category_name', 'user_id'];
$order_column = isset($columns[$order_column_index]) ? $columns[$order_column_index] : 'category_name';

// Base query with optional filtering by user_type
$sql = "SELECT c.category_id, c.category_name, c.user_id, u.user_name
        FROM tbl_category c
        LEFT JOIN tbl_user u ON c.user_id = u.user_id
        WHERE c.user_id = ? AND c.category_name LIKE ?
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

$categories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Get total records for DataTables
$sql_count = "SELECT COUNT(*) as total FROM tbl_category c WHERE (c.category_name LIKE ?) AND c.user_id = ?";
$count_stmt = $conn->prepare($sql_count);
if ($count_stmt === false) {
    echo json_encode(["error" => "Failed to prepare statement for count"]);
    exit();
}
$count_stmt->bind_param("si", $search_param, $id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$row_count = $count_result->fetch_assoc();
$total_records = $row_count['total'];

// Get filtered records count
$sql_filtered_count = "SELECT COUNT(*) as count FROM tbl_category c WHERE (c.category_name LIKE ?) AND c.user_id = ?";
$filtered_stmt = $conn->prepare($sql_filtered_count);
$filtered_stmt->bind_param("si", $search_param, $id);
$filtered_stmt->execute();
$filtered_result = $filtered_stmt->get_result();
$filtered_count = $filtered_result->fetch_assoc()['count'];

// Return JSON response
echo json_encode([
    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    "recordsTotal" => $total_records,
    "recordsFiltered" => $filtered_count,
    "data" => $categories
]);

$stmt->close();
$count_stmt->close();
$conn->close();
?>
