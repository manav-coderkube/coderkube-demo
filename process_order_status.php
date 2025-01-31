<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

if (isset($_POST['order_id'], $_POST['action'])) {
    $order_id = intval($_POST['order_id']);
    $action = $_POST['action'];

    // Start Transaction
    $conn->begin_transaction();

    try {
        if ($action == 'cancel') {
            // Update status to 2 (Failed)
            $stmt = $conn->prepare("UPDATE tbl_orders SET order_status = 2 WHERE order_id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $stmt->close();
        } elseif ($action == 'continue') {
            // Update status back to 0 (Pending)
            $stmt = $conn->prepare("UPDATE tbl_orders SET order_status = 0 WHERE order_id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $stmt->close();
        }

        $conn->commit(); // Commit Transaction

        // Return success response
        echo json_encode(['success' => true]);
        exit;
    } catch (Exception $e) {
        $conn->rollback(); // Rollback Transaction on Error

        // Return error response
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

$conn->close();
?>
