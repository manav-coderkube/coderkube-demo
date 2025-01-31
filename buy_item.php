<?php
session_start();
include 'db_connect.php';  // Include database connection

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

if (!isset($_GET['item_id'])) {
    die("Invalid request.");
}

$item_id = intval($_GET['item_id']);

// Fetch item details with category and subcategory names
$stmt = $conn->prepare("
    SELECT 
        i.*, 
        c.category_name, 
        s.subcategory_name 
    FROM tbl_items i
    JOIN tbl_category c ON i.category_id = c.category_id
    JOIN tbl_subcategories s ON i.subcategory_id = s.subcategory_id
    WHERE i.item_id = ?
");

$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    die("Item not found.");
}

// Price calculations
$subtotal = $item['item_price'];
$sgst = ($subtotal * 9) / 100; // 9% SGST
$cgst = ($subtotal * 9) / 100; // 9% CGST
$discount = ($subtotal * 10) / 100; // 10% Discount
$delivery_charge = ($subtotal < 500) ? 100 : 50;
$total = ($subtotal + $sgst + $cgst - $discount + $delivery_charge);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Buy Item</title>
    <style>
        .container {
            width: 60%;
            margin: auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .item-box {
            display: flex;
            justify-content: space-between;
            padding-bottom: 20px;
        }
        .item-box img {
            width: 250px;
            height: 250px;
            object-fit: contain;
        }
        .item-details {
            flex: 1;
            padding-left: 20px;
        }
        .price-details {
            border-top: 1px solid #ddd;
            margin-top: 20px;
            padding-top: 10px;
        }
        .price-details p {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .total {
            border-top: 2px solid black;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 10px;
        }
        .process-btn {
            width: 100%;
            padding: 10px;
            background-color: green;
            color: white;
            text-align: center;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Confirm Purchase</h2>

    <!-- Item Box -->
    <div class="item-box">
        <img src="uploads/<?php echo $item['item_image']; ?>" alt="Item Image">
        <div class="item-details">
            <h3><?php echo $item['item_name']; ?></h3>
            <p>Category: <?php echo $item['category_name']; ?></p>
            <p>Subcategory: <?php echo $item['subcategory_name']; ?></p>
            <p><strong>Price: ₹<?php echo number_format($subtotal, 2); ?></strong></p>
        </div>
    </div>

    <hr>

    <!-- Price Calculation -->
    <div class="price-details">
        <p><span>Subtotal:</span> <span>₹<?php echo number_format($subtotal, 2); ?></span></p>
        <p><span>SGST (9%):</span> <span>₹<?php echo number_format($sgst, 2); ?></span></p>
        <p><span>CGST (9%):</span> <span>₹<?php echo number_format($cgst, 2); ?></span></p>
        <p><span>Discount (10%):</span> <span>-₹<?php echo number_format($discount, 2); ?></span></p>
        <p><span>Delivery Charge:</span> <span>₹<?php echo number_format($delivery_charge, 2); ?></span></p>

        <div class="total">
            <p><span>Total Amount:</span> <span>₹<?php echo number_format($total, 2); ?></span></p>
        </div>
    </div>

    <hr>

    <!-- Process Order Button -->
    <form action="process_order.php" method="POST">
        <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
        <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
        <button type="submit" class="process-btn">Confirm</button>
    </form>
    <button onclick="history.back()" class="process-btn" style="background-color: gray; margin-top: 10px;">Back</button>
</div>

</body>
</html>
