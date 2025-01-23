<?php 
    session_start();
    
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            margin-bottom: 30px;
        }

        .btn {
            padding: 10px 20px;
            font-size: 16px;
            margin: 10px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn-login {
            background-color: #4CAF50;
            color: white;
        }

        .btn-register {
            background-color: #008CBA;
            color: white;
        }

        .btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Welcome to Our Website</h2>
    <button class="btn btn-login" onclick="window.location.href='login.php'">Login</button>
    <button class="btn btn-register" onclick="window.location.href='registration.php'">Register</button>
</div>

</body>
</html>
