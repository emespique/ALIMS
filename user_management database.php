<?php
// Database connection details
$servername = "localhost";
$username = "root"; // Default XAMPP MySQL username
$password = ""; // Default XAMPP MySQL password (blank)
$dbname = "user_management"; // Name of database to be created

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
    email VARCHAR(100) NOT NULL,
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

// SQL query to insert admins and users with hashed passwords
$insert_sql = "INSERT INTO users (last_name, first_name, middle_initial, designation, laboratory, username, password, email, role) VALUES
('Admin1', 'Admin1', 'A', 'Lab Manager', 'Microbiology', 'HeadAdmin1', '" . hashPassword('password1@') . "', 'admin1@example.com', 'admin'),
('Admin2', 'Admin2', 'B', 'Lab Manager', 'Pathology', 'PathAdmin2', '" . hashPassword('password2@') . "', 'admin2@example.com', 'admin'),
('Admin3', 'Admin3', 'C', 'Medical Technologist', 'Immunology', 'ImmunAdmin3', '" . hashPassword('password3@') . "', 'admin3@example.com', 'admin'),
('Admin4', 'Admin4', 'D', 'Technician', 'Microbiology', 'MicroAdmin4', '" . hashPassword('password4@') . "', 'admin4@example.com', 'admin'),
('TestUser1', 'TestUser1', 'E', 'Researcher', 'Microbiology', 'TestUser1', '" . hashPassword('password5@') . "', 'testuser1@example.com', 'user'),
('TestUser2', 'TestUser2', 'F', 'Student', 'Pathology', 'TestUser2', '" . hashPassword('password6@') . "', 'testuser2@example.com', 'user')";

// Execute the insert query
if ($conn->query($insert_sql) === TRUE) {
    echo "Test admins and users created successfully.<br>";
} else {
    echo "Error: " . $conn->error;
}

// Close the database connection
$conn->close();
?>