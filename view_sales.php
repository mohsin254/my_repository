<?php
include 'connect.php';

// Fetch all sales from the database, ordered by date descending
$sql = "SELECT s.id, s.shop_name, s.date, s.total_amount, COUNT(sd.id) AS products_count
        FROM sales s
        LEFT JOIN sale_details sd ON s.id = sd.sale_id
        GROUP BY s.id
        ORDER BY s.date DESC"; // Sort by date descending
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Sales</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2>Sales</h2>
        <a href="add_sale.php" class="btn btn-primary mb-3">Add New Sale</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Shop Name</th>
                    <th>Date</th>
                    <th>Total Amount</th>
                    <th>Products Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                        // Convert the date format from mm-dd-yyyy to dd-mm-yyyy
                        $date = DateTime::createFromFormat('Y-m-d', $row['date']);
                        $formatted_date = $date->format('d-m-Y'); // Convert to dd-mm-yyyy
                        ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['shop_name']); ?></td>
                            <td><?php echo htmlspecialchars($formatted_date); ?></td>
                            <td>Rs <?php echo number_format($row['total_amount'], 2); ?></td>
                            <td><?php echo $row['products_count']; ?></td>
                            <td>
                                <a href="view_sale_details.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">View Details</a>
                                <a href="edit_sale.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="del_sale.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this sale?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No sales found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>
