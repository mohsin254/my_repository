<?php
include 'connect.php';

$employee_id = $_GET['id'] ?? null;
$employee_data = null;
$showModal = false; // Control modal display

if ($employee_id) {
    $result = $conn->query("SELECT * FROM employees WHERE id = '$employee_id'");
    $employee_data = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $monthly_salary = $_POST['monthly_salary'];
    $cnic = $_POST['cnic'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    if ($employee_id) {
        $per_hour_salary = $monthly_salary / 240; // Recalculate per-hour salary

        $update_sql = "UPDATE employees SET name = '$name', monthly_salary = '$monthly_salary', per_hour_salary = '$per_hour_salary', cnic = '$cnic', phone = '$phone', address = '$address' WHERE id = '$employee_id'";
        if ($conn->query($update_sql) === TRUE) {
            $showModal = true; // Set to true when the employee is successfully updated
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
    <title>Edit Employee</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2>Edit Employee</h2>
        <?php if ($employee_data): ?>
            <form method="post">
                <div class="mb-3">
                    <label for="name" class="form-label">Employee Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($employee_data['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="monthly_salary" class="form-label">Monthly Salary</label>
                    <input type="number" step="500" class="form-control" id="monthly_salary" name="monthly_salary" value="<?php echo htmlspecialchars($employee_data['monthly_salary']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="cnic" class="form-label">CNIC</label>
                    <input type="text" class="form-control" id="cnic" name="cnic" value="<?php echo htmlspecialchars($employee_data['cnic']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($employee_data['phone']); ?>">
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($employee_data['address']); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        <?php else: ?>
            <p>Employee not found.</p>
        <?php endif; ?>
        <a href="calculate_salary.php" class="btn btn-secondary mt-3">Back</a>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                </div>
                <div class="modal-body">
                    Employee data updated successfully.
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="window.location.href='calculate_salary.php'">OK</button>
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
