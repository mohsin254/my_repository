<?php
include 'connect.php';

$purchase_id = $_GET['id'];

// Delete purchase details first
$delete_details_sql = "DELETE FROM purchase_details WHERE purchase_id = ?";
$delete_details_stmt = $conn->prepare($delete_details_sql);
$delete_details_stmt->bind_param('i', $purchase_id);
$delete_details_stmt->execute();

// Delete the purchase itself
$delete_purchase_sql = "DELETE FROM purchase WHERE id = ?";
$delete_purchase_stmt = $conn->prepare($delete_purchase_sql);
$delete_purchase_stmt->bind_param('i', $purchase_id);
$delete_purchase_stmt->execute();

header("Location: view_purchase.php");
exit;
?>
