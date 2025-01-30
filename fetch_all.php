<?php
session_start();
include 'db_connect.php';

// Get parameters from DataTables
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$order_column_index = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0;
$order_dir = isset($_POST['order'][0]['dir']) && ($_POST['order'][0]['dir'] === 'asc' || $_POST['order'][0]['dir'] === 'desc') ? $_POST['order'][0]['dir'] : 'asc';
$page_user_type = isset($_POST['user_type']) ? intval($_POST['user_type']) : -1; // Pass `user_type` from the front-end (e.g., Admin, User, Seller)

// Define columns for ordering
$columns = ['user_id', 'user_name', 'user_image', 'user_email', 'user_phone', 'user_gender', 'user_type'];
$order_column = isset($columns[$order_column_index]) ? $columns[$order_column_index] : 'user_name';

// Map search values for specific terms
if (strtolower($search) === "male") {
    $search_gender = 0; // Male
} elseif (strtolower($search) === "female") {
    $search_gender = 1; // Female
} else {
    $search_gender = -1; // No filtering by gender
}

if (strtolower($search) === "admin") {
    $search_user_type = 0; // Admin
} elseif (strtolower($search) === "user") {
    $search_user_type = 1; // User
} elseif (strtolower($search) === "seller") {
    $search_user_type = 2; // Seller
} else {
    $search_user_type = -1; // No filtering by user_type
}


// Base query with optional filtering by user_type
$sql = "SELECT user_id, user_name, user_image, user_email, user_phone, user_gender, user_type 
        FROM tbl_user 
        -- tbl_user WHERE (user_name LIKE ? OR user_email LIKE ? OR user_phone LIKE ?)
        WHERE (user_name LIKE ? OR user_email LIKE ? OR user_phone LIKE ? OR user_gender LIKE ? AND user_type LIKE ?)";

if ($page_user_type >= 0) { // Add user_type condition if provided
    $sql .= " AND user_type = ?";
}

$sql .= " ORDER BY $order_column $order_dir LIMIT ?, ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => "Failed to prepare statement"]);
    exit();
}

// Bind parameters
$search_param = "%" . $search . "%";
if ($page_user_type >= 0) {
    $stmt->bind_param("ssssiiii", $search_param, $search_param, $search_param, $search_param, $search_param, $page_user_type, $start, $length);
} else {
    $stmt->bind_param("sssssii", $search_param, $search_param, $search_param, $search_param, $search_param, $start, $length);
}
// Execute and fetch results
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

// Get total records for DataTables
$sql_count = "SELECT COUNT(*) as total FROM tbl_user WHERE (user_name LIKE ? OR user_email LIKE ? OR user_phone LIKE ?)";
if ($page_user_type >= 0) {
    $sql_count .= " AND user_type = ?";
}

$count_stmt = $conn->prepare($sql_count);
if ($count_stmt === false) {
    echo json_encode(["error" => "Failed to prepare statement for count"]);
    exit();
}

if ($page_user_type >= 0) {
    $count_stmt->bind_param("sssi", $search_param, $search_param, $search_param, $page_user_type);
} else {
    $count_stmt->bind_param("sss", $search_param, $search_param, $search_param);
}

$count_stmt->execute();
$count_result = $count_stmt->get_result();
$row_count = $count_result->fetch_assoc();
$total_records = $row_count['total'];

$sql_filtered_count = "SELECT COUNT(*) as count FROM tbl_user WHERE (user_name LIKE ? OR user_email LIKE ? OR user_phone LIKE ?)";
if ($page_user_type >= 0) {
    $sql_filtered_count .= " AND user_type = ?";
}

$filtered_stmt = $conn->prepare($sql_filtered_count);
if ($page_user_type >= 0) {
    $filtered_stmt->bind_param("sssi", $search_param, $search_param, $search_param, $page_user_type);
} else {
    $filtered_stmt->bind_param("sss", $search_param, $search_param, $search_param);
}
$filtered_stmt->execute();
$filtered_result = $filtered_stmt->get_result();
$filtered_count = $filtered_result->fetch_assoc()['count'];

// Return JSON response
echo json_encode([
    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    "recordsTotal" => $total_records,
    "recordsFiltered" => $filtered_count,
    "data" => $users
]);

$stmt->close();
$count_stmt->close();
$conn->close();
?>
