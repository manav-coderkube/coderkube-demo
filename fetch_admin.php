<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 0) {
    http_response_code(403); // Forbidden
    echo json_encode(["error" => "Unauthorized access"]);
    exit();
}

$type = $_SESSION['user_type'];

// Get pagination and search parameters
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$order_column_index = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0;
$order_dir = isset($_POST['order'][0]['dir']) && ($_POST['order'][0]['dir'] === 'asc' || $_POST['order'][0]['dir'] === 'desc') ? $_POST['order'][0]['dir'] : 'asc';

// Columns to be used for ordering
$columns = ['user_id', 'user_name', 'user_image', 'user_email', 'user_phone', 'user_gender', 'user_type'];
$order_column = isset($columns[$order_column_index]) ? $columns[$order_column_index] : 'user_name';

// Prepare the SQL query for fetching the filtered and paginated users
$sql = "SELECT user_id, user_name, user_image, user_email, user_phone, user_gender, user_type 
        FROM tbl_user 
        WHERE user_name LIKE ? OR user_email LIKE ? OR user_phone LIKE ? 
        ORDER BY $order_column $order_dir 
        LIMIT ?, ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => "Failed to prepare statement"]);
    exit();
}

// Bind parameters for search
$search_param = "%" . $search . "%";
$stmt->bind_param("sssii", $search_param, $search_param, $search_param, $start, $length);

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Format the gender and user_type fields
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

// Get total record count for pagination (including any filter)
$sql_count = "SELECT COUNT(*) as total FROM tbl_user WHERE user_name LIKE ? OR user_email LIKE ? OR user_phone LIKE ?";
$count_stmt = $conn->prepare($sql_count);
if ($count_stmt === false) {
    echo json_encode(["error" => "Failed to prepare statement for count"]);
    exit();
}

// Bind parameters for count query
$count_stmt->bind_param("sss", $search_param, $search_param, $search_param);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$row_count = $count_result->fetch_assoc();
$total_records = $row_count['total'];

// Return the JSON response to DataTables
echo json_encode([
    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1, // Draw counter for DataTables
    "recordsTotal" => $total_records, // Total records without filter
    "recordsFiltered" => $total_records, // Filtered records (same as total for now)
    "data" => $users // Fetched user data
]);

// Close the statements and database connection
$stmt->close();
$count_stmt->close();
$conn->close();
?>
