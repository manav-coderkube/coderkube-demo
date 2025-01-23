<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 0) { // Assuming user_type 0 is admin
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['user_name'];
$type = $_SESSION['user_type'];

// Fetch all users from the database
$sql = "SELECT user_id, user_name, user_email, user_phone, user_gender, user_type 
        FROM tbl_user"; // Removed WHERE clause to fetch all users
$result = $conn->query($sql);

// Logout functionality
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        .logout-btn {
            display: inline-block;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #FF5733;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #C70039;
        }

        .welcome {
            text-align: center;
            margin-bottom: 20px;
            font-size: 18px;
            color: #333;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Admin Dashboard</h2>
    <p class="welcome">Welcome, <?php echo htmlspecialchars($admin_name); ?>!</p>

    <?php if ($result->num_rows > 0): ?>
        <table id="myTable" class="display">
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
                <?php 
                $counter = 1; // Initialize the counter
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $counter++; ?></td> <!-- Display counter and increment it -->
                        <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_phone']); ?></td>
                        <td><?php echo $row['user_gender'] == 0 ? "Male" : "Female"; ?></td>
                        <td>
                            <?php
                            switch ($row['user_type']) {
                                case 0:
                                    echo "Admin";
                                    break;
                                case 1:
                                    echo "User ";
                                    break;
                                case 2:
                                    echo "Seller";
                                    break;
                                default:
                                    echo "Unknown";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>

    <form method="post">
        <button class="logout-btn" type="submit" name="logout">Logout</button>
    </form>
</div>
<!-- DataTables CSS -->
<link rel="stylesheet" href="//cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css">
    
    <!-- jQuery -->
    <script src="jquery.js"></script>
    
    <!-- DataTables JS -->
    <script src="//cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>
    
<script>
    $(document).ready(function() {
        $('#myTable').DataTable(); // Correct way to initialize DataTable
    });
</script>
</body>
</html>