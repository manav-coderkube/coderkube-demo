<?php
include 'user_layout.php';
?>

<title>Cart</title>
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
</style>
<center>
    <h2>Cart</h2>
</center>

<!-- Display Cart Items -->
<div id="cart-container" class="card-container">
    <!-- Cart items will be loaded dynamically via AJAX -->
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        fetchCartItems();

        function fetchCartItems() {
            $.ajax({
                url: 'fetch_cart.php',  // PHP file to fetch cart data
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.error) {
                        alert(response.error);
                    } else if (response.length === 0) {
                        $('#cart-container').html('<b><p style="text-align:center;">***** Your Cart is empty. *****</p></b>');
                    } else {
                        var html = '';
                        $.each(response, function (index, item) {
                            html += '<div class="card">';
                            html += '<img src="uploads/' + item.item_image + '" alt="' + item.item_name + '">';
                            html += '<div class="card-content">';
                            html += '<h3>' + item.item_name + '</h3>';
                            html += '<p>Category: ' + item.category_name + '</p>';
                            html += '<p>Subcategory: ' + item.subcategory_name + '</p>';
                            html += '<div class="price">$' + item.item_price + '</div>';
                            html += '<center><button class="button buy-item-btn" data-item-id="' + item.item_id + '">Buy</button></center>';
                            html += '<center><button class="button remove-item-btn" data-item-id="' + item.item_id + '">Remove from Cart</button></center>';
                            html += '</div>';
                            html += '</div>';
                        });
                        $('#cart-container').html(html);
                    }
                },
                error: function () {
                    alert('Error fetching cart items.');
                }
            });
        }

        // Remove item from cart
        $(document).on('click', '.remove-item-btn', function() {
            var itemId = $(this).data('item-id');
            $.ajax({
                url: 'cart_action.php',
                type: 'POST',
                data: {
                    action: 'remove',
                    item_id: itemId
                },
                success: function(response) {
                    alert('Removing item from cart.');
                    fetchCartItems();
                },
                error: function() {
                    alert('Error removing item from cart.');
                }
            });
        });

        $(document).on('click', '.buy-item-btn', function() {
            var itemId = $(this).data('item-id');
            
            $.ajax({
                url: 'cart_action.php',
                type: 'POST',
                data: {
                    action: 'buy',
                    item_id: itemId
                },
                success: function(response) {
                    window.location.href = 'buy_item.php?item_id=' + itemId;
                    // fetchCartItems();  // Refresh cart after purchase
                },
                error: function() {
                    alert('Error purchasing item.');
                }
            });
        });
    });
</script>