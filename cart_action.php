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

// Check if the item is already in the user's cart
$query_check = "SELECT * FROM tbl_cart WHERE user_id = ? AND item_id = ?";
$stmt_check = $conn->prepare($query_check);
$stmt_check->bind_param("ii", $user_id, $item_id);
$stmt_check->execute();
$check_result = $stmt_check->get_result();

if ($action == 'add' && $check_result->num_rows == 0) {
    // Add item to cart
    $query_add = "INSERT INTO tbl_cart (user_id, item_id) VALUES (?, ?)";
    $stmt_add = $conn->prepare($query_add);
    $stmt_add->bind_param("ii", $user_id, $item_id);
    $stmt_add->execute();
    echo json_encode(['success' => 'Item added to cart']);
} elseif ($action == 'remove' && $check_result->num_rows > 0) {
    // Remove item from cart
    $query_remove = "DELETE FROM tbl_cart WHERE user_id = ? AND item_id = ?";
    $stmt_remove = $conn->prepare($query_remove);
    $stmt_remove->bind_param("ii", $user_id, $item_id);
    $stmt_remove->execute();
    echo json_encode(['success' => 'Item removed from cart']);
} elseif ($action == 'buy' && isset($_POST['item_id'])) {
    $item_id = intval($_POST['item_id']);

    // Insert into buy table
    $stmt = $conn->prepare("INSERT INTO tbl_buy (user_id, item_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $item_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => 'Item purchased successfully.']);
    } else {
        echo json_encode(['error' => 'Error processing purchase.']);
    }
}else {
    echo json_encode(['error' => 'Action not valid']);
}

$stmt_check->close();
$conn->close();
?>
