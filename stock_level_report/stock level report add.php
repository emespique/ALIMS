<!-- stock level report add.php -->
<?php
// Start the session and include the database connection
require '../header.php';
require '../db_connection.php';

// Connect to the form_data database
$conn = connectToDatabase('form_data');

$supplier_stmt = $conn->prepare("SELECT supplier_id, supplier_name FROM suppliers");
$supplier_stmt->execute();
$supplier_result = $supplier_stmt->get_result();

// Initialize variables and error messages
$item_id = $supplier_id = $item_name = $item_desc = $item_type =
$unit_of_measure = $expiry_date = "";

$price_per_unit = $weight_per_unit = $min_stock = $max_stock = 
$pathology_stock = $immunology_stock = $microbiology_stock = 
$pathology_min_stock = $immunology_min_stock = $microbiology_min_stock = 
$pathology_max_stock = $immunology_max_stock = $microbiology_max_stock = 0;

$item_id_list = $item_type_code = "";

function findLowestMissing($arr) {
    $left = 0;
    $right = count($arr) - 1;

    while ($left <= $right) {
        $mid = intval(($left + $right) / 2);
        $expected = $mid + 1;

        if ($arr[$mid] == $expected) {
            $left = $mid + 1;
        } else {
            $right = $mid - 1;
        }
    }

    return $left + 1;
}

// Example usage
// $ids = [1, 2, 3, 4, 6, 7, 8, 10];
// $newId = findLowestMissing($ids);
// echo "Assigning new ID: $newId";

// Handle form submission when the form is posted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Retrieve and sanitize the form data
    $supplier_id = htmlspecialchars(trim($_POST['supplier_id']));
    $item_name = htmlspecialchars(trim($_POST['item_name']));
    $item_desc = htmlspecialchars(trim($_POST['item_desc']));

    $item_type = htmlspecialchars(trim($_POST['item_type']));
    $unit_of_measure = htmlspecialchars(trim($_POST['unit_of_measure']));
    $price_per_unit = htmlspecialchars(trim($_POST['price_per_unit']));
    $expiry_date = htmlspecialchars(trim($_POST['expiry_date']));

    $weight_per_unit = htmlspecialchars(trim($_POST['weight_per_unit']));


    if (empty($expiry_date)) {
        $expiry_date = null;
    }

    // Generate item_id
    if ($item_type == "Biological") {
        $item_id_list = "SELECT item_id FROM items WHERE item_id LIKE 'BI%'";
        $item_type_code = "BI";
    } else if ($item_type == "Chemical") {
        $item_id_list = "SELECT item_id FROM items WHERE item_id LIKE 'CH%'";
        $item_type_code = "CH";
    } else {
        $item_id_list = "SELECT item_id FROM items WHERE item_id LIKE 'LS%'";
        $item_type_code = "LS";
    }

    $item_id_stmt = $conn->prepare($item_id_list);
    $item_id_stmt->execute();
    $item_id_result = $item_id_stmt->get_result();

    $item_ids = [];
    while ($row = $item_id_result->fetch_assoc()) {
        $item_ids[] = $row['item_id'];
    }

    $item_ids_num = [];
    foreach ($item_ids as $code) {
        $item_ids_num[] = intval(substr($code, 2));
    }

    $new_item_id = max($item_ids_num) + 1;
    // sort($item_ids_num);
    // $new_item_id = findLowestMissing($item_ids_num);
    $item_id = $item_type_code . str_pad($new_item_id, 3, '0', STR_PAD_LEFT);

    // Prepare an insert statement
    $stmt = $conn->prepare("INSERT INTO items (
        item_id, supplier_id, item_name, item_desc, item_type, unit_of_measure, 
        price_per_unit, expiry_date, pathology_min_stock, immunology_min_stock, microbiology_min_stock, 
        pathology_max_stock, immunology_max_stock, microbiology_max_stock, 
        pathology_stock, immunology_stock, microbiology_stock, weight_per_unit
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssssdsiiiiiiiiid",
        $item_id, $supplier_id, $item_name, $item_desc, $item_type, $unit_of_measure, 
        $price_per_unit, $expiry_date, $pathology_min_stock, $immunology_min_stock, $microbiology_min_stock, 
        $pathology_max_stock, $immunology_max_stock, $microbiology_max_stock, 
        $pathology_stock, $immunology_stock, $microbiology_stock, $weight_per_unit
    );

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        // Redirect back to the stock level page if the insertion is successful
        header("Location: stock level report.php");
        exit();
    } else {
        echo "Error adding item: " . $conn->error;
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <link rel="stylesheet" type="text/css" href="../css/main.css?version=<?php echo time() - 1000000000; ?>">
    <link rel="stylesheet" type="text/css" href="../css/accounts.css?version=<?php echo time() - 1000000000; ?>">
    <link rel="stylesheet" type="text/css" href="../css/input_design.css?version=<?php echo time() - 1000000000; ?>"> 
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script defer src="../js/user_validation.js"></script>
</head>
<body>
    <?php include '../header.php'; ?> 
    
    <div class="content">
        <div class="add-container">
            <div class="information">
                <h2 id="add-user-header">ADD ITEM STOCK</h2>
                <form action="stock level report add.php" method="POST">

                    <div class="form-row">
                        <div class="input-group form-field">
                            <label for="supplier_id">Supplier</label>
                            <select class="info-dropdown" name="supplier_id" required>
                                <option value="">Select Supplier</option>
                                <?php
                                    while ($row = $supplier_result->fetch_assoc()) {
                                        $id = $row['supplier_id'];
                                        $name = $row['supplier_name'];
                                        $selected = ($supplier_id == $id) ? 'selected' : '';
                                        echo "<option value=\"$id\" $selected>$id - $name</option>";
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="input-group form-field">
                            <label for="item_name">Item Name</label>
                            <input type="text" maxlength="100" class="name-input" name="item_name" 
                                value="<?php echo htmlspecialchars($item_name); ?>" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="item_desc">Item Description</label>
                        <input type="text" maxlength="100" class="desc-input" name="item_desc" value="<?php echo htmlspecialchars($item_desc); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="input-group form-field">
                            <label for="item_type">Item Type</label>
                            <select class="info-dropdown" name="item_type" required>
                                <option value="">Select Item Type</option>
                                <option value="Biological" <?php if ($item_type == "Biological") echo "selected"; ?>>Biological</option>
                                <option value="Chemical" <?php if ($item_type == "Chemical") echo "selected"; ?>>Chemical</option>
                                <option value="General Lab Supplies" <?php if ($item_type == "General Lab Supplies") echo "selected"; ?>>General Lab Supplies</option>
                            </select>
                        </div>
                        
                        <div class="input-group form-field">
                            <label for="unit_of_measure">Unit Of Measure</label>
                            <input type="text" maxlength="20" class="num-input" name="unit_of_measure" value="<?php echo htmlspecialchars($unit_of_measure); ?>" required>
                        </div>

                        <div class="input-group form-field">
                            <label for="price_per_unit">Price Per Unit</label>
                            <input type="number" min="0.01" step=0.01 class="num-input" name="price_per_unit" value="<?php echo htmlspecialchars($price_per_unit); ?>"
                            onkeypress="return (event.charCode != 8 && event.charCode == 0 || (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46))" required>
                        </div>
                    </div>

                    <div class="input-group form-field">
                        <label for="weight_per_unit">Weight Per Unit (kg)</label>
                        <input type="number" min="0.0001" step=0.0001 class="num-input" name="weight_per_unit" value="<?php echo htmlspecialchars($weight_per_unit); ?>"
                        onkeypress="return (event.charCode != 8 && event.charCode == 0 || (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46))" required>
                    </div>
                    
                    <div class="input-group inline-input-group">
                        <label>Does it expire?</label><br>
                        <label>
                            <input type="radio" name="expiry_toggle" value="yes" onclick="toggleExpiryDate(true)"> Yes
                        </label>
                        <label>
                            <input type="radio" name="expiry_toggle" value="no" onclick="toggleExpiryDate(false)" checked> No
                        </label>
                    </div>

                    <div class="input-group" id="expiry_date_group" style="visibility: hidden;">
                        <label for="expiry_date">Expiry Date</label>
                        <input type="date" max="9999-12-31" class="num-input" name="expiry_date" 
                            value="<?php echo htmlspecialchars($expiry_date); ?>">
                    </div>

                    <div class="button-container">
                        <button type="submit" class="save-button">ADD</button>
                        <button type="button" class="cancel-button" onclick="window.location.href='stock level report.php';">CANCEL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include '../footer.php'; ?>

    <script>
        function toggleExpiryDate(show) {
            document.getElementById('expiry_date_group').style.visibility = show ? 'visible' : 'hidden';
        }
    </script>
</body>
</html>