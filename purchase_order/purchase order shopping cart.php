<!-- stock level report add.php -->
<?php
// Start the session and include the database connection
// require '../header.php';
require '../db_connection.php';

session_start();

$supplier_id_session = $_SESSION['supplier_id'];
$item_shopping_cart = $_SESSION['items_list'];

// Connect to the form_data database
$conn = connectToDatabase('form_data');

$stmt = $conn->prepare("SELECT * FROM suppliers");
$stmt->execute();
$supplier_result = $stmt->get_result();

if (empty($_SESSION['laboratory_choice'])) {
    $laboratory_choice = null;
} else {
    $laboratory_choice = $_SESSION['laboratory_choice'];
}

$supplier_name_session = "";
$dash = "";
if (!empty($supplier_id_session)) {
    $stmt = $conn->prepare("SELECT supplier_id, supplier_name FROM suppliers WHERE supplier_id = ?");
    $stmt->bind_param("s", $supplier_id_session);
    $stmt->execute();
    $selected_supplier_result = $stmt->get_result();

    $row = $selected_supplier_result->fetch_assoc();
    $supplier_name_session = $row['supplier_name'];
    $dash = "-";
}

$user_id = $_SESSION['user_id'];

$supplier_id = $item_id = '';

$quantity = 1;

// Handle form submission when the form is posted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // For purchase_order
    
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Order</title>
    <link rel="stylesheet" type="text/css" href="../css/main.css?version=<?php echo time() - 1000000000; ?>">
    <link rel="stylesheet" type="text/css" href="../css/accounts.css?version=<?php echo time() - 1000000000; ?>">
    <link rel="stylesheet" type="text/css" href="../css/input_design.css?version=<?php echo time() - 1000000000; ?>"> 
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script defer src="../js/user_validation.js"></script>
</head>
<body style="background-color: lightgray;">
    <?php // include '../header.php'; ?> 
    
    <div class="content" style="width: 60%;">
        <div class="add-container">
            <div class="information">
                <h2 id="add-user-header">MAKE PURCHASE ORDER</h2>
                <div class="input-group" style="display: flex; align-items: center; gap: 50px;">
                    <button type="button" class="add-button" onclick="chooseSupplier()">Choose Supplier</button>
                    <p><strong>Supplier: </strong><?php echo "$supplier_id_session $dash $supplier_name_session"?></p>
                </div>

                <br>
                
                <?php if (!empty($supplier_id_session)): ?>
                <div class="input-group" id="add_item">
                    <button type="button" class="add-button" onclick="addItem()">Add item</button>
                </div><br>
                <table>
                    <thead>
                        <tr>
                            <th>Item No.</th>
                            <th>Item Name</th>
                            <th>Item Description</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($item_shopping_cart as $item) {
                                if ($item['is_selected'] === true) {
                                $item_id = htmlspecialchars($item['item_id'], ENT_QUOTES); // prevent JS injection
                                echo "
                                    <tr>
                                        <td>{$item['item_id']}</td>
                                        <td>{$item['item_name']}</td>
                                        <td>{$item['item_desc']}</td>
                                        <td>{$item['quantity']}</td>
                                        <td>{$item['unit_of_measure']}</td>
                                        <td>
                                            <div class=\"input-group\">
                                                <button type=\"button\" class=\"delete-button\" onclick=\"deleteItem('$item_id')\">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                ";
                            }
                        }
                        ?>
                    </tbody>
                </table>
                
                    <?php if ($user_id == 1): ?>
                        <br><br>
                        <div class="input-group" style="display: flex; align-items: center; gap: 50px;">
                            <button type="button" class="add-button" onclick="chooseLab()">Pay for Laboratory</button>
                            <p><strong>Laboratory: </strong><?php echo "$laboratory_choice"?></p>
                        </div>
                    <?php endif; ?>

                <?php endif ?>

                <br><br>

                <div class="button-container">
                    <button 
                        type="button" 
                        class="save-button" 
                        onclick="validateAndProceed()" 
                        <?php
                            $cart_empty = '1';
                            if (!empty($supplier_id_session)) {
                                foreach ($item_shopping_cart as $item) {
                                    if ($item['is_selected'] === true) {
                                        $cart_empty = '0';
                                        break;
                                    }
                                }
                            }
                            $lab_empty = '0';
                            if ($user_id == 1) {
                                $lab_empty = empty($laboratory_choice) ? '1' : '0';
                            }
                        ?>
                        data-cart-empty="<?= $cart_empty ?>"
                        data-lab-empty="<?= $lab_empty ?>"
                        data-supplier-empty="<?= empty($supplier_id_session) ? '1' : '0' ?>"
                    >
                        PROCEED TO CHECKOUT
                    </button>
                    <button type="button" class="cancel-button" onclick="window.location.href='purchase order.php';">CANCEL</button>
                </div>
            </div>
        </div>

        <div class="modal" id="supplierModal" style="display: none;">
            <div class="modal-content">
                <h3>Choose Supplier</h3>
                <br>
                <form id="deleteForm" action="purchase order choose supplier.php" method="POST">
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
                    <div class="modal-buttons">
                        <button type="submit" class="restore-button">Choose</button>
                        <button type="button" class="delete-button" onclick="closeSupplierModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal" id="shoppingCartModal" style="display: none;">
            <div class="modal-content">
                <h3>Add item</h3>
                <form id="deleteForm" action="purchase order add item.php" method="POST">
                    <div class="custom-dropdown">
                        <input type="hidden" name="item_id" id="selectedItemId" required>
                        <div class="dropdown-list" id="dropdownList">
                            <?php 
                                foreach ($item_shopping_cart as $item):
                                    if ($item['is_selected'] === false) {
                                        $i_id = $item['item_id'];
                                        $i_name = $item['item_name'];
                                        $i_desc = $item['item_desc'];
                                        $i_unit = $item['unit_of_measure']
                            ?>
                                <div class="dropdown-item" data-id="<?= $i_id ?>">
                                    <strong><?= htmlspecialchars($i_id) ?> - <?= htmlspecialchars($i_name) ?> </strong><br>
                                    <small><?= htmlspecialchars($i_desc) ?> / Unit: <?= htmlspecialchars($i_unit) ?></small>
                                </div>
                            <?php } endforeach; ?>
                        </div>
                    </div>
                    <div class="input-group" style="display: flex; justify-content: center; align-items: center; gap: 8px; ">
                        <label for="quantity">Quantity:</label>
                        <input type="number" min="1" class="num-input" style="width: 50%;" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>"
                        onkeypress="return (event.charCode != 8 && event.charCode == 0 || (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46))" required>
                    </div>

                    <div class="modal-buttons">
                        <button type="submit" class="restore-button">Select</button>
                        <button type="button" class="delete-button" onclick="closeAddItemModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal" id="chooseLabModal" style="display: none;">
            <div class="modal-content">
                <h3>Choose Laboratory</h3>
                <br>
                <form id="deleteForm" action="purchase order choose lab.php" method="POST">
                    <select class="info-dropdown" name="laboratory_choice" required>
                        <option value="">Select Laboratory</option>
                        <option value="Pathology" <?php if ($laboratory_choice == "Pathology") echo "selected"; ?>>Pathology</option>
                        <option value="Immunology" <?php if ($laboratory_choice == "Immunology") echo "selected"; ?>>Immunology</option>
                        <option value="Microbiology" <?php if ($laboratory_choice == "Microbiology") echo "selected"; ?>>Microbiology</option>
                    </select>
                    <div class="modal-buttons">
                        <button type="submit" class="restore-button">Choose</button>
                        <button type="button" class="delete-button" onclick="closeChooseLabModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal" id="deleteItemModal" style="display: none;">
            <div class="modal-content">
                <h3>Are you sure you want to remove this item?</h3>
                <form id="deleteForm" action="purchase order delete item.php" method="POST">
                    <input type="hidden" name="item_id" id="modalItemId" required>

                    <div class="modal-buttons">
                        <button type="submit" class="delete-button">Delete</button>
                        <button type="button" class="cancel-button" onclick="closeDeleteItemModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>


    </div>
    
    <?php // include '../footer.php'; ?>

    <script>
    document.querySelector('select[name="supplier_id"]').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const base = selectedOption.getAttribute('data-base') || '—';
        const perKg = selectedOption.getAttribute('data-perkg') || '—';

        document.getElementById('base-cost').textContent = base;
        document.getElementById('per-kg-cost').textContent = perKg;
    });

    function chooseSupplier() {
        document.getElementById('supplierModal').style.display = 'flex';
    }

    function closeSupplierModal() {
        // Hide the modal
        document.getElementById('supplierModal').style.display = 'none';
    }

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


    function validateAndProceed() {
        const btn = document.querySelector('.save-button');

        const cartEmpty = btn.getAttribute('data-cart-empty') === '1';
        const supplierEmpty = btn.getAttribute('data-supplier-empty') === '1';
        const labEmpty = btn.getAttribute('data-lab-empty') === '1';

        if (supplierEmpty) {
            alert("Please select a supplier");
        } else if (cartEmpty) {
            alert("You must have at least one item in your cart");
        } else if (labEmpty) {
            alert("Please select a laboratory");
        } else {
            window.location.href = 'purchase order checkout.php';
        }
    }

    </script>

</body>
</html>