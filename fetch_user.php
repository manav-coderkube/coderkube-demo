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
        switch ($row['user_type']) {
            case 0:
                $row['user_type'] = "Admin";
                break;
            case 1:
                $row['user_type'] = "User ";
                break;
            case 2:
                $row['user_type'] = "Seller";
                break;
            default:
                $row['user_type'] = "Unknown";
        }
        $users[] = $row;
    }
}

echo json_encode($users);
?>
