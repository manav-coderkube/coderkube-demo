<?php
include 'user_layout.php';

$subcategoryErr = $categoryErr = "";
$subcategory_id = $category_id = "";

// Fetch all categories for the category dropdown
$query = "SELECT category_id, category_name FROM tbl_category";
$result = $conn->query($query);
$categories = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>All Items</title>
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

        select {
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

        select:hover {
            background-color: rgb(63, 61, 62);
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
        /* category message styles */
        .category-message {
            text-align: center;
            margin: 20px 0;
            font-size: 24px;
            font-weight: bold;
        }

        .Category-description {
            text-align: center;
            margin: 20px;
            font-size: 18px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="category-message">
        View Items By Category
    </div>

    <!-- Category Description -->
    <div class="Category-description">
        Explore the latest trends, exclusive deals, and much more with what uhh want.
        <br>
        <h4>Select Category & Sub-Category</h4>
    </div>
    <center>
    <form id="ViewItems">
    
        <!-- category Dropdown -->
        <select id="category_id" name="category_id">
            <option value="">Select category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
            <?php endforeach; ?>
        </select>
        <span class="error" id="categoryError"><?php echo $categoryErr; ?></span>

        <!-- Subcategory Dropdown -->
        <select id="subcategory_id" name="subcategory_id">
            <option value="">Select Subcategory</option>
            <!-- Subcategories will be loaded dynamically based on category selection -->
        </select>
        <span class="error" id="subcategoryError"><?php echo $subcategoryErr; ?></span>
    </form>
    </center>

    <!-- Section to display items in cards -->
    <div id="item-cards-container" class="card-container">
        <!-- Items will be loaded here dynamically -->
    </div>

    <!-- Modal for View item -->
    <div id="viewModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Item Detail</h2>
                <div id="itemDetailContent">
                    <!-- Item details will be loaded here via AJAX -->
                </div>
            </div>
        </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Update subcategories based on selected category
            $('#category_id').change(function () {
                let categoryId = $(this).val();
                if (categoryId) {
                    $.ajax({
                        url: 'fetch_subcategories.php',
                        type: 'GET',
                        data: { category_id: categoryId },
                        success: function (response) {
                            let subcategories = JSON.parse(response);
                            let subcategoryDropdown = $('#subcategory_id');
                            subcategoryDropdown.empty(); // Clear previous options
                            subcategoryDropdown.append('<option value="">Select Subcategory</option>');
                            $.each(subcategories, function (index, subcategory) {
                                subcategoryDropdown.append('<option value="' + subcategory.subcategory_id + '">' + subcategory.subcategory_name + '</option>');
                            });
                        }
                    });
                } else {
                    $('#subcategory_id').html('<option value="">Select Subcategory</option>'); // Reset subcategory dropdown
                    $('#item-cards-container').empty(); // Clear previous items
                }
            });

            // Load items based on selected category and subcategory
            $('#subcategory_id').change(function () {
                let categoryId = $('#category_id').val();
                let subcategoryId = $(this).val();
                if (categoryId && subcategoryId) {
                    $.ajax({
                        url: 'fetch_items_by_subcategory.php',
                        type: 'GET',
                        data: { category_id: categoryId, subcategory_id: subcategoryId },
                        success: function (response) {
                            let items = JSON.parse(response);
                            let itemContainer = $('#item-cards-container');
                            itemContainer.empty(); // Clear previous items
                            if (items.length > 0) {
                                $.each(items, function (index, item) {
                                    itemContainer.append(`
                                        <div class="card">
                                            <img src="uploads/${item.item_image}" alt="${item.item_name}">
                                            <div class="card-content">
                                                <h3>${item.item_name}</h3>
                                                <p>Category: ${item.category_name}</p>
                                                <p>Subcategory: ${item.subcategory_name}</p>
                                                <div class="price">$${item.item_price}</div>
                                                <center><button class="button view-item-btn" data-item-id="${item.item_id}">View Item</button></center>
                                            </div>
                                        </div>
                                    `);
                                });
                            } else {
                                itemContainer.append('<p>No items found for this category/subcategory.</p>');
                            }
                        },
                        error: function () {
                            alert('Error fetching items.');
                        }
                    });
                }
            });
        });
        var modal = document.getElementById("viewModal");
            var closeBtn = document.getElementsByClassName("close")[0];

            // Function to show modal with item details
            function openModal(itemId) {
                $.ajax({
                    url: 'get_item_details.php', // PHP file to fetch item details
                    type: 'GET',
                    data: { item_id: itemId },
                    success: function(response) {
                        $('#itemDetailContent').html(response);
                        modal.style.display = "flex";
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
