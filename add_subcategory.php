<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
    exit;
}

$id = $_SESSION['user_id'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add Subcategory
    if (isset($_POST['subcategory_name']) && !empty($_POST['subcategory_name']) && isset($_POST['category_id']) && !empty($_POST['category_id'])) {
        $subcategory_name = trim($_POST['subcategory_name']);
        $category_id = $_POST['category_id'];

        // Check if the subcategory name already exists within the selected category
        $subcategory_check = $conn->prepare("SELECT subcategory_id FROM tbl_subcategories WHERE subcategory_name = ? AND category_id = ?");
        $subcategory_check->bind_param("si", $subcategory_name, $category_id);
        $subcategory_check->execute();
        $subcategory_check->store_result();

        if ($subcategory_check->num_rows > 0) {
            // Subcategory name already exists within the selected category
            $subcategory_nameerr = "Subcategory name already exists in this category.";
            echo json_encode(['success' => false, 'message' => $subcategory_nameerr]);
        } else {
            // Subcategory name is unique, proceed with the insertion
            if (strlen($subcategory_name) < 3) {
                $subcategory_nameerr = "Subcategory name should be at least 3 characters long.";
                echo json_encode(['success' => false, 'message' => $subcategory_nameerr]);
            } else {
                // Check if the category exists before adding subcategory
                $category_check = $conn->prepare("SELECT category_id FROM tbl_category WHERE category_id = ?");
                $category_check->bind_param("i", $category_id);
                $category_check->execute();
                $category_check->store_result();

                if ($category_check->num_rows > 0) {
                    // Insert the subcategory if category exists
                    $stmt = $conn->prepare("INSERT INTO tbl_subcategories (subcategory_name, category_id, user_id) VALUES (?, ?, ?)");
                    $stmt->bind_param("sii", $subcategory_name, $category_id, $id);
                    if ($stmt->execute()) {
                        echo json_encode(['success' => true]);
                        exit();
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Error in adding subcategory.']);
                    }
                    $stmt->close();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Selected category does not exist.']);
                }
                $category_check->close();
            }
        }
        $subcategory_check->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>