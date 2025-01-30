<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $items_id = $_POST['item_id'];
    $item_name = $_POST['item_name'];
    $item_price = $_POST['item_price'];
    $item_stock = $_POST['item_stock'];

    // Handling file upload if a new image is provided
    if (!empty($_FILES['item_image']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["item_image"]["name"]);
        move_uploaded_file($_FILES["item_image"]["tmp_name"], $target_file);
        $item_image = $_FILES["item_image"]["name"];

        $stmt = $conn->prepare("UPDATE tbl_items SET item_name=?, item_price=?, item_stock=?, item_image=? WHERE item_id=?");
        $stmt->bind_param("sdisi", $item_name, $item_price, $item_stock, $item_image, $items_id);
    } else {
        $stmt = $conn->prepare("UPDATE tbl_items SET item_name=?, item_price=?, item_stock=? WHERE item_id=?");
        $stmt->bind_param("sdis", $item_name, $item_price, $item_stock, $items_id);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update item']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
