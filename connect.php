<?php
$servername = "sql.freedb.tech"; // Remote host
$username = "freedb_mohsin";      // Remote database username
$password = "7%yDK5sagC#T4Wf";    // Remote database password
$dbname = "freedb_factory_management"; // Remote database name
$port = 3306;                     // Remote port

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully";
?>
