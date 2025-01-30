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
    <title>Admin Dashboard</title>
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
            transition: all 0.3s;
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
            transition: all 0.3s;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar.collapsed a {
            text-align: center;
            font-size: 14px;
            padding: 10px;
        }

        .container.collapsed {
            margin-left: 70px;
            width: calc(100% - 70px);
        }

        .welcome, h2 {
            margin-left: 30px;
        }

        .logout-btn {
            display: inline-block;
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
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 5px;
            width: 60%;
        }

        .modal-header, .modal-body, .modal-footer {
            margin: 10px 0;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }

            .sidebar a {
                text-align: center;
                font-size: 14px;
                padding: 10px;
            }

            .container {
                margin-left: 70px;
                width: calc(100% - 70px);
            }

            .sidebar .toggle-btn {
                display: block;
            }
        }
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
    <div class="sidebar">
        <a href="Seller_welcome.php">Dashboard</a>
        <a href="add_category_subcategory.php">Add Category | Sub-Category</a>
        <a href="view_category_subcategory.php">View Category | Sub-Category</a>
        <a href="add_items.php">Add Items</a>
        <a href="view_items.php">View Items</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container" id="mainContainer">
        <table id="itemTable" class="display">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Sub-Category</th>
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

    <!-- Modal for editing user -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Item</h2>
            <form id="editUserForm">
                <input type="hidden" name="item_id" id="edit_item_id">
                <label for="edit_item_name">Item Name:</label>
                <input type="text" name="item_name" id="edit_item_name"><br>

                <label for="edit_item_price">Price:</label>
                <input type="text" name="item_price" id="edit_item_price"><br>

                <label for="edit_item_stock">Stock:</label>
                <input type="text" name="item_stock" id="edit_item_stock"><br>

                <label for="edit_item_image">Item Image:</label>
                <input type="file" name="item_image" id="edit_item_image"><br>

                <button type="submit">Update Item</button>
            </form>
        </div>
    </div>

    <!-- Include jQuery and DataTables -->
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

            var table = $('#itemTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: 'fetch_items.php',
                    type: 'POST',
                    data: 'json'
                },
                columns: [
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        },
                        orderable: false
                    },
                    { data: 'item_name' },
                    { data: 'item_price' },
                    { data: 'item_stock' },
                    { data: 'subcategory_name' },
                    {
                        data: 'item_image',
                        render: function (data) {
                            return `<img src="uploads/${data}" alt="Item Image" style="width: 50px; height: 50px; border-radius: 50%;">`;
                        },
                        orderable: false
                    },
                    {
                        data: null,
                        render: function (data) {
                            return `
                                <button class="edit-btn" data-id="${data.item_id}">Edit</button>
                                <button class="delete-btn" data-id="${data.item_id}">Delete</button>
                            `;
                        },
                        orderable: false
                    }
                ],
                paging: true,
                info: true,
                language: {
                    emptyTable: "No data available"
                }
            });

            // Show Edit Modal and load user data
            $('#itemTable').on('click', '.edit-btn', function () {
                const itemID = $(this).data('id');
                $.ajax({
                    url: 'get_items_data.php',
                    type: 'POST',
                    data: { items_id: itemID },
                    success: function (response) {
                        const itemData = JSON.parse(response);
                        if (itemData.success) {
                            // Populate the form fields
                            $('#edit_item_id').val(itemData.data.item_id);
                            $('#edit_item_name').val(itemData.data.item_name);
                            $('#edit_item_price').val(itemData.data.item_price);
                            $('#edit_item_stock').val(itemData.data.item_stock);

                            // Set the profile image in the modal
                            const imagePath = itemData.data.item_image ? 'uploads/' + itemData.data.item_image : 'uploads/placeholder.jpg';
                            $('#edit_item_image').attr('src', imagePath);

                            // Show the modal
                            $('#editModal').fadeIn();
                        } else {
                            alert('Error loading Item data');
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

                // Clear previous error messages
                $('.error-message').remove();

                // Validation flags
                let isValid = true;

                // Get form values
                const itemName = $('#edit_item_name').val().trim();
                const itemPrice = $('#edit_item_price').val().trim();
                const itemstock = $('#edit_item_stock').val().trim();

                // Validate Name (not empty)
                if (itemName === "") {
                    $('#edit_item_name').after('<span class="error-message" style="color: red;">Name is required.</span>');
                    isValid = false;
                }

                // Validate Phone (not empty and valid format)
                let pricePattern = /^\d+(\.\d{1,2})?$/;
                if (!pricePattern.test(itemPrice)) {
                    isValid = false;
                    $('#edit_item_price').after('<span class="error-message" style="color: red;">Price is required.</span>');
                }

                // Validate Gender (selected)
                if (itemstock === "") {
                    $('#edit_item_stock').after('<span class="error-message" style="color: red;">Stock is required.</span>');
                    isValid = false;
                }

                // If all fields are valid, submit the form
                if (isValid) {
                    $.ajax({
                        url: 'update_items.php',
                        type: 'POST',
                        data: new FormData(this),
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            const data = JSON.parse(response);
                            if (data.success) {
                                alert('Item updated successfully');
                                $('#itemTable').DataTable().ajax.reload();
                                $('#editModal').fadeOut();
                            } else {
                                alert('Failed to update user');
                            }
                        },
                        error: function (xhr, status, error) {
                            alert("An error occurred: " + error);
                    }
                });
            }});
    
        $('#itemTable').on('click', '.delete-btn', function () {
        const itemID = $(this).data('id'); 
        if (confirm("Are you sure you want to delete this user?")) {
            $.ajax({
                url: 'delete_items.php', 
                type: 'POST',
                data: { item_id: itemID },
                success: function (response) {
                    try {
                        const data = JSON.parse(response); 
                        if (data.message) {
                            alert(data.message); 
                        } else {
                            alert("Unexpected response: " + response); 
                        }
                        $('#itemTable').DataTable().ajax.reload(); 
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
