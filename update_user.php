<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {

    $user_id = intval($_POST['user_id']);
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $user_phone = $_POST['user_phone'];
    $user_gender = intval($_POST['user_gender']);
    $user_type = intval($_POST['user_type']);
    $user_image = $_FILES['user_image']['name'];
    
    if (!empty($user_image)) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($user_image);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (move_uploaded_file($_FILES['user_image']['tmp_name'], $target_file)) {    
            $stmt = $conn->prepare("UPDATE tbl_user SET user_name = ?, user_email = ?, user_phone = ?, user_gender = ?, user_type = ?, user_image = ? WHERE user_id = ?");
            $stmt->bind_param("sssiiss", $user_name, $user_email, $user_phone, $user_gender, $user_type, $user_image, $user_id);
        } else {
            echo json_encode(["success" => false, "message" => "Sorry, there was an error uploading your file."]);
            exit();
        }
    } else {
        $stmt = $conn->prepare("UPDATE tbl_user SET user_name = ?, user_email = ?, user_phone = ?, user_gender = ?, user_type = ? WHERE user_id = ?");
        $stmt->bind_param("sssiis", $user_name, $user_email, $user_phone, $user_gender, $user_type, $user_id);
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>
