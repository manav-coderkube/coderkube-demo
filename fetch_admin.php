<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 0) {
    http_response_code(403); // Forbidden
    echo json_encode(["error" => "Unauthorized access"]);
    exit();
}

$type = $_SESSION['user_type'];

$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$order_column_index = $_POST['order'][0]['column'];
$order_dir = $_POST['order'][0]['dir'];

$columns = ['user_id', 'user_name', 'user_email', 'user_phone', 'user_gender', 'user_type'];
$order_column = isset($columns[$order_column_index]) ? $columns[$order_column_index] : 'user_name';

$order_dir = ($order_dir === 'asc' || $order_dir === 'desc') ? $order_dir : 'asc';

$sql = "SELECT user_id, user_name, user_email, user_phone, user_gender, user_type 
        FROM tbl_user WHERE user_name LIKE ? OR user_email LIKE ? OR user_phone LIKE ? 
        ORDER BY $order_column $order_dir 
        LIMIT ?, ?";

$stmt = $conn->prepare($sql);

$search_param = "%$search%";
$stmt->bind_param("sssii",  $search_param, $search_param, $search_param, $start, $length);

$stmt->execute();
$result = $stmt->get_result();

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['user_gender'] = $row['user_gender'] == 0 ? "Male" : "Female";
        switch ($row['user_type']) {
            case 0:
                $row['user_type'] = "Admin";
                break;
            case 1:
                $row['user_type'] = "User";
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

$sql_count = "SELECT COUNT(*) as total FROM tbl_user";
$count_stmt = $conn->prepare($sql_count);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$row_count = $count_result->fetch_assoc();
$total_records = $row_count['total'];

echo json_encode([
    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    "recordsTotal" => $total_records,
    "recordsFiltered" => $total_records,
    "data" => $users
]);

$stmt->close();
$count_stmt->close();
$conn->close();
?>
