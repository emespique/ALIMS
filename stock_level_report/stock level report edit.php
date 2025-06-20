<!-- stock level report edit.php --> 
<?php
// Start the session and include the database connection
require '../header.php';
require '../db_connection.php';

// Connect to the user_management database
$conn = connectToDatabase('form_data');

$supplier_stmt = $conn->prepare("SELECT supplier_id, supplier_name FROM suppliers");
$supplier_stmt->execute();
$supplier_result = $supplier_stmt->get_result();

// Fetch user ID from GET or POST request
$item_id = isset($_GET['item_id']) ? $_GET['item_id'] : (isset($_POST['item_id']) ? $_POST['item_id'] : '');

$user_id = $_SESSION['user_id'];

// Initialize variables and error messages
$supplier_id = $item_name = $item_desc = $item_type = $unit_of_measure = $expiry_date = "";

$price_per_unit = $weight_per_unit = 
$pathology_stock = $immunology_stock = $microbiology_stock = 
$min_stock = $min_pathology_stock = $min_immunology_stock = $min_microbiology_stock = 
$max_stock = $max_pathology_stock = $max_immunology_stock = $max_microbiology_stock = 0;

$max_stock_error = "";

$item_id_list = $item_type_code = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Retrieve and sanitize the form data
    if ($user_id == 1) {
        $supplier_id = htmlspecialchars(trim($_POST['supplier_id']));
        $item_name = htmlspecialchars(trim($_POST['item_name']));
        $item_desc = htmlspecialchars(trim($_POST['item_desc']));

        $item_type = htmlspecialchars(trim($_POST['item_type']));
        $unit_of_measure = htmlspecialchars(trim($_POST['unit_of_measure']));
        $price_per_unit = (float) htmlspecialchars(trim($_POST['price_per_unit']));
        $expiry_date = htmlspecialchars(trim($_POST['expiry_date']));
        $weight_per_unit = (float) htmlspecialchars(trim($_POST['weight_per_unit']));
    } else {
        $min_stock = (int) htmlspecialchars(trim($_POST['min_stock']));
        $max_stock = (int) htmlspecialchars(trim($_POST['max_stock']));
    } 

    if ($user_id != 1) {
        if ($max_stock <= $min_stock) {
        $max_stock_error = "The max stock value can't be less than <br> or equal to the min stock value";
        }

        if ($max_stock > 9999) {
            $max_stock_error = "The max stock value can't exceed 9999";
        }
    }

    if ($user_id == 2) {
    $pathology_min_stock = $min_stock;
    $pathology_max_stock = $max_stock;
    } elseif ($user_id == 3) {
        $immunology_min_stock = $min_stock;
        $immunology_max_stock = $max_stock;
    } else {
        $microbiology_min_stock = $min_stock;
        $microbiology_max_stock = $max_stock;
    }

    // If there are no errors, proceed to add item stock
    if (empty($max_stock_error)) {

        // Prepare an insert statement
        if ($user_id == 1) {
            date_default_timezone_set('Asia/Manila');
            $date_updated = date('Y-m-d H:i:s');
            $stmt = $conn->prepare("UPDATE items SET
                supplier_id = ?, item_name = ?, item_desc = ?, item_type = ?, unit_of_measure = ?, 
                price_per_unit = ?, expiry_date = ?, weight_per_unit = ?, date_updated_latest = ?
                WHERE item_id = ?");

            $stmt->bind_param("sssssdsdss",
                $supplier_id, $item_name, $item_desc, $item_type, $unit_of_measure,
                $price_per_unit, $expiry_date, $weight_per_unit, $date_updated, $item_id);

        } elseif ($user_id == 2) {
            $stmt = $conn->prepare("UPDATE items SET pathology_min_stock = ?, pathology_max_stock = ?
                WHERE item_id = ?");

            $stmt->bind_param("iis", $min_stock, $max_stock, $item_id);

        } elseif ($user_id == 3) {
            $stmt = $conn->prepare("UPDATE items SET immunology_stock = ?, immunology_max_stock = ?
                WHERE item_id = ?");

            $stmt->bind_param("iis", $min_stock, $max_stock, $item_id);

        } elseif ($user_id == 4) {
            $stmt = $conn->prepare("UPDATE items SET microbiology_stock = ?, microbiology_max_stock = ?
                WHERE item_id = ?");

            $stmt->bind_param("iis", $min_stock, $max_stock, $item_id);
        }

        // Execute the statement and check for errors
        if ($stmt->execute()) {
            // Redirect back to the stock level page if the insertion is successful
            header("Location: stock level report.php");
            exit();
        } else {
            echo "Error adding item: " . $conn->error;
        }
    }


} else {
    // Fetch existing information to populate the form if it's a GET request
    $stmt = $conn->prepare("SELECT supplier_id, item_name, item_desc, item_type, unit_of_measure, 
    price_per_unit, expiry_date, pathology_stock, immunology_stock, microbiology_stock, 
    pathology_min_stock, pathology_max_stock, immunology_min_stock, immunology_max_stock, 
    microbiology_min_stock, microbiology_max_stock, weight_per_unit FROM items WHERE item_id = ?");
    $stmt->bind_param("s", $item_id);
    $stmt->execute();
    $stmt->bind_result($supplier_id, $item_name, $item_desc, $item_type, $unit_of_measure, 
    $price_per_unit, $expiry_date, $pathology_stock, $immunology_stock, $microbiology_stock, 
    $pathology_min_stock, $pathology_max_stock, $immunology_min_stock, $immunology_max_stock, 
    $microbiology_min_stock, $microbiology_max_stock, $weight_per_unit);
    $stmt->fetch();
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
    <title>Edit Stock</title>
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
                <h2 id="add-user-header">UPDATE STOCK</h2>
                <form action="stock level report edit.php?item_id=<?php echo $item_id; ?>" method="POST">
                    <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_id); ?>">
                    <?php if ($user_id == 1): ?>
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
                                <input type="number" min="0" step=0.01 class="num-input" name="price_per_unit" value="<?php echo htmlspecialchars($price_per_unit); ?>"
                                onkeypress="return (event.charCode != 8 && event.charCode == 0 || (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46))" required>
                            </div>
                        </div>

                        <div class="input-group form-field">
                            <label for="weight_per_unit">Weight Per Unit (kg)</label>
                            <input type="number" min="0" step=0.0001 class="num-input" name="weight_per_unit" value="<?php echo htmlspecialchars($weight_per_unit); ?>"
                            onkeypress="return (event.charCode != 8 && event.charCode == 0 || (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46))" required>
                        </div>
                        
                        <div class="input-group inline-input-group">
                            <label>Does it expire?</label><br>
                            <label>
                                <input type="radio" name="expiry_toggle" value="yes" onclick="toggleExpiryDate(true)"
                                <?php if (isset($expiry_date) && trim($expiry_date) !== '') echo 'checked'; ?> > Yes
                            </label>
                            <label>
                                <input type="radio" name="expiry_toggle" value="no" onclick="toggleExpiryDate(false)"
                                 <?php if (!isset($expiry_date) || trim($expiry_date) === '') echo 'checked'; ?> > No
                            </label>
                        </div>

                        <div class="input-group" id="expiry_date_group" style="visibility: <?php echo empty($expiry_date) ? 'hidden' : 'visible'; ?>;">
                            <label for="expiry_date">Expiry Date</label>
                            <input type="date" max="9999-12-31" class="num-input" name="expiry_date" 
                                value="<?php echo htmlspecialchars($expiry_date); ?>">
                        </div>
                    <?php else: ?>
                        <div class="input-group">
                            <label for="min_stock">Minimum Stock Level</label>
                            <input type="number" min="0" class="num-input" name="min_stock" value=
                            "<?php 
                                $min_stock_lab = '';
                                if ($user_id == 2) {
                                  $min_stock_lab = $pathology_min_stock;
                                } elseif ($user_id == 3) {
                                    $min_stock_lab = $immunology_min_stock;
                                } else {
                                    $min_stock_lab = $microbiology_min_stock;
                                }
                                echo htmlspecialchars($min_stock_lab);
                            ?>"
                            onkeypress="return (event.charCode != 8 && event.charCode == 0 || (event.charCode >= 48 && event.charCode <= 57))" required>
                        </div>

                        <div class="input-group" id="username_group">
                            <label for="max_stock">Maximum Stock Level</label>
                            <input type="number" min="0" class="num-input" name="max_stock" value=
                            "<?php 
                                $max_stock_lab = '';
                                if ($user_id == 2) {
                                  $max_stock_lab = $pathology_max_stock;
                                } elseif ($user_id == 3) {
                                    $max_stock_lab = $immunology_max_stock;
                                } else {
                                    $max_stock_lab = $microbiology_max_stock;
                                }
                                echo htmlspecialchars($max_stock_lab);
                            ?>"
                            onkeypress="return (event.charCode != 8 && event.charCode == 0 || (event.charCode >= 48 && event.charCode <= 57))" required>
                            <?php if (!empty($max_stock_error)): ?>
                                <span class="error" id="username_error"><?php echo $max_stock_error; ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                        <div class="button-container">
                            <button type="submit" class="save-button">UPDATE</button>
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