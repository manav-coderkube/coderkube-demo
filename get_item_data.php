<?php
include('db_connect.php');

$item_id = $_POST['item_id'];
$query = "SELECT i.*, c.category_name, s.subcategory_name
          FROM tbl_items i
          LEFT JOIN tbl_category c ON i.category_id = c.category_id
          LEFT JOIN tbl_subcategories s ON i.subcategory_id = s.subcategory_id
          WHERE i.item_id = '$item_id'";

$result = mysqli_query($conn, $query);
$item = mysqli_fetch_assoc($result);

echo json_encode(['success' => true, 'data' => $item]);
?>
