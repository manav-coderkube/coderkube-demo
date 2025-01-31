<?php
include('db_connect.php');

// Get the parameters for pagination and search
$limit = isset($_POST['length']) ? (int)$_POST['length'] : 10;  // Default to 10 rows per page
$offset = isset($_POST['start']) ? (int)$_POST['start'] : 0;     // Default to first page
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

// Get the total number of records for pagination info (without search)
$totalQuery = "SELECT COUNT(*) as total FROM tbl_items";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRecords = $totalRow['total'];

// Prepare the search condition
$searchCondition = "";
if ($searchValue) {
    $searchCondition = "WHERE i.item_name LIKE '%$searchValue%' 
                        OR i.item_price LIKE '%$searchValue%' 
                        OR i.item_stock LIKE '%$searchValue%'";
}

// Get the filtered total number of records
$filteredQuery = "SELECT COUNT(*) as filtered FROM tbl_items i
                  LEFT JOIN tbl_category c ON i.category_id = c.category_id
                  LEFT JOIN tbl_subcategories s ON i.subcategory_id = s.subcategory_id
                  $searchCondition";
$filteredResult = mysqli_query($conn, $filteredQuery);
$filteredRow = mysqli_fetch_assoc($filteredResult);
$filteredRecords = $filteredRow['filtered'];

// Get the data for the current page with search condition
$query = "SELECT i.*, c.category_name, s.subcategory_name
          FROM tbl_items i
          LEFT JOIN tbl_category c ON i.category_id = c.category_id
          LEFT JOIN tbl_subcategories s ON i.subcategory_id = s.subcategory_id
          $searchCondition
          LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Return the data, total records, and filtered records for pagination and search
echo json_encode([
    'draw' => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,  // DataTables draw counter
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $filteredRecords,  // Filtered count
    'data' => $data
]);
?>
