<?php
// Database connection details
$servername = "localhost";
$dbusername = "root"; // Replace with your username
$dbpassword = "";     // Replace with your password

// Function to create a connection to a specific database
function connectToDatabase($dbname) {
    global $servername, $dbusername, $dbpassword;
    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>