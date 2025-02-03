<?php
include 'db_connect.php'; 

if (isset($_POST['order_id']) && isset($_POST['order_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = intval($_POST['order_status']);

    $sql = "UPDATE tbl_orders SET order_status = ? WHERE order_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $status, $order_id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => "Order status updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update order status"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
