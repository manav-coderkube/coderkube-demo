<?php
session_start();
include 'db_connect.php';

if (isset($_POST['category_id'])) {
    $categoryId = $_POST['category_id'];
    $response = [];

    $sql = "SELECT c.category_id, c.category_name, u.user_name, u.user_id
            FROM tbl_category c 
            JOIN tbl_user u ON c.user_id = u.user_id
            WHERE c.category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response['success'] = true;
        $response['data'] = $result->fetch_assoc();
    } else {
        $response['success'] = false;
        $response['message'] = "Category not found.";
    }

    echo json_encode($response);
}
?>
