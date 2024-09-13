<?php include 'connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Customer</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2>Edit Customer</h2>

        <?php
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $conn->query("SELECT * FROM customers WHERE id = $id");
            $customer = $result->fetch_assoc();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $customer_name = $_POST['customer_name'];
                $shop_name = $_POST['shop_name'];
                $address = $_POST['address'];
                $city = $_POST['city'];
                $phone = $_POST['phone'];
                $category = $_POST['category'];

                $stmt = $conn->prepare("UPDATE customers SET customer_name = ?, shop_name = ?, address = ?, city = ?, phone = ?, category = ? WHERE id = ?");
                $stmt->bind_param("ssssssi", $customer_name, $shop_name, $address, $city, $phone, $category, $id);

                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>Customer updated successfully!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                }

                $stmt->close();
            }
        } else {
            echo "<p>No customer selected.</p>";
        }
        ?>

        <form action="edit_customers.php?id=<?php echo $id; ?>" method="POST">
            <div class="mb-3">
                <label for="customer_name" class="form-label">Customer Name</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($customer['customer_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="shop_name" class="form-label">Shop Name</label>
                <input type="text" class="form-control" id="shop_name" name="shop_name" value="<?php echo htmlspecialchars($customer['shop_name']); ?>">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($customer['address']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="city" class="form-label">City</label>
                <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($customer['city']); ?>">
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>">
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="motorbike" <?php echo $customer['category'] == 'motorbike' ? 'selected' : ''; ?>>Motorbike</option>
                    <option value="rikshaw" <?php echo $customer['category'] == 'rikshaw' ? 'selected' : ''; ?>>Rikshaw</option>
                    <option value="truck" <?php echo $customer['category'] == 'truck' ? 'selected' : ''; ?>>Truck</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Customer</button>
        </form>
        <button type="button" class="btn btn-secondary" onclick="window.location.href='view_customers.php'">Back</button>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
