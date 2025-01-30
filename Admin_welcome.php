<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 0) {
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['user_name'];

$AdminCount = $conn->query("SELECT COUNT(*) AS count FROM tbl_user WHERE user_type = 0")->fetch_assoc()['count'];
$UserCount = $conn->query("SELECT COUNT(*) AS count FROM tbl_user WHERE user_type = 1")->fetch_assoc()['count'];
$SellerCount = $conn->query("SELECT COUNT(*) AS count FROM tbl_user WHERE user_type = 2")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="//cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            overflow-x: auto;
        }

        .container {
            margin-left: 260px;
            padding: 20px;
            width: calc(100% - 250px);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar {
            width: 250px;
            background-color: #333;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
            transition: all 0.3s ease;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            margin: 5px 0;
        }

        .sidebar a:hover {
            background-color: #575757;
        }

        .card-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-top: px;
            width: 100%;
            flex-wrap: nowrap;
            padding: 10px;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 250px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .card h3 {
            font-size: 36px;
            color: #333;
        }

        .card p {
            font-size: 16px;
            font-weight: bold;
            color: #555;
        }

        .card a {
            text-decoration: none;
            color: #FF5733;
            font-weight: bold;
        }

        .card a:hover {
            text-decoration: underline;
        }

        .chart-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            width: 100%;
            max-width: 800px;
            text-align: center;
        }

        .grid-container {
            display: flex;
            gap: 0px;
            margin-top: 0px;
            width: 100%;
            max-width: 800px;
        }

        .grid-item {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }

            .sidebar a {
                text-align: center;
                padding: 10px;
            }

            .container {
                margin-left: 70px;
                width: calc(100% - 70px);
            }

            .card-container, .grid-container {
                flex-direction: column;
                align-items: center;
            }

            .grid-item {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="#">Dashboard</a>
        <a href="add_user.php">Add User</a>
        <a href="Admin_manage.php">Manage Admin</a>
        <a href="User_manage.php">Manage Users</a>
        <a href="Seller_manage.php">Manage Seller</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h2>Admin Dashboard</h2>
        <p class="welcome">Welcome, <?php echo htmlspecialchars($admin_name); ?>!</p>

        <div class="card-container">
            <div class="card">
                <h3><?php echo $AdminCount; ?></h3>
                <p><a href="Admin_manage.php">Total Admin</a></p>
            </div>
            <div class="card">
                <h3><?php echo $UserCount; ?></h3>
                <p><a href="User_manage.php">Total User</a></p>
            </div>
            <div class="card">
                <h3><?php echo $SellerCount; ?></h3>
                <p><a href="Seller_manage.php">Total Seller</a></p>
            </div>
        </div>

        <!-- Two-Part Grid Below the Chart -->
        <div class="grid-container">
            <div class="grid-item">
            <h3>User Statistics</h3>
                <canvas id="userChart"></canvas>
            </div>
            <div class="grid-item">
                <h3>Section 2</h3>
                <p>Content for the second section.</p>
            </div>
        </div>
    </div>    

    <script>
        $(document).ready(function () {
            var ctx = document.getElementById('userChart').getContext('2d');
            var userChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Admins', 'Users', 'Sellers'],
                    datasets: [{
                        label: 'Total Count',
                        data: [<?php echo $AdminCount; ?>, <?php echo $UserCount; ?>, <?php echo $SellerCount; ?>],
                        backgroundColor: ['#FF5733', '#33B5E5', '#66BB6A']
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
</body>
</html>
