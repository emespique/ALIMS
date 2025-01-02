<!-- add_item_stock.php -->
<?php
// Start the session and include the database connection
require '../header.php';
require '../db_connection.php';

// Connect to the form_data database
$conn = connectToDatabase('form_data');

// Initialize variables and error messages
$item_desc = $stock_on_hand = $min_stock = $max_stock = $stock_status = $action_req = "";

// Handle form submission when the form is posted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve and sanitize the form data
    $item_desc = htmlspecialchars(trim($_POST['item_desc']));
    $stock_on_hand = htmlspecialchars(trim($_POST['stock_on_hand']));
    $min_stock = htmlspecialchars(trim($_POST['min_stock']));
    $max_stock = htmlspecialchars(trim($_POST['max_stock']));
    $stock_status = htmlspecialchars(trim($_POST['stock_status']));
    $action_req = htmlspecialchars(trim($_POST['action_req']));

    // Prepare an insert statement
    $stmt = $conn->prepare("INSERT INTO stock_level (item_desc, stock_on_hand, min_stock, max_stock, stock_status, action_req) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siiiss", $item_desc, $stock_on_hand, $min_stock, $max_stock, $stock_status, $action_req);

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
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <link rel="stylesheet" type="text/css" href="../css/accounts.css">
    <link rel="stylesheet" type="text/css" href="../css/input_design.css?version=51"> 
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script defer src="../js/user_validation.js"></script>
</head>
<body>
    <?php include '../header.php'; ?> 
    
    <div class="content">
        <div class="add-container">
            <div class="information">
                <h2 id="add-user-header">ADD ITEM</h2>
                <form action="add_item_stock.php" method="POST">
                    <div class="input-group">
                        <label for="item_desc">Item Description</label>
                        <input type="text" class="desc-input" name="item_desc" value="<?php echo htmlspecialchars($item_desc); ?>">
                    </div>

                    <div class="input-group">
                        <label for="stock_on_hand">Stock on Hand</label>
                        <input type="number" min="0" class="num-input" name="stock_on_hand" value="<?php echo htmlspecialchars($stock_on_hand); ?>"
                        onkeypress="return (event.charCode != 8 && event.charCode == 0 || (event.charCode >= 48 && event.charCode <= 57))">
                    </div>

                    <div class="input-group">
                        <label for="min_stock">Minimum Stock Level</label>
                        <input type="number" min="0" class="num-input" name="min_stock" value="<?php echo htmlspecialchars($min_stock); ?>"
                        onkeypress="return (event.charCode != 8 && event.charCode == 0 || (event.charCode >= 48 && event.charCode <= 57))">
                    </div>

                    <div class="input-group">
                        <label for="max_stock">Maximum Stock Level</label>
                        <input type="number" min="0" class="num-input" name="max_stock" value="<?php echo htmlspecialchars($max_stock); ?>"
                        onkeypress="return (event.charCode != 8 && event.charCode == 0 || (event.charCode >= 48 && event.charCode <= 57))">
                    </div>
                    
                    <div class="input-group">
                        <label for="stock_status">Status</label>
                        <select class="info-dropdown" name="stock_status" required>
                            <option value="">Select Status</option>
                            <option value="Sufficient" <?php if ($stock_status == "Sufficient") echo "selected"; ?>>Sufficient</option>
                            <option value="Below Reorder Level" <?php if ($stock_status == "Below Reorder Level") echo "selected"; ?>>Below Reorder Level</option>
                            <option value="Critical Stockout" <?php if ($stock_status == "Critical Stockout") echo "selected"; ?>>Critical Stockout</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="action_req">Action Required</label>
                        <select class="info-dropdown" name="action_req" required>
                            <option value="">Select Action</option>
                            <option value="None" <?php if ($action_req == "None") echo "selected"; ?>>None</option>
                            <option value="Reorder Immediately" <?php if ($action_req == "Reorder Immediately") echo "selected"; ?>>Reorder Immediately</option>
                            <option value="Urgent Order Needed" <?php if ($action_req == "Urgent Order Needed") echo "selected"; ?>>Urgent Order Needed</option>
                            <option value="Reorder Soon" <?php if ($action_req == "Reorder Soon") echo "selected"; ?>>Reorder Soon</option>
                            <option value="Monitor Usage" <?php if ($action_req == "None") echo "selected"; ?>>Monitor Usage</option>
                        </select>
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
</body>
</html>