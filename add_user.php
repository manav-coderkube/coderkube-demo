<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 0) {
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['user_name'];

$nameErr = $emailErr = $mobilenoErr = $genderErr = $typeErr = $passwordErr = "";
$user_name = $user_email = $user_phone = $user_gender = $user_type = $user_password = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db_connect.php';

    // Get user input and validate
    if (empty($_POST["user_name"])) {  
        $nameErr = "Name is required";  
    } else {  
        $user_name = trim($_POST["user_name"]);
        if (!preg_match("/^(?!\s)(?!.*\s{2,})[a-zA-Z ]+$/", $user_name)) {  
            $nameErr = "Name should not contain consecutive spaces";  
        } else {
            // Check if the name already exists in the database
            $name_check = "SELECT * FROM tbl_user WHERE user_name = ?";
            $stmt = $conn->prepare($name_check);
            $stmt->bind_param("s", $user_name);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $nameErr = "Name is already taken.";
            }
            $stmt->close();
        }
    }

    // Validate Email
    if (empty($_POST["user_email"])) {  
        $emailErr = "Email is required";  
    } else {  
        $user_email = trim($_POST["user_email"]);  
        if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {  
            $emailErr = "Invalid email format";  
        } else {
            // Check if the email already exists in the database
            $email_check = "SELECT * FROM tbl_user WHERE user_email = ?";
            $stmt = $conn->prepare($email_check);
            $stmt->bind_param("s", $user_email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $emailErr = "Email is already taken.";
            }
            $stmt->close();
        }
    }

    // Validate Mobile No
    if (empty($_POST["user_phone"])) {  
        $mobilenoErr = "Mobile no is required";  
    } else {  
        $user_phone = trim($_POST["user_phone"]);
        if (!preg_match("/^[0-9]*$/", $user_phone)) {  
            $mobilenoErr = "Only numeric value is allowed.";  
        }  
        if (strlen($user_phone) != 10) {  
            $mobilenoErr = "Mobile no must contain 10 digits.";  
        } else {
            // Check if the mobile number already exists in the database
            $mobileno_check = "SELECT * FROM tbl_user WHERE user_phone = ?";
            $stmt = $conn->prepare($mobileno_check);
            $stmt->bind_param("s", $user_phone);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $mobilenoErr = "Mobile number is already registered.";
            }
            $stmt->close();
        }
    }

    // Validate Gender
    if (!isset($_POST["user_gender"])) {  
        $genderErr = "Gender is required";  
    } else {  
        $user_gender = $_POST["user_gender"];  
    }

    // Validate Type
    if (!isset($_POST['user_type'])) {  
        $typeErr = "Select User type.";  
    } else {  
        $user_type = $_POST["user_type"];  
    }

    // Validate Password
    if (empty($_POST["user_password"])) {  
        $passwordErr = "Password is required";  
    } else {  
        $user_password = $_POST["user_password"];
        if (strlen($user_password) < 6) {  
            $passwordErr = "Password must be at least 6 characters.";  
        }  
    }

    // If no errors, proceed with inserting into the database
    if (empty($nameErr) && empty($emailErr) && empty($mobilenoErr) && empty($genderErr) && empty($typeErr) && empty($passwordErr)) {
        // Hash password for security
        $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO tbl_user (user_name, user_email, user_phone, user_gender, user_type, user_password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $user_name, $user_email, $user_phone, $user_gender, $user_type, $hashed_password);

        if ($stmt->execute()) {
            $message = "User added successfully!";
        } else {
            $message = "Error adding user: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 30px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"], input[type="email"], input[type="password"], select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, select:focus {
            outline-color: #4CAF50;
        }

        button.submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        button.submit-btn:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            font-size: 14px;
        }

        .welcome {
            text-align: center;
            margin-bottom: 20px;
            font-size: 18px;
            color: #333;
        }

        .back-btn {
            text-align: center;
            margin-top: 20px;
        }

        .back-btn a {
            font-size: 16px;
            text-decoration: none;
            color: #4CAF50;
        }

        .back-btn a:hover {
            text-decoration: underline;
        }

        /* For the Logout button */
        .logout-btn {
            display: inline-block;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #FF5733;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #C70039;
        }

    </style>
</head>
<body>
<div class="container">
    <h2>Add New User</h2>

    <?php if (isset($message)): ?>
        <p style="text-align: center; color: <?php echo strpos($message, 'success') !== false ? 'green' : 'red'; ?>;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="add_user.php">
        <input type="text" name="user_name" placeholder="Name" value="<?php echo htmlspecialchars($user_name); ?>">
        <span class="error"><?php echo $nameErr; ?></span>
        
        <input type="email" name="user_email" placeholder="Email" value="<?php echo htmlspecialchars($user_email); ?>">
        <span class="error"><?php echo $emailErr; ?></span>

        <input type="text" name="user_phone" placeholder="Phone" value="<?php echo htmlspecialchars($user_phone); ?>">
        <span class="error"><?php echo $mobilenoErr; ?></span>

        <select name="user_gender">
            <option value="0" <?php if ($user_gender == 0) echo "selected"; ?>>Male</option>
            <option value="1" <?php if ($user_gender == 1) echo "selected"; ?>>Female</option>
        </select>
        <span class="error"><?php echo $genderErr; ?></span>

        <select name="user_type">
            <option value="1" <?php if ($user_type == 1) echo "selected"; ?>>User</option>
            <option value="2" <?php if ($user_type == 2) echo "selected"; ?>>Seller</option>
        </select>
        <span class="error"><?php echo $typeErr; ?></span>

        <input type="password" name="user_password" placeholder="Password" value="<?php echo htmlspecialchars($user_password); ?>">
        <span class="error"><?php echo $passwordErr; ?></span>

        <button class="submit-btn" type="submit">Add User</button>
    </form>

    <div class="back-btn">
        <a href="index.php">Back to Dashboard</a>
    </div>
</div>
<script>
    document.querySelector("form").addEventListener("submit", function(event) {
    let valid = true;

    // Reset error messages
    document.querySelectorAll(".error").forEach(function(span) {
        span.textContent = '';
    });

    // Name Validation
    if (document.querySelector("input[name='user_name']").value === "") {
        document.querySelector("#nameErr").textContent = "Name is required";
        valid = false;
    }

    // Email Validation
    if (document.querySelector("input[name='user_email']").value === "") {
        document.querySelector("#emailErr").textContent = "Email is required";
        valid = false;
    }

    // Phone Number Validation
    if (document.querySelector("input[name='user_phone']").value === "") {
        document.querySelector("#mobilenoErr").textContent = "Phone number is required";
        valid = false;
    }

    // Gender Validation
    if (!document.querySelector("input[name='user_gender']:checked")) {
        document.querySelector("#genderErr").textContent = "Gender is required";
        valid = false;
    }

    // Password Validation
    if (document.querySelector("input[name='user_password']").value === "") {
        document.querySelector("#passwordErr").textContent = "Password is required";
        valid = false;
    }

    // User Type Validation
    if (!document.querySelector("input[name='user_type']:checked")) {
        document.querySelector("#typeErr").textContent = "User type is required";
        valid = false;
    }

    if (!valid) {
        event.preventDefault();
    }
});
</script>
</body>
</html>
