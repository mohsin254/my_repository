<?php
include 'connect.php';

// Fetch all purchases, sorted by date in descending order
$purchases_sql = "SELECT * FROM purchase ORDER BY date DESC";
$purchases_result = $conn->query($purchases_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Purchases</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'header.php'; // Include the navigation ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>All Purchases</h2>
            <a href="add_purchase.php" class="btn btn-success">Add Purchase</a> <!-- Add Purchase Button -->
        </div>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Shop Name</th>
                    <th>Date</th>
                    <th>Total Amount (Rs)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $purchases_result->fetch_assoc()) {
                    // Convert the date format from mm-dd-yyyy to dd-mm-yyyy
                    $date = DateTime::createFromFormat('Y-m-d', $row['date']);
                    $formatted_date = $date ? $date->format('d-m-Y') : 'Invalid Date';
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['shop_name']); ?></td>
                        <td><?php echo $formatted_date; ?></td>
                        <td><?php echo htmlspecialchars($row['total_amount']); ?></td>
                        <td>
                            <a href="view_purchase_details.php?id=<?php echo $row['id']; ?>" class="btn btn-info">View Details</a>
                            <a href="edit_purchase.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="del_purchase.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this purchase?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Back Button at the bottom left -->
        <div class="d-flex justify-content-start mt-3">
            <a href="index.php" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
