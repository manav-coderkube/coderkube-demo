<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 0) {
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="//cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { width: 90%; margin: 30px auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        table th { background-color: #4CAF50; color: white; }
        .logout-btn { display: inline-block; margin: 20px auto; padding: 10px 20px; font-size: 16px; background-color: #FF5733; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .logout-btn:hover { background-color: #C70039; }
        .welcome { text-align: center; margin-bottom: 20px; font-size: 18px; color: #333; }

        /* Modal styles */
        .modal {display: none;position: fixed;z-index: 1000;  left: 0;top: 0;width: 100%;height: 100%;background-color: rgba(0, 0, 0, 0.5); padding: 20px; box-sizing: border-box; overflow: auto; }
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
    <h2>Admin Dashboard</h2>
    <p class="welcome">Welcome, <?php echo htmlspecialchars($admin_name); ?>!</p>
    <div style="text-align: center; margin-bottom: 20px;">
        <a href="add_user.php" class="add-user-btn" style="font-size: 16px; background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            Add User
        </a>
    </div>
    <table id="userTable" class="display">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>Type</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be dynamically populated using AJAX -->
        </tbody>
    </table>

    <form method="post" action="logout.php">
        <button class="logout-btn" type="submit" name="logout">Logout</button>
    </form>
</div>

<!-- Modal for Editing User -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Edit User</h3>
        <form id="editUserForm">
            <div class="form-group">
                <label>Current Profile Image:</label>
                <!-- Display Image -->
                <img src="" id="edit_user_image" alt="Profile Image" class="profile-image" style="width: 100px; height: 100px; border-radius: 50%;">
            </div>
            <input type="hidden" id="edit_user_id" name="user_id">
            <label for="edit_user_name">Name:</label>
            <input type="text" id="edit_user_name" name="user_name">
            
            <label for="edit_user_email">Email:</label>
            <input type="email" id="edit_user_email" name="user_email">
            
            <label for="edit_user_phone">Phone:</label>
            <input type="text" id="edit_user_phone" name="user_phone">
            
            <label for="edit_user_gender">Gender:</label>
            <select id="edit_user_gender" name="user_gender">
                <option value="0">Male</option>
                <option value="1">Female</option>
            </select>
            
            <label for="edit_user_type">User Type:</label>
            <select id="edit_user_type" name="user_type">
                <option value="0">Admin</option>
                <option value="1">User</option>
                <option value="2">Seller</option>
            </select>

            <div class="form-group">
                <label for="user_image">Update Profile Image:</label>
                <input type="file" id="user_image" name="user_image">
            </div>
            
            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>

<!-- Include jQuery and DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>

<script>
$(document).ready(function () {
    var table = $('#userTable').DataTable({
    processing: true, 
    serverSide: true, 
    ajax: {
        url: 'fetch_admin.php', 
        type: 'POST'
    },
    columns: [
        {
            data: null, 
            render: function (data, type, row, meta) {
                return meta.row + 1; // meta.row gives the index of the row, and adding 1 to make it 1-based index
            }
        },
        { data: 'user_name' }, 
        { data: 'user_email' }, 
        { data: 'user_phone' }, 
        { data: 'user_gender' }, 
        { data: 'user_type' },
        {
            data: 'user_image',
            render: function (data) {
                return `<img src="uploads/${data}" alt="User Image" style="width: 50px; height: 50px; border-radius: 50%;">`;
            }
        },
        {
            data: null, 
            render: function (data) {
                return `
                    <button class="edit-btn" data-id="${data.user_id}">Edit</button>
                    <button class="delete-btn" data-id="${data.user_id}">Delete</button>
                `;
            }
        }
    ],
        paging: true,
        info: true,
        language: {
            emptyTable: "No data available"
        }
    });


    // Show Edit Modal and load user data
    $('#userTable').on('click', '.edit-btn', function () {
        const userId = $(this).data('id'); 
        $.ajax({
            url: 'get_user_data.php',
            type: 'POST',
            data: { user_id: userId },
            success: function (response) {
                const userData = JSON.parse(response);
                if (userData.success) {
                    // Populate the form fields
                    $('#edit_user_id').val(userData.data.user_id);
                    $('#edit_user_name').val(userData.data.user_name);
                    $('#edit_user_email').val(userData.data.user_email);
                    $('#edit_user_phone').val(userData.data.user_phone);
                    $('#edit_user_gender').val(userData.data.user_gender);
                    $('#edit_user_type').val(userData.data.user_type);

                    // Set the profile image in the modal
                    const imagePath = userData.data.user_image ? 'uploads/' + userData.data.user_image : 'uploads/placeholder.jpg';
                    $('#edit_user_image').attr('src', imagePath); // Set image source

                    // Show the modal
                    $('#editModal').fadeIn();
                } else {
                    alert('Error loading user data');
                }
            },
            error: function (xhr, status, error) {
                alert("An error occurred: " + error);
            }
        });
    });

    // Close Modal
    $('.close').click(function () {
        $('#editModal').fadeOut();
    });

    // Handle form submission
    $('#editUserForm').submit(function (e) {
        e.preventDefault();
        $.ajax({
            url: 'update_user.php', 
            type: 'POST',
            data: new FormData(this), // FormData to handle file uploads
            contentType: false,
            processData: false,
            success: function (response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('User updated successfully');
                    $('#userTable').DataTable().ajax.reload(); // Refresh DataTable
                    $('#editModal').fadeOut(); // Close the modal
                } else {
                    alert('Failed to update user');
                }
            },
            error: function (xhr, status, error) {
                alert("An error occurred: " + error);
            }
        });
    });

    $('#userTable').on('click', '.delete-btn', function () {
    const userId = $(this).data('id'); 
    if (confirm("Are you sure you want to delete this user?")) {
        $.ajax({
            url: 'delete_user.php', 
            type: 'POST',
            data: { user_id: userId },
            success: function (response) {
                try {
                    const data = JSON.parse(response); 
                    if (data.message) {
                        alert(data.message); 
                    } else {
                        alert("Unexpected response: " + response); 
                    }
                    $('#userTable').DataTable().ajax.reload(); 
                } catch (e) {
                    alert("Error parsing response: " + e.message);
                }
            },
            error: function (xhr, status, error) {
                alert("An error occurred: " + error);
            }
        });

    }else{
        alert('Confirmation is not received.');
    }
});
});

</script>

</body>
</html>
