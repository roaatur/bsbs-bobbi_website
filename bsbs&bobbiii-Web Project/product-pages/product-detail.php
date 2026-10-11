<?php
require '../include/db_connect.php';

// Get product slug from URL
$id = isset($_GET['id']) ? $_GET['id'] : '';

// Fetch product details from database
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    // Redirect to products page if product not found
    header("Location: ../products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Pet Food Store</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<header>
        <nav>
            <div class="logo">
                <img src="../logo.jpg" alt="BSBS & Bobbi Logo">
                <div class="brand-name">
                    <span class="main-brand">BSBS & Bobbi</span>
                    <span class="sub-brand">PetFood Store</span>
                </div>
            </div>
            <ul class="nav-links">
                <li><a href="../homepage.php">Home</a></li>
                <li><a href="../products.php">Products</a></li>
                <li><a href="../contact.php">Contact Us</a></li>
            </ul>
            <div class="icon-bar">
                <div class="login-icon" id="login-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="cart-icon" id="cart-icon" onclick="window.location.href='../cart.php'">
                    <i class="fas fa-shopping-cart"></i>
                    
                    <span id="cart-count">0</span>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="product-detail-section">
            <div class="product-detail-container">
                <div class="product-image">
                    <img src="../product-images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="product-info">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                    <div class="product-description">
                        <h3>Description</h3>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        <?php if(isset($product['category']) && !empty($product['category'])): ?>
                        <h3>Category</h3>
                        <p><?php echo htmlspecialchars($product['category']); ?></p>
                        <?php endif; ?>
                        <h3>Stock Status</h3>
                        <p class="stock-status"><?php echo $product['quantity'] > 0 ? 'In Stock (' . $product['quantity'] . ' available)' : 'Out of Stock'; ?></p>
                    </div>
                    <div class="product-actions">
                        <div class="quantity-selector">
                            <button class="quantity-btn minus" type="button">-</button>
                            <input type="number" value="1" min="1" max="<?php echo $product['quantity']; ?>" class="quantity-input" 
                                   data-stock="<?php echo $product['quantity']; ?>"
                                   onchange="validateQuantity(this)">
                            <button class="quantity-btn plus" type="button">+</button>
                        </div>
                        <button class="add-to-cart-btn" 
                                data-id="<?php echo $product['id']; ?>"
                                data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                data-price="<?php echo $product['price']; ?>"
                                data-stock="<?php echo $product['quantity']; ?>"
                                type="button"
                                onclick="addToCartWithValidation(this, event)"
                                <?php echo $product['quantity'] <= 0 ? 'disabled' : ''; ?>>
                            <?php echo $product['quantity'] <= 0 ? 'Out of Stock' : 'Add to Cart'; ?>
                        </button>
                    </div>
                    <script>
                        function validateQuantity(input) {
                            const stock = parseInt(input.getAttribute('data-stock'));
                            let value = parseInt(input.value);
                            
                            if (isNaN(value) || value < 1) {
                                input.value = 1;
                            } else if (value > stock) {
                                input.value = stock;
                                showErrorPopup(`Sorry, we only have ${stock} items in stock.`);
                            }
                        }

                        function addToCartWithValidation(button, event) {
                            event.preventDefault();
                            
                            const stock = parseInt(button.getAttribute('data-stock'));
                            const quantity = parseInt(document.querySelector('.quantity-input').value);
                            
                            if (quantity > stock) {
                                showErrorPopup(`Sorry, we only have ${stock} items in stock.`);
                                return false;
                            }
                            
                            const id = button.getAttribute('data-id');
                            const name = button.getAttribute('data-name');
                            const price = parseFloat(button.getAttribute('data-price'));
                            
                            // Check if item already exists in cart
                            let cart = JSON.parse(localStorage.getItem('cart')) || [];
                            const existingItem = cart.find(item => item.id === id);
                            
                            if (existingItem) {
                                const totalQuantity = existingItem.quantity + quantity;
                                if (totalQuantity > stock) {
                                    showErrorPopup(`Sorry, we only have ${stock} items in stock. You already have ${existingItem.quantity} in your cart.`);
                                    return false;
                                }
                            }
                            
                            // If all checks pass, add to cart
                            addToCart(id, name, price, quantity);
                            return false;
                        }

                        function showErrorPopup(message) {
                            const popup = document.createElement('div');
                            popup.className = 'error-popup';
                            popup.innerHTML = `
                                <div class="error-content">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <p>${message}</p>
                                    <button class="close-btn" onclick="this.parentElement.parentElement.remove()">OK</button>
                                </div>
                            `;
                            document.body.appendChild(popup);
                            setTimeout(() => popup.remove(), 5000);
                        }
                    </script>
                    <style>
                        .error-popup {
                            position: fixed;
                            top: 20px;
                            right: 20px;
                            z-index: 1000;
                            animation: slideIn 0.3s ease-out;
                        }
                        
                        .error-content {
                            background: white;
                            padding: 15px 20px;
                            border-radius: 8px;
                            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                            display: flex;
                            align-items: center;
                            gap: 10px;
                            border-left: 4px solid #dc3545;
                        }
                        
                        .error-content i {
                            color: #dc3545;
                            font-size: 1.2rem;
                        }
                        
                        .error-content p {
                            margin: 0;
                            flex-grow: 1;
                        }
                        
                        .close-btn {
                            background: none;
                            border: none;
                            color: #666;
                            cursor: pointer;
                            padding: 5px 10px;
                        }
                        
                        @keyframes slideIn {
                            from { transform: translateX(100%); opacity: 0; }
                            to { transform: translateX(0); opacity: 1; }
                        }
                    </style>
                </div>
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