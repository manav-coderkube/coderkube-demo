<?php
include 'user_layout.php';

$user_id = $_SESSION['user_id'];
$query_user = "SELECT * FROM tbl_user WHERE user_id = '$user_id'";
$user_result = $conn->query($query_user);
$user = $user_result->fetch_assoc();
$username = $user['user_name'];

// Fetch random items for the card section
$query_cards = "SELECT i.*, c.category_name, s.subcategory_name
FROM tbl_items i
LEFT JOIN tbl_category c ON i.category_id = c.category_id
LEFT JOIN tbl_subcategories s ON i.subcategory_id = s.subcategory_id
WHERE i.item_stock > 0
ORDER BY RAND()
LIMIT 12";

$card_items = $conn->query($query_cards);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Welcome to OnStore</title>
    <style>
        /* Card styles */
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin: 20px;
        }

        .card {
            width: 30%;
            margin-bottom: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: contain;
        }

        .card-content {
            padding: 15px;
            text-align: center;
        }

        .card h3 {
            font-size: 18px;
            margin: 10px 0;
        }

        .card p {
            font-size: 14px;
            color: #555;
        }

        .card .price {
            font-size: 16px;
            font-weight: bold;
            color: #000;
        }

        .card .button {
            background-color:rgb(136, 134, 134);
            width: 200px;
            color: white;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            display: block;
            margin-top: 10px;
            border-radius: 5px;
        }

        .card .button:hover {
            background-color:rgb(63, 61, 62);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            display: flex;
            flex-direction: column;
        }

        .modal .close {
            align-self: flex-end;
            cursor: pointer;
            font-size: 24px;
            color: #333;
        }

        .modal h2 {
            margin-bottom: 10px;
        }

        .modal img {
            width: 100%;
            height: 300px;
            object-fit: contain;
            margin-bottom: 20px;
        }

        .modal .button {
            background-color:rgb(99, 96, 96);
            color: white;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            display: block;
            margin-top: 10px;
            border-radius: 5px;
        }
        /* Welcome message styles */
        .welcome-message {
            text-align: center;
            margin: 20px 0;
            font-size: 24px;
            font-weight: bold;
        }

        .store-description {
            text-align: center;
            margin: 20px;
            font-size: 18px;
            color: #555;
        }

    </style>
</head>
<body>
    <!-- Welcome Message -->
    <div class="welcome-message">
        Welcome, <?php echo htmlspecialchars($username); ?>!
    </div>

    <!-- Store Description -->
    <div class="store-description">
        Explore the latest trends, exclusive deals, and much more at OnStore.<br> Your one-stop destination for all your shopping needs!
        <br>
        <br>
        <h2>Explore now.!</h2>
        <br>
    </div>
    <!-- Random Item Cards Section -->
     
    <div class="card-container">
        <?php if ($card_items && $card_items->num_rows > 0): ?>
            <?php while ($item = $card_items->fetch_assoc()): ?>
                <div class="card">
                    <img src="uploads/<?php echo $item['item_image']; ?>" alt="<?php echo $item['item_name']; ?>">
                    <div class="card-content">
                        <h3><?php echo $item['item_name']; ?></h3>
                        <p>Category: <?php echo $item['category_name']; ?></p>
                        <p>Subcategory: <?php echo $item['subcategory_name']; ?></p>
                        <p>Stock: <?php echo $item['item_stock']; ?></p>
                        <div class="price">$<?php echo $item['item_price']; ?></div>
                        <center><button class="button view-item-btn" data-item-id="<?php echo $item['item_id']; ?>">View Item</button></center>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No items available for display.</p>
        <?php endif; ?>
    </div>

    <center>
    <!-- Modal for View item -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Item Detail</h2>
            <div id="itemDetailContent">
                <!-- Item details will be loaded here via AJAX -->
            </div>
            <!-- Heart Icon for Wishlist -->
            <div id="wishlistIcon" style="cursor: pointer; font-size: 24px; color: gray;">
                <i class="fa fa-heart"></i> Add to Wishlist
            </div>
            <!-- Cart Icon -->
            <div id="cartIcon" style="cursor: pointer; font-size: 24px; color: gray;">
                <i class="fa fa-shopping-cart"></i> Add to Cart
            </div>
        </div>
    </div>
    </center>
    <!-- Script for Modal and AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Get modal and close button
        var modal = document.getElementById("viewModal");
        var closeBtn = document.getElementsByClassName("close")[0];

        // Function to show modal with item details
        function openModal(itemId) {
            $.ajax({
                url: 'get_item_details.php', // PHP file to fetch item details
                type: 'GET',
                data: { item_id: itemId },
                success: function(response) {
                    var itemDetails = JSON.parse(response);
                    if (itemDetails.error) {
                        alert(itemDetails.error);
                    } else {
                        $('#itemDetailContent').html(`
                            <img src="uploads/${itemDetails.item_image}" alt="${itemDetails.item_name}">
                            <p>${itemDetails.item_name}</p>
                            <p>Price: $${itemDetails.item_price}</p>
                            <p>Category: ${itemDetails.category_name}</p>
                            <p>Subcategory: ${itemDetails.subcategory_name}</p>
                        `);

                        // Set the heart icon based on whether the item is in the wishlist
                        if (itemDetails.is_in_wishlist) {
                            $('#wishlistIcon').html('<i class="fa fa-heart" style="color: red;"></i> Remove from Wishlist');
                        } else {
                            $('#wishlistIcon').html('<i class="fa fa-heart" style="color: gray;"></i> Add to Wishlist');
                        }

                        // Set the cart icon based on whether the item is in the cart
                        if (itemDetails.is_in_cart) {
                            $('#cartIcon').html('<i class="fa fa-shopping-cart" style="color: green;"></i> Remove from Cart');
                        } else {
                            $('#cartIcon').html('<i class="fa fa-shopping-cart" style="color: gray;"></i> Add to Cart');
                        }

                        $('#viewModal').show();
                        $('#viewModal').data('item-id', itemDetails.item_id);
                    }
                },
                error: function() {
                    alert('Error fetching item details.');
                }
            });
        }

        // Event listener for the "View Item" buttons
        $(document).on('click', '.view-item-btn', function() {
            var itemId = $(this).data('item-id');
            openModal(itemId);
        });

         // Event listener for the heart icon click to toggle wishlist
         $(document).on('click', '#wishlistIcon', function() {
            var itemId = $('#viewModal').data('item-id');
            var action = $(this).find('i').css('color') === 'rgb(255, 0, 0)' ? 'remove' : 'add';

            $.ajax({
                url: 'wishlist_action.php', // PHP file to add/remove from wishlist
                type: 'POST',
                data: {
                    action: action,
                    item_id: itemId
                },
                success: function(response) {
                    if (action === 'add') {
                        $('#wishlistIcon').html('<i class="fa fa-heart" style="color: red;"></i> Remove from Wishlist');
                    } else {
                        $('#wishlistIcon').html('<i class="fa fa-heart" style="color: gray;"></i> Add to Wishlist');
                    }
                },
                error: function() {
                    alert('Error updating wishlist.');
                }
            });
        });

        // Event listener for the cart icon click to toggle cart
        $(document).on('click', '#cartIcon', function() {
            var itemId = $('#viewModal').data('item-id');
            var action = $(this).find('i').css('color') === 'rgb(0, 128, 0)' ? 'remove' : 'add';

            $.ajax({
                url: 'cart_action.php', // PHP file to add/remove from cart
                type: 'POST',
                data: {
                    action: action,
                    item_id: itemId
                },
                success: function(response) {
                    if (action === 'add') {
                        $('#cartIcon').html('<i class="fa fa-shopping-cart" style="color: green;"></i> Remove from Cart');
                    } else {
                        $('#cartIcon').html('<i class="fa fa-shopping-cart" style="color: gray;"></i> Add to Cart');
                    }
                },
                error: function() {
                    alert('Error updating cart.');
                }
            });
        });

        // Close the modal when the user clicks the close button
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }

        // Close the modal if the user clicks outside the modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
