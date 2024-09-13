<?php
include 'connect.php';

$product_id = $_GET['id'] ?? null;
$product_data = null;
$showModal = false; // Control modal display

if ($product_id) {
    $result = $conn->query("SELECT * FROM products WHERE id = '$product_id'");
    $product_data = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    if ($product_id) {
        // Handle file upload
        $image = $_FILES['image']['name'];
        $target = "images/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);

        $update_sql = "UPDATE products SET name = '$name', description = '$description', price = '$price', image = '$image' WHERE id = '$product_id'";
        if ($conn->query($update_sql) === TRUE) {
            $showModal = true; // Set to true when the product is successfully updated
        } else {
            echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2>Edit Product</h2>
        <?php if ($product_data): ?>
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product_data['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($product_data['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($product_data['price']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Image</label>
                    <input type="file" class="form-control" id="image" name="image">
                    <img src="images/<?php echo htmlspecialchars($product_data['image']); ?>" alt="<?php echo htmlspecialchars($product_data['name']); ?>" class="mt-2" style="max-width: 100px;">
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        <?php else: ?>
            <p>Product not found.</p>
        <?php endif; ?>
        <a href="view_products.php" class="btn btn-secondary mt-3">Back</a>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                </div>
                <div class="modal-body">
                    Product data updated successfully.
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="window.location.href='view_products.php'">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Show modal if update was successful -->
    <?php if ($showModal) : ?>
    <script>
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    </script>
    <?php endif; ?>

    <?php include 'footer.php'; ?>
</body>
</html>
