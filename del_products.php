<?php
include 'connect.php'; // Includes the MySQLi connection from connect.php

// Get the product ID from the URL
$product_id = $_GET['id'] ?? null;

if ($product_id) {
    // Prepare and execute the deletion query
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        // Optionally, delete the product image from the server
        $stmt->close();
        header('Location: view_products.php'); // Redirect to the product list page
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Product ID not provided.</div>";
}

$conn->close(); // Close the database connection
?>
