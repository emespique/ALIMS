<?php
// Database connection
$servername = "localhost";
$username = "root"; // Default XAMPP MySQL username
$password = ""; // Default XAMPP MySQL password (blank)
$dbname = "user_management"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to add three admin users
$sql = "INSERT INTO users (last_name, first_name, middle_initial, designation, laboratory, username, password, role) VALUES 
('Admin1', 'Admin1', 'A', 'Lab Manager', 'Pathology', 'PathAdmin1', 'password1@', 'admin'),
('Admin2', 'Admin2', 'B', 'Medical Technologist', 'Immunology', 'ImmunAdmin2', 'password2@', 'admin'),
('Admin3', 'Admin3', 'C', 'Technician', 'Microbiology', 'MicroAdmin3', 'password3@', 'admin')";

if ($conn->query($sql) === TRUE) {
    echo "Admin users created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
