<?php
// fetch_subcategories.php
include 'db_connect.php';

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    $query = "SELECT subcategory_id, subcategory_name FROM tbl_subcategories WHERE category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $subcategories = [];
    while ($row = $result->fetch_assoc()) {
        $subcategories[] = $row;
    }

    echo json_encode($subcategories);
}
?>
