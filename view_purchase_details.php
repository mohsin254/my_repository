<?php
include 'connect.php';

// Get the purchase ID from the URL
$purchase_id = $_GET['id'];

// Fetch purchase data
$purchase_sql = "SELECT * FROM purchase WHERE id = ?";
$purchase_stmt = $conn->prepare($purchase_sql);
$purchase_stmt->bind_param('i', $purchase_id);
$purchase_stmt->execute();
$purchase_result = $purchase_stmt->get_result();
$purchase = $purchase_result->fetch_assoc();

// Fetch purchase details
$details_sql = "SELECT pd.*, pi.product_name 
                FROM purchase_details pd 
                JOIN purchasing_items pi ON pd.product_id = pi.id 
                WHERE pd.purchase_id = ?";
$details_stmt = $conn->prepare($details_sql);
$details_stmt->bind_param('i', $purchase_id);
$details_stmt->execute();
$details_result = $details_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Purchase Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'header.php'; // Include the navigation ?>

    <div class="container mt-5">
        <h2>Purchase Details for <?php echo htmlspecialchars($purchase['shop_name']); ?></h2>
        <?php
        // Convert the date format from mm-dd-yyyy to dd-mm-yyyy
        $date = DateTime::createFromFormat('Y-m-d', $purchase['date']);
        $formatted_date = $date ? $date->format('d-m-Y') : 'Invalid Date';
        ?>
        <p>Date: <?php echo htmlspecialchars($formatted_date); ?></p>
        <p>Total Amount: Rs <?php echo number_format($purchase['total_amount'], 2); ?></p> <!-- Fix to display the amount properly -->

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Per Piece Price</th>
                    <th>Weight</th>
                    <th>Per Kg Price</th>
                    <th>Amount (Rs)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($detail = $details_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($detail['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($detail['quantity']); ?></td>
                        <td><?php echo number_format($detail['per_piece_price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($detail['weight']); ?></td>
                        <td><?php echo number_format($detail['per_kg_price'], 2); ?></td>
                        <td><?php echo number_format($detail['amount'], 2); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Back Button at the bottom left -->
        <div class="d-flex justify-content-start mt-3">
            <a href="view_purchase.php" class="btn btn-secondary">Back</a>
        </div>
    </div>
</body>
</html>
