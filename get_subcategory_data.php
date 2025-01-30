<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['subcategory_id'])) {
    $subcategory_id = $_POST['subcategory_id'];

    // Fetch the subcategory data from the database
    $query = "SELECT * FROM Tbl_SubCategories WHERE subcategory_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $subcategory_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $subcategory_data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $subcategory_data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Subcategory not found']);
    }

    $stmt->close();
    $conn->close();
}
?>
