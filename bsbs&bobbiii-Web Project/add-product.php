<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Pet Food Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<?php require 'include/header.php'; ?>

    <main>
        <div class="add-product-section">
            <h1>Add New Product</h1>
            <div class="add-product-form">
                <form action="process_add_product.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="product-name">Product Name:</label>
                        <input type="text" id="product-name" name="product-name" required>
                    </div>
                    <div class="form-group">
                        <label for="product-price">Price:</label>
                        <input type="number" id="product-price" name="product-price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="product-category">Category:</label>
                        <select id="product-category" name="product-category" required>
                            <option value="dog">Dog Food</option>
                            <option value="cat">Cat Food</option>
                            <option value="puppy">Puppy Food</option>
                            <option value="kitten">Kitten Food</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="product-description">Description:</label>
                        <textarea id="product-description" name="product-description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="product-image">Product Image:</label>
                        <input type="file" id="product-image" name="product-image" accept="image/*" required>
                    </div>
                    <button type="submit" class="submit-btn">Add Product</button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 BSBS & Bobbi PetFood Store. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html> 