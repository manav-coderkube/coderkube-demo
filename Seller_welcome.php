<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
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
    <title>Seller Dashboard</title>
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
    </style>
</head>
<body>
<div class="container">
    <h2>Seller Dashboard</h2>
    <p class="welcome">Welcome, <?php echo htmlspecialchars($admin_name); ?>!</p>

    <table id="userTable" class="display">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>Type</th>
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

<!-- Include jQuery and DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>

<script>
$(document).ready(function () {
    $('#userTable').DataTable({
        processing: true, 
        serverSide: true, 
        ajax: {
            url: 'fetch_user.php', 
            type: 'POST'
        },
        columns: [
            {
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1; 
                },
                orderable: false 
            },
            { data: 'user_name' }, 
            { data: 'user_email' }, 
            { data: 'user_phone' }, 
            { data: 'user_gender' }, 
            { data: 'user_type' },
            {
                data: null, 
                orderable: false, 
                render: function (data, type, row) {
                    
                    return `
                        <button class="edit-btn" data-id="${row.user_id}" style="background-color: #4CAF50; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer;">Edit</button>
                        <button class="delete-btn" data-id="${row.user_id}" style="background-color: #FF5733; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer;">Delete</button>
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

    
    $('#userTable').on('click', '.edit-btn', function () {
        const userId = $(this).data('id'); 
        window.location.href = `edit_user.php?user_id=${userId}`; 
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
