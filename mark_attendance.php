<?php include 'connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mark Attendance</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'header.php'; // Include the navigation ?>

    <!-- Main Content -->
    <div class="container my-4">
        <h1>Mark Daily Attendance</h1>
        <button type="button" class="btn btn-secondary mt-3" onclick="window.location.href='calculate_salary.php'">Back</button>
        
        <?php
        $message_type = '';
        $message_content = '';
        
        if (isset($_POST['mark_attendance'])) {
            $employee_id = $_POST['employee_id'];
            $date = $_POST['date'];
            $hours_worked = $_POST['hours_worked'];
            $overtime_hours = $_POST['overtime_hours'];
            $money_taken = $_POST['money_taken'];
            $is_holiday = isset($_POST['is_holiday']) ? 1 : 0;

            // Adjust hours_worked for holidays
            if ($hours_worked == 'Holiday') {
                $hours_worked = 8;
            } elseif ($hours_worked == 'Absent') {
                $hours_worked = 0;
            }

            $sql = "INSERT INTO attendance (employee_id, date, hours_worked, overtime_hours, money_taken, is_holiday)
                    VALUES ('$employee_id', '$date', '$hours_worked', '$overtime_hours', '$money_taken', '$is_holiday')";
            if ($conn->query($sql) === TRUE) {
                $message_type = 'success';
                $message_content = 'Attendance marked successfully';
            } else {
                $message_type = 'danger';
                $message_content = 'Error: ' . $conn->error;
            }
        }

        $employees = $conn->query("SELECT * FROM employees");
        ?>

        <form method="post" class="mt-3">
            <div class="mb-3">
                <label for="employee_id" class="form-label">Employee:</label>
                <select name="employee_id" id="employee_id" class="form-select" required>
                    <?php while ($row = $employees->fetch_assoc()) { ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date:</label>
                <input type="date" name="date" id="date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="hours_worked" class="form-label">Hours Worked:</label>
                <select name="hours_worked" id="hours_worked" class="form-select" required>
                    <option value="Absent">Absent</option>
                    <option value="Holiday">Holiday</option>
                    <?php for ($i = 0; $i <= 8; $i += 0.5) { ?>
                        <option value="<?php echo $i; ?>"><?php echo $i . " hours"; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="overtime_hours" class="form-label">Overtime Hours:</label>
                <input type="number" step="0.5" name="overtime_hours" id="overtime_hours" class="form-control" value="0">
            </div>
            <div class="mb-3">
                <label for="money_taken" class="form-label">Money Taken:</label>
                <input type="number" step="50" name="money_taken" id="money_taken" class="form-control" value="0">
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_holiday" id="is_holiday" class="form-check-input">
                    <label for="is_holiday" class="form-check-label">Official Holiday</label>
                </div>
            </div>
            <button type="submit" name="mark_attendance" class="btn btn-primary">Mark Attendance</button>
        </form>
    </div>

    <!-- Success/Error Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel"><?php echo $message_type == 'success' ? 'Success' : 'Error'; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo $message_content; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show modal on page load if there's a message to display
        document.addEventListener('DOMContentLoaded', function () {
            <?php if ($message_type) { ?>
                var messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
                messageModal.show();
            <?php } ?>
        });
    </script>
</body>
</html>
