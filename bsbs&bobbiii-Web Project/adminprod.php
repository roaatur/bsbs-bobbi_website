<?php
require_once 'auth_check.php';
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


$editMode = false;
$productToEdit = null;

if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editQuery = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $editQuery->bind_param("i", $editId);
    $editQuery->execute();
    $result = $editQuery->get_result();
    if ($result->num_rows === 1) {
        $productToEdit = $result->fetch_assoc();
        $editMode = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update-product'])) {
    $id = intval($_POST['product-id']);
    $name = $_POST['product-name'];
    $price = $_POST['product-price'];
    $category = $_POST['product-category'];
    $description = $_POST['product-description'];
    $quantity = $_POST['product-quantity'];

    if (!empty($_FILES['product-image']['name'])) {
        $imageName = $_FILES['product-image']['name'];
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($imageName);
        move_uploaded_file($_FILES['product-image']['tmp_name'], $targetFile);
    } else {
        $targetFile = $_POST['existing-image'];
    }

    $update = $conn->prepare("UPDATE products SET name=?, price=?, type=?, description=?, image=?, quantity=? WHERE id=?");
    $update->bind_param("sdsssii", $name, $price, $category, $description, $targetFile, $quantity, $id);

    if ($update->execute()) {
        header("Location: adminprod.php?success=1");
        exit();
    } else {
        header("Location: adminprod.php?error=1");
        exit();
    }
}

$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchSql = "SELECT * FROM products";
if ($searchTerm !== '') {
    $searchSql .= " WHERE name LIKE ?";
    $searchStmt = $conn->prepare($searchSql);
    $likeTerm = "%" . $searchTerm . "%";
    $searchStmt->bind_param("s", $likeTerm);
    $searchStmt->execute();
    $products = $searchStmt->get_result();
} else {
    $products = $conn->query("SELECT * FROM products");
}

// Now include the header after all potential redirects
require 'include/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Products - Pet Food Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="adminprod.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="page-wrapper">
<main>
    <div class="admin-section">
        <div class="admin-header">
            <h1>Product Management</h1>
            <div class="admin-controls">
                <span class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <?php if (isset($_GET['success'])) echo '<div class="alert success">Product updated successfully!</div>'; ?>
        <?php if (isset($_GET['error'])) echo '<div class="alert error">Error updating product. Please try again.</div>'; ?>

        <div class="edit-form-container">
        <style>
        .edit-form-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 32px 24px 24px 24px;
            max-width: 700px;
            margin: 0 auto 32px auto;
        }
        .edit-form-container form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px 24px;
            align-items: center;
        }
        .edit-form-container label {
            font-weight: 500;
            margin-bottom: 4px;
            grid-column: 1 / 2;
        }
        .edit-form-container input[type="text"],
        .edit-form-container input[type="number"],
        .edit-form-container select,
        .edit-form-container textarea {
            width: 100%;
            padding: 7px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .edit-form-container textarea {
            min-height: 40px;
            resize: vertical;
        }
        .edit-form-container button,
        .edit-form-container .cancel-btn {
            grid-column: span 2;
            width: 180px;
            margin-top: 10px;
            margin-bottom: 0;
        }
        .edit-form-container img {
            max-height: 50px;
            display: block;
            margin-top: 4px;
        }
        </style>
        <?php if ($editMode && $productToEdit && !isset($_GET['success'])): ?>
        <div class="edit-form">
            <h2>Edit Product: <?= htmlspecialchars($productToEdit['name']) ?></h2>
            <form action="adminprod.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product-id" value="<?= $productToEdit['id'] ?>">
                <input type="hidden" name="existing-image" value="<?= $productToEdit['image'] ?>">

                <label>Name:</label>
                <input type="text" name="product-name" value="<?= htmlspecialchars($productToEdit['name']) ?>" required>

                <label>Price:</label>
                <input type="number" step="0.01" name="product-price" value="<?= $productToEdit['price'] ?>" required>

                <label>Quantity:</label>
                <input type="number" name="product-quantity" value="<?= $productToEdit['quantity'] ?>" min="1" required>

                <label>Category:</label>
                <select name="product-category" required>
                    <option value="cat" <?= $productToEdit['type'] == 'cat' ? 'selected' : '' ?>>Cat Food</option>
                    <option value="dog" <?= $productToEdit['type'] == 'dog' ? 'selected' : '' ?>>Dog Food</option>
                </select>

                <label>Description:</label>
                <textarea name="product-description" required><?= htmlspecialchars($productToEdit['description']) ?></textarea>

                <label>Image:</label>
                <input type="file" name="product-image" accept="image/*">
                <span style="grid-column: 2 / 3;">Current Image: <img src="<?= $productToEdit['image'] ?>" style="height:50px;"></span>
              

                <button type="submit" name="update-product">Update Product</button>
                <a href="adminprod.php" class="cancel-btn">Cancel</a>
            </form>
        </div>
        <?php endif; ?>
        </div>

        <div class="admin-actions">
            <button onclick="window.location.href='addProduct.php'" class="add-product-btn">Add New Product</button>
        </div>

        <div class="search-bar-container">
            <form method="GET" action="adminprod.php" class="search-bar-form">
                <input type="text" name="search" placeholder="Search products by name..." value="<?= htmlspecialchars($searchTerm) ?>" class="search-input">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>

        <div class="products-table-container">
            <div class="products-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Image</th><th>Name</th><th>Price</th><th>Quantity</th><th>Category</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($products->num_rows > 0): ?>
                            <?php while ($row = $products->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><img src="<?= (strpos($row['image'], 'uploads/') === 0 ? htmlspecialchars($row['image']) : 'uploads/' . htmlspecialchars($row['image'])) ?>" class="product-thumbnail" alt="<?= htmlspecialchars($row['name']) ?>"></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td>$<?= number_format($row['price'], 2) ?></td>
                                <td><?= $row['quantity'] ?></td>
                                <td><?= htmlspecialchars($row['type']) ?></td>
                                <td>
                                    <a href="adminprod.php?edit=<?= $row['id'] ?>" class="edit-btn"><i class="fas fa-edit"></i> Edit</a>
                                    <button onclick="deleteProduct(<?= $row['id'] ?>)" class="delete-btn"><i class="fas fa-trash"></i> Delete</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7">No products found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
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
  }
  footer {
    margin-top: auto;
    background: #6c3483;
    color: #fff;
    text-align: center;
    padding: 16px 0 10px 0;
    font-size: 1rem;
  }
  .products-table-container {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    margin: 32px 0 0 0;
    width: 100%;
  }
  .products-table {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    padding: 24px 18px 18px 18px;
    max-width: 1100px;
    width: 100%;
    margin: 0 auto;
  }
  .products-table table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 1rem;
    background: transparent;
  }
  .products-table th, .products-table td {
    padding: 12px 10px;
    text-align: center;
    border-bottom: 1px solid #eee;
  }
  .products-table th {
    background: #f5f2fa;
    font-weight: 600;
    color: #6c3483;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
  }
  .products-table tr:last-child td {
    border-bottom: none;
  }
  .products-table tr:hover {
    background: #f9f6fd;
  }
  .products-table img.product-thumbnail {
    max-height: 48px;
    border-radius: 6px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.07);
  }
  .edit-btn, .delete-btn {
    margin: 0 2px;
    padding: 6px 12px;
    border-radius: 5px;
    font-size: 0.97rem;
    border: none;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
  }
  .edit-btn {
    background: #f5f2fa;
    color: #6c3483;
    border: 1px solid #6c3483;
    transition: background 0.2s, color 0.2s;
  }
  .edit-btn:hover {
    background: #6c3483;
    color: #fff;
  }
  .delete-btn {
    background: #6c3483;
    color: #fff;
    border: 1px solid #6c3483;
    transition: background 0.2s, color 0.2s;
  }
  .delete-btn:hover {
    background: #fff;
    color: #6c3483;
  }
  .search-bar-container {
    display: flex;
    justify-content: center;
    margin-bottom: 18px;
    margin-top: 18px;
  }
  .search-bar-form {
    display: flex;
    gap: 8px;
    width: 100%;
    max-width: 400px;
  }
  .search-input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
  }
  .search-btn {
    background: #6c3483;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 8px 16px;
    font-size: 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
    transition: background 0.2s;
  }
  .search-btn:hover {
    background: #51276a;
  }
  .admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
  .admin-controls {
    display: flex;
    align-items: center;
    gap: 20px;
  }
  .welcome-message {
    color: #6c3483;
    font-weight: 500;
  }
  .logout-btn {
    background: #6c3483;
    color: white;
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.3s;
  }
  .logout-btn:hover {
    background: #51276a;
  }
</style>

<script>
function deleteProduct(id) {
    if (confirm("Are you sure you want to delete this product?")) {
        window.location.href = "delete_product.php?id=" + id;
    }
}

// Remove success/error message from URL after showing it
window.addEventListener('DOMContentLoaded', function() {
  const url = new URL(window.location);
  let changed = false;
  if (url.searchParams.has('success')) {
    url.searchParams.delete('success');
    changed = true;
  }
  if (url.searchParams.has('error')) {
    url.searchParams.delete('error');
    changed = true;
  }
  if (changed) {
    setTimeout(function() {
      window.history.replaceState({}, document.title, url.pathname + url.search);
    }, 2000); // 2 seconds
  }
});
</script>
</body>
</html>

<?php $conn->close(); ?>