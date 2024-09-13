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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update purchase details
    $shop_name = $_POST['shop_name'];
    $date = $_POST['date'];
    $total_amount = 0;

    // Update purchase data
    $update_purchase_sql = "UPDATE purchase SET shop_name = ?, date = ? WHERE id = ?";
    $update_purchase_stmt = $conn->prepare($update_purchase_sql);
    $update_purchase_stmt->bind_param('ssi', $shop_name, $date, $purchase_id);
    $update_purchase_stmt->execute();

    // Loop through products to update or add new ones
    foreach ($_POST['products'] as $product) {
        $product_name = $product['product_name'];
        $product_id = $product['product_id'];

        // Check if product exists in purchasing_items, if not, add it
        $check_product_sql = "SELECT id FROM purchasing_items WHERE product_name = ?";
        $check_product_stmt = $conn->prepare($check_product_sql);
        $check_product_stmt->bind_param('s', $product_name);
        $check_product_stmt->execute();
        $product_result = $check_product_stmt->get_result();

        if ($product_result->num_rows == 0) {
            $add_product_sql = "INSERT INTO purchasing_items (product_name) VALUES (?)";
            $add_product_stmt = $conn->prepare($add_product_sql);
            $add_product_stmt->bind_param('s', $product_name);
            $add_product_stmt->execute();
            $product_id = $conn->insert_id;
        } else {
            $product = $product_result->fetch_assoc();
            $product_id = $product['id'];
        }

        // Update purchase details
        if ($product['type'] == 'quantity') {
            $quantity = $product['quantity'];
            $per_piece_price = $product['per_piece_price'];
            $amount = $quantity * $per_piece_price;
            $update_details_sql = "UPDATE purchase_details SET quantity = ?, per_piece_price = ?, amount = ? WHERE id = ?";
            $details_stmt = $conn->prepare($update_details_sql);
            $details_stmt->bind_param('dddi', $quantity, $per_piece_price, $amount, $product['detail_id']);
        } else {
            $weight = $product['weight'];
            $per_kg_price = $product['per_kg_price'];
            $amount = $weight * $per_kg_price;
            $update_details_sql = "UPDATE purchase_details SET weight = ?, per_kg_price = ?, amount = ? WHERE id = ?";
            $details_stmt = $conn->prepare($update_details_sql);
            $details_stmt->bind_param('dddi', $weight, $per_kg_price, $amount, $product['detail_id']);
        }
        $details_stmt->execute();
        $total_amount += $amount;
    }

    // Update total amount
    $update_total_sql = "UPDATE purchase SET total_amount = ? WHERE id = ?";
    $update_total_stmt = $conn->prepare($update_total_sql);
    $update_total_stmt->bind_param('di', $total_amount, $purchase_id);
    $update_total_stmt->execute();

    header("Location: view_purchase.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Purchase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'header.php'; // Include the navigation ?>

<div class="container mt-5">
    <h2>Edit Purchase</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="shop_name" class="form-label">Shop Name</label>
            <input type="text" class="form-control" id="shop_name" name="shop_name" value="<?php echo $purchase['shop_name']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" id="date" name="date" value="<?php echo $purchase['date']; ?>" required>
        </div>

        <?php while ($detail = $details_result->fetch_assoc()) { ?>
            <div class="product-item mb-4">
                <input type="hidden" name="products[<?php echo $detail['id']; ?>][detail_id]" value="<?php echo $detail['id']; ?>">
                <label class="form-label">Product</label>
                <input type="text" class="form-control" name="products[<?php echo $detail['id']; ?>][product_name]" value="<?php echo $detail['product_name']; ?>" required>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="products[<?php echo $detail['id']; ?>][type]" value="quantity" <?php if ($detail['quantity']) echo 'checked'; ?> onclick="showQuantityFields(<?php echo $detail['id']; ?>)" required>
                    <label class="form-check-label">By Quantity</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="products[<?php echo $detail['id']; ?>][type]" value="weight" <?php if ($detail['weight']) echo 'checked'; ?> onclick="showWeightFields(<?php echo $detail['id']; ?>)" required>
                    <label class="form-check-label">By Weight</label>
                </div>

                <div class="quantity-fields" id="quantity-fields-<?php echo $detail['id']; ?>" style="display: <?php if ($detail['quantity']) echo 'block'; else echo 'none'; ?>">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" name="products[<?php echo $detail['id']; ?>][quantity]" step="0.01" value="<?php echo $detail['quantity']; ?>">
                    <label class="form-label">Per Piece Price</label>
                    <input type="number" class="form-control" name="products[<?php echo $detail['id']; ?>][per_piece_price]" step="0.01" value="<?php echo $detail['per_piece_price']; ?>">
                </div>

                <div class="weight-fields" id="weight-fields-<?php echo $detail['id']; ?>" style="display: <?php if ($detail['weight']) echo 'block'; else echo 'none'; ?>">
                    <label class="form-label">Weight</label>
                    <input type="number" class="form-control" name="products[<?php echo $detail['id']; ?>][weight]" step="0.01" value="<?php echo $detail['weight']; ?>">
                    <label class="form-label">Per Kg Price</label>
                    <input type="number" class="form-control" name="products[<?php echo $detail['id']; ?>][per_kg_price]" step="0.01" value="<?php echo $detail['per_kg_price']; ?>">
                </div>
            </div>
        <?php } ?>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<script>
function showQuantityFields(id) {
    document.getElementById(`quantity-fields-${id}`).style.display = 'block';
    document.getElementById(`weight-fields-${id}`).style.display = 'none';
}

function showWeightFields(id) {
    document.getElementById(`quantity-fields-${id}`).style.display = 'none';
    document.getElementById(`weight-fields-${id}`).style.display = 'block';
}
</script>
</body>
</html>
