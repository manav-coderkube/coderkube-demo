<?php
session_start();
include 'db_connect.php';

if (isset($_POST['category_id'])) {
    $categoryId = $_POST['category_id'];
    $response = [];

    // Check if category exists
    $sql = "SELECT * FROM tbl_category WHERE category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Category exists, proceed to delete
        $sqlDelete = "DELETE FROM tbl_category WHERE category_id = ?";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bind_param('i', $categoryId);

        if ($stmtDelete->execute()) {
            $response['success'] = true;
            $response['message'] = "Category deleted successfully.";
        } else {
            $response['success'] = false;
            $response['message'] = "Failed to delete Category.";
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Category not found.";
    }

    echo json_encode($response);
}
?>
