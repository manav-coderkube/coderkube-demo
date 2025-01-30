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
            margin-left: 130px;
            width: calc(100% - 140px);
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
        <a href="#">View Category | Sub-Category</a>
        <a href="add_items.php">Add Items</a>
        <a href="view_items.php">View Items</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="container">

    <div class="container" id="mainContainer">
        <center>
        <h2>Category | Sub-Category</h2>
        
        <h3 style="width:fit-content; font-family: 'Times New Roman', Times, serif; background-color:#575757; color:white">Category</h3>
        <table id="CategoryTable" class="display">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Created By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be dynamically populated using AJAX -->
            </tbody>
        </table>

        <br>
        
        <h3 style="width:fit-content; font-family: 'Times New Roman', Times, serif; background-color:#575757; color:white">Sub - Category</h3>
        <table id="subCategoryTable" class="display">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sub-Category</th>
                    <th>Category</th>
                    <th>Created By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be dynamically populated using AJAX -->
            </tbody>
        </table>
    </div>
</div>
    <!-- Modal for editing user -->
    <div id="editModal1" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Category</h2>
            <form id="editCategoryForm">

                <input type="hidden" name="category_id" id="edit_category_id">
                <label for="edit_category_name">Category Name:</label>
                <input type="text" name="category_name" id="edit_category_name"><br>
                <input type="hidden" name="user_id" id="edit_user_id">
                <button type="submit">Update User</button>

            </form>
        </div>
    </div>




    <div id="editModal2" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Sub-Category</h2>
            <form id="editSubcategoryForm">
                
                <input type="hidden" name="subcategory_id" id="edit_subcategory_id">
                <label for="edit_subcategory_name">subCategory Name:</label>
                <input type="text" name="subcategory_name" id="edit_subcategory_name"><br>
                <input type="hidden" name="category_id" id="edit_category_id">
                <input type="hidden" name="user_id" id="edit_user_id">
                <button type="submit">Update Sub Category</button>

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

            var table = $('#CategoryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: 'fetch_category.php',
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
                    { data: 'category_name' },
                    { 
                        data: 'user_name' ,
                        orderable: false
                    },
                    {
                        data: null,
                        render: function (data) {
                            return `
                                <button class="edit-btn" data-id="${data.category_id}">Edit</button>
                                <button class="delete-btn" data-id="${data.category_id}">Delete</button>
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



            var table = $('#subCategoryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: 'fetch_subcategory.php',
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
                    { data: 'subcategory_name' },
                    { data: 'category_name' },
                    { 
                        data: 'user_name',
                        orderable: false 
                    },
                    {
                        data: null,
                        render: function (data) {
                            return `
                                <button class="edit-btn" data-id="${data.subcategory_id}">Edit</button>
                                <button class="delete-btn" data-id="${data.subcategory_id}">Delete</button>
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

            // Show Edit Modal and load category data
            $('#CategoryTable').on('click', '.edit-btn', function () {
                const categoryId = $(this).data('id');
                console.log("Category ID: " + categoryId); 
                $.ajax({
                    url: 'get_category_data.php',
                    type: 'POST',
                    data: { category_id: categoryId },
                    success: function (response) {
                        const categoryData = JSON.parse(response);
                        if (categoryData.success) {
                            // Populate the form fields
                            $('#edit_category_id').val(categoryData.data.category_id);
                            $('#edit_category_name').val(categoryData.data.category_name);
                            $('#edit_user_id').val(categoryData.data.user_id);

                            // Show the modal
                            $('#editModal1').fadeIn();
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
                $('#editModal1').fadeOut();
            });

            // Handle form submission
            $('#editCategoryForm').submit(function (e) {
                e.preventDefault();

                // Clear previous error messages
                $('.error-message').remove();

                // Validation flags
                let isValid = true;

                // Get form values
                const categoryId = $('#edit_category_id').val().trim();
                const categoryName = $('#edit_category_name').val().trim();
                const userId = $('#edit_user_id').val().trim();

                // Validate Name (not empty)
                if (categoryName === "") {
                    $('#edit_category_name').after('<span class="error-message" style="color: red;">Category name is required.</span>');
                    isValid = false;
                }

                // If all fields are valid, submit the form
                if (isValid) {
                    $.ajax({
                        url: 'update_category.php',
                        type: 'POST',
                        data: new FormData(this),
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            const data = JSON.parse(response);
                            if (data.success) {
                                alert('Category updated successfully');
                                $('#editModal1').fadeOut();
                                $('#CategoryTable').DataTable().ajax.reload();
                                $('#subCategoryTable').DataTable().ajax.reload();
                            } else {
                                alert('Failed to update Category');
                            }
                        },
                        error: function (xhr, status, error) {
                            alert("An error occurred: " + error);
                    }
                });
            }});
    
        $('#CategoryTable').on('click', '.delete-btn', function () {
        const categoryId = $(this).data('id'); 
        if (confirm("Are you sure you want to delete this Category?")) {
            $.ajax({
                url: 'delete_category.php', 
                type: 'POST',
                data: { category_id: categoryId },
                success: function (response) {
                    try {
                        const data = JSON.parse(response); 
                        if (data.message) {
                            alert(data.message); 
                        } else {
                            alert("Unexpected response: " + response); 
                        }
                        $('#CategoryTable').DataTable().ajax.reload(); 
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

        // Show Edit Modal and load category data
        $('#subCategoryTable').on('click', '.edit-btn', function () {
                const subcategoryId = $(this).data('id');
                $.ajax({
                    url: 'get_subcategory_data.php',
                    type: 'POST',
                    data: { subcategory_id: subcategoryId },
                    success: function (response) {
                        const subcategoryData = JSON.parse(response);
                        if (subcategoryData.success) {
                            // Populate the form fields
                            $('#edit_subcategory_id').val(subcategoryData.data.subcategory_id);
                            $('#edit_subcategory_name').val(subcategoryData.data.subcategory_name);
                            $('#edit_category_id').val(subcategoryData.data.category_id);
                            $('#edit_user_id').val(subcategoryData.data.user_id);
                            
                            // Show the modal
                            $('#editModal2').fadeIn();
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
                $('#editModal2').fadeOut();
            });

            // Handle form submission
            $('#editSubcategoryForm').submit(function (e) {
                e.preventDefault();

                // Clear previous error messages
                $('.error-message').remove();

                // Validation flags
                let isValid = true;

                // Get form values
                const subcategoryId = $('#edit_subcategory_id').val().trim();
                const subcategoryName = $('#edit_subcategory_name').val().trim();
                const categoryId = $('#edit_category_id').val().trim();
                const userId = $('#edit_user_id').val().trim();

                // Validate Name (not empty)
                if (subcategoryName === "") {
                    $('#edit_subcategory_name').after('<span class="error-message" style="color: red;">SubCategory name is required.</span>');
                    isValid = false;
                }

                // If all fields are valid, submit the form
                if (isValid) {
                    $.ajax({
                        url: 'update_subcategory.php',
                        type: 'POST',
                        data: new FormData(this),
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            const data = JSON.parse(response);
                            if (data.success) {
                                alert('Sub Category updated successfully');
                                $('#subCategoryTable').DataTable().ajax.reload();
                                $('#editModal2').fadeOut();
                            } else {
                                alert('Failed to update Sub Category');
                            }
                        },
                        error: function (xhr, status, error) {
                            alert("An error occurred: " + error);
                    }
                });
            }});
    
        $('#subCategoryTable').on('click', '.delete-btn', function () {
        const subcategoryId = $(this).data('id'); 
        if (confirm("Are you sure you want to delete this Sub Category?")) {
            $.ajax({
                url: 'delete_subcategory.php', 
                type: 'POST',
                data: { subcategory_id: subcategoryId },
                success: function (response) {
                    try {
                        const data = JSON.parse(response); 
                        if (data.message) {
                            alert(data.message); 
                        } else {
                            alert("Unexpected response: " + response); 
                        }
                        $('#subCategoryTable').DataTable().ajax.reload(); 
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
