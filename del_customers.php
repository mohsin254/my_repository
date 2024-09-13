<?php include 'connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Customer</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2>Delete Customer</h2>

        <?php
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            
            $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Customer deleted successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }

            $stmt->close();
        } else {
            echo "<p>No customer selected.</p>";
        }
        ?>

        <a href="view_customers.php" class="btn btn-secondary">Back to Customers List</a>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>