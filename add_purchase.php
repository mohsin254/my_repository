<?php
include 'connect.php';

// Fetch existing products from the database
$products_sql = "SELECT id, product_name FROM purchasing_items";
$products_result = $conn->query($products_sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shop_name = $_POST['shop_name'];
    $date = $_POST['date'];
    $total_amount = 0;

    // Insert into purchase table
    $purchase_sql = "INSERT INTO purchase (shop_name, date, total_amount) VALUES (?, ?, ?)";
    $purchase_stmt = $conn->prepare($purchase_sql);
    $purchase_stmt->bind_param('ssd', $shop_name, $date, $total_amount);
    $purchase_stmt->execute();
    $purchase_id = $conn->insert_id;

    // Loop through products and insert into purchase_details
    foreach ($_POST['products'] as $product) {
        $product_name = $product['product_name'];
        $product_type = $product['type'];

        // Check if product exists
        $product_sql = "SELECT id FROM purchasing_items WHERE product_name = ?";
        $product_stmt = $conn->prepare($product_sql);
        $product_stmt->bind_param('s', $product_name);
        $product_stmt->execute();
        $product_result = $product_stmt->get_result();
        
        if ($product_result->num_rows == 0) {
            // Add new product if not exists
            $add_product_sql = "INSERT INTO purchasing_items (product_name) VALUES (?)";
            $add_product_stmt = $conn->prepare($add_product_sql);
            $add_product_stmt->bind_param('s', $product_name);
            $add_product_stmt->execute();
            $product_id = $conn->insert_id;
        } else {
            $product_data = $product_result->fetch_assoc();
            $product_id = $product_data['id'];
        }

        // Calculate amount and insert purchase details
        if ($product_type == 'quantity') {
            $quantity = $product['quantity'];
            $per_piece_price = $product['per_piece_price'];
            $amount = $quantity * $per_piece_price;
            $insert_details_sql = "INSERT INTO purchase_details (purchase_id, product_id, quantity, per_piece_price, amount) VALUES (?, ?, ?, ?, ?)";
            $details_stmt = $conn->prepare($insert_details_sql);
            $details_stmt->bind_param('iiddd', $purchase_id, $product_id, $quantity, $per_piece_price, $amount);
        } else {
            $weight = $product['weight'];
            $per_kg_price = $product['per_kg_price'];
            $amount = $weight * $per_kg_price;
            $insert_details_sql = "INSERT INTO purchase_details (purchase_id, product_id, weight, per_kg_price, amount) VALUES (?, ?, ?, ?, ?)";
            $details_stmt = $conn->prepare($insert_details_sql);
            $details_stmt->bind_param('iiddd', $purchase_id, $product_id, $weight, $per_kg_price, $amount);
        }
        $details_stmt->execute();
        $total_amount += $amount;
    }

    // Update total amount in purchase table
    $update_purchase_sql = "UPDATE purchase SET total_amount = ? WHERE id = ?";
    $update_purchase_stmt = $conn->prepare($update_purchase_sql);
    $update_purchase_stmt->bind_param('di', $total_amount, $purchase_id);
    $update_purchase_stmt->execute();

    header("Location: view_purchase.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Purchase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2>Add Purchase</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="shop_name" class="form-label">Shop Name</label>
                <input type="text" class="form-control" id="shop_name" name="shop_name" required>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>

            <div id="product-section">
                <div class="product-item mb-4">
                    <label class="form-label">Product</label>
                    <select class="form-select" name="products[0][product_name]" onchange="handleProductSelection(0)">
                        <option value="">Select Product</option>
                        <?php while ($product = $products_result->fetch_assoc()) { ?>
                            <option value="<?php echo htmlspecialchars($product['product_name']); ?>"><?php echo htmlspecialchars($product['product_name']); ?></option>
                        <?php } ?>
                        <option value="new">Add New Product</option>
                    </select>
                    <input type="text" class="form-control" name="products[0][new_product_name]" placeholder="New Product Name" style="display: none;">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="products[0][type]" value="quantity" onclick="showQuantityFields(0)" required>
                        <label class="form-check-label">By Quantity</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="products[0][type]" value="weight" onclick="showWeightFields(0)" required>
                        <label class="form-check-label">By Weight</label>
                    </div>

                    <!-- Quantity Fields -->
                    <div class="quantity-fields" id="quantity-fields-0" style="display: none;">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="products[0][quantity]" step="0.01">
                        <label class="form-label">Per Piece Price</label>
                        <input type="number" class="form-control" name="products[0][per_piece_price]" step="0.01">
                    </div>

                    <!-- Weight Fields -->
                    <div class="weight-fields" id="weight-fields-0" style="display: none;">
                        <label class="form-label">Weight</label>
                        <input type="number" class="form-control" name="products[0][weight]" step="0.01">
                        <label class="form-label">Per Kg Price</label>
                        <input type="number" class="form-control" name="products[0][per_kg_price]" step="0.01">
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-secondary" onclick="addProduct()">Add Product</button>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script>
let productIndex = 1;

function addProduct() {
    const productSection = document.getElementById('product-section');
    const productItem = `
        <div class="product-item mb-4">
            <label class="form-label">Product</label>
            <select class="form-select" name="products[${productIndex}][product_name]" onchange="handleProductSelection(${productIndex})">
                <option value="">Select Product</option>
                <?php while ($product = $products_result->fetch_assoc()) { ?>
                    <option value="<?php echo htmlspecialchars($product['product_name']); ?>"><?php echo htmlspecialchars($product['product_name']); ?></option>
                <?php } ?>
                <option value="new">Add New Product</option>
            </select>
            <input type="text" class="form-control" name="products[${productIndex}][new_product_name]" placeholder="New Product Name" style="display: none;">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="products[${productIndex}][type]" value="quantity" onclick="showQuantityFields(${productIndex})" required>
                <label class="form-check-label">By Quantity</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="products[${productIndex}][type]" value="weight" onclick="showWeightFields(${productIndex})" required>
                <label class="form-check-label">By Weight</label>
            </div>

            <div class="quantity-fields" id="quantity-fields-${productIndex}" style="display: none;">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control" name="products[${productIndex}][quantity]" step="0.01">
                <label class="form-label">Per Piece Price</label>
                <input type="number" class="form-control" name="products[${productIndex}][per_piece_price]" step="0.01">
            </div>

                        <div class="weight-fields" id="weight-fields-${productIndex}" style="display: none;">
                <label class="form-label">Weight</label>
                <input type="number" class="form-control" name="products[${productIndex}][weight]" step="0.01">
                <label class="form-label">Per Kg Price</label>
                <input type="number" class="form-control" name="products[${productIndex}][per_kg_price]" step="0.01">
            </div>
        </div>`;
    productSection.insertAdjacentHTML('beforeend', productItem);
    productIndex++;
}

function showQuantityFields(index) {
    document.getElementById(`quantity-fields-${index}`).style.display = 'block';
    document.getElementById(`weight-fields-${index}`).style.display = 'none';
}

function showWeightFields(index) {
    document.getElementById(`quantity-fields-${index}`).style.display = 'none';
    document.getElementById(`weight-fields-${index}`).style.display = 'block';
}

function handleProductSelection(index) {
    const productSelect = document.querySelector(`select[name='products[${index}][product_name]']`);
    const newProductInput = document.querySelector(`input[name='products[${index}][new_product_name]']`);
    const selectedValue = productSelect.value;
    
    if (selectedValue === 'new') {
        newProductInput.style.display = 'block';
        productSelect.required = false; // Make the dropdown non-required if adding new product
    } else {
        newProductInput.style.display = 'none';
        productSelect.required = true; // Make the dropdown required if not adding new product
    }
}
</script>

<!-- Footer -->
<?php include 'footer.php'; ?>
</body>
</html>
