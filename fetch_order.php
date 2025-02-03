<?php
include('db_connect.php');

// Get parameters for pagination and search
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$offset = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

// Get total number of records (without search)
$totalQuery = "SELECT COUNT(*) as total FROM tbl_orders";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRecords = $totalRow['total'];

// Prepare search condition
$searchCondition = "";
if ($searchValue) {
    $searchCondition = "WHERE u.user_name LIKE '%$searchValue%' 
                        OR i.item_name LIKE '%$searchValue%' 
                        OR o.order_date LIKE '%$searchValue%'";
}

// Get filtered total number of records
$filteredQuery = "SELECT COUNT(*) as filtered FROM tbl_orders o
                  LEFT JOIN tbl_user u ON o.user_id = u.user_id
                  LEFT JOIN tbl_items i ON o.item_id = i.item_id
                  $searchCondition";
$filteredResult = mysqli_query($conn, $filteredQuery);
$filteredRow = mysqli_fetch_assoc($filteredResult);
$filteredRecords = $filteredRow['filtered'];

// Get paginated order data
$query = "SELECT o.order_id, o.user_id, o.item_id, o.order_date, o.item_quantity, o.order_status, 
                 u.user_name, i.item_name, i.item_image, 
                 (o.item_quantity * i.item_price) AS total_amount
          FROM tbl_orders o
          LEFT JOIN tbl_user u ON o.user_id = u.user_id
          LEFT JOIN tbl_items i ON o.item_id = i.item_id
          $searchCondition
          LIMIT $limit OFFSET $offset";


$result = mysqli_query($conn, $query);
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Return data as JSON
echo json_encode([
    'draw' => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $filteredRecords,
    'data' => $data
]);
?>
