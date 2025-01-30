<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['category_id'], $_POST['category_name'], $_POST['user_id'])) {
        $categoryId = $_POST['category_id'];
        $categoryName = $_POST['category_name'];
        $userId = $_POST['user_id'];
        $response = [];

        // Validate inputs
        if (empty($categoryName)) {
            $response['success'] = false;
            $response['message'] = "Category name is required.";
            echo json_encode($response);
            exit();
        }

        $sql = "UPDATE tbl_category 
                SET category_name = ?
                WHERE category_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $categoryName, $categoryId);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Category updated successfully.";
        } else {
            $response['success'] = false;
            $response['message'] = "Failed to update Category.";
        }

        echo json_encode($response);
    } else {
        $response['success'] = false;
        $response['message'] = "Invalid input.";
        echo json_encode($response);
    }
}
?>
