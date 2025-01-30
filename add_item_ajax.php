<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if (isset($_POST['category_id'],$_POST['subcategory_id'], $_POST['item_name'], $_POST['item_price'], $_POST['item_stock'], $_FILES['item_image'], $_POST['user_id'])) {
    $category_id = $_POST['category_id'];
    $subcategory_id = $_POST['subcategory_id'];
    $item_name = $_POST['item_name'];
    $item_price = $_POST['item_price'];
    $item_stock = $_POST['item_stock'];
    $user_id = $_POST['user_id'];

    // Handle file upload
    $item_image = $_FILES['item_image'];
    $image_name = time() . '_' . basename($item_image['name']);
    $target_dir = 'uploads/';
    $target_file = $target_dir . $image_name;

    if (move_uploaded_file($item_image['tmp_name'], $target_file)) {
        // Prepare SQL query to insert item
        $query = "INSERT INTO tbl_items (category_id, subcategory_id, item_name, item_image, item_price, item_stock, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iissdii", $category_id,$subcategory_id, $item_name, $image_name, $item_price, $item_stock, $user_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Item added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error adding item']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error uploading image']);
    }
    $conn->close();
}
?>
