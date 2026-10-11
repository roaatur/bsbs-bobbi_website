<?php 
require 'include/header.php';
// require 'include/db_connect.php';
//jana
// Fetch latest 4 products
// $sql = "SELECT * FROM products ORDER BY id DESC LIMIT 4";
// $result = $conn->query($sql);


$conn = new mysqli('localhost:3307', 'root', '', 'bsbs_and_bobbie');
$sql = "SELECT * FROM products";

$result = mysqli_query($conn,$sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage-Pet Food Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<main>
    <div class="hero-section">
        <h1>Welcome to PetFood Store</h1>
        <p>Quality food for your beloved pets</p>
    </div>

    <div class="products-container">
        <?php
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="product-card">';
                echo '<img src="product-images/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                echo '<p class="price">$' . number_format($row['price'], 2) . '</p>';
                echo '<p class="description">' . htmlspecialchars($row['description']) . '</p>';
                echo '<a href="product-pages/product-detail.php?id=' . $row['id'] . '" class="view-details">View Details</a>';
                echo '</div>';
            }
        } else {
            echo '<p>No products found.</p>';
        }
        ?>
    </div>
</main>

<footer>
    <p>&copy; 2025 BSBS & Bobbi PetFood Store. All rights reserved.</p>
</footer>

<script src="script.js"></script>
</body>
</html>
<?php $conn->close(); ?>
