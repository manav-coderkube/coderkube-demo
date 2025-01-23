<?php
session_start();
include 'db_connect.php';

$usernameErr = $passwordErr = "";
$username = $password = $type ="";

if (isset($_SESSION['user_id']) || isset($_SESSION['user_type']) || isset($_SESSION['user_name'])) {
    if($_SESSION['user_type'] === 0){
        header("Location: Admin_welcome.php");
    }else if($_SESSION['user_type'] === 1){
        header("Location: User_welcome.php");
    }else {
        header("Location: Seller_welcome.php");
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Handle form validation
    if (empty($_POST["username"])) {  
        $usernameErr = "Username | Email is required";  
    } else {  
        $username = $_POST["username"];  
    }

    if (empty($_POST["passw"])) {  
        $passwordErr = "Password is required";  
    } else {  
        $password = $_POST["passw"];
    }

    if ($usernameErr == "" && $passwordErr == "") {
        
        // Fetch user from the database
        $sql = "SELECT * FROM tbl_user WHERE user_name = ? OR user_email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username,$username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Check if the password matches
            if ($password == $row['user_password']) {
                
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['user_name'] = $row['user_name'];
                $_SESSION['user_type'] = $row['user_type'];

                if($row['user_type'] === 0){
                    header("Location: Admin_welcome.php");
                }else if($row['user_type'] === 1){
                    header("Location: User_welcome.php");
                }else {
                    header("Location: Seller_welcome.php");
                }
                exit();
            } else {
                $passwordErr = "Invalid password.";
            }
        } else {
            $usernameErr = "No user found with this username.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 300px;
            margin: 100px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-group input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .form-group input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error {
            color: #FF0000;
            font-size: 12px;
        }

        .form-actions {
            text-align: center;
        }

        .form-actions a {
            color: #4CAF50;
            text-decoration: none;
            font-size: 14px;
        }

        .form-actions a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>

<div class="container">
    <h2>Login Form</h2>
    <form method="post" action="login.php">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Enter Username Or Email">
            <span class="error"><?php echo $usernameErr; ?></span>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="passw" value="<?php echo htmlspecialchars($password); ?>" placeholder="Enter Password">
            <span class="error"><?php echo $passwordErr; ?></span>
        </div>

        <div class="form-group form-actions">
            <input type="submit" value="Login">
        </div>
    </form>
    <div class="form-actions">
        <p>Don't have an account? <a href="registration.php">Register here</a></p>
    </div>
</div>

</body>
</html>
