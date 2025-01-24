<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    
    
    $stmt = $conn->prepare("DELETE FROM tbl_user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        
        echo json_encode(["message" => "User deleted successfully."]);
    } else {
        
        echo json_encode(["message" => "Failed to delete user."]);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(400); 
    echo json_encode(["message" => "Invalid request."]);
}
?>
