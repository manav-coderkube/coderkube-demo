<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated user details from the form
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $user_phone = $_POST['user_phone'];
    $user_gender = $_POST['user_gender'];
    $user_password = $_POST['user_password'];

    // Handle the profile image upload
    if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] == 0) {
        $file_name = $_FILES['user_image']['name'];
        $file_tmp = $_FILES['user_image']['tmp_name'];
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');

        // Check if the file extension is valid
        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            $upload_dir = 'uploads/';
            $new_file_name = 'profile_' . $user_id . '.' . $file_extension;
            $upload_path = $upload_dir . $new_file_name;

            // Move the uploaded file to the uploads directory
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $user_image = $new_file_name;
            } else {
                die("Failed to upload image.");
            }
        } else {
            die("Invalid file type.");
        }
    } else {
        // If no image was uploaded, keep the existing image
        $stmt = $conn->prepare("SELECT user_image FROM tbl_user WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $user_image = $user['user_image'];
    }

    // Update the user details in the database
    $stmt = $conn->prepare("UPDATE tbl_user SET user_name = ?, user_email = ?, user_phone = ?, user_gender = ?, user_password = ?, user_image = ? WHERE user_id = ?");
    $stmt->bind_param("ssssssi", $user_name, $user_email, $user_phone, $user_gender, $user_password, $user_image, $user_id);

    if ($stmt->execute()) {
        header('Location: view_profile.php'); // Redirect back to the profile page
    } else {
        die("Failed to update profile.");
    }

    $stmt->close();
    $conn->close();
}
?>
