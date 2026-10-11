<?php
require_once 'include/db_connection.php';

// Get the cart items from the POST data
$cart_items = json_decode($_POST['cart_items'], true);

if (!$cart_items) {
    echo json_encode(['status' => 'error', 'message' => 'No cart items received']);
    exit;
}

try {
    // Start transaction
    $conn->beginTransaction();
    
    foreach ($cart_items as $item) {
        // Update the quantity in the database
        $stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
        $stmt->execute([$item['quantity'], $item['id']]);
        
        // Check if the update was successful
        if ($stmt->rowCount() === 0) {
            throw new Exception("Product not found or quantity update failed");
        }
    }
    
    // Commit the transaction
    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Quantities updated successfully']);
    
} catch (Exception $e) {
    // Rollback the transaction on error
    $conn->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?> 