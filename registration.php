<?php
include 'db_connect.php';

session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect if user is already logged in
if (isset($_SESSION['user_id']) || isset($_SESSION['user_type']) || isset($_SESSION['user_name'])) {
    if ($_SESSION['user_type'] === 0) {
        header("Location: Admin_welcome.php");
    } else if ($_SESSION['user_type'] === 1) {
        header("Location: User_welcome.php");
    } else {
        header("Location: Seller_welcome.php");
    }
    exit();
}

// Initialize error messages and variables
$nameErr = $emailErr = $mobilenoErr = $genderErr = $typeErr = $passwordErr = $imageErr= "";
$name = $email = $mobileno = $gender = $type = $image = $passw = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // PHP Validation
    if (empty($_POST["user_name"])) {  
        $nameErr = "Name is required";  
    } else {  
        $name = trim($_POST["user_name"]);  
        if (!preg_match("/^(?!\s)(?!.*\s{2,})[a-zA-Z ]+$/", $name)) {  
            $nameErr = "Name should not contain consecutive spaces";  
        } else {
            // Check if the name already exists in the database
            $name_check = "SELECT * FROM tbl_user WHERE user_name = ?";
            $stmt = $conn->prepare($name_check);
            $stmt->bind_param("s", $name);
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
        $email = trim($_POST["user_email"]);  
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {  
            $emailErr = "Invalid email format";  
        } else {
            // Check if the email already exists in the database
            $email_check = "SELECT * FROM tbl_user WHERE user_email = ?";
            $stmt = $conn->prepare($email_check);
            $stmt->bind_param("s", $email);
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
        $mobileno = trim($_POST["user_phone"]);  
        if (!preg_match("/^[0-9]*$/", $mobileno)) {  
            $mobilenoErr = "Only numeric value is allowed.";  
        }  
        if (strlen($mobileno) != 10) {  
            $mobilenoErr = "Mobile no must contain 10 digits.";  
        } else {
            // Check if the mobile number already exists in the database
            $mobileno_check = "SELECT * FROM tbl_user WHERE user_phone = ?";
            $stmt = $conn->prepare($mobileno_check);
            $stmt->bind_param("s", $mobileno);
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
        $gender = $_POST["user_gender"];  
    }

    // Validate Type
    if (!isset($_POST['user_type'])) {  
        $typeErr = "Select User type.";  
    } else {  
        $type = $_POST["user_type"];  
    }

    // Validate Password
    if (empty($_POST["user_password"])) {  
        $passwordErr = "Password is required";  
    } else {  
        $passw = $_POST["user_password"];
        if (strlen($passw) < 6) {  
            $passwordErr = "Password must be at least 6 characters.";  
        }  
    }

    $image = $_FILES['user_image']['name'];
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($image);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // If no errors, proceed with inserting into the database
    if (empty($imageErr) && empty($nameErr) && empty($emailErr) && empty($mobilenoErr) && empty($genderErr) && empty($typeErr) && empty($passwordErr)) {
        // Hash the password
        // $passw = password_hash($passw, PASSWORD_DEFAULT);
        
        // Insert into the database
        if (!move_uploaded_file($_FILES['user_image']['tmp_name'], $targetFile)) {
            $itemsErr = "<p>Failed to upload the image. Please try again.</p>";
        } else {
            $stmt = $conn->prepare("INSERT INTO tbl_user (user_name, user_image, user_email, user_phone, user_gender, user_type, user_password) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $name, $image, $email, $mobileno, $gender, $type, $passw);

            if ($stmt->execute()) {
                header("Location: registration.php?register=success");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>  
<html>  
<head>  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
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
        .form-group input[type="password"],
        .form-group input[type="radio"],
        .form-group input[type="checkbox"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-group input[type="radio"] {
            width: auto;
        }

        .form-group input[type="checkbox"] {
            width: auto;
        }

        .form-group span {
            color: #FF0001;
            font-size: 12px;
        }

        .form-actions {
            text-align: center;
        }

        .form-actions input[type="submit"],
        .form-actions input[type="reset"] {
            padding: 10px 20px;
            font-size: 16px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            transition: background-color 0.3s;
        }

        .form-actions input[type="reset"] {
            background-color: #ccc;
        }

        .form-actions input[type="submit"]:hover,
        .form-actions input[type="reset"]:hover {
            background-color: #45a049;
        }

        .form-actions input[type="reset"]:hover {
            background-color: #bbb;
        }

        .error {
            color: #FF0001;
        }
    </style>
</head>  
<body>  
<?php
if (isset($_GET['register']) && $_GET['register'] == 'success') {
    echo "<script>
        $(document).ready(function() {
            toastr.options.positionClass = 'toast-top-right'; // Change position
            toastr.options.timeOut = 5000; // Duration in milliseconds
            toastr.success('Registration Successful! Wait for 5 sec to redirect log in page.', 'Success');

            // Redirect to login.php after the toast timeout
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 5000); // Match this duration with the timeOut
        });
    </script>";
}
?>
<div class="container">
    <h2>Registration Form</h2>
    <form id="registrationForm" method="post" action="registration.php" enctype="multipart/form-data">    
        <div class="form-group">
            <label for="name">Name:<span class="error">*</span></label>
            <input type="text" name="user_name" id="user_name" value="<?php echo htmlspecialchars($name); ?>" placeholder="Enter Name (Only Alphabets)">
            <span class="error" id="nameErr"><?php echo $nameErr; ?></span>
        </div>

        <div class="form-group">
            <label for="email">E-mail:<span class="error">*</span></label>
            <input type="text" name="user_email" id="user_email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter Email">
            <span class="error" id="emailErr"><?php echo $emailErr; ?></span>
        </div>

        <div class="form-group">
            <label for="mobileno">Mobile No:<span class="error">*</span></label>
            <input type="text" name="user_phone" id="user_phone" value="<?php echo htmlspecialchars($mobileno); ?>" placeholder="Enter Mobile No">
            <span class="error" id="mobilenoErr"><?php echo $mobilenoErr; ?></span>
        </div>

        <div class="form-group">
            <label>Gender:<span class="error">*</span></label>
            <input type="radio" name="user_gender" value="0" id="gender_male" <?php if ($gender == 0) echo "checked"; ?>> Male  
            <input type="radio" name="user_gender" value="1" id="gender_female" <?php if ($gender == 1) echo "checked"; ?>> Female  
            <span class="error" id="genderErr"><?php echo $genderErr; ?></span>
        </div>

        <div class="form-group">
            <label for="password">Password:<span class="error">*</span></label>
            <input type="password" name="user_password" id="user_password" placeholder="Enter Password" value="<?php echo htmlspecialchars($passw); ?>">
            <span class="error" id="passwordErr"><?php echo $passwordErr; ?></span>
        </div>

        <div class="form-group">
            <label>Type:<span class="error">*</span></label>
            <input type="radio" name="user_type" value="0" <?php if ($type == 0) echo "checked"; ?>> Admin  
            <input type="radio" name="user_type" value="1" <?php if ($type == 1) echo "checked"; ?>> User  
            <input type="radio" name="user_type" value="2" <?php if ($type == 2) echo "checked"; ?>> Seller  
            <span class="error" id="typeErr"><?php echo $typeErr; ?></span>
        </div>

        <label for="user_image">Upload Image:</label>
        <input type="file" name="user_image">
        <span class="error" id="typeErr"><?php echo $imageErr; ?></span>

        <div class="form-actions">
            <input type="submit" name="submit" value="Submit">
            <input type="reset" value="Reset">
        </div>
    </form>
    <div class="form-actions">
        <p>Already have an account? <a href="login.php">Log-IN Now</a></p>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#registrationForm").submit(function(event) {
        let valid = true;

        // Clear previous error messages
        $(".error").text("");

        // Name Validation
        if ($("#user_name").val() == "") {
            $("#nameErr").text("Name------ is required.");
            valid = false;
        } else if (!/^[a-zA-Z ]+$/.test($("#user_name").val())) {
            $("#nameErr").text("Only alphabets and spaces are allowed.");
            valid = false;
        }

        // Email Validation
        if ($("#user_email").val() == "") {
            $("#emailErr").text("Email is required.");
            valid = false;
        } else if (!/\S+@\S+\.\S+/.test($("#user_email").val())) {
            $("#emailErr").text("Invalid email format.");
            valid = false;
        }

        // Mobile No Validation
        if ($("#user_phone").val() == "") {
            $("#mobilenoErr").text("Mobile number is required.");
            valid = false;
        } else if (!/^[0-9]{10}$/.test($("#user_phone").val())) {
            $("#mobilenoErr").text("Mobile number must be 10 digits.");
            valid = false;
        }

        // Gender Validation
        if (!$("input[name='user_gender']:checked").val()) {
            $("#genderErr").text("Gender is required.");
            valid = false;
        }

        // Password Validation
        if ($("#user_password").val() == "") {
            $("#passwordErr").text("Password is required.");
            valid = false;
        } else if ($("#user_password").val().length < 6) {
            $("#passwordErr").text("Password must be at least 6 characters.");
            valid = false;
        }

        // Type Validation
        if (!$("input[name='user_type']:checked").val()) {
            $("#typeErr").text("User  type is required.");
            valid = false;
        }

        // Prevent form submission if validation fails
        if (!valid) {
            event.preventDefault();
        }
    });
});
</script>

</body>  
</html>