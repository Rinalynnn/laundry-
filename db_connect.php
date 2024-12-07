<?php
// Create a connection to the database
$conn = new mysqli('localhost', 'root', '', 'laundry_db');

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
