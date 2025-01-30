<?php
session_start();
include 'db_connect.php';

// Fetch updated categories from the database
$query = "SELECT category_id, category_name FROM tbl_category";
$result = $conn->query($query);

$output = '<option value="">Select Category</option>';

while ($row = $result->fetch_assoc()) {
    $output .= '<option value="' . $row['category_id'] . '">' . htmlspecialchars($row['category_name']) . '</option>';
}

echo $output;
?>
