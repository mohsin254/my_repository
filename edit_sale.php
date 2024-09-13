<?php
include 'connect.php';

$sale_id = $_GET['id'] ?? null;
$sale_data = null;
$sale_details = [];

if ($sale_id) {
    // Fetch sale details
    $sale_result = $conn->query("SELECT * FROM sales WHERE id = '$sale_id'");
    $sale_data = $sale_result->fetch_assoc();
    
    // Fetch sale detail records
    $details_result = $conn->query("SELECT * FROM sale_details WHERE sale_id = '$sale_id'");
    while ($row = $details_result->fetch_assoc()) {
        $sale_details[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shop_name = $_POST['shop_name'];
    $product_ids = $_POST['product_id'];
    $per_piece_prices = $_POST['per_piece_price'];
    $quantities = $_POST['quantity'];
    $date = $_POST['date'];
    
    $total_amount = 0;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update sale record
        $update_sale_sql = "UPDATE sales SET shop_name = '$shop_name', date = '$date', total_amount = 0 WHERE id = '$sale_id'";
        $conn->query($update_sale_sql);
        
        // Delete existing sale details
        $conn->query("DELETE FROM sale_details WHERE sale_id = '$sale_id'");
        
        // Insert updated sale details
        for ($i = 0; $i < count($product_ids); $i++) {
            $product_id = $product_ids[$i];
            $per_piece_price = $per_piece_prices[$i];
            $quantity = $quantities[$i];
            $amount = $per_piece_price * $quantity;
            $total_amount += $amount;

            $insert_sale_detail_sql = "INSERT INTO sale_details (sale_id, product_id, per_piece_price, quantity, amount) VALUES ('$sale_id', '$product_id', '$per_piece_price', '$quantity', '$amount')";
            $conn->query($insert_sale_detail_sql);
        }
        
        // Update total amount in sales
        $update_total_amount_sql = "UPDATE sales SET total_amount = '$total_amount' WHERE id = '$sale_id'";
        $conn->query($update_total_amount_sql);

        // Commit transaction
        $conn->commit();
        
        echo "<p>Sale updated successfully!</p>";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Sale</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2>Edit Sale</h2>
        <?php if ($sale_data): ?>
            <form method="post">
                <div class="mb-3">
                    <label for="shop_name" class="form-label">Shop Name</label>
                    <input type="text" class="form-control" id="shop_name" name="shop_name" value="<?php echo htmlspecialchars($sale_data['shop_name']); ?>" required>
                </div>
                <div id="products-container">
                    <?php foreach ($sale_details as $detail): ?>
                        <div class="product-entry mb-3">
                            <label for="product_id[]" class="form-label">Product</label>
                            <select name="product_id[]" class="form-select" required>
                                <option value="">Select a product</option>
                                <?php
                                // Fetch products for the dropdown
                                $product_sql = "SELECT id, name FROM products";
                                $product_result = $conn->query($product_sql);
                                while ($row = $product_result->fetch_assoc()):
                                    $selected = ($row['id'] == $detail['product_id']) ? 'selected' : '';
                                    echo '<option value="' . $row['id'] . '" ' . $selected . '>' . $row['name'] . '</option>';
                                endwhile;
                                ?>
                            </select>
                            <label for="per_piece_price[]" class="form-label">Per Piece Price</label>
                            <input type="number" step="0.01" class="form-control" name="per_piece_price[]" value="<?php echo htmlspecialchars($detail['per_piece_price']); ?>" required>
                            <label for="quantity[]" class="form-label">Quantity</label>
                            <input type="number" class="form-control" name="quantity[]" value="<?php echo htmlspecialchars($detail['quantity']); ?>" required>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-secondary mb-3" id="add-product">Add Another Product</button>
                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($sale_data['date']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        <?php else: ?>
            <p>Sale not found.</p>
        <?php endif; ?>
        <a href="view_sales.php" class="btn btn-secondary mt-3">Back</a>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('add-product').addEventListener('click', function() {
            var container = document.getElementById('products-container');
            var newEntry = document.createElement('div');
            newEntry.classList.add('product-entry', 'mb-3');
            newEntry.innerHTML = `
                <label for="product_id[]" class="form-label">Product</label>
                <select name="product_id[]" class="form-select" required>
                    <option value="">Select a product</option>
                    <?php
                    // Fetch products for the dropdown
                    $product_sql = "SELECT id, name FROM products";
                    $product_result = $conn->query($product_sql);
                    while ($row = $product_result->fetch_assoc()) {
                        echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                    }
                    ?>
                </select>
                <label for="per_piece_price[]" class="form-label">Per Piece Price</label>
                <input type="number" step="0.01" class="form-control" name="per_piece_price[]" required>
                <label for="quantity[]" class="form-label">Quantity</label>
                <input type="number" class="form-control" name="quantity[]" required>
            `;
            container.appendChild(newEntry);
        });
    </script>
</body>
</html>
