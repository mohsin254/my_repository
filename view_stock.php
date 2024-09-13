<?php
include 'connect.php';

// Fetch all stock items
$sql = "SELECT s.id, si.product_name, si.category, si.sub_category, s.quantity
        FROM stock s
        JOIN stock_items si ON s.product_id = si.id";
$result = $conn->query($sql);

// Group by category and then by sub-category
$categories = ['Packing' => [], 'Products' => []];
while ($row = $result->fetch_assoc()) {
    $categories[$row['category']][$row['sub_category']][] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2>Stock Inventory</h2>
        <a href="add_stock.php" class="btn btn-primary mb-3">Add Stock</a>
        
        <?php foreach ($categories as $category => $subCategories): ?>
            <h3><?php echo htmlspecialchars($category); ?></h3>
            
            <?php foreach ($subCategories as $subCategory => $stocks): ?>
                <hr />
                <h5><?php echo htmlspecialchars($subCategory); ?></h5>
                <table class="table table-striped mb-5">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stocks as $stock): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stock['id']); ?></td>
                            <td><?php echo htmlspecialchars($stock['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($stock['quantity']); ?></td>
                            <td>
                                <a href="edit_stock.php?id=<?php echo $stock['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="del_stock.php?id=<?php echo $stock['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
