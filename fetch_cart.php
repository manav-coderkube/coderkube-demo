<?php
session_start();
include 'db_connect.php'; // Ensure this file contains the DB connection

$user_id = $_SESSION['user_id']; // Assuming the user is logged in

if (!$user_id) {
    echo json_encode(['error' => 'User not logged in.']);
    exit;
}

$sql = "SELECT c.item_id, i.item_name, i.item_price, i.item_image, 
               cat.category_name, sub.subcategory_name
        FROM tbl_cart c
        JOIN tbl_items i ON c.item_id = i.item_id
        JOIN tbl_category cat ON i.category_id = cat.category_id
        JOIN tbl_subcategories sub ON i.subcategory_id = sub.subcategory_id
        WHERE c.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

echo json_encode($cart_items);

$stmt->close();
$conn->close();
?>
