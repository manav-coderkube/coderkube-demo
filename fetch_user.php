<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    http_response_code(403); // Forbidden
    echo json_encode(["error" => "Unauthorized access"]);
    exit();
}

$type = $_SESSION['user_type'];

// Fetch users from the database
$sql = "SELECT user_id, user_name, user_email, user_phone, user_gender, user_type FROM tbl_user WHERE user_type = $type";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['user_gender'] = $row['user_gender'] == 0 ? "Male" : "Female";
        $row['user_type'] = match ($row['user_type']) {
            0 => "Admin",
            1 => "User",
            2 => "Seller",
            default => "Unknown",
        };
        $users[] = $row;
    }
}

echo json_encode($users);
?>
