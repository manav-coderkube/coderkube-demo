<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Query to fetch wishlist items for the logged-in user
$query = "
    SELECT i.item_id, i.item_name, i.item_image, i.item_price, i.item_stock, c.category_name, s.subcategory_name
    FROM tbl_wishlist w
    JOIN tbl_items i ON w.item_id = i.item_id
    JOIN tbl_category c ON i.category_id = c.category_id
    JOIN tbl_subcategories s ON i.subcategory_id = s.subcategory_id
    WHERE w.user_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$wishlist_items = [];
if ($result && $result->num_rows > 0) {
    while ($item = $result->fetch_assoc()) {
        $wishlist_items[] = $item;
    }
}

echo json_encode($wishlist_items);

$stmt->close();
$conn->close();
?>
