<?php
// Database connection details
$servername = "localhost";
$username = "root"; // Default XAMPP MySQL username
$password = ""; // Default XAMPP MySQL password (blank)
$dbname = "user_management"; // Database name you want to create

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// SQL query to create the `users` table
$table_sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_initial CHAR(1),
    designation ENUM('Medical Technologist', 'Researcher', 'Lab Manager', 'Student', 'Technician') NOT NULL,
    laboratory ENUM('Pathology', 'Immunology', 'Microbiology') NOT NULL,
    username VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($table_sql) === TRUE) {
    echo "Table 'users' created successfully or already exists.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// Function to hash passwords
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// SQL query to insert admin users with hashed passwords
$insert_sql = "INSERT INTO users (last_name, first_name, middle_initial, designation, laboratory, username, password, role) VALUES 
('Admin1', 'Admin1', 'A', 'Lab Manager', 'Pathology', 'PathAdmin1', '" . hashPassword('password1@') . "', 'admin'),
('Admin2', 'Admin2', 'B', 'Medical Technologist', 'Immunology', 'ImmunAdmin2', '" . hashPassword('password2@') . "', 'admin'),
('Admin3', 'Admin3', 'C', 'Technician', 'Microbiology', 'MicroAdmin3', '" . hashPassword('password3@') . "', 'admin'),
('TestUser', 'TestUser', 'D', 'Researcher', 'Microbiology', 'TestUser1', '" . hashPassword('password4@') . "', 'user'),
('TestUser2', 'TestUser2', 'E', 'Student', 'Pathology', 'TestUser2', '" . hashPassword('password5@') . "', 'user')";

// Execute the insert query
if ($conn->query($insert_sql) === TRUE) {
    echo "Test admins and users created successfully.<br>";
} else {
    echo "Error: " . $conn->error;
}

// Close the database connection
$conn->close();
?>