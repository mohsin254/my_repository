<?php
include 'connect.php';

$sale_id = $_GET['id'] ?? null;

if ($sale_id) {
    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete sale details
        $conn->query("DELETE FROM sale_details WHERE sale_id = '$sale_id'");

        // Delete sale record
        $conn->query("DELETE FROM sales WHERE id = '$sale_id'");

        // Commit transaction
        $conn->commit();

        echo "<p>Sale deleted successfully!</p>";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<a href="view_sales.php" class="btn btn-secondary">Back to Sales</a>
