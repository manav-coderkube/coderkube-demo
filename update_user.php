<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {

    $user_id = intval($_POST['user_id']);
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $user_phone = $_POST['user_phone'];
    $user_gender = intval($_POST['user_gender']);
    $user_type = intval($_POST['user_type']);


    $stmt = $conn->prepare("UPDATE tbl_user SET user_name = ?, user_email = ?, user_phone = ?, user_gender = ?, user_type = ? WHERE user_id = ?");
    $stmt->bind_param("sssiis", $user_name, $user_email, $user_phone, $user_gender, $user_type, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update user."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>
