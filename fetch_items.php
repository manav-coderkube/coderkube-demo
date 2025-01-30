<?php
session_start();
include 'db_connect.php';
$id = $_SESSION['user_id']; // Get user_id from session

// Get parameters from DataTables
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$order_column_index = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0;
$order_dir = isset($_POST['order'][0]['dir']) && ($_POST['order'][0]['dir'] === 'asc' || $_POST['order'][0]['dir'] === 'desc') ? $_POST['order'][0]['dir'] : 'asc';

// Define columns for ordering
$columns = ['item_id', 'subcategory_id', 'item_name', 'item_image', 'item_price', 'item_stock', 'user_id'];
$order_column = isset($columns[$order_column_index]) ? $columns[$order_column_index] : 'item_name';

// Base query with optional filtering by user_id
$sql = "SELECT i.item_id, i.item_name, i.item_image, i.item_price, i.item_stock, i.subcategory_id, s.subcategory_name, i.user_id
        FROM tbl_items i
        LEFT JOIN tbl_subcategories s ON i.subcategory_id = s.subcategory_id
        WHERE i.user_id = ? AND (i.item_name LIKE ? OR i.item_price LIKE ?)
        ORDER BY $order_column $order_dir LIMIT ?, ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => "Failed to prepare statement"]);
    exit();
}

// Bind parameters
$search_param = "%" . $search . "%";
$stmt->bind_param("ssssi", $id, $search_param, $search_param, $start, $length);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

// Get total records for DataTables
$sql_count = "SELECT COUNT(*) as total FROM tbl_items i WHERE i.user_id = ? AND (i.item_name LIKE ? OR i.subcategory_id LIKE ?)";
$count_stmt = $conn->prepare($sql_count);
if ($count_stmt === false) {
    echo json_encode(["error" => "Failed to prepare statement for count"]);
    exit();
}
$count_stmt->bind_param("sss", $id, $search_param, $search_param);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$row_count = $count_result->fetch_assoc();
$total_records = $row_count['total'];

// Get filtered records count
$sql_filtered_count = "SELECT COUNT(*) as count FROM tbl_items i WHERE i.user_id = ? AND (i.item_name LIKE ? OR i.subcategory_id LIKE ?)";
$filtered_stmt = $conn->prepare($sql_filtered_count);
$filtered_stmt->bind_param("sss", $id, $search_param, $search_param);
$filtered_stmt->execute();
$filtered_result = $filtered_stmt->get_result();
$filtered_count = $filtered_result->fetch_assoc()['count'];

// Return JSON response
echo json_encode([
    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    "recordsTotal" => $total_records,
    "recordsFiltered" => $filtered_count,
    "data" => $items
]);

$stmt->close();
$count_stmt->close();
$filtered_stmt->close();
$conn->close();
?>
