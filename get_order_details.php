<?php
include 'db_connect.php';

if (isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    $sql = "SELECT o.order_id, u.user_name, i.item_name, o.order_date, o.item_quantity, i.item_image, o.total_amount, o.order_status 
            FROM tbl_orders o
            JOIN tbl_user u ON o.user_id = u.user_id
            JOIN tbl_items i ON o.item_id = i.item_id
            WHERE o.order_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "Order not found"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
