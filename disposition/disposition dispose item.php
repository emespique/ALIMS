<!-- stock level report add.php -->
<?php
// Start the session and include the database connection
require '../header.php';
require '../db_connection.php';

// session_start();
$session_laboratory = $_SESSION['laboratory'];
$session_user_id = $_SESSION['user_id'];

// Connect to the form_data database
$conn = connectToDatabase('form_data');

$quantity = 0;
$current_stock_value = 0;

$disposal_reason = $disposal_method = $disposal_date = $disposed_by = $comments = 
$post_laboratory = $unit_of_measure = $quantity_error = "";

// Handle form submission when the form is posted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if(!empty($_POST['quantity'])) {

        $item_id = htmlspecialchars(trim($_POST['item_id']));
        $quantity = htmlspecialchars(trim($_POST['quantity']));
        $disposal_reason = htmlspecialchars(trim($_POST['disposal_reason']));
        $disposal_method = htmlspecialchars(trim($_POST['disposal_method']));
        $disposal_date = htmlspecialchars(trim($_POST['disposal_date']));
        $disposed_by = htmlspecialchars(trim($_POST['disposed_by']));
        $comments = htmlspecialchars(trim($_POST['comments']));
    }

    $stock = "";
    if ($session_laboratory == "Pathology") {
        $stock = "pathology_stock";
    } else if ($session_laboratory == "Immunology") {
        $stock = "immunology_stock";
    } else if ($session_laboratory == "Microbiology") {
        $stock = "microbiology_stock";
    }

    $item_id = $_POST['item_id'];

    $stmt = $conn->prepare("SELECT $stock, unit_of_measure FROM items WHERE item_id = ?");
    $stmt->bind_param("s", $item_id);
    $stmt->execute();
    $stock_result = $stmt->get_result();

    if ($row = $stock_result->fetch_assoc()) {
        $current_stock_value = $row[$stock];
        $unit_of_measure = $row['unit_of_measure'];
    }

    if (!empty($quantity) && $quantity > $current_stock_value) {
        $quantity_error = "The quantity input can't be greater then the current stock.";
    }


    if (empty($quantity_error) && !empty($quantity)) {

        $stmt = $conn->prepare("INSERT INTO disposition (
            item_id, quantity, disposal_reason, disposal_method, disposal_date, disposed_by, comments, lab_disposed_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sissssss", $item_id, $quantity, $disposal_reason, $disposal_method, $disposal_date, $disposed_by, $comments, $session_laboratory);
        $stmt->execute();
        $stmt->close();

        header("Location: disposition dispose item final.php?qty=$quantity&lab_stock=$stock&item_id=$item_id");
        exit();
    }

}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disposition Form</title>
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
                <h2 id="add-user-header">DISPOSITION FORM</h2>
                <form action="disposition dispose item.php" method="POST">
                <input type="hidden" name="item_id" value="<?= htmlspecialchars($item_id) ?>">

                    <div class="form-row">
                        <div class="input-group form-field" id="username_group">
                            <label for="quantity">Quantity (Current: <?= $current_stock_value . ' ' . $unit_of_measure?>)</label>
                            <input type="number" min="1" step=1 class="num-input" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>"
                            onkeypress="return (event.charCode != 8 && event.charCode == 0 || (event.charCode >= 48 && event.charCode <= 57))" required>
                            <?php if (!empty($quantity_error)): ?>
                                <span class="error" id="username_error"><?php echo $quantity_error; ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="input-group form-field">
                            <label for="disposal_reason">Reason for Disposition</label>
                            <input type="text" maxlength="100" class="name-input" name="disposal_reason" 
                                value="<?php echo htmlspecialchars($disposal_reason); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="input-group form-field">
                            <label for="disposal_method">Disposition Method</label>
                            <input type="text" maxlength="100" class="name-input" name="disposal_method" 
                                value="<?php echo htmlspecialchars($disposal_method); ?>" required>
                        </div>

                        <div class="input-group form-field">
                            <label for="disposal_date">Date of Disposition</label>
                            <input type="date" max="9999-12-31" class="num-input" name="disposal_date" 
                                value="<?php echo htmlspecialchars($disposal_date); ?>">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="disposed_by">Dispositioned by</label>
                        <input type="text" maxlength="100" class="name-input" name="disposed_by" 
                            value="<?php echo htmlspecialchars($disposed_by); ?>" required>
                    </div>

                    <div class="input-group">
                        <label for="comments">Comments</label>
                        <input type="text" maxlength="100" class="desc-input" name="comments" value="<?php echo htmlspecialchars($comments); ?>" required>
                    </div>

                    <div class="button-container">
                        <button type="submit" class="save-button">ADD</button>
                        <button type="button" class="cancel-button" onclick="window.location.href='disposition.php';">CANCEL</button>
                    </div>
                </form>
                
               
            </div>
        </div>

    </div>
    
    <?php include '../footer.php'; ?>

    <script>
    
    function addItem() {
        document.getElementById('shoppingCartModal').style.display = 'flex';
    }

    function closeAddItemModal() {
        // Hide the modal
        document.getElementById('shoppingCartModal').style.display = 'none';
    }

    function chooseLab() {
        document.getElementById('chooseLabModal').style.display = 'flex';
    }

    function closeChooseLabModal() {
        // Hide the modal
        document.getElementById('chooseLabModal').style.display = 'none';
    }


    function deleteItem(itemId) {
        document.getElementById('modalItemId').value = itemId;
        document.getElementById('deleteItemModal').style.display = 'flex';
    }

    function closeDeleteItemModal() {
        // Hide the modal
        document.getElementById('deleteItemModal').style.display = 'none';
    }


    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.dropdown-item').forEach(el => el.classList.remove('selected'));

            // Add selected class to clicked item
            this.classList.add('selected');

            // Set hidden input value
            document.getElementById('selectedItemId').value = this.dataset.id;
        });
    });

    </script>

</body>
</html>