<?php
include 'db_connect.php';

if (isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']); 
    $stmt = $conn->prepare("SELECT user_id, user_name, user_email, user_phone, user_gender, user_type, user_image FROM tbl_user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(["success" => true, "data" => $user]); 
    } else {
        echo json_encode(["success" => false, "message" => "User not found"]); 
    }
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "User ID not provided"]);
}
?>
