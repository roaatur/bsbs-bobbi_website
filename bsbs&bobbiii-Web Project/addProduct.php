<?php
require 'auth_check.php';
require 'include/db_connect.php';
// Check and start session if not active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is not logged in
// if(!isset($_SESSION['admin_name'])) {
//     header("Location: adminlogin.php");
//     exit();
// }

// require 'include/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productName = $_POST['product-name'];
    $productPrice = $_POST['product-price'];
    $productCategory = $_POST['product-category'];
    $productDescription = $_POST['product-description'];
    $productImage = $_FILES['product-image']['name'];
    $productQuantity = $_POST['product-quantity'];

    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($productImage);
    move_uploaded_file($_FILES['product-image']['tmp_name'], $targetFile);

    $sql = "INSERT INTO products (name, price, quantity, image, description, admin_id, type) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $adminId = 1; // يمكن تغييره حسب السيشن إذا كنتِ تستخدمين تسجيل دخول

    $stmt->bind_param("sdissis", $productName, $productPrice, $productQuantity, $targetFile, $productDescription, $adminId, $productCategory);

    if ($stmt->execute()) {
        header("Location: addProduct.php?success=1");
        exit();
    } else {
        $error = "Error adding product: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - Pet Food Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php require 'include/header.php'; ?>
<div class="page-wrapper">
<main>
    <div class="add-product-section add-product-container">
        <div class="admin-header">
            <h1>Add New Product</h1>
            <div class="admin-controls">
                <span class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                <a href="adminprod.php" class="back-btn">Back to Products</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert success">Product added successfully!</div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="product-name">Product Name:</label>
                <input type="text" name="product-name" id="product-name" required>
            </div>
            <div class="form-group">
                <label for="product-price">Price:</label>
                <input type="number" name="product-price" id="product-price" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="product-quantity">Quantity:</label>
                <input type="number" name="product-quantity" id="product-quantity" min="1" required>
            </div>
            <div class="form-group">
                <label for="product-category">Category:</label>
                <select name="product-category" id="product-category" required>
                    <option value="dog">Dog Food</option>
                    <option value="cat">Cat Food</option>
                    <option value="puppy">Puppy Food</option>
                    <option value="kitten">Kitten Food</option>
                </select>
            </div>
            <div class="form-group">
                <label for="product-description">Description:</label>
                <textarea name="product-description" id="product-description" required></textarea>
            </div>
            <div class="form-group">
                <label for="product-image">Product Image:</label>
                <input type="file" name="product-image" id="product-image" accept="image/*" required>
            </div>
            <button type="submit" class="submit-btn">Add Product</button>
        </form>
    </div>
</main>
<footer>
    <p>&copy; 2025 BSBS & Bobbi PetFood Store. All rights reserved.</p>
</footer>
</div>
<style>
  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
  }
  .page-wrapper {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }
  main {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .add-product-container {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 32px 24px 24px 24px;
    max-width: 500px;
    width: 100%;
    margin: 32px auto;
  }
  .admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
  }
  .admin-controls {
    display: flex;
    align-items: center;
    gap: 15px;
  }
  .welcome-message {
    color: #6c3483;
    font-weight: 500;
  }
  .back-btn, .logout-btn {
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.3s;
  }
  .back-btn {
    background: #f5f2fa;
    color: #6c3483;
    border: 1px solid #6c3483;
  }
  .back-btn:hover {
    background: #6c3483;
    color: #fff;
  }
  .logout-btn {
    background: #6c3483;
    color: white;
  }
  .logout-btn:hover {
    background: #51276a;
  }
  .alert {
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 5px;
    text-align: center;
  }
  .alert.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
  }
  .alert.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
  }
  .add-product-container h1 {
    text-align: center;
    margin-bottom: 24px;
  }
  .add-product-container .form-group {
    margin-bottom: 16px;
  }
  .add-product-container label {
    display: block;
    font-weight: 500;
    margin-bottom: 6px;
  }
  .add-product-container input[type="text"],
  .add-product-container input[type="number"],
  .add-product-container select,
  .add-product-container textarea {
    width: 100%;
    padding: 7px 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
    box-sizing: border-box;
  }
  .add-product-container textarea {
    min-height: 40px;
    resize: vertical;
  }
  .add-product-container .submit-btn {
    width: 100%;
    padding: 10px 0;
    background: #6c3483;
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 10px;
  }
  footer {
    margin-top: auto;
    background: #6c3483;
    color: #fff;
    text-align: center;
    padding: 16px 0 10px 0;
    font-size: 1rem;
  }
</style>
</body>
</html>