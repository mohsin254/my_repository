<?php
include 'connect.php';

// Get employee ID from query string
$employee_id = $_GET['id'] ?? null;

if ($employee_id) {
    // Prepare and execute SQL statement
    $delete_sql = "DELETE FROM employees WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    
    if ($stmt) {
        $stmt->bind_param('i', $employee_id);
        
        if ($stmt->execute()) {
            // Redirect to calculate_salary.php on success
            header('Location: calculate_salary.php');
            exit();
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
        
        $stmt->close();
    } else {
        echo "<p>Failed to prepare statement.</p>";
    }
} else {
    echo "<p>Employee ID is missing.</p>";
}
?>
