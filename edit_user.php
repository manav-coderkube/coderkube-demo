<?php
include 'db_connect.php';

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']); 

    
    $stmt = $conn->prepare("SELECT user_name, user_email, user_phone, user_gender, user_type FROM tbl_user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_name = $user['user_name'];
        $user_email = $user['user_email'];
        $user_phone = $user['user_phone'];
        $user_gender = $user['user_gender'];
        $user_type = $user['user_type'];
    } else {
        echo "User not found!";
        exit();
    }

    $stmt->close();
} else {
    echo "User ID not provided!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
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

        .alert {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            text-align: center;
            font-size: 16px;
        }

        .alert.success {
            background-color: #4CAF50;
            color: white;
        }

        .alert.error {
            background-color: #f44336;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit User</h2>

    <form id="editUserForm">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

        <div class="form-group">
            <label for="user_name">Name:</label>
            <input type="text" id="user_name" name="user_name" value="<?php echo htmlspecialchars($user_name); ?>" required>
        </div>

        <div class="form-group">
            <label for="user_email">Email:</label>
            <input type="email" id="user_email" name="user_email" value="<?php echo htmlspecialchars($user_email); ?>" required>
        </div>

        <div class="form-group">
            <label for="user_phone">Phone:</label>
            <input type="text" id="user_phone" name="user_phone" value="<?php echo htmlspecialchars($user_phone); ?>">
        </div>

        <div class="form-group">
            <label for="user_gender">Gender:</label>
            <select id="user_gender" name="user_gender">
                <option value="0" <?php echo ($user_gender == 0) ? 'selected' : ''; ?>>Male</option>
                <option value="1" <?php echo ($user_gender == 1) ? 'selected' : ''; ?>>Female</option>
            </select>
        </div>

        <div class="form-group">
            <label for="user_type">Type:</label>
            <select id="user_type" name="user_type">
                <option value="0" <?php echo ($user_type == 0) ? 'selected' : ''; ?>>Admin</option>
                <option value="1" <?php echo ($user_type == 1) ? 'selected' : ''; ?>>User</option>
                <option value="2" <?php echo ($user_type == 2) ? 'selected' : ''; ?>>Seller</option>
            </select>
        </div>

        <button type="submit">Update User</button>
    </form>

    <a href="Seller_welcome.php" class="back-btn">Back</a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $('#editUserForm').submit(function (e) {
        e.preventDefault(); 

        var formData = $(this).serialize(); 

        $.ajax({
            url: 'update_user.php', 
            type: 'POST',
            data: formData, 
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert('Updated successfully!');
                    window.location.href = 'Seller_welcome.php'; 
                } else {
                    alert('Error in updating: ' + response.message);
                }
            },
            error: function () {
                alert('There was an error with the request.');
            }
        });
    });
});
</script>

</body>
</html>
