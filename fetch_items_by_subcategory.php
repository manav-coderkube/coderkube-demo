<?php
include 'db_connect.php';

if (isset($_GET['category_id']) && isset($_GET['subcategory_id'])) {
    $category_id = $_GET['category_id'];
    $subcategory_id = $_GET['subcategory_id'];
    
    $query = "SELECT i.item_id, i.item_name, i.item_image, i.item_price, c.category_name, s.subcategory_name
          FROM tbl_items i
          JOIN tbl_category c ON i.category_id = c.category_id
          JOIN tbl_subcategories s ON i.subcategory_id = s.subcategory_id
          WHERE i.category_id = ? AND i.subcategory_id = ?  AND i.item_stock > 0
          ORDER BY RAND()
          LIMIT 9";

    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $category_id, $subcategory_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    echo json_encode($items);
}
?>
