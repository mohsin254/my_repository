<?php include 'connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Calculate Salary</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container my-5">
        <h1 class="text-center mb-4">Calculate Monthly Salary</h1>

        <?php
        $sql = "
            SELECT e.id, e.name, e.per_hour_salary,
                   COALESCE(SUM(a.hours_worked), 0) AS total_hours_worked,
                   COALESCE(SUM(a.overtime_hours), 0) AS total_overtime_hours,
                   COALESCE(SUM(a.money_taken), 0) AS total_money_taken
            FROM employees e
            LEFT JOIN attendance a ON e.id = a.employee_id
            GROUP BY e.id, e.name, e.per_hour_salary
        ";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table class='table table-bordered table-striped'>
                    <thead class='table-dark'>
                        <tr>
                            <th>Employee Name</th>
                            <th>Per Hour Salary</th>
                            <th>Total Hours Worked</th>
                            <th>Total Overtime Hours</th>
                            <th>Total Money Taken</th>
                            <th>Monthly Salary</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>";

            while ($row = $result->fetch_assoc()) {
                $total_hours = $row['total_hours_worked'];
                $overtime_hours = $row['total_overtime_hours'];
                $per_hour_salary = $row['per_hour_salary'];
                $money_taken = $row['total_money_taken'];

                // Convert total hours worked and overtime hours into days and hours
                $days_worked = intdiv($total_hours, 8); // Calculate full days worked
                $remaining_hours_worked = $total_hours % 8; // Calculate remaining hours
                
                $days_overtime = intdiv($overtime_hours, 8); // Calculate full overtime days
                $remaining_overtime_hours = $overtime_hours % 8; // Calculate remaining overtime hours

                $regular_income = $total_hours * $per_hour_salary;
                $overtime_income = $overtime_hours * $per_hour_salary * 1.33;
                $monthly_salary = $regular_income + $overtime_income - $money_taken;

                $monthly_salary = number_format($monthly_salary, 0);

                echo "<tr>
                        <td>{$row['name']}</td>
                        <td>Rs {$per_hour_salary}</td>
                        <td>{$days_worked} day(s) {$remaining_hours_worked} hour(s)</td>
                        <td>{$days_overtime} day(s) {$remaining_overtime_hours} hour(s)</td>
                        <td>{$money_taken}</td>
                        <td>Rs {$monthly_salary}</td>
                        <td>
                            <a href='view_attendance.php?employee_id={$row['id']}' class='btn btn-primary btn-sm'>View Details</a>
                            <a href='edit_employee.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                            <a href='del_employee.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this employee?\");'>Delete</a>
                            <a href='mark_attendance.php?employee_id={$row['id']}' class='btn btn-success btn-sm'>Mark Attendance</a>
                        </td>
                    </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p class='text-center'>No data available.</p>";
        }
        ?>

        <button type="button" class="btn btn-secondary mt-3" onclick="window.location.href='index.php'">Back</button>
        <a href="add_employee.php" class="btn btn-primary mt-3">Add New Employee</a>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
