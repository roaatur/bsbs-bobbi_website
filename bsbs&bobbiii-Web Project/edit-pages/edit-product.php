<?php
require '../include/db_connect.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch product details from database
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    // Redirect to admin products page if product not found
    header("Location: ../adminprod.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Pet Food Store</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<?php require '../include/header.php'; ?>

    <main>
        <div class="edit-product-section">
            <h1>Edit Product</h1>
            <div class="edit-product-form">
                <form action="process_edit_product.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    
                    <div class="form-group">
                        <label for="product-name">Product Name:</label>
                        <input type="text" id="product-name" name="product-name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="product-price">Price:</label>
                        <input type="number" id="product-price" name="product-price" step="0.01" value="<?php echo $product['price']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="product-type">Product Type:</label>
                        <select id="product-type" name="product-type" required>
                            <option value="cat" <?php echo $product['type'] == 'cat' ? 'selected' : ''; ?>>Cat Food</option>
                            <option value="dog" <?php echo $product['type'] == 'dog' ? 'selected' : ''; ?>>Dog Food</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="product-category">Category:</label>
                        <select id="product-category" name="product-category" required>
                            <option value="adult" <?php echo $product['category'] == 'adult' ? 'selected' : ''; ?>>Adult</option>
                            <option value="puppy" <?php echo $product['category'] == 'puppy' ? 'selected' : ''; ?>>Puppy</option>
                            <option value="kitten" <?php echo $product['category'] == 'kitten' ? 'selected' : ''; ?>>Kitten</option>
                            <option value="senior" <?php echo $product['category'] == 'senior' ? 'selected' : ''; ?>>Senior</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="product-description">Description:</label>
                        <textarea id="product-description" name="product-description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="product-image">Product Image:</label>
                        <input type="file" id="product-image" name="product-image" accept="image/*">
                        <p class="current-image">Current image: <?php echo htmlspecialchars($product['image_url']); ?></p>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="save-btn">Save Changes</button>
                        <a href="../adminprod.php" class="cancel-btn">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 BSBS & Bobbi PetFood Store. All rights reserved.</p>
    </footer>

    <script src="../script.js"></script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?> 