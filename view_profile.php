<?php
include 'db_connect.php';  
include 'user_layout.php';

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$stmt = $conn->prepare("SELECT user_id, user_name, user_email, user_phone, user_gender, user_password, user_image FROM tbl_user WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user exists
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("User not found.");
}
?>

<title>View Profile</title>
<style>
    .container {
        width: 80%;
        margin: auto;
        padding: 20px;
        background-color: white;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    .profile-card {
        width: 300px;
        margin: 0 auto;
        padding: 20px;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .profile-card img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        margin-bottom: 15px;
    }
    .profile-card h3 {
        margin-bottom: 10px;
    }
    .profile-card p {
        font-size: 16px;
        color: #333;
    }
    .back-btn, .edit-btn {
        display: block;
        width: 100%;
        padding: 10px;
        background-color: gray;
        color: white;
        text-align: center;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 20px;
    }
    .edit-btn {
        background-color: green;
    }
    /* Modal styles */
    .modal {display: none;position: fixed;z-index: 1000;left: 0;top: 0;width: 100%;height: 100%;background-color: rgba(0, 0, 0, 0.5); padding: 20px; box-sizing: border-box; overflow: auto; }
    .modal-content {position: relative;background-color: #fff;width: 60%; max-width: 600px; padding: 30px;border-radius: 10px;box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);margin: auto; top: 50%; transform: translateY(-50%); box-sizing: border-box;}
    .close {position: absolute;top: 10px;right: 20px;font-size: 28px;font-weight: bold;color: #aaa;cursor: pointer;}
    .close:hover,
    .close:focus {color: black;text-decoration: none;}
    .modal input[type="text"],
    .modal input[type="email"],
    .modal input[type="file"],
    .modal select {width: 100%;padding: 12px;margin: 10px 0;border: 1px solid #ccc;border-radius: 5px;box-sizing: border-box;}
    .modal button {padding: 12px 20px;background-color: #4CAF50;color: white;border: none;border-radius: 5px;cursor: pointer;margin-top: 10px;width: 100%; }
    .modal button:hover {background-color: #45a049;}
</style>

</head>
<body>

<div class="container">
    <h2>User Profile</h2>

    <div class="profile-card">
        <!-- Display user details -->
        <img src="uploads/<?php echo $user['user_image']; ?>" alt="Profile Picture">
        <h3><?php echo $user['user_name']; ?></h3>
        <p><strong>Email:</strong> <?php echo $user['user_email']; ?></p>
        <p><strong>Phone:</strong> <?php echo $user['user_phone']; ?></p>
        <p><strong>Gender:</strong> <?php echo $user['user_gender'] == 0 ? 'Male' : 'Female'; ?></p>
        <p><strong>Password:</strong> <?php echo $user['user_password']; ?></p>
    </div>

    <center>
        <button onclick="openEditModal()" class="edit-btn" style="width:300px">Edit Profile</button>
        <button onclick="window.location.href='User_welcome.php'" class="back-btn" style="width:300px">Back to Shop</button>
    </center>
</div>

<!-- Modal for editing user -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Profile</h2>
        <form id="editUserForm" method="POST" action="update_profile.php" enctype="multipart/form-data">
            <input type="hidden" name="user_id" id="edit_user_id" value="<?php echo $user['user_id']; ?>">
            <label for="edit_user_name">Name:</label>
            <input type="text" name="user_name" id="edit_user_name" value="<?php echo $user['user_name']; ?>" required><br>

            <label for="edit_user_email">Email:</label>
            <input type="email" name="user_email" id="edit_user_email" value="<?php echo $user['user_email']; ?>" required><br>

            <label for="edit_user_phone">Phone:</label>
            <input type="text" name="user_phone" id="edit_user_phone" value="<?php echo $user['user_phone']; ?>" required><br>

            <label for="edit_user_gender">Gender:</label>
            <select name="user_gender" id="edit_user_gender" required>
                <option value="0" <?php echo ($user['user_gender'] == 0) ? 'selected' : ''; ?>>Male</option>
                <option value="1" <?php echo ($user['user_gender'] == 1) ? 'selected' : ''; ?>>Female</option>
            </select><br>

            <label for="edit_user_password">Password:</label>
            <input type="text" name="user_password" id="edit_user_password" value="<?php echo $user['user_password']; ?>" required><br>

            <label for="edit_user_image">Profile Image:</label>
            <input type="file" name="user_image" id="edit_user_image"><br>

            <button type="submit">Update Profile</button>
        </form>
    </div>
</div>

<script>
    function openEditModal() {
        document.getElementById('editModal').style.display = "block";
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = "none";
    }
</script>

<?php
$stmt->close();
$conn->close();
?>

</body>
</html>
