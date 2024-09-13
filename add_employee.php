<?php 
include 'connect.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; // Include the navigation ?>

    <!-- Main Panel -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="text-center mb-4">Add New Employee</h1>

                <?php
                $showModal = false; // This will control whether the modal is shown

                if (isset($_POST['add_employee'])) {
                    $name = $_POST['name'];
                    $monthly_salary = $_POST['monthly_salary'];
                    $cnic = $_POST['cnic'];
                    $phone = $_POST['phone'];
                    $address = $_POST['address'];

                    // Calculate per-hour salary
                    $per_hour_salary = $monthly_salary / 240;

                    // Ensure connection is established
                    if ($conn) {
                        $sql = "INSERT INTO employees (name, monthly_salary, per_hour_salary, cnic, phone, address)
                                VALUES ('$name', '$monthly_salary', '$per_hour_salary', '$cnic', '$phone', '$address')";
                        if ($conn->query($sql) === TRUE) {
                            $showModal = true; // Set to true when the employee is successfully added
                        } else {
                            echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Database connection failed.</div>";
                    }
                }
                ?>

                <!-- Form for adding employee -->
                <form method="post" class="p-4 shadow bg-light rounded">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name:</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="monthly_salary" class="form-label">Monthly Salary:</label>
                        <input type="number" id="monthly_salary" name="monthly_salary" class="form-control" step="500" required>
                    </div>
                    <div class="mb-3">
                        <label for="cnic" class="form-label">CNIC:</label>
                        <input type="text" id="cnic" name="cnic" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number:</label>
                        <input type="text" id="phone" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address:</label>
                        <input type="text" id="address" name="address" class="form-control">
                    </div>
                    <button type="submit" name="add_employee" class="btn btn-success">Add Employee</button>
                </form>
            </div>
        </div>
        <button type="button" class="btn btn-secondary" onclick="window.location.href='calculate_salary.php'">Back</button>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                </div>
                <div class="modal-body">
                    Employee Data added successfully.
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="window.location.href='add_employee.php'">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and necessary dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php if ($showModal) : ?>
    <script>
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    </script>
    <?php endif; ?>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

</body>
</html>
