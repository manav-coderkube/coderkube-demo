<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['user_name'];

$category_nameerr = $subcategory_nameerr = $category_err = "";
$category_name = $subcategory_name = $category_id = "";

$query = "SELECT category_id, category_name FROM tbl_category";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Styles */
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
            margin-left: 0px;
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

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        input, select, button {
            width: 90%;
            padding: 10px;
            margin: 10px 0 20px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: rgb(58, 60, 58);
            color: white;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color: rgb(59, 60, 59);
        }

        .error {
            color: red;
            font-size: 14px;
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
            box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="Seller_welcome.php">Dashboard</a>
        <a href="#">Add Category | Sub-Category</a>
        <a href="view_category_subcategory.php">View Category | Sub-Category</a>
        <a href="add_items.php">Add Items</a>
        <a href="view_items.php">View Items</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="container">
        <center>
            <div class="grid-container" style="margin-top: 20%; margin-left: 25%;">
                <div class="grid-item">
                    <h2>Add category</h2>
                    <form id="addcategoryForm">
                        <input type="text" id="category_name" name="category_name" placeholder="Category Name" value="<?php echo htmlspecialchars($category_name); ?>" style="width:84%">
                        <span class="error" id="category_nameerr"><?php echo $category_nameerr; ?></span>

                        <button type="submit">Add category</button>
                    </form>
                </div>

                <hr style="border:0;width:10px">

                <div class="grid-item">
                    <h2>Add sub-category</h2>
                    <form id="addsubcategoriesForm">
                        <input type="text" id="subcategory_name" name="subcategory_name" placeholder="Subcategory Name" value="<?php echo htmlspecialchars($subcategory_name); ?>" style="width:84%">
                        <span class="error" id="subcategory_nameerr"><?php echo $subcategory_nameerr; ?></span>

                        <select id="category_id" name="category_id">
                            <option value="">Select Category</option>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                                <option value="<?php echo $row['category_id']; ?>" 
                                    <?php if (isset($category_id) && $category_id == $row['category_id']) echo "selected"; ?>>
                                    <?php echo htmlspecialchars($row['category_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <span class="error" id="category_err"><?php echo $category_err; ?></span>

                        <button type="submit">Add subcategory</button>
                    </form>
                </div>
            </div>
        </center>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function () {
        $('#addcategoryForm').submit(function (e) {
            e.preventDefault(); // Prevent the form from submitting

            let isValid = true;

            $('.error').text(''); // Clear any previous errors

            let category_name = $('#category_name').val();
            if (category_name.length < 3) {
                isValid = false;
                $('#category_nameerr').text('Name should be at least 3 characters long.');
            }

            // If everything is valid, submit the form via AJAX
            if (isValid) {
                var formData = new FormData(this);

                $.ajax({
                    url: 'add_category.php', // PHP script to handle adding category
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert('Category added successfully!');
                            $('#addcategoryForm')[0].reset();
                            loadcategory();  // Reload category list
                            $('#category_nameerr').text('');
                        } else {
                            alert('Error adding category: ' + response.message);
                        }
                    },
                    error: function () {
                        alert('There was an error with the request.');
                    }
                });
            }
        });

        $('#addsubcategoriesForm').submit(function (e) {
            e.preventDefault(); // Prevent the form from submitting

            let isValid = true;

            $('.error').text(''); // Clear any previous errors

            let subcategory_name = $('#subcategory_name').val();
            if (subcategory_name.length < 3) {
                isValid = false;
                $('#subcategory_nameerr').text('Subcategory name should be at least 3 characters long.');
            }

            let category_id = $('#category_id').val();
            if (!category_id) {
                isValid = false;
                $('#category_err').text('Please select a category.');
            }

            if (isValid) {
                var formData = new FormData(this);

                $.ajax({
                    url: 'add_subcategory.php', // PHP script for adding subcategory
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert('Subcategory added successfully!');
                            $('#addsubcategoriesForm')[0].reset(); // Reset the form fields
                            $('#subcategory_nameerr').text('');
                            $('#category_err').text('');
                        } else {
                            alert('Error adding subcategory: ' + response.message);
                        }
                    },
                    error: function () {
                        alert('There was an error with the request.');
                    }
                });
            }
        });
    });

    function loadcategory() {
        $.ajax({
            url: 'fetch_categories.php', // PHP script to fetch updated category list
            type: 'GET',
            success: function (data) {
                $('#category_id').html(data); // Update the category dropdown
            }
        });
    }
    </script>
</body>
</html>
