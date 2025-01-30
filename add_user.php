<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 0) {
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['user_name'];

$nameErr = $emailErr = $mobilenoErr = $genderErr = $typeErr = $imageErr = $passwordErr = "";
$user_name = $user_email = $user_phone = $user_gender = $user_type = $image = $user_image = $user_password = "";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color: #45a049;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input, .form-group select {
            width: calc(100% - 22px);
        }

        .error {
            color: red;
            font-size: 14px;
            text-align: center;
        }

        .success {
            color: green;
            font-size: 14px;
            text-align: center;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #FF5733;
            color: white;
            border: none;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }

        .back-btn:hover {
            background-color: #C70039;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add User</h2>

    <form id="addUserForm" enctype="multipart/form-data">
        <input type="text" id="user_name" name="user_name" placeholder="Name" value="<?php echo htmlspecialchars($user_name); ?>">
        <span class="error" id="nameError"><?php echo $nameErr; ?></span>
        
        <input type="text" id="user_email" name="user_email" placeholder="Email" value="<?php echo htmlspecialchars($user_email); ?>">
        <span class="error" id="emailError"><?php echo $emailErr; ?></span>

        <input type="text" id="user_phone" name="user_phone" placeholder="Phone" value="<?php echo htmlspecialchars($user_phone); ?>">
        <span class="error" id="phoneError"><?php echo $mobilenoErr; ?></span>

        <select id="user_gender" name="user_gender">
            <option value="0" <?php if ($user_gender == 0) echo "selected"; ?>>Male</option>
            <option value="1" <?php if ($user_gender == 1) echo "selected"; ?>>Female</option>
        </select>
        <span class="error" id="genderError"><?php echo $genderErr; ?></span>

        <select id="user_type" name="user_type">
            <option value="1" <?php if ($user_type == 1) echo "selected"; ?>>User</option>
            <option value="2" <?php if ($user_type == 2) echo "selected"; ?>>Seller</option>
        </select>
        <span class="error" id="typeError"><?php echo $typeErr; ?></span>

        <input type="password" id="user_password" name="user_password" placeholder="Password" value="<?php echo htmlspecialchars($user_password); ?>">
        <span class="error" id="passwordError"><?php echo $passwordErr; ?></span>

        <label for="user_image">Upload Image:</label>
        <input type="file" id="user_image" name="user_image">
        <span class="error" id="imageError"><?php echo $imageErr; ?></span>

        <button type="submit">Add User</button>
    </form>

    <a href="Seller_welcome.php" class="back-btn">Back</a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $('#addUserForm').submit(function (e) {
        e.preventDefault(); // Prevent form submission

        let isValid = true; // Flag to track form validation status

        // Reset all error messages
        $('.error').text('');

        // Validate name
        let name = $('#user_name').val();
        if (name.length < 3) {
            isValid = false;
            $('#nameError').text('Name should be at least 3 characters long.');
        }

        // Validate email
        let email = $('#user_email').val();
        let emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!emailPattern.test(email)) {
            isValid = false;
            $('#emailError').text('Please enter a valid email address.');
        }

        // Validate phone number
        let phone = $('#user_phone').val();
        let phonePattern = /^[0-9]{10}$/;
        if (!phonePattern.test(phone)) {
            isValid = false;
            $('#phoneError').text('Please enter a valid phone number (10 digits).');
        }

        // Validate gender selection
        let gender = $('#user_gender').val();
        if (!gender) {
            isValid = false;
            $('#genderError').text('Please select a gender.');
        }

        // Validate user type selection
        let userType = $('#user_type').val();
        if (!userType) {
            isValid = false;
            $('#typeError').text('Please select a user type.');
        }

        // Validate password
        let password = $('#user_password').val();
        if (password.length < 6) {
            isValid = false;
            $('#passwordError').text('Password should be at least 6 characters long.');
        }

        // Validate profile image
        let image = $('#user_image').val();
        if (!image) {
            isValid = false;
            $('#imageError').text('Please upload a profile image.');
        }

        // If everything is valid, submit the form via AJAX
        if (isValid) {
            var formData = new FormData(this);

            $.ajax({
                url: 'add_user_ajax.php', 
                type: 'POST',
                data: formData, 
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('User added successfully!');
                        
                        // window.location.href = 'Seller_welcome.php'; 
                    } else {
                        alert('Error in adding user: ' + response.message);
                    }
                },
                error: function () {
                    alert('There was an error with the request.');
                }
            });
        }
    });
});
</script>

</body>
</html>
