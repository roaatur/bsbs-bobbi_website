<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Pet Food Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .confirmation-section {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            text-align: center;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .confirmation-icon {
            font-size: 64px;
            color: #2ecc71;
            margin-bottom: 20px;
        }
        .confirmation-message {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .order-details {
            text-align: left;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .action-buttons {
            margin-top: 30px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .btn-primary {
            background: #3498db;
            color: white;
        }
        .btn-secondary {
            background: #2ecc71;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
<?php require 'include/header.php'; ?>

    <main>
        <div class="confirmation-section">
            <div class="confirmation-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="confirmation-message">Thank You for Your Order!</h1>
            <p>Your order has been successfully placed.</p>
            
            <?php
            // Get the most recent order from the cookie
            if (isset($_COOKIE['orders'])) {
                $orders = json_decode($_COOKIE['orders'], true);
                if (!empty($orders)) {
                    $latest_order = end($orders);
                    echo '<div class="order-details">';
                    echo '<p><strong>Order Number:</strong> ' . htmlspecialchars($latest_order['order_id']) . '</p>';
                    echo '<p><strong>Date:</strong> ' . htmlspecialchars($latest_order['date']) . '</p>';
                    echo '<p><strong>Total Amount:</strong> $' . number_format($latest_order['total'], 2) . '</p>';
                    echo '</div>';
                }
            }
            ?>
            
            <div class="action-buttons">
                <a href="orders.php" class="btn btn-primary">View All Orders</a>
                <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 BSBS & Bobbi PetFood Store. All rights reserved.</p>
    </footer>
</body>
</html> 