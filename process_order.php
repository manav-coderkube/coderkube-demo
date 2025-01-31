<?php
session_start();
include 'db_connect.php';  

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $item_id = intval($_POST['item_id']);
    $total_amount = floatval($_POST['total_amount']);
    $quantity = 1; // Since only 1 item is purchased
    $status = 0;

    $conn->begin_transaction();  // Start Transaction

    try {
        // Insert order into orders table
        $stmt = $conn->prepare("INSERT INTO tbl_orders (user_id, item_id, item_quantity, total_amount, order_status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiidi", $user_id, $item_id, $quantity, $total_amount, $status);
        
        if (!$stmt->execute()) {
            throw new Exception("Error placing order.");
        }
        $stmt->close();

        // Deduct 1 stock from the item
        $stmt = $conn->prepare("UPDATE tbl_items SET item_stock = item_stock - 1 WHERE item_id = ? AND item_stock > 0");
        $stmt->bind_param("i", $item_id);
        
        if (!$stmt->execute() || $stmt->affected_rows == 0) {
            throw new Exception("Insufficient stock.");
        }
        $stmt->close();

        // Remove item from cart after successful order
        $stmt = $conn->prepare("DELETE FROM tbl_cart WHERE user_id = ? AND item_id = ?");
        $stmt->bind_param("ii", $user_id, $item_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();  // Commit Transaction

        echo "<script>alert('Order placed successfully!'); window.location.href='orders.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();  // Rollback Transaction on Error
        echo "<script>alert('".$e->getMessage()."'); window.history.back();</script>";
    }
}

$conn->close();
?>
