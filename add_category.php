<?php
session_start();  // Start the session to access session variables
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
    exit;
}

$id = $_SESSION['user_id'];  // User ID from session

// Check if the form for adding a category is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['category_name']) && !empty($_POST['category_name'])) {
        $category_name = trim($_POST['category_name']);
        
        // Check if the category name already exists
        $category_check = $conn->prepare("SELECT category_id FROM tbl_category WHERE category_name = ?");
        $category_check->bind_param("s", $category_name);
        $category_check->execute();
        $category_check->store_result();

        if ($category_check->num_rows > 0) {
            // Category name already exists
            echo json_encode(['success' => false, 'message' => 'Category name already exists.']);
        } else {
            // Category name is unique, proceed with the insertion
            if (strlen($category_name) < 3) {
                echo json_encode(['success' => false, 'message' => 'Category name should be at least 3 characters long.']);
            } else {
                $stmt = $conn->prepare("INSERT INTO tbl_category (category_name, user_id) VALUES (?, ?)");
                $stmt->bind_param("si", $category_name, $id);
                if ($stmt->execute()) {
                    $category_id = $stmt->insert_id;  // Get the last inserted category_id
                    echo json_encode(['success' => true, 'category_id' => $category_id]);
                    exit();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error in adding category.']);
                }
                $stmt->close();
            }
        }
        $category_check->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Category name cannot be empty.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>
