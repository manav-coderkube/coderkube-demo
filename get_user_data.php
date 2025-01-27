<?php
// Include the database connection
include 'db_connect.php';

// Check if the user_id is set in the request
if (isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);  // Make sure to sanitize the input
    
    // Prepare the SQL query to fetch user data
    $stmt = $conn->prepare("SELECT user_id, user_name, user_email, user_phone, user_gender, user_type, user_image FROM tbl_user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);  // Bind the user_id parameter to the query
    
    // Execute the query
    $stmt->execute();
    
    // Fetch the result
    $result = $stmt->get_result();
    
    // Check if a user is found
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(["success" => true, "data" => $user]);  // Return the user data as a JSON response
    } else {
        echo json_encode(["success" => false, "message" => "User not found"]);  // Handle case when no user is found
    }
    
    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // If user_id is not provided, return an error message
    echo json_encode(["success" => false, "message" => "User ID not provided"]);
}
?>
