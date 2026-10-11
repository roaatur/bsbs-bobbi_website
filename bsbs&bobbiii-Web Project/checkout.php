<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Pet Food Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<?php require 'include/header.php'; ?>

    <main>
        <div class="checkout-section">
            <h1>Checkout</h1>
            <div class="checkout-form">
                <form id="checkout-form" action="process_order.php" method="POST">
                    <div class="form-section">
                        <h2>Shipping Information</h2>
                        <div class="form-group">
                            <label for="name">Full Name:</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                    </div>
                    <div class="form-section">
                        <h2>Payment Information</h2>
                        <div class="form-group">
                            <label for="cardnumber">Card Number:</label>
                            <input type="text" id="cardnumber" name="cardnumber" required>
                        </div>
                        <div class="form-group">
                            <label for="expiry">Expiry Date:</label>
                            <input type="text" id="expiry" name="expiry" placeholder="MM/YY" required>
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV:</label>
                            <input type="text" id="cvv" name="cvv" required>
                        </div>
                    </div>
                    <button type="submit" class="submit-btn">Place Order</button>
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