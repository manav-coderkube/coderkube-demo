<?php
require 'db_connect.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['items_id'])) {
    $items_id = $_POST['items_id'];

    $stmt = $conn->prepare("SELECT item_id, item_name, item_price, item_stock, item_image FROM tbl_items WHERE item_id = ?");
    $stmt->bind_param("i", $items_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $response['success'] = true;
        $response['data'] = $result->fetch_assoc();
    } else {
        $response['success'] = false;
        $response['message'] = "Item not found.";
    }

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

?>
