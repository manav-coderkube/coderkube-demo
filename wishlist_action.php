<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$item_id = $_POST['item_id'];
$action = $_POST['action']; // 'add' or 'remove'

if ($action === 'add') {
    // Add to wishlist
    $query = "INSERT INTO tbl_wishlist (user_id, item_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $item_id);
    $stmt->execute();
    echo json_encode(['success' => 'Item added to wishlist']);
} elseif ($action === 'remove') {
    // Remove from wishlist
    $query = "DELETE FROM tbl_wishlist WHERE user_id = ? AND item_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $item_id);
    $stmt->execute();
    echo json_encode(['success' => 'Item removed from wishlist']);
}

$stmt->close();
$conn->close();
?>
