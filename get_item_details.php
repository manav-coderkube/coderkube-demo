<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$item_id = $_GET['item_id'];
$user_id = $_SESSION['user_id'];

// Fetch item details with category and subcategory names
$query_item = "SELECT i.*, c.category_name, s.subcategory_name 
               FROM tbl_items i
               LEFT JOIN tbl_category c ON i.category_id = c.category_id
               LEFT JOIN tbl_subcategories s ON i.subcategory_id = s.subcategory_id
               WHERE i.item_id = ?  AND i.item_stock > 0";
$stmt = $conn->prepare($query_item);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item_result = $stmt->get_result();
$item = $item_result->fetch_assoc();

if (!$item) {
    echo json_encode(['error' => 'Item not found']);
    exit();
}

// Check if item is already in the user's wishlist
$query_wishlist = "SELECT * FROM tbl_wishlist WHERE user_id = ? AND item_id = ? ";
$stmt_wishlist = $conn->prepare($query_wishlist);
$stmt_wishlist->bind_param("ii", $user_id, $item_id);
$stmt_wishlist->execute();
$wishlist_result = $stmt_wishlist->get_result();
$is_in_wishlist = $wishlist_result->num_rows > 0;
// Check if item is already in the cart
$query_cart = "SELECT * FROM tbl_cart WHERE user_id = ? AND item_id = ? ";
$stmt_cart = $conn->prepare($query_cart);
$stmt_cart->bind_param("ii", $user_id, $item_id);
$stmt_cart->execute();
$cart_result = $stmt_cart->get_result();
$is_in_cart = $cart_result->num_rows > 0;

$item_details = [
    'item_id' => $item['item_id'],
    'item_name' => $item['item_name'],
    'item_image' => $item['item_image'],
    'item_price' => $item['item_price'],
    'category_name' => $item['category_name'],
    'subcategory_name' => $item['subcategory_name'],
    'is_in_wishlist' => $is_in_wishlist,
    'is_in_cart' => $is_in_cart
];

echo json_encode($item_details);

$stmt->close();
$stmt_wishlist->close();
$conn->close();
?>
