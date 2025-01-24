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
$(document).ready(function() {
    // Initialize DataTable
    let table = $('#userTable').DataTable();

    // Fetch data using AJAX
    function fetchUserData() {
        $.ajax({
            url: 'fetch_user.php',
            method: 'POST',
            dataType: 'json',
            success: function(data) {
                table.clear();
                let counter = 1;
                data.forEach(user => {
                    table.row.add([
                        counter++,
                        user.user_name,
                        user.user_email,
                        user.user_phone,
                        user.user_gender,
                        user.user_type
                    ]).draw();
                });
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
                alert("Failed to load user data.");
            }
        });
    }

    fetchUserData();
});
</script>
</body>
</html>
