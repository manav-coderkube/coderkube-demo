<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 1) { // Assuming user_type 0 is user
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Basic Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Navbar styles */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #333;
            padding: 10px 20px;
            color: white;
        }

        .navbar .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }

        .navbar .logo small {
            font-size: 12px;
            font-weight: normal;
        }

        .navbar input[type="text"] {
            padding: 5px 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            margin-left: 20px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
        }

        .navbar a:hover {
            background-color: #575757;
            border-radius: 5px;
        }
        /* Profile Dropdown Styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #333;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #575757;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">
            <a href="User_welcome.php" style="all: unset;cursor: pointer;"><div>OnStore</div></a>
            <small>all in one</small>
        </div>
        <!-- <input type="text" placeholder="Search..."> -->
        <div>
            <a href="User_welcome.php">Home</a>
            <a href="store.php">Store</a>
            <a href="wishlist.php">Wishlist</a>
            <a href="cart.php">Cart</a>
            <a href="orders.php">Order</a>
            <!-- Profile Dropdown -->
            <div class="dropdown">
                <a href="#" class="dropdown-toggle">Profile <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="view_profile.php">View Profile</a>
                    <a href="javascript:void(0);" onclick="confirmLogout()">Log Out</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to log out?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Log out',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, redirect to the logout page
                    window.location.href = 'logout.php';
                }
            });
        }
    </script>

</body>
</html>
