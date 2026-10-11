<?php
require '../include/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = (int)$_POST['product_id'];
    $name = $_POST['product-name'];
    $price = (float)$_POST['product-price'];
    $type = $_POST['product-type'];
    $category = $_POST['product-category'];
    $description = $_POST['product-description'];
    
    // Generate slug from name
    $slug = strtolower(str_replace(' ', '-', $name));
    
    // Handle image upload if a new image is provided
    $image_url = '';
    if (isset($_FILES['product-image']) && $_FILES['product-image']['error'] == 0) {
        $target_dir = "../";
        $file_extension = strtolower(pathinfo($_FILES["product-image"]["name"], PATHINFO_EXTENSION));
        $new_filename = $slug . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["product-image"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["product-image"]["tmp_name"], $target_file)) {
                $image_url = $new_filename;
            }
        }
    }
    
    // Update product in database
    if ($image_url) {
        // Update with new image
        $sql = "UPDATE products SET name=?, price=?, type=?, category=?, description=?, image_url=?, slug=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsssssi", $name, $price, $type, $category, $description, $image_url, $slug, $product_id);
    } else {
        // Update without changing image
        $sql = "UPDATE products SET name=?, price=?, type=?, category=?, description=?, slug=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdssssi", $name, $price, $type, $category, $description, $slug, $product_id);
    }
    
    if ($stmt->execute()) {
        // Redirect back to admin products page with success message
        header("Location: ../adminprod.php?success=1");
    } else {
        // Redirect back with error message
        header("Location: ../adminprod.php?error=1");
    }
    
    $stmt->close();
} else {
    // If not POST request, redirect to admin products page
    header("Location: ../adminprod.php");
}

$conn->close();
?> 