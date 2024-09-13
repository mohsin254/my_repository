<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $category = $_POST['category'];
    $sub_category = $_POST['sub_category'];

    // Check if product exists
    $product_sql = "SELECT id FROM stock_items WHERE product_name = ? AND category = ? AND sub_category = ?";
    $product_stmt = $conn->prepare($product_sql);
    $product_stmt->bind_param('sss', $product_name, $category, $sub_category);
    $product_stmt->execute();
    $product_result = $product_stmt->get_result();

    if ($product_result->num_rows == 0) {
        // Add new product if not exists
        $add_product_sql = "INSERT INTO stock_items (product_name, category, sub_category) VALUES (?, ?, ?)";
        $add_product_stmt = $conn->prepare($add_product_sql);
        $add_product_stmt->bind_param('sss', $product_name, $category, $sub_category);
        $add_product_stmt->execute();
        $product_id = $conn->insert_id;
    } else {
        $product_data = $product_result->fetch_assoc();
        $product_id = $product_data['id'];
    }

    // Insert stock entry
    $stock_sql = "INSERT INTO stock (product_id, quantity) VALUES (?, ?)";
    $stock_stmt = $conn->prepare($stock_sql);
    $stock_stmt->bind_param('ii', $product_id, $quantity);
    $stock_stmt->execute();

    header("Location: view_stock.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2>Add Stock</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="product_name" class="form-label">Product</label>
                <input type="text" class="form-control" id="product_name" name="product_name" required>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="Packing">Packing</option>
                    <option value="Products">Products</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="sub_category" class="form-label">Sub Category</label>
                <select class="form-select" id="sub_category" name="sub_category" required>
                    <option value="Rickshaw">Rickshaw</option>
                    <option value="Bike">Bike</option>
                    <option value="Truck">Truck</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Stock</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
