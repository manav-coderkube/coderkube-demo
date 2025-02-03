<?php
include 'db_connect.php';  
include 'user_layout.php';

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];

// Fetch orders with item details
$stmt = $conn->prepare("
    SELECT o.order_id, o.total_amount, o.order_date, o.order_status, 
           i.item_name, i.item_image 
    FROM tbl_orders o
    JOIN tbl_items i ON o.item_id = i.item_id
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<title>My Orders</title>
<style>
    .container {
        width: 80%;
        margin: auto;
        padding: 20px;
        background-color: white;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-wrap: wrap; /* Allows multiple items to wrap to the next line */
        gap: 20px; /* Space between cards */
        justify-content: space-between; /* Align the cards evenly */
    }
    .order-card {
        width: calc(100% / 2.2);
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .order-card img {
        width: 120px;
        height: 120px;
        object-fit: contain;
        border-radius: 5px;
    }
    .order-details {
        flex: 1;
        padding-left: 20px;
    }
    .order-details h3 {
        margin-top: 0;
    }
    .status {
        font-weight: bold;
        padding: 5px 10px;
        border-radius: 5px;
    }
    .pending {
        color: orange;
    }
    .success {
        color: green;
    }
    .failed {
        color: red;
    }
    .order-actions {
        margin-top: 10px;
    }
    .back-btn {
        display: block;
        width: 100%;
        padding: 10px;
        background-color: gray;
        color: white;
        text-align: center;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 20px;
    }
    .cancel-btn, .continue-btn, .Pdf-btn {
        padding: 8px 16px;
        font-size: 14px;
        background-color: red;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .continue-btn {
        background-color: green;
    }
    .Pdf-btn {
        background-color: green;
    }
</style>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
<center><h2>My Orders</h2></center>
<div class="container">
    
    <?php if ($result->num_rows > 0): ?>
        <?php while ($order = $result->fetch_assoc()): ?>
            <div class="order-card" id="order-<?php echo $order['order_id']; ?>">
                <img src="uploads/<?php echo $order['item_image']; ?>" alt="Item Image">
                <div class="order-details">
                    <h3><?php echo $order['item_name']; ?></h3>
                    <p>Order Date: <?php echo $order['order_date']; ?></p>
                    <p>Total Amount: ₹<?php echo number_format($order['total_amount'], 2); ?></p>
                    <p>Payment Mode: Cash on Delivery</p>
                    <p class="status <?php 
                        // Set the status class based on the order_status value
                        if ($order['order_status'] == 0) {
                            echo 'pending';
                        } elseif ($order['order_status'] == 1) {
                            echo 'success';
                        } elseif ($order['order_status'] == 2) {
                            echo 'failed';
                        }
                    ?>">
                        <?php 
                        // Set the status text based on the order_status value
                        if ($order['order_status'] == 0) {
                            echo 'Pending';
                        } elseif ($order['order_status'] == 1) {
                            echo 'Success';
                        } elseif ($order['order_status'] == 2) {
                            echo 'Failed';
                        }
                        ?>
                    </p>
                    <div class="order-actions">
                        <?php if ($order['order_status'] == 0): ?>
                            <button class="cancel-btn" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'cancel')">Cancel Order</button>
                        <?php elseif ($order['order_status'] == 1): ?>
                            <button class="Pdf-btn" onclick="downloadBill(<?php echo $order['order_id']; ?>)">Bill Download</button>
                        <?php elseif ($order['order_status'] == 2): ?>
                            <button class="continue-btn" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'continue')">Continue Order</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No orders found.</p>
    <?php endif; ?>

    <button onclick="window.location.href='User_welcome.php'" class="back-btn">Back to Shop</button>
</div>

<script>
    function downloadBill(orderId) {
        var form = document.createElement("form");
        form.method = "POST";
        form.action = "bill_download.php";
        form.target = "_blank";

        var input = document.createElement("input");
        input.type = "hidden";
        input.name = "order_id";
        input.value = orderId;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
    
    function updateOrderStatus(orderId, action) {
        $.ajax({
            url: 'process_order_status.php',
            type: 'POST',
            data: {
                order_id: orderId,
                action: action
            },
            success: function(response) {
                var result = JSON.parse(response);
                
                if (result.success) {
                    var orderElement = $('#order-' + orderId);
                    var statusElement = orderElement.find('.status');
                    var actionButtons = orderElement.find('.order-actions');

                    if (action == 'cancel') {
                        statusElement.text('Failed').removeClass('pending').addClass('failed');
                        actionButtons.html('<button class="continue-btn">Continue Order</button>');
                    } else if (action == 'continue') {
                        statusElement.text('Pending').removeClass('failed').addClass('pending');
                        actionButtons.html('<button class="cancel-btn">Cancel Order</button>');
                    }

                    actionButtons.find('button').on('click', function() {
                        var newAction = $(this).hasClass('cancel-btn') ? 'cancel' : 'continue';
                        updateOrderStatus(orderId, newAction);
                    });
                } else {
                    alert('Error: ' + result.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
        

    }
</script>

<?php
$stmt->close();
$conn->close();
?>
