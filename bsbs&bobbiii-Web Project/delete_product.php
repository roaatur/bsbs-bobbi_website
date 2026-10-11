<?php
require 'include/db_connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    
    $check = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: adminprod.php?success=1");
            exit();
        } else {
            header("Location: adminprod.php?error=1");
            exit();
        }
    } else {
        header("Location: adminprod.php?error=1");
        exit();
    }
} else {
    header("Location: adminprod.php?error=1");
    exit();
}
?>
