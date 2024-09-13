<?php
include 'connect.php'; // Includes the MySQLi connection from connect.php

// Fetch all products from the database
$sql = "SELECT id, name, description, price, image FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <?php include 'header.php'; // Include the navigation ?>

    <div class="container my-4">
        <h1 class="mb-4">Product Operations</h1>
        
        <div class="mb-4">
            <a href="add_products.php" class="btn btn-success">Add New Product</a>
        </div>

        <h2>Product List</h2>
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                // Loop through all the products and display them in Bootstrap cards
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-4 mb-3">';
                    echo '<div class="card">';
                    echo '<img src="images/' . $row['image'] . '" class="card-img-top" alt="' . $row['name'] . '">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . $row['name'] . '</h5>';
                    echo '<p class="card-text">' . $row['description'] . '</p>';
                    echo '<p class="card-text"><strong>Price: </strong> Rs ' . $row['price'] . '</p>';
                    echo '<a href="edit_products.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">Edit</a>';
                    echo '<a href="del_products.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this product?\');">Delete</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No products found.</p>';
            }
            ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>

<?php
$conn->close(); // Close the database connection
?>
