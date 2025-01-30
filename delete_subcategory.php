<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['subcategory_id'])) {
    $subcategory_id = $_POST['subcategory_id'];

    // Delete the subcategory from the database
    $query = "DELETE FROM Tbl_SubCategories WHERE subcategory_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $subcategory_id);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Subcategory deleted successfully']);
    } else {
        echo json_encode(['message' => 'Error deleting subcategory']);
    }

    $stmt->close();
    $conn->close();
}
?>
