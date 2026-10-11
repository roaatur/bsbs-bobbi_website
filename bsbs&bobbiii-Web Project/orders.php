<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Pet Food Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .orders-section {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .order-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            background: #fff;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .order-id {
            font-weight: bold;
            color: #2c3e50;
        }
        .order-date {
            color: #7f8c8d;
        }
        .order-items {
            margin: 15px 0;
        }
        .order-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .order-total {
            text-align: right;
            font-weight: bold;
            font-size: 1.2em;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .customer-info {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .empty-orders {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
<?php require 'include/header.php'; ?>

    <main>
        <div class="orders-section">
            <h1>My Orders</h1>
            <div id="orders-container">
                <?php
                if (isset($_COOKIE['orders'])) {
                    $orders = json_decode($_COOKIE['orders'], true);
                    if (!empty($orders)) {
                        foreach (array_reverse($orders) as $order) {
                            echo '<div class="order-card">';
                            echo '<div class="order-header">';
                            echo '<span class="order-id">Order #' . htmlspecialchars($order['order_id']) . '</span>';
                            echo '<span class="order-date">' . htmlspecialchars($order['date']) . '</span>';
                            echo '</div>';
                            
                            echo '<div class="customer-info">';
                            echo '<p><strong>Customer:</strong> ' . htmlspecialchars($order['customer']['name']) . '</p>';
                            echo '<p><strong>Email:</strong> ' . htmlspecialchars($order['customer']['email']) . '</p>';
                            echo '<p><strong>Address:</strong> ' . htmlspecialchars($order['customer']['address']) . '</p>';
                            echo '</div>';
                            
                            echo '<div class="order-items">';
                            foreach ($order['items'] as $item) {
                                echo '<div class="order-item">';
                                echo '<div>' . htmlspecialchars($item['name']) . '</div>';
                                echo '<div>Qty: ' . htmlspecialchars($item['quantity']) . '</div>';
                                echo '<div>$' . number_format($item['price'] * $item['quantity'], 2) . '</div>';
                                echo '</div>';
                            }
                            echo '</div>';
                            
                            echo '<div class="order-total">';
                            echo 'Total: $' . number_format($order['total'], 2);
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="empty-orders">No orders found</div>';
                    }
                } else {
                    echo '<div class="empty-orders">No orders found</div>';
                }
                ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 BSBS & Bobbi PetFood Store. All rights reserved.</p>
    </footer>
</body>
</html> 