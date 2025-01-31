<?php
include 'user_layout.php';
?>

<title>Wishlist</title>
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
        background-color: rgb(136, 134, 134);
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
        background-color: rgb(63, 61, 62);
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: absolute;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        overflow: auto;
    }

    .modal-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        width: 80%;
        max-width: 600px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .modal .close {
        align-self: flex-end;
        cursor: pointer;
        font-size: 24px;
        color: #333;
        padding: 10px;
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
        background-color: rgb(99, 96, 96);
        color: white;
        padding: 10px;
        text-align: center;
        text-decoration: none;
        display: block;
        margin-top: 10px;
        border-radius: 5px;
    }

    /* Add custom transitions for modal */
    .modal-content {
        animation: modalFadeIn 0.3s ease-in-out;
    }

    @keyframes modalFadeIn {
        0% {
            transform: scale(0.9);
            opacity: 0;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

</style>
<center>
    <h2>Wishlist</h2>
</center>

<!-- Display Wishlist Items -->
<div id="wishlist-container" class="card-container">
    <!-- Wishlist items will be loaded dynamically via AJAX -->
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Fetch wishlist items via AJAX
        fetchWishlistItems();
        function fetchWishlistItems() {
            $.ajax({
                url: 'fetch_wishlist.php',  // PHP file to fetch wishlist data
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.error) {
                        alert(response.error);
                    } else if (response.length === 0) {
                        $('#wishlist-container').html('<b><p style="text-align:center;">***** Your wishlist is empty. *****</p></b>');
                    } else {
                        // Iterate through the items and create HTML for each item
                        var html = '';
                        $.each(response, function (index, item) {
                            html += '<div class="card">';
                            html += '<img src="uploads/' + item.item_image + '" alt="' + item.item_name + '">';
                            html += '<div class="card-content">';
                            html += '<h3>' + item.item_name + '</h3>';
                            html += '<p>Category: ' + item.category_name + '</p>';
                            html += '<p>Subcategory: ' + item.subcategory_name + '</p>';
                            html += '<p>Stock: ' + item.item_stock + '</p>';
                            html += '<div class="price">$' + item.item_price + '</div>';
                            html += '<center><button class="button view-item-btn" data-item-id="' + item.item_id + '">View Item</button></center>';
                            html += '</div>';
                            html += '</div>';
                        });
                        // Append generated HTML to the wishlist container
                        $('#wishlist-container').html(html);
                    }
                },
                error: function () {
                    alert('Error fetching wishlist items.');
                }
            });
        }

        // Event listener for the "View Item" buttons
        $(document).on('click', '.view-item-btn', function() {
            var itemId = $(this).data('item-id');
            openModal(itemId);
        });

        // Function to open modal and load item details
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


                        // Show modal
                        $('#viewModal').show();
                        $('#viewModal').data('item-id', itemDetails.item_id);
                    }
                },
                error: function() {
                    alert('Error fetching item details.');
                }
            });
        }

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

        // Close modal
        var modal = document.getElementById('viewModal');
        var closeBtn = document.getElementsByClassName('close')[0];

        closeBtn.onclick = function() {
            modal.style.display = "none";
            fetchWishlistItems();
        }

        // Close the modal if the user clicks outside the modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                fetchWishlistItems();
            }
        }
    });
</script>
