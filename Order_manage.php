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
    <title>Orders Dashboard</title>
    <link rel="stylesheet" href="//cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #333;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            padding-top: 20px;
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

        .container {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        .welcome, h2 {
            text-align: center;
        }

        .logout-btn {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #FF5733;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #C70039;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
        }

        .modal-content {
            background-color: white;
            margin: auto;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
            position: relative;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 28px;
            cursor: pointer;
        }

        .modal label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }

        .modal select, .modal input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .modal button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
        }

        .modal button:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" id="toggleBtn">≡</button>
        <a href="Admin_welcome.php">Dashboard</a>
        <a href="add_user.php">Add User</a>
        <a href="Admin_manage.php">Manage Admin</a>
        <a href="User_manage.php">Manage Users</a>
        <a href="Seller_manage.php">Manage Seller</a>
        <a href="Items_manage.php">Manage Items</a>
        <a href="Order_manage.php">Manage Orders</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h2>Order Dashboard</h2>
        <p class="welcome">Welcome, <?php echo htmlspecialchars($admin_name); ?>!</p>

        <table id="orderTable" class="display">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User Name</th>
                    <th>Item Name</th>
                    <th>Order Date</th>
                    <th>Qty</th>
                    <th>Image</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Modal for editing order -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Update Order Status</h2>
            <form id="editOrderForm">
                <input type="hidden" id="edit_order_id" name="order_id">
                <p><strong>User:</strong> <span id="edit_user_name"></span></p>
                <p><strong>Item:</strong> <span id="edit_item_name"></span></p>
                <p><strong>Order Date:</strong> <span id="edit_order_date"></span></p>
                <p><strong>Quantity:</strong> <span id="edit_quantity"></span></p>
                <p><strong>Total Amount:</strong> $<span id="edit_total_amount"></span></p>

                <p><strong>Item Image:</strong></p>
                <img id="edit_item_image" src="" alt="Item Image" width="100" height="100">

                <label for="order_status">Order Status:</label>
                <select id="order_status" name="order_status" style="width: fit-content;">
                    <option value="0">Pending</option>
                    <option value="1">Success</option>
                    <option value="2">Failed</option>
                </select>

                <button type="submit">Update Status</button>
            </form>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
        const sidebar = $('#sidebar');
        const mainContainer = $('#mainContainer');
        const toggleBtn = $('#toggleBtn');

        toggleBtn.click(function () {
            sidebar.toggleClass('collapsed');
            mainContainer.toggleClass('collapsed');
        });
        var table = $('#orderTable').DataTable({
            ajax: 'fetch_order.php',
            columns: [
                { data: 'order_id' },
                { data: 'user_name' },
                { data: 'item_name' },
                { data: 'order_date' },
                { data: 'item_quantity' },
                { data: 'item_image', render: data => `<img src="uploads/${data}" width="50" height="50">` },
                { data: 'total_amount' },
                { 
                    data: 'order_status',
                    render: function (data) {
                        if (data == 0) return '<span style="color: orange;">Pending</span>';
                        if (data == 1) return '<span style="color: green;">Success</span>';
                        if (data == 2) return '<span style="color: red;">Failed</span>';
                        return 'Unknown';
                    }
                },
                { 
                    data: null, 
                    render: data => `
                        <button class="status-btn" data-id="${data.order_id}">Update Status</button>
                    ` 
                }
            ]
        });


        $('#orderTable').on('click', '.status-btn', function () {
            let orderId = $(this).data('id');
            $.post('get_order_details.php', { order_id: orderId }, function (response) {
                let data = JSON.parse(response);
                console.log(data);
                console.log('Order Status: ' + data.status);  // Ensure this logs the correct value
                $('#edit_order_id').val(data.order_id);
                $('#edit_user_name').text(data.user_name);
                $('#edit_item_name').text(data.item_name);
                $('#edit_order_date').text(data.order_date);
                $('#edit_quantity').text(data.item_quantity);
                $('#edit_total_amount').text(data.total_amount);
                $('#edit_item_image').attr('src', 'uploads/' + data.item_image);
                $('#order_status').val(data.order_status);
                $('#orderModal').fadeIn();
            });
        });

        // Close modal
        $('.close').click(() => $('#orderModal').fadeOut());

        // Submit form to update status
        $('#editOrderForm').submit(function (e) {
            e.preventDefault();
            $.post('update_order.php', $(this).serialize(), function () {
                alert('Order status updated successfully!');
                table.ajax.reload();
                $('#orderModal').fadeOut();
            });
        });
        });

    </script>
</body>
</html>
