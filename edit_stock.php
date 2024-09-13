<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $category = $_POST['category'];
    $sub_category = $_POST['sub_category'];

    // Get product ID
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

    // Update stock entry
    $update_stock_sql = "UPDATE stock SET product_id = ?, quantity = ? WHERE id = ?";
    $update_stock_stmt = $conn->prepare($update_stock_sql);
    $update_stock_stmt->bind_param('iii', $product_id, $quantity, $id);
    $update_stock_stmt->execute();

    header("Location: view_stock.php");
    exit;
}

// Fetch stock details for editing
$id = $_GET['id'];
$stock_sql = "SELECT s.id, si.product_name, si.category, si.sub_category, s.quantity
              FROM stock s
              JOIN stock_items si ON s.product_id = si.id
              WHERE s.id = ?";
$stock_stmt = $conn->prepare($stock_sql);
$stock_stmt->bind_param('i', $id);
$stock_stmt->execute();
$stock_result = $stock_stmt->get_result();
$stock = $stock_result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2>Edit Stock</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($stock['id']); ?>">
            <div class="mb-3">
                <label for="product_name" class="form-label">Product</label>
                <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($stock['product_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo htmlspecialchars($stock['quantity']); ?>" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="Packing" <?php echo $stock['category'] == 'Packing' ? 'selected' : ''; ?>>Packing</option>
                    <option value="Products" <?php echo $stock['category'] == 'Products' ? 'selected' : ''; ?>>Products</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="sub_category" class="form-label">Sub Category</label>
                <select class="form-select" id="sub_category" name="sub_category" required>
                    <option value="Rickshaw" <?php echo $stock['sub_category'] == 'Rickshaw' ? 'selected' : ''; ?>>Rickshaw</option>
                    <option value="Bike" <?php echo $stock['sub_category'] == 'Bike' ? 'selected' : ''; ?>>Bike</option>
                    <option value="Truck" <?php echo $stock['sub_category'] == 'Truck' ? 'selected' : ''; ?>>Truck</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Stock</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
