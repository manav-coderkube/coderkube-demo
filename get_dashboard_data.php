<?php
// Include database connection
include('db_connection.php');

// Query for count of admins, users, and sellers
$query = "SELECT 
    (SELECT COUNT(*) FROM users WHERE user_type = 0) AS total_admins,
    (SELECT COUNT(*) FROM users WHERE user_type = 1) AS total_users,
    (SELECT COUNT(*) FROM users WHERE user_type = 2) AS total_sellers";

$result = mysqli_query($conn, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo json_encode([
        'success' => true,
        'total_admins' => $row['total_admins'],
        'total_users' => $row['total_users'],
        'total_sellers' => $row['total_sellers']
    ]);
} else {
    echo json_encode(['success' => false]);
}
?>
