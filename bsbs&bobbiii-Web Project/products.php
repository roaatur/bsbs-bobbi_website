<?php 
require 'include/header.php';
require 'include/db_connect.php';

// Fetch products from database
$sql = "SELECT * FROM products";

$result = mysqli_query($conn,$sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Pet Food Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        
        .help-btn {
            background: none;
            border: none;
            color: #62368B;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 5px;
            transition: transform 0.2s;
        }
        
        .help-btn:hover {
            transform: scale(1.1);
        }
        
        .help-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            max-width: 400px;
            width: 90%;
        }
        
        .help-popup h3 {
            color: #62368B;
            margin-bottom: 10px;
        }
        
        .help-popup p {
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .help-popup .close-btn {
            background: #62368B;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            float: right;
        }
        
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
    </style>
</head>
<body>


    <main>
        <div class="products-section">
            <h1>Our Products</h1>
            <div class="products-grid">
                <?php
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="product-card">';
                           echo '<img src="product-images/' . htmlspecialchars($row['image']) .'" alt="' . htmlspecialchars($row['name']) . '">';
                        echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                        echo '<p class="price">$' . number_format($row['price'], 2) . '</p>';
                        echo '<p class="description">' . htmlspecialchars($row['description']) . '</p>';
                        echo '<div class="product-actions">';
                        echo '<a href="product-pages/product-detail.php?id=' . htmlspecialchars($row['id']) . '" class="view-details">View Details</a>';
                        echo '<button class="help-btn" data-type="' . htmlspecialchars($row['type']) . '" data-name="' . htmlspecialchars($row['name']) . '"><i class="fas fa-question-circle"></i></button>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No products found.</p>';
                }
                ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 BSBS & Bobbi PetFood Store. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
    
    <div class="overlay" id="overlay"></div>
    <div class="help-popup" id="helpPopup">
        <h3 id="popupTitle"></h3>
        <p id="popupContent"></p>
        <button class="close-btn" onclick="closeHelpPopup()">Close</button>
    </div>
</body>
</html>
<?php
$conn->close();
?> 