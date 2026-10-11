<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Pet Food Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<?php require 'include/header.php'; ?>

    <main>
        <div class="cart-section">
            <h1>Shopping Cart</h1>
            <div class="cart-items">
                <!-- Cart items will be dynamically loaded here -->
            </div>
            <div class="cart-summary">
                <div class="subtotal">
                    <span>Subtotal:</span>
                    <span id="cart-subtotal">$0.00</span>
                </div>
                <button id="remove-all-btn" class="remove-all-btn">Remove All Items</button>
                <button id="checkout-btn" class="checkout-btn" disabled>Proceed to Checkout</button>
                <button class="continue-shopping-btn" onclick="window.location.href='products.php'">Continue Shopping</button>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 BSBS & Bobbi PetFood Store. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html> 