<?php
// Database configuration for quantity updates only
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "bsbs_and_bobbie";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    
    // Get cart items from POST
    $cart_items = isset($_POST['cart_items']) ? json_decode($_POST['cart_items'], true) : [];

    if (empty($name) || empty($email) || empty($address)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit;
    }

    if (empty($cart_items)) {
        echo json_encode(['status' => 'error', 'message' => 'Cart is empty']);
        exit;
    }

    try {
        // Start transaction for quantity updates
        $conn->begin_transaction();

        // Update quantities in database
        foreach ($cart_items as $item) {
            $stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            $stmt->bind_param("ii", $item['quantity'], $item['id']);
            $stmt->execute();
            
            if ($stmt->error) {
                throw new Exception("Error updating quantity: " . $stmt->error);
            }
        }

        // Commit transaction
        $conn->commit();

        // Create order data
        $order_data = [
            'order_id' => uniqid('ORD-'),
            'date' => date('Y-m-d H:i:s'),
            'customer' => [
                'name' => $name,
                'email' => $email,
                'address' => $address
            ],
            'items' => $cart_items,
            'total' => array_sum(array_map(function($item) {
                return $item['price'] * $item['quantity'];
            }, $cart_items))
        ];

        // Get existing orders from cookie
        $orders = isset($_COOKIE['orders']) ? json_decode($_COOKIE['orders'], true) : [];
        
        // Add new order
        $orders[] = $order_data;
        
        // Save updated orders to cookie (expires in 1 year)
        setcookie('orders', json_encode($orders), time() + (86400 * 365), '/');
        
        echo json_encode(['status' => 'success', 'message' => 'Order processed successfully!']);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Order processing failed: ' . $e->getMessage()]);
    } finally {
        $conn->close();
    }
}
?> 