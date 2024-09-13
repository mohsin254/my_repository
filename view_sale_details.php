<?php
include 'connect.php';

// Get sale ID from query string
$sale_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch sale details including address and city
$sale_sql = "SELECT s.id, s.shop_name, s.date, s.total_amount, c.address, c.city
             FROM sales s
             JOIN customers c ON s.shop_name = c.shop_name
             WHERE s.id = ?";
$sale_stmt = $conn->prepare($sale_sql);
$sale_stmt->bind_param('i', $sale_id);
$sale_stmt->execute();
$sale_result = $sale_stmt->get_result();
$sale = $sale_result->fetch_assoc();

// Fetch sale products
$details_sql = "SELECT p.name AS product_name, sd.per_piece_price, sd.quantity, sd.amount
                FROM sale_details sd
                JOIN products p ON sd.product_id = p.id
                WHERE sd.sale_id = ?";
$details_stmt = $conn->prepare($details_sql);
$details_stmt->bind_param('i', $sale_id);
$details_stmt->execute();
$details_result = $details_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sale Details</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <style>
        .print-button, .share-button, .capture-button {
            margin: 5px;
        }

        @media print {
            body {
                font-family: Arial, sans-serif;
            }

            h2 {
                margin-bottom: 20px;
            }

            p {
                font-size: 18px;
                margin: 10px 0;
            }

            button, footer, .nav, header {
                display: none; /* Hide non-essential elements */
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'header.php'; // Include the navigation ?>

    <div class="container mt-5" id="printableArea">
        <h2>Sale Details</h2>
        <?php if ($sale): ?>
            <?php
            // Convert the date format from mm-dd-yyyy to dd-mm-yyyy
            $date = DateTime::createFromFormat('Y-m-d', $sale['date']);
            $formatted_date = $date->format('d-m-Y'); // Convert to dd-mm-yyyy
            ?>
            <div class="mb-4">
                <h3>Sale ID: <?php echo htmlspecialchars($sale['id']); ?></h3>
                <p><strong>Shop Name:</strong> <?php echo htmlspecialchars($sale['shop_name']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($sale['address']); ?></p>
                <p><strong>City:</strong> <?php echo htmlspecialchars($sale['city']); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($formatted_date); ?></p>
                <p><strong>Total Amount:</strong> Rs <?php echo number_format($sale['total_amount'], 2); ?></p>
            </div>

            <h4>Products</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Per Piece Price</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($details_result->num_rows > 0): ?>
                        <?php $total_amount = 0; ?>
                        <?php while ($row = $details_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                <td>Rs <?php echo number_format($row['per_piece_price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td>Rs <?php echo number_format($row['amount'], 2); ?></td>
                            </tr>
                            <?php $total_amount += $row['amount']; ?>
                        <?php endwhile; ?>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total Bill:</strong></td>
                            <td><strong>Rs <?php echo number_format($total_amount, 2); ?></strong></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No details found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php else: ?>
            <p>Sale not found.</p>
        <?php endif; ?>

        <div class="mb-4">
            <button type="button" class="btn btn-secondary back-button" onclick="window.location.href='view_sales.php'">Back</button>
            <button onclick="printSaleDetails()" class="btn btn-primary print-button">Print Bill</button>
            <button onclick="shareOnWhatsApp()" class="btn btn-success share-button">Share on WhatsApp</button>
            <button onclick="captureSaleDetails()" class="btn btn-info capture-button">Capture Sale Details</button>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function printSaleDetails() {
        const saleID = "<?php echo $sale['id']; ?>";
        const shopName = "<?php echo htmlspecialchars($sale['shop_name']); ?>";
        const address = "<?php echo htmlspecialchars($sale['address']); ?>";
        const city = "<?php echo htmlspecialchars($sale['city']); ?>";
        const date = "<?php echo $formatted_date; ?>";
        const totalAmount = "Rs <?php echo number_format($sale['total_amount'], 2); ?>";

        const products = <?php
            $products = [];
            $details_result->data_seek(0); // Reset pointer to the beginning
            while ($row = $details_result->fetch_assoc()) {
                $products[] = [
                    'name' => $row['product_name'],
                    'price' => number_format($row['per_piece_price'], 2),
                    'quantity' => $row['quantity'],
                    'amount' => number_format($row['amount'], 2)
                ];
            }
            echo json_encode($products);
        ?>;

        let productsDetails = `
            <table border="1" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Per Piece Price</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    ${products.map(p => `
                        <tr>
                            <td>${p.name}</td>
                            <td>Rs ${p.price}</td>
                            <td>${p.quantity}</td>
                            <td>Rs ${p.amount}</td>
                        </tr>
                    `).join('')}
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Total Bill:</strong></td>
                        <td><strong>Rs ${totalAmount}</strong></td>
                    </tr>
                </tbody>
            </table>
        `;

        const summaryContent = `
            <div>
                <h2>Ittefaq Engineering Works</h2>
                <h4>Sale ID: ${saleID}</h4>
                <p>Shop Name: <strong>${shopName}</strong></p>
                <p>Address: <strong>${address}, ${city}</strong></p>
                <p>Date: <strong>${date}</strong></p>
                <p>Total Amount: <strong>${totalAmount}</strong></p>
                <h4>Products</h4>
                ${productsDetails}
            </div>
        `;

        // Store original content
        const originalContent = document.body.innerHTML;

        // Replace with summary content
        document.body.innerHTML = summaryContent;

        // Print
        window.print();

        // Restore original content
        document.body.innerHTML = originalContent;

        // Reload page
        location.reload();
    }

    function shareOnWhatsApp() {
        const saleID = "<?php echo $sale['id']; ?>";
        const shopName = "<?php echo htmlspecialchars($sale['shop_name']); ?>";
        const address = "<?php echo htmlspecialchars($sale['address']); ?>";
        const city = "<?php echo htmlspecialchars($sale['city']); ?>";
        const date = "<?php echo $formatted_date; ?>";
        const totalAmount = "Rs <?php echo number_format($sale['total_amount'], 2); ?>";

        const products = <?php
            $products = [];
            $details_result->data_seek(0); // Reset pointer to the beginning
            while ($row = $details_result->fetch_assoc()) {
                $products[] = [
                    'name' => $row['product_name'],
                    'price' => number_format($row['per_piece_price'], 2),
                    'quantity' => $row['quantity'],
                    'amount' => number_format($row['amount'], 2)
                ];
            }
            echo json_encode($products);
        ?>;

        let productsDetails = products.map(p => `
            Product Name: ${p.name}, Per Piece Price: Rs ${p.price}, Quantity: ${p.quantity}, Amount: Rs ${p.amount}
        `).join('%0A');

        let message = `
            Sale ID: ${saleID}%0A
            Shop Name: ${shopName}%0A
            Address: ${address}, ${city}%0A
            Date: ${date}%0A
            Total Amount: ${totalAmount}%0A
            Products:%0A${productsDetails}
        `.trim();

        let whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
        window.open(whatsappUrl, '_blank');
    }

    function captureSaleDetails() {
        // Hide buttons before capturing
        document.querySelectorAll('.btn').forEach(btn => btn.style.display = 'none');
        html2canvas(document.getElementById('printableArea')).then(function(canvas) {
            let date = "<?php echo date('Ymd'); ?>"; // Use current date as suffix
            let shopName = "<?php echo preg_replace('/[^a-zA-Z0-9]/', '', htmlspecialchars($sale['shop_name'])); ?>"; // Clean up shop name
            let filename = `${shopName}_${date}.png`;

            // Save the image
            let link = document.createElement('a');
            link.href = canvas.toDataURL();
            link.download = filename;
            link.click();

            // Show buttons again
            document.querySelectorAll('.btn').forEach(btn => btn.style.display = '');
        });
    }
    </script>
</body>
</html>
