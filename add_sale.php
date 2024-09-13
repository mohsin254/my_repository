<?php
include 'connect.php';

// Fetch products for the dropdown
$product_sql = "SELECT id, name FROM products";
$product_result = $conn->query($product_sql);

// Fetch shop names from customers table
$shop_sql = "SELECT DISTINCT shop_name FROM customers";
$shop_result = $conn->query($shop_sql);

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
        // Insert sale record
        $insert_sale_sql = "INSERT INTO sales (shop_name, date, total_amount) VALUES ('$shop_name', '$date', 0)";
        $conn->query($insert_sale_sql);
        $sale_id = $conn->insert_id;
        
        // Insert sale details
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
        
        echo "<p>Sale added successfully!</p>";
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
    <title>Add Sale</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2>Add Sale</h2>
        <form method="post">
            <div class="mb-3">
                <label for="shop_name" class="form-label">Shop Name</label>
                <select name="shop_name" id="shop_name" class="form-select" required>
                    <option value="">Select a shop</option>
                    <?php while ($row = $shop_result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($row['shop_name']); ?>"><?php echo htmlspecialchars($row['shop_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div id="products-container">
                <div class="product-entry mb-3">
                    <label for="product_id[]" class="form-label">Product</label>
                    <select name="product_id[]" class="form-select" required>
                        <option value="">Select a product</option>
                        <?php while ($row = $product_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                    <label for="per_piece_price[]" class="form-label">Per Piece Price</label>
                    <input type="number" step="1" class="form-control" name="per_piece_price[]" required>
                    <label for="quantity[]" class="form-label">Quantity</label>
                    <input type="number" class="form-control" name="quantity[]" required>
                </div>
            </div>
            <button type="button" class="btn btn-secondary mb-3" id="add-product">Add Another Product</button>
            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Sale</button>
        </form>
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
