<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['user_name'];
$user_id = $_SESSION['user_id']; // Get the user_id from the session

// Initialize error variables
$item_nameErr = $item_priceErr = $item_stockErr = $subcategoryErr = $item_imageErr = "";
$item_name = $item_price = $item_stock = $subcategory_id = $item_image = "";

// Fetch subcategories from the database for dropdown
include 'db_connect.php';
$query = "SELECT subcategory_id, subcategory_name FROM tbl_subcategories";
$result = $conn->query($query);
$subcategories = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $subcategories[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
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
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 310px);
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
            width: calc(100% - 25px);
            padding: 10px;
            margin: 10px 0 20px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color:rgb(63, 66, 63);
            color: white;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color:rgb(51, 54, 51);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input, .form-group select {
            width: calc(100% - 22px);
        }

        .error {
            color: red;
            font-size: 14px;
            text-align: center;
        }

        .success {
            color: green;
            font-size: 14px;
            text-align: center;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #FF5733;
            color: white;
            border: none;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }

        .back-btn:hover {
            background-color: #C70039;
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
    <h2>Add Item</h2>

    <form id="addItemForm" enctype="multipart/form-data">
        <!-- Subcategory Dropdown -->
        <select id="subcategory_id" name="subcategory_id">
            <option value="">Select Subcategory</option>
            <?php foreach ($subcategories as $subcategory): ?>
                <option value="<?php echo $subcategory['subcategory_id']; ?>"><?php echo $subcategory['subcategory_name']; ?></option>
            <?php endforeach; ?>
        </select>
        <span class="error" id="subcategoryError"><?php echo $subcategoryErr; ?></span>

        <!-- Item Name -->
        <input type="text" id="item_name" name="item_name" placeholder="Item Name" value="<?php echo htmlspecialchars($item_name); ?>">
        <span class="error" id="itemNameError"><?php echo $item_nameErr; ?></span>

        <!-- Item Price -->
        <input type="text" id="item_price" name="item_price" placeholder="Item Price" value="<?php echo htmlspecialchars($item_price); ?>">
        <span class="error" id="itemPriceError"><?php echo $item_priceErr; ?></span>

        <!-- Item Stock -->
        <input type="text" id="item_stock" name="item_stock" placeholder="Item Stock" value="<?php echo htmlspecialchars($item_stock); ?>">
        <span class="error" id="itemStockError"><?php echo $item_stockErr; ?></span>

        <!-- Item Image -->
        <label for="item_image">Upload Image:</label>
        <input type="file" id="item_image" name="item_image">
        <span class="error" id="itemImageError"><?php echo $item_imageErr; ?></span>

        <button type="submit">Add Item</button>
    </form>

    <a href="Seller_welcome.php" class="back-btn">Back</a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $('#addItemForm').submit(function (e) {
        e.preventDefault(); // Prevent form submission

        let isValid = true; // Flag to track form validation status

        // Reset all error messages
        $('.error').text('');

        // Validate subcategory selection
        let subcategory = $('#subcategory_id').val();
        if (!subcategory) {
            isValid = false;
            $('#subcategoryError').text('Please select a subcategory.');
        }

        // Validate item name
        let itemName = $('#item_name').val();
        if (itemName.length < 3) {
            isValid = false;
            $('#itemNameError').text('Item name should be at least 3 characters long.');
        }

        // Validate item price
        let itemPrice = $('#item_price').val();
        let pricePattern = /^\d+(\.\d{1,2})?$/;
        if (!pricePattern.test(itemPrice)) {
            isValid = false;
            $('#itemPriceError').text('Please enter a valid price (e.g., 10.99).');
        }

        // Validate item stock
        let itemStock = $('#item_stock').val();
        let stockPattern = /^[0-9]+$/;
        if (!stockPattern.test(itemStock)) {
            isValid = false;
            $('#itemStockError').text('Please enter a valid stock quantity.');
        }

        // Validate item image
        let itemImage = $('#item_image').val();
        if (!itemImage) {
            isValid = false;
            $('#itemImageError').text('Please upload an item image.');
        }

        // If everything is valid, submit the form via AJAX
        if (isValid) {
            var formData = new FormData(this);
            formData.append('user_id', <?php echo $user_id; ?>); // Add user_id from session

            $.ajax({
                url: 'add_item_ajax.php', 
                type: 'POST',
                data: formData, 
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Item added successfully!');
                        window.location.href = 'add_items.php';
                    } else {
                        alert('Error in adding item: ' + response.message);
                    }
                },
                error: function () {
                    alert('There was an error with the request.');
                }
            });
        }
    });
});
</script>

</body>
</html>
