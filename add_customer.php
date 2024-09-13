<?php include 'connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Customer</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2>Add New Customer</h2>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $customer_name = $_POST['customer_name'];
            $shop_name = $_POST['shop_name'];
            $address = $_POST['address'];
            $city = $_POST['city'];
            $phone = $_POST['phone'];
            $category = $_POST['category'];

            $stmt = $conn->prepare("INSERT INTO customers (customer_name, shop_name, address, city, phone, category) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $customer_name, $shop_name, $address, $city, $phone, $category);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Customer added successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }

            $stmt->close();
        }
        ?>

        <form action="add_customer.php" method="POST">
            <div class="mb-3">
                <label for="customer_name" class="form-label">Customer Name</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
            </div>
            <div class="mb-3">
                <label for="shop_name" class="form-label">Shop Name</label>
                <input type="text" class="form-control" id="shop_name" name="shop_name">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="city" class="form-label">City</label>
                <input type="text" class="form-control" id="city" name="city">
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone">
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="motorbike">Motorbike</option>
                    <option value="rikshaw">Rikshaw</option>
                    <option value="truck">Truck</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Customer</button>
        </form>
        <button type="button" class="btn btn-secondary" onclick="window.location.href='view_customers.php'">Back</button>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
