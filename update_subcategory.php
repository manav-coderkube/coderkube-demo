<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['subcategory_id'], $_POST['subcategory_name'], $_POST['category_id'])) {
    $subcategory_id = $_POST['subcategory_id'];
    $subcategory_name = $_POST['subcategory_name'];
    $category_id = $_POST['category_id'];

    // Update the subcategory in the database
    $query = "UPDATE tbl_SubCategories SET subcategory_name = ? WHERE subcategory_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $subcategory_name, $subcategory_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Subcategory updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating subcategory']);
    }

    $stmt->close();
    $conn->close();
}
?>
