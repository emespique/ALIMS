<?php
// Form data with multiple tables
// Database connection details
$servername = "localhost";
$username = "root"; // Default XAMPP MySQL username
$password = ""; // Default XAMPP MySQL password (blank)
$dbname = "form_data"; // Name of database to be created

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists.<br><br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// *****************
// ** Table Maker **
// *****************

// Table 1: 'stock_level'
// SQL query to create the table
$table_sql = "CREATE TABLE IF NOT EXISTS stock_level (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_desc VARCHAR(100) NOT NULL,
    stock_on_hand INT NOT NULL,
    min_stock INT NOT NULL,
    max_stock INT NOT NULL,
    stock_status ENUM('Sufficient', 'Below Reorder Level', 'Critical Stockout') NOT NULL,
    action_req ENUM('None', 'Reorder Immediately', 'Urgent Order Needed', 'Reorder Soon', 'Monitor Usage') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($table_sql) === TRUE) {
    echo "Table 'stock_level' created successfully or already exists.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// SQL query to insert sample data
$insert_sql = "INSERT INTO stock_level (item_desc, stock_on_hand, min_stock, max_stock, stock_status, action_req) VALUES
('Microscope, Model XYZ', 5, 2, 10, 'Sufficient', 'None'),
('Glass Beakers, 1000 mL', 50, 5, 100, 'Below Reorder Level', 'Reorder Soon')";

// Check if table is empty, then insert values
$result = $conn->query("SELECT 1 FROM stock_level LIMIT 1");
if ($result && $result->num_rows > 0) {
    // Don't insert values
    echo "At least one row data exists. Insert not needed.<br><br>";
} else {
    // Reset auto increment for ID to 1
    $conn->query("ALTER TABLE stock_level AUTO_INCREMENT = 1");

    // Execute the insert query
    if ($conn->query($insert_sql) === TRUE) {
        echo "Data values successfully inserted.<br><br>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Table 2: 'disposition'
// SQL query to create the table
$table_sql = "CREATE TABLE IF NOT EXISTS disposition (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_desc VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    unit VARCHAR(50) NOT NULL,
    disposal_reason VARCHAR(100) NOT NULL,
    disposal_method VARCHAR(100) NOT NULL,
    disposal_date VARCHAR(50) NOT NULL,
    disposed_by VARCHAR(50) NOT NULL,
    comments VARCHAR(150) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($table_sql) === TRUE) {
    echo "Table 'disposition' created successfully or already exists.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// SQL query to insert sample data
$insert_sql = "INSERT INTO disposition (item_desc, quantity, unit, disposal_reason, disposal_method, disposal_date, disposed_by, comments) VALUES
('Ethanol, 95%, 500 mL', 2, 'bottles', 'Expired', 'Hazardous Waste Disposal', '09/01/2024', 'Name', 'Properly labeled, disposed via approved vendor'),
('Centrifuge, Model ABC', 1, 'unit', 'Malfunctioned, Unrepairable', 'Decommission and Recycle', '08/30/2024', 'Name', 'Decommissioned, parts recycled'),
('Safety Goggles, Anti-Fog', 5, 'pairs', 'Damaged', 'General Waste Disposal', '08/28/2024', 'Name', 'Disposed of according to lab protocol')";

// Check if table is empty, then insert values
$result = $conn->query("SELECT 1 FROM disposition LIMIT 1");
if ($result && $result->num_rows > 0) {
    // Don't insert values
    echo "At least one row data exists. Insert not needed.<br><br>";
} else {
    // Reset auto increment for ID to 1
    $conn->query("ALTER TABLE disposition AUTO_INCREMENT = 1");

    // Execute the insert query
    if ($conn->query($insert_sql) === TRUE) {
        echo "Data values successfully inserted.<br><br>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Table 3.1: 'purchase_order'
// SQL query to create the table
$table_sql = "CREATE TABLE IF NOT EXISTS purchase_order (
    PO_no VARCHAR(50) PRIMARY KEY NOT NULL,
    PO_date VARCHAR(50) NOT NULL,
    PO_status ENUM('Submitted', 'Procurement Office', 'Accounting Office', 'Delivered', 'Canceled') NOT NULL,
    supplier_name VARCHAR(50) NOT NULL,
    supplier_address VARCHAR(100) NOT NULL,
    supplier_phone_no INT(20) NOT NULL,
    supplier_email VARCHAR(50) NOT NULL,
    supplier_contact_person VARCHAR(50) NOT NULL,
    buyer_lab_name VARCHAR(50) NOT NULL,
    buyer_lab_address VARCHAR(100) NOT NULL,
    buyer_lab_phone_no INT(20) NOT NULL,
    buyer_lab_email VARCHAR(50) NOT NULL,
    buyer_contact_person VARCHAR(50) NOT NULL,
    subtotal DECIMAL(30, 2) NOT NULL,
    tax DECIMAL(30, 2) NOT NULL,
    shipping_cost DECIMAL(30, 2) NOT NULL,
    grand_total DECIMAL(30, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($table_sql) === TRUE) {
    echo "Table 'purchase_order' created successfully or already exists.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// SQL query to insert sample data
$insert_sql = "INSERT INTO purchase_order (PO_no, PO_date, PO_status, supplier_name, supplier_address, supplier_phone_no, supplier_email, supplier_contact_person, 
buyer_lab_name, buyer_lab_address, buyer_lab_phone_no, buyer_lab_email, buyer_contact_person, subtotal, tax, shipping_cost, grand_total) VALUES
('IMN0001', '12/31/2024', 'Submitted', 'James Gonzales', 'Manila, Philippines', 0913853953, 'jgonzales@gmail.com', 'Cedric Bueno',
'MRL - Pathology', 'Quezon City, Philippines', 0928672962, 'mrlpathology@gmail.com', 'Daniel Uy', 140000.00, 14000.00, 5000.00, 159000.00),
('MIC0001', '11/29/2024', 'Submitted', 'James Gonzales', 'Manila, Philippines', 0913853953, 'jgonzales@gmail.com', 'Cedric Bueno',
'MRL - Pathology', 'Quezon City, Philippines', 0928672962, 'mrlpathology@gmail.com', 'Daniel Uy', 140000.00, 14000.00, 5000.00, 159000.00)";

// Check if table is empty, then insert values
$result = $conn->query("SELECT 1 FROM purchase_order LIMIT 1");
if ($result && $result->num_rows > 0) {
    // Don't insert values
    echo "At least one row data exists. Insert not needed.<br><br>";
} else {
    // Reset auto increment for ID to 1
    $conn->query("ALTER TABLE purchase_order AUTO_INCREMENT = 1");

    // Execute the insert query
    if ($conn->query($insert_sql) === TRUE) {
        echo "Data values successfully inserted.<br><br>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Table 3.2: 'purchase_order_items'
// SQL query to create the table
$table_sql = "CREATE TABLE IF NOT EXISTS purchase_order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    PO_no VARCHAR(20) NOT NULL,
    item_desc TEXT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(30, 2) NOT NULL,
    total_price DECIMAL(30, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (PO_no) REFERENCES purchase_order(PO_no)
)";

if ($conn->query($table_sql) === TRUE) {
    echo "Table 'purchase_order_items' created successfully or already exists.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// SQL query to insert sample data
$insert_sql = "INSERT INTO purchase_order_items (PO_no, item_desc, quantity, unit_price, total_price) VALUES
('IMN0001', 'Microscope, Model XYZ, 200x magnification', 2, 30000.00, 60000),
('IMN0001', 'Centrifuge, Model AB', 1, 55000.00, 55000.00),
('IMN0001', 'Glass Beakers, 100mL, pack of 10', 5, 5000.00, 25000.00),
('MIC0001', 'Microscope Slides, Brand M', 4, 3000.00, 12000.00)";

// Check if table is empty, then insert values
$result = $conn->query("SELECT 1 FROM purchase_order_items LIMIT 1");
if ($result && $result->num_rows > 0) {
    // Don't insert values
    echo "At least one row data exists. Insert not needed.<br><br>";
} else {
    // Reset auto increment for ID to 1
    $conn->query("ALTER TABLE purchase_order_items AUTO_INCREMENT = 1");

    // Execute the insert query
    if ($conn->query($insert_sql) === TRUE) {
        echo "Data values successfully inserted.<br><br>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>