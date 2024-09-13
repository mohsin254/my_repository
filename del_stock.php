<?php
include 'connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete stock entry
    $delete_stock_sql = "DELETE FROM stock WHERE id = ?";
    $delete_stock_stmt = $conn->prepare($delete_stock_sql);
    $delete_stock_stmt->bind_param('i', $id);
    $delete_stock_stmt->execute();

    header("Location: view_stock.php");
    exit;
}
?>
