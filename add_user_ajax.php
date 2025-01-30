<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Retrieve form data
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $user_phone = $_POST['user_phone'];
    $user_gender = intval($_POST['user_gender']);
    $user_type = intval($_POST['user_type']);
    $user_password = $_POST['user_password'];  // Retrieve the password
    $user_image = $_FILES['user_image']['name'];  // Image field

    // Check if an image is uploaded
    if (empty($user_image)) {
        echo json_encode(["success" => false, "message" => "Please upload a profile image."]);
        exit();  // Stop execution if no image is uploaded
    }

    // Hash the password
    $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

    // File upload logic for the profile image
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($user_image);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate image file type (Optional)
    $allowedFileTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedFileTypes)) {
        echo json_encode(["success" => false, "message" => "Invalid file type. Only JPG, JPEG, PNG & GIF are allowed."]);
        exit();
    }

    // Move the uploaded image to the target directory
    if (!move_uploaded_file($_FILES['user_image']['tmp_name'], $target_file)) {
        echo json_encode(["success" => false, "message" => "Sorry, there was an error uploading the image."]);
        exit();
    }

    // Insert into the database with image and hashed password
    $stmt = $conn->prepare("INSERT INTO tbl_user (user_name, user_email, user_phone, user_gender, user_type, user_image, user_password) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiiss", $user_name, $user_email, $user_phone, $user_gender, $user_type, $user_image, $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User added successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to add user."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>
