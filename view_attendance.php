<?php include 'connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
    
    <!-- Main Content -->
    <div class="container my-4">
        <h1>Employee Attendance Details</h1>
        
        <?php
        if (isset($_GET['employee_id'])) {
            $employee_id = $_GET['employee_id'];
            $selected_month = $_GET['month'] ?? date('Y-m'); // Default to the current month

            // Handle delete attendance request
            if (isset($_POST['delete_attendance'])) {
                $delete_sql = "DELETE FROM attendance WHERE employee_id = '$employee_id'";
                if ($conn->query($delete_sql) === TRUE) {
                    echo "<p class='alert alert-success'>All attendance records for employee ID $employee_id have been deleted.</p>";
                } else {
                    echo "<p class='alert alert-danger'>Error deleting records: " . $conn->error . "</p>";
                }
            }

            // Fetch employee details and per-hour salary
            $employee_result = $conn->query("SELECT name, per_hour_salary FROM employees WHERE id = '$employee_id'");
            $employee = $employee_result->fetch_assoc();
            $employee_name = $employee['name']; // Store employee name for the print summary

            echo "<h2>Attendance for: " . $employee_name . "</h2>";

            // Fetch attendance records for the employee for the selected month
            $attendance_sql = "
                SELECT date, hours_worked, overtime_hours, money_taken
                FROM attendance
                WHERE employee_id = '$employee_id'
                AND DATE_FORMAT(date, '%Y-%m') = '$selected_month'
                ORDER BY date ASC
            ";

            $attendance_result = $conn->query($attendance_sql);

            if ($attendance_result->num_rows > 0) {
                $total_hours_worked = 0;
                $total_overtime_hours = 0;
                $total_money_taken = 0;
                $total_daily_salary = 0;

                echo "<table class='table table-striped'>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Hours Worked</th>
                                <th>Overtime Hours</th>
                                <th>Money Taken</th>
                                <th>Daily Salary</th>
                            </tr>
                        </thead>
                        <tbody>";

                        while ($row = $attendance_result->fetch_assoc()) {
                            $date = DateTime::createFromFormat('Y-m-d', $row['date']);
                            $formatted_date = $date->format('d-m-Y'); // Convert to dd-mm-yyyy
                        
                            // Calculate days and hours for hours worked and overtime
                            $hours_worked = $row['hours_worked'];
                            $days_worked = floor($hours_worked / 8);
                            $remaining_hours_worked = $hours_worked % 8;
                            $formatted_hours_worked = "{$days_worked} day(s) {$remaining_hours_worked} hour(s)";
                        
                            $overtime_hours = $row['overtime_hours'];
                            $days_overtime = floor($overtime_hours / 8);
                            $remaining_overtime_hours = $overtime_hours % 8;
                            $formatted_overtime_hours = "{$days_overtime} day(s) {$remaining_overtime_hours} hour(s)";
                        
                            $money_taken = $row['money_taken'];
                        
                            $effective_hours = $hours_worked + ($overtime_hours * 1.33);
                            $daily_salary = ($effective_hours * $employee['per_hour_salary']) - $money_taken;
                            
                            $total_hours_worked += $hours_worked;
                            $total_overtime_hours += $overtime_hours;
                            $total_money_taken += $money_taken;
                            $total_daily_salary += $daily_salary;
                        
                            $daily_salary = number_format($daily_salary, 0);
                        
                            echo "<tr>
                                    <td>{$formatted_date}</td>
                                    <td>{$formatted_hours_worked}</td>
                                    <td>{$formatted_overtime_hours}</td>
                                    <td>{$money_taken}</td>
                                    <td>{$daily_salary}</td>
                                </tr>";
                        }

                // Calculate total days and hours for total working hours and overtime
                $total_days_worked = floor($total_hours_worked / 8);
                $total_remaining_hours_worked = $total_hours_worked % 8;
                $formatted_total_hours_worked = "{$total_days_worked} day(s) {$total_remaining_hours_worked} hour(s)";
                
                $total_days_overtime = floor($total_overtime_hours / 8);
                $total_remaining_overtime_hours = $total_overtime_hours % 8;
                $formatted_total_overtime_hours = "{$total_days_overtime} day(s) {$total_remaining_overtime_hours} hour(s)";
                
                // Calculate total monthly salary
                $monthly_salary = $total_daily_salary;

                echo "<tr>
                        <td><strong>Total</strong></td>
                        <td><strong>{$formatted_total_hours_worked}</strong></td>
                        <td><strong>{$formatted_total_overtime_hours}</strong></td>
                        <td><strong>{$total_money_taken}</strong></td>
                        <td><strong>" . number_format($monthly_salary, 0) . "</strong></td>
                    </tr>";
                echo "</tbody></table>";
            } else {
                echo "<p>No attendance records found for this employee for the selected month.</p>";
            }
        } else {
            echo "<p>No employee selected.</p>";
        }
        ?>

        <!-- Print Summary Button -->
        <button type="button" class="btn btn-primary" onclick="printSummary()">Print Summary</button>
        <button type="button" class="btn btn-secondary" onclick="window.location.href='calculate_salary.php'">Back</button>

        <!-- Delete Attendance Button -->
        <form method="post">
            <button type="submit" name="delete_attendance" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete all attendance records for this employee?');">Delete All Attendance Records</button>
        </form>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap and Print Summary Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function printSummary() {
            const employeeName = "<?= $employee_name; ?>";
            const selectedMonth = "<?= date('F Y', strtotime($selected_month)); ?>";
            const totalHoursWorked = "<?= $formatted_total_hours_worked; ?>";
            const totalOvertimeHours = "<?= $formatted_total_overtime_hours; ?>";
            const totalMoneyTaken = "<?= $total_money_taken; ?>";
            const monthlySalary = "<?= number_format($monthly_salary, 0); ?>";

            const summaryContent = `
                <div>
                    <h2>Ittefaq Engineering Works</h2>
                    <h4>Employee:  ${employeeName}</h4>
                    <p>Month: <strong>${selectedMonth}</strong></p>
                    <p>Total Hours Worked: <strong>${totalHoursWorked}</strong></p>
                    <p>Total Overtime Hours: <strong>${totalOvertimeHours}</strong></p>
                    <p>Total Money Taken: <strong>${totalMoneyTaken}</strong></p>
                    <p>Remaining Salary: <strong>${monthlySalary}</strong></p>
                </div>
            `;

            // Store the original content so it can be restored later
            const originalContent = document.body.innerHTML;

            // Replace the page content with the summary
            document.body.innerHTML = summaryContent;

            // Print the summary
            window.print();

            // Restore the original page content after printing
            document.body.innerHTML = originalContent;

            // Reload the page to refresh any dynamic content
            location.reload();
        }
    </script>
</body>
</html>
