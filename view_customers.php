<?php include 'connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Customers</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2>View Customers</h2>

        <?php
        // Fetch customers by category
        $categories = ['motorbike', 'rikshaw', 'truck'];
        
        foreach ($categories as $category) {
            echo "<h3>" . ucfirst($category) . "</h3>";
            $result = $conn->query("SELECT * FROM customers WHERE category = '$category'");

            if ($result->num_rows > 0) {
                echo "<table class='table table-striped'>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer Name</th>
                                <th>Shop Name</th>
                                <th>Address</th>
                                <th>City</th>
                                <th>Phone</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['customer_name']}</td>
                            <td>{$row['shop_name']}</td>
                            <td>{$row['address']}</td>
                            <td>{$row['city']}</td>
                            <td>{$row['phone']}</td>
                            <td>{$row['category']}</td>
                            <td>
                                <a href='edit_customers.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                <a href='del_customers.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this customer?\");'>Delete</a>
                            </td>
                        </tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No customers found in this category.</p>";
            }
        }
        ?>
        
        <a href="add_customer.php" class="btn btn-primary mt-3">Add New Customer</a>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
