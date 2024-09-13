<?php
include 'connect.php'; // Includes the MySQLi connection from connect.php

$success = false; // Initialize success flag

// Your form submission logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    
    // File upload logic
    $image = $_FILES['image']['name'];
    $target = "images/" . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        // Prepare and bind the MySQLi statement
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $name, $description, $price, $image);

        // Execute the statement
        if ($stmt->execute()) {
            $success = true; // Set success flag to true when product is added successfully
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Failed to upload image!";
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <title>Add Product - IEW</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <?php include 'header.php'; // Include the navigation ?>

    <div class="container mt-5">
        <h2>Add New Product</h2>

        <form action="add_products.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Product Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($description ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Product Price ($)</label>
                <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" value="<?= htmlspecialchars($price ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Product Image (JPG, JPEG, PNG, GIF)</label>
                <input class="form-control" type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.gif" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="successModalLabel">Product Added</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            The product has been added successfully!
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <?php if ($success): ?>
        <script>
            // Show the success modal when product is added successfully
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        </script>
    <?php endif; ?>

    <?php include 'footer.php'; ?>

</body>

</html>
