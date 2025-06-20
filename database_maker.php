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

// Table: 'suppliers'
// SQL query to create the table
$table_sql = "CREATE TABLE IF NOT EXISTS suppliers (
    supplier_id VARCHAR(20) NOT NULL PRIMARY KEY,
    supplier_name VARCHAR(50) NOT NULL,
    supplier_address VARCHAR(100) NOT NULL,
    supplier_phone_no VARCHAR(20) NOT NULL,
    supplier_email VARCHAR(50) NOT NULL,
    supplier_contact_person VARCHAR(50) NOT NULL,

    shipping_costs_by_dist DECIMAL(30,2) NOT NULL,

    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($table_sql) === TRUE) {
    echo "Table 'suppliers' created successfully or already exists.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// SQL query to insert sample data
$insert_sql = "INSERT INTO suppliers (supplier_id, supplier_name, supplier_address, supplier_phone_no, supplier_email, supplier_contact_person, shipping_costs_by_dist) VALUES
('SU001', 'SafetyLab Inc.', 'Quezon City, Philippines', '09758234910', 'safetylab_inc@gmail.com', 'Mark Anthony Reyes', 150.00),
('SU002', 'LabEquip Solutions', 'Manila, Philippines', '09173458293', 'labequip_solutions@gmail.com', 'Jasmine Dela Cruz', 100.00),
('SU003', 'BioCore Instruments', 'Manila, Philippines', '09361245783', 'biocore_instruments@gmail.com', 'Andrea Santos', 100.00),
('SU004', 'ChemLab Supplies', 'Makati, Philippines', '09284579831', 'chemlab_supplies@gmail.com', 'Miguel Angelo Torres', 120.00)

";

// Check if table is empty, then insert values
$result = $conn->query("SELECT 1 FROM suppliers LIMIT 1");
if ($result && $result->num_rows > 0) {
    // Don't insert values
    echo "At least one row data exists. Insert not needed.<br><br>";
} else {
    // Reset auto increment for ID to 1
    $conn->query("ALTER TABLE suppliers AUTO_INCREMENT = 1");

    // Execute the insert query
    if ($conn->query($insert_sql) === TRUE) {
        echo "Data values successfully inserted.<br><br>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Table: 'suppliers_shipping_rates'
// SQL query to create the table
$table_sql = "CREATE TABLE IF NOT EXISTS suppliers_shipping_rates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id VARCHAR(20) NOT NULL,
    lower_bound_weight_kg DECIMAL(14,4) NOT NULL,
    lower_bound_price DECIMAL(30,3) NOT NULL,
    
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id)
)";

if ($conn->query($table_sql) === TRUE) {
    echo "Table 'suppliers_shipping_rates' created successfully or already exists.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// SQL query to insert sample data
$insert_sql = "INSERT INTO suppliers_shipping_rates (supplier_id, lower_bound_weight_kg, lower_bound_price) VALUES
('SU001', 0.0, 95),
('SU001', 2.5, 180),
('SU001', 4.0, 250),
('SU001', 6.5, 340),
('SU001', 10.0, 460),
('SU001', 13.0, 580),

('SU002', 0.0, 90),
('SU002', 2.0, 135),
('SU002', 5.0, 185),
('SU002', 8.5, 250),
('SU002', 11.5, 310),

('SU003', 0.0, 92),
('SU003', 5.0, 255),
('SU003', 9.5, 415),
('SU003', 14.5, 605),

('SU004', 0.0, 110),
('SU004', 1.5, 180),
('SU004', 3.0, 250),
('SU004', 5.0, 330),
('SU004', 7.5, 420),
('SU004', 10.5, 525),
('SU004', 13.0, 640)

";

// Check if table is empty, then insert values
$result = $conn->query("SELECT 1 FROM suppliers_shipping_rates LIMIT 1");
if ($result && $result->num_rows > 0) {
    // Don't insert values
    echo "At least one row data exists. Insert not needed.<br><br>";
} else {
    // Reset auto increment for ID to 1
    $conn->query("ALTER TABLE suppliers_shipping_rates AUTO_INCREMENT = 1");

    // Execute the insert query
    if ($conn->query($insert_sql) === TRUE) {
        echo "Data values successfully inserted.<br><br>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Table: 'items'
// SQL query to create the table
$table_sql = "CREATE TABLE IF NOT EXISTS items (
    item_id VARCHAR(20) PRIMARY KEY NOT NULL,
    supplier_id VARCHAR(50) NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    item_desc VARCHAR(200) NOT NULL,
    item_type ENUM('Biological', 'Chemical', 'General Lab Supplies') NOT NULL,
    unit_of_measure VARCHAR(20) NOT NULL,
    price_per_unit DECIMAL(30, 2) NOT NULL,
    expiry_date DATE,

    pathology_stock INT NOT NULL,
    immunology_stock INT NOT NULL,
    microbiology_stock INT NOT NULL,

    pathology_min_stock INT NOT NULL,
    pathology_max_stock INT NOT NULL,
    immunology_min_stock INT NOT NULL,
    immunology_max_stock INT NOT NULL,
    microbiology_min_stock INT NOT NULL,
    microbiology_max_stock INT NOT NULL,

    weight_per_unit DECIMAL(30, 4) NOT NULL,

    is_deleted BOOLEAN NOT NULL,
    was_purchased BOOLEAN NOT NULL,

    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated_latest DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id)
)";

if ($conn->query($table_sql) === TRUE) {
    echo "Table 'items' created successfully or already exists.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// SQL query to insert sample data
$insert_sql = "INSERT INTO items (item_id, supplier_id, item_name, item_desc, item_type, unit_of_measure, price_per_unit, expiry_date,
pathology_stock, immunology_stock, microbiology_stock, pathology_min_stock, pathology_max_stock, immunology_min_stock, immunology_max_stock,
microbiology_min_stock, microbiology_max_stock, weight_per_unit, is_deleted, was_purchased) VALUES
('BI001', 'SU002', 'Agar Plates', '100 mm x 15 mm, sterile, handle carefully', 'Biological', 'Plates', 800.00, '2025-09-10',
0, 0, 0, 20, 150, 25, 160, 18, 145, 0.0120, FALSE, FALSE),
('BI002', 'SU003', 'E. Coli Culture', 'Requires -80°C storage', 'Biological', 'Vials', 780.00, '2025-07-14',
0, 0, 0, 10, 80, 12, 85, 8, 70, 0.0030, FALSE, FALSE),
('BI003', 'SU003', 'Yeast Culture', 'Active strain, refrigerated', 'Biological', 'Vials', 750.00, '2025-10-22',
0, 0, 0, 11, 95, 13, 100, 9, 90, 0.0030, FALSE, FALSE),
('BI004', 'SU003', 'DNA Extraction Kit', 'Mini prep, 50 preps per kit', 'Biological', 'Kits', 1850.00, '2026-07-18',
0, 0, 0, 10, 50, 12, 55, 8, 45, 0.2000, FALSE, FALSE),
('BI005', 'SU003', 'Blood Agar Plates', 'Enriched medium, sterile, 90 mm x 10 mm', 'Biological', 'Plates', 75.00, '2025-07-25',
0, 0, 0, 20, 100, 22, 110, 18, 95, 0.0100, FALSE, FALSE),
('BI006', 'SU003', 'Streptococcus pyogenes Culture', 'Hemolytic strain, requires enriched media', 'Biological', 'Vials', 880.00, '2025-11-15',
0, 0, 0, 9, 85, 10, 90, 8, 80, 0.0030, FALSE, FALSE),
('BI007', 'SU003', 'Salmonella typhimurium Culture', 'Pathogen model, lab-safe strain', 'Biological', 'Vials', 890.00, '2026-02-25',
0, 0, 0, 10, 80, 11, 85, 9, 75, 0.0030, FALSE, FALSE),

('CH001', 'SU004', 'Acetone', 'Flammable, store in cool place', 'Chemical', 'liters', 260.00, '2025-12-01',
0, 0, 0, 10, 60, 12, 70, 9, 55, 0.7900, FALSE, FALSE),
('CH002', 'SU004', 'Sodium Chloride', 'General Lab use, granular', 'Chemical', 'kg', 1500.00, '2026-06-22',
0, 0, 0, 40, 200, 38, 210, 42, 190, 1.0000, FALSE, FALSE),
('CH003', 'SU002', 'Ethanol', '95% lab grade, flammable', 'Chemical', 'liters', 280.00, '2025-11-15',
0, 0, 0, 50, 400, 55, 420, 48, 390, 0.7890, FALSE, FALSE),
('CH004', 'SU004', 'Hydrochloric Acid', '37% concentration, corrosive', 'Chemical', 'liters', 320.00, '2026-01-10',
0, 0, 0, 30, 150, 28, 140, 32, 155, 1.1900, FALSE, FALSE),
('CH005', 'SU004', 'Sodium Hydroxide', 'Pellets, caustic, store dry', 'Chemical', 'kg', 1700.00, '2027-03-01',
0, 0, 0, 15, 80, 18, 85, 14, 78, 1.0000, FALSE, FALSE),
('CH006', 'SU004', 'Potassium Nitrate', 'Oxidizer, white crystalline', 'Chemical', 'kg', 1850.00, '2026-09-18',
0, 0, 0, 10, 40, 11, 42, 9, 38, 1.0000, FALSE, FALSE),
('CH007', 'SU004', 'Ammonium Sulfate', 'Fertilizer-grade, lab use', 'Chemical', 'kg', 1350.00, '2026-04-12',
0, 0, 0, 30, 150, 33, 160, 29, 140, 1.0000, FALSE, FALSE),
('CH008', 'SU004', 'Nitric Acid', 'Strong oxidizer, concentrated', 'Chemical', 'liters', 390.00, '2025-08-30',
0, 0, 0, 5, 30, 6, 32, 4, 28, 1.5100, FALSE, FALSE),
('CH009', 'SU004', 'Copper(II) Sulfate', 'Blue crystals, lab reagent', 'Chemical', 'kg', 2100.00, '2027-02-28',
0, 0, 0, 10, 40, 12, 45, 9, 38, 1.0000, FALSE, FALSE),

('LS001', 'SU001', 'Glass Beaker', '100ml, heat-resistant', 'General Lab Supplies', 'Pieces', 75.00, NULL,
0, 0, 0, 10, 100, 12, 110, 9, 95, 0.1500, FALSE, FALSE),
('LS002', 'SU002', 'Pipette Tips', 'Sterile, 10μL capacity', 'General Lab Supplies', 'Pieces', 4.00, NULL,
0, 0, 0, 9, 99, 11, 105, 8, 95, 0.0001, FALSE, FALSE),
('LS003', 'SU001', 'Test Tube Rack', 'Holds 12 tubes, plastic', 'General Lab Supplies', 'Pieces', 250.00, NULL,
0, 0, 0, 5, 40, 6, 42, 4, 38, 0.2500, FALSE, FALSE),
('LS004', 'SU001', 'Glass Funnel', '100mm diameter, chemical-resistant', 'General Lab Supplies', 'Pieces', 60.00, NULL,
0, 0, 0, 8, 30, 9, 32, 7, 28, 0.2000, FALSE, FALSE),
('LS005', 'SU001', 'Graduated Cylinder', '250ml, glass, with spout', 'General Lab Supplies', 'Pieces', 130.00, NULL,
0, 0, 0, 6, 20, 7, 22, 5, 18, 0.3000, FALSE, FALSE),
('LS006', 'SU002', 'Bunsen Burner', 'Adjustable flame, brass', 'General Lab Supplies', 'Pieces', 465.00, NULL,
0, 0, 0, 5, 30, 6, 35, 4, 28, 0.8500, FALSE, FALSE),
('LS007', 'SU001', 'Volumetric Flask', '250ml, glass, narrow neck', 'General Lab Supplies', 'Pieces', 140.00, NULL,
0, 0, 0, 5, 25, 6, 26, 4, 23, 0.2000, FALSE, FALSE),
('LS008', 'SU002', 'Petri Dish', '90mm diameter, glass, reusable', 'General Lab Supplies', 'Pieces', 35.00, NULL,
0, 0, 0, 20, 120, 22, 125, 18, 110, 0.0700, FALSE, FALSE),
('LS009', 'SU001', 'Glass Beaker', '500ml, heat-resistant', 'General Lab Supplies', 'Pieces', 225.00, NULL,
0, 0, 0, 20, 150, 22, 160, 18, 140, 0.4000, FALSE, FALSE),
('LS010', 'SU001', 'Centrifuge', 'Model ABC, max 6000 RPM', 'General Lab Supplies', 'Pieces', 12500.00, NULL,
0, 0, 0, 2, 5, 3, 6, 2, 4, 9.0000, FALSE, FALSE),
('LS011', 'SU003', 'Microscope', 'Model XYZ, 1000x magnification', 'General Lab Supplies', 'Pieces', 18500.00, NULL,
0, 0, 0, 2, 10, 3, 12, 2, 8, 5.5000, FALSE, FALSE),
('LS012', 'SU001', 'Lab Thermometer', 'Brand X, -10°C to 110°C range', 'General Lab Supplies', 'Pieces', 250.00, NULL,
0, 0, 0, 10, 60, 12, 65, 9, 55, 0.0800, FALSE, FALSE),
('LS013', 'SU001', 'Test Tube', '15x125mm, borosilicate glass', 'General Lab Supplies', 'Pieces', 18.00, NULL,
0, 0, 0, 20, 250, 25, 260, 18, 240, 0.0300, FALSE, FALSE)

";

// Check if table is empty, then insert values
$result = $conn->query("SELECT 1 FROM items LIMIT 1");
if ($result && $result->num_rows > 0) {
    // Don't insert values
    echo "At least one row data exists. Insert not needed.<br><br>";
} else {
    // Reset auto increment for ID to 1
    $conn->query("ALTER TABLE items AUTO_INCREMENT = 1");

    // Execute the insert query
    if ($conn->query($insert_sql) === TRUE) {
        echo "Data values successfully inserted.<br><br>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Table: 'laboratories'
// SQL query to create the table
$table_sql = "CREATE TABLE IF NOT EXISTS laboratories (
    lab_id INT AUTO_INCREMENT PRIMARY KEY,
    lab_name VARCHAR(50) NOT NULL,
    lab_address VARCHAR(100) NOT NULL,
    lab_phone_no VARCHAR(20) NOT NULL,
    lab_email VARCHAR(50) NOT NULL,
    contact_person VARCHAR(50) NOT NULL,

    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($table_sql) === TRUE) {
    echo "Table 'laboratories' created successfully or already exists.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// SQL query to insert sample data
$insert_sql = "INSERT INTO laboratories (lab_name, lab_address, lab_phone_no, lab_email, contact_person) VALUES
('Pathology', 'Bldg. A, Hall B', '09192658431', 'pathology@gmail.com', 'Ryan Lim'),
('Immunology', 'Bldg. A, Room X', '09984567213', 'immunology@gmail.com', 'Sofia Aguirre'),
('Microbiology', 'Bldg. B, Room Z', '09276543012', 'microbiology@gmail.com', 'Iris Marcos')
";

// Check if table is empty, then insert values
$result = $conn->query("SELECT 1 FROM laboratories LIMIT 1");
if ($result && $result->num_rows > 0) {
    // Don't insert values
    echo "At least one row data exists. Insert not needed.<br><br>";
} else {
    // Reset auto increment for ID to 1
    $conn->query("ALTER TABLE laboratories AUTO_INCREMENT = 1");

    // Execute the insert query
    if ($conn->query($insert_sql) === TRUE) {
        echo "Data values successfully inserted.<br><br>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Table: 'purchase_order'
// SQL query to create the table
$table_sql = "CREATE TABLE IF NOT EXISTS purchase_order (
    PO_no VARCHAR(20) PRIMARY KEY NOT NULL,

    supplier_id VARCHAR(20) NOT NULL,
    lab_id INT NOT NULL,
    user_id INT(6) NOT NULL,

    status ENUM('Submitted', 'Procurement Office', 'Accounting Office', 'Delivered', 'Canceled') NOT NULL,

    subtotal DECIMAL(30, 2) NOT NULL,
    tax DECIMAL(30, 2) NOT NULL,
    shipping_cost DECIMAL(30, 2) NOT NULL,
    grand_total DECIMAL(30, 2) NOT NULL,

    grand_total_weight DECIMAL(30,4),

    admin_id INT,
    admin_status ENUM('Pending', 'Approved', 'Rejected') NOT NULL,
    head_admin_id INT,
    head_admin_status ENUM('Pending', 'Approved', 'Rejected') NOT NULL,
    final_status ENUM('Pending', 'Approved', 'Rejected'),

    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id),
    FOREIGN KEY (lab_id) REFERENCES laboratories(lab_id)
)";

if ($conn->query($table_sql) === TRUE) {
    echo "Table 'purchase_order' created successfully or already exists.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// // SQL query to insert sample data
// $insert_sql = "INSERT INTO purchase_order (PO_no, PO_date, PO_status, supplier_name, supplier_address, supplier_phone_no, supplier_email, supplier_contact_person, 
// buyer_lab_name, buyer_lab_address, buyer_lab_phone_no, buyer_lab_email, buyer_contact_person, subtotal, tax, shipping_cost, grand_total) VALUES
// ('IMN0001', '12/31/2024', 'Submitted', 'James Gonzales', 'Manila, Philippines', 0913853953, 'jgonzales@gmail.com', 'Cedric Bueno',
// 'MRL - Pathology', 'Quezon City, Philippines', 0928672962, 'mrlpathology@gmail.com', 'Daniel Uy', 140000.00, 14000.00, 5000.00, 159000.00),
// ('MIC0001', '11/29/2024', 'Submitted', 'James Gonzales', 'Manila, Philippines', 0913853953, 'jgonzales@gmail.com', 'Cedric Bueno',
// 'MRL - Pathology', 'Quezon City, Philippines', 0928672962, 'mrlpathology@gmail.com', 'Daniel Uy', 140000.00, 14000.00, 5000.00, 159000.00)";

// // Check if table is empty, then insert values
// $result = $conn->query("SELECT 1 FROM purchase_order LIMIT 1");
// if ($result && $result->num_rows > 0) {
//     // Don't insert values
//     echo "At least one row data exists. Insert not needed.<br><br>";
// } else {
//     // Reset auto increment for ID to 1
//     $conn->query("ALTER TABLE purchase_order AUTO_INCREMENT = 1");

//     // Execute the insert query
//     if ($conn->query($insert_sql) === TRUE) {
//         echo "Data values successfully inserted.<br><br>";
//     } else {
//         echo "Error: " . $conn->error;
//     }
// }


// Table: 'purchase_order_items'
// SQL query to create the table
$table_sql = "CREATE TABLE IF NOT EXISTS purchase_order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    PO_no VARCHAR(20) NOT NULL,
    item_id VARCHAR(20) NOT NULL,
    quantity INT NOT NULL,
    unit_weight DECIMAL(30, 4) NOT NULL,
    total_weight DECIMAL(30, 4) NOT NULL,
    unit_price DECIMAL(30, 2) NOT NULL,
    total_price DECIMAL(30, 2) NOT NULL,

    FOREIGN KEY (item_id) REFERENCES items(item_id),
    FOREIGN KEY (PO_no) REFERENCES purchase_order(PO_no)
)";

if ($conn->query($table_sql) === TRUE) {
    echo "Table 'purchase_order_items' created successfully or already exists.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// // SQL query to insert sample data
// $insert_sql = "INSERT INTO purchase_order_items (PO_no, item_desc, quantity, unit_price, total_price) VALUES
// ('IMN0001', 'Microscope, Model XYZ, 200x magnification', 2, 30000.00, 60000),
// ('IMN0001', 'Centrifuge, Model AB', 1, 55000.00, 55000.00),
// ('IMN0001', 'Glass Beakers, 100mL, pack of 10', 5, 5000.00, 25000.00),
// ('MIC0001', 'Microscope Slides, Brand M', 4, 3000.00, 12000.00)";

// // Check if table is empty, then insert values
// $result = $conn->query("SELECT 1 FROM purchase_order_items LIMIT 1");
// if ($result && $result->num_rows > 0) {
//     // Don't insert values
//     echo "At least one row data exists. Insert not needed.<br><br>";
// } else {
//     // Reset auto increment for ID to 1
//     $conn->query("ALTER TABLE purchase_order_items AUTO_INCREMENT = 1");

//     // Execute the insert query
//     if ($conn->query($insert_sql) === TRUE) {
//         echo "Data values successfully inserted.<br><br>";
//     } else {
//         echo "Error: " . $conn->error;
//     }
// }


// Table: 'disposition'
// SQL query to create the table
$table_sql = "CREATE TABLE IF NOT EXISTS disposition (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id VARCHAR(20) NOT NULL,
    quantity INT NOT NULL,
    disposal_reason VARCHAR(100) NOT NULL,
    disposal_method VARCHAR(100) NOT NULL,
    disposal_date DATE NOT NULL,
    disposed_by VARCHAR(50) NOT NULL,
    comments VARCHAR(150) NOT NULL,
    lab_disposed_by ENUM('Pathology', 'Immunology', 'Microbiology') NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (item_id) REFERENCES items(item_id)
)";

if ($conn->query($table_sql) === TRUE) {
    echo "Table 'disposition' created successfully or already exists.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// SQL query to insert sample data
// $insert_sql = "INSERT INTO disposition (item_id, quantity, disposal_reason, disposal_method, disposal_date, disposed_by, comments) VALUES
// ('CH003', 5, 'Expired', 'Hazardous Waste Disposal', '2024-09-01', 'Name', 'Properly labeled, disposed via approved vendor'),
// ('LS010', 1, 'Decommission and Recycle', 'Decommissioning', '2024-08-30', 'Name', 'Decommissioned, parts recycled'),
// ('BI004', 2, 'General Waste Disposal', 'General Waste', '2024-08-28', 'Name', 'Disposed of according to lab protocol')";


// Check if table is empty, then insert values
// $result = $conn->query("SELECT 1 FROM disposition LIMIT 1");
// if ($result && $result->num_rows > 0) {
//     // Don't insert values
//     echo "At least one row data exists. Insert not needed.<br><br>";
// } else {
//     // Reset auto increment for ID to 1
//     $conn->query("ALTER TABLE disposition AUTO_INCREMENT = 1");

//     // Execute the insert query
//     if ($conn->query($insert_sql) === TRUE) {
//         echo "Data values successfully inserted.<br><br>";
//     } else {
//         echo "Error: " . $conn->error;
//     }
// }


// Table: 'settings'
$table_sql = "CREATE TABLE IF NOT EXISTS settings (
    setting_name VARCHAR(50) PRIMARY KEY,
    setting_value DECIMAL(10, 4) NOT NULL
)";

if ($conn->query($table_sql) === TRUE) {
    echo "Table 'settings' created successfully or already exists.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// SQL query to insert sample data
$insert_sql = "INSERT INTO settings (setting_name, setting_value) VALUES ('tax', 0.12)";

// Check if table is empty, then insert values
$result = $conn->query("SELECT 1 FROM settings LIMIT 1");
if ($result && $result->num_rows > 0) {
    // Don't insert values
    echo "At least one row data exists. Insert not needed.<br><br>";
} else {
    // Reset auto increment for ID to 1
    $conn->query("ALTER TABLE settings AUTO_INCREMENT = 1");

    // Execute the insert query
    if ($conn->query($insert_sql) === TRUE) {
        echo "Data values successfully inserted.<br><br>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Table: 'lab_connection'
$table_sql = "CREATE TABLE IF NOT EXISTS lab_connection (
    lab_id INT AUTO_INCREMENT PRIMARY KEY,
    lab_name ENUM('Pathology', 'Immunology', 'Microbiology') NOT NULL
)";

if ($conn->query($table_sql) === TRUE) {
    echo "Table 'lab_connection' created successfully or already exists.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// SQL query to insert sample data
$insert_sql = "INSERT INTO lab_connection (lab_name) VALUES ('Pathology'), ('Immunology'), ('Microbiology')";

// Check if table is empty, then insert values
$result = $conn->query("SELECT 1 FROM lab_connection LIMIT 1");
if ($result && $result->num_rows > 0) {
    // Don't insert values
    echo "At least one row data exists. Insert not needed.<br><br>";
} else {
    // Reset auto increment for ID to 1
    $conn->query("ALTER TABLE lab_connection AUTO_INCREMENT = 1");

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

