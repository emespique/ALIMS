<!-- stock level report add.php -->
<?php
// Start the session and include the database connection
// require '../header.php';
require '../db_connection.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['laboratory_choice'] = $_POST['laboratory_choice'];
}

$user_id = $_SESSION['user_id'];

$laboratory = "";
if ($user_id == 1) {
    $laboratory = $_SESSION['laboratory_choice'];
} else {
    $laboratory = $_SESSION['laboratory'];
}

$supplier_id = $_SESSION['supplier_id'];

$selected_items = array_filter($_SESSION['items_list'], function ($item) {
    return $item['is_selected'] === true;
});

// Connect to the form_data database
$conn = connectToDatabase('form_data');

// Get tax value
$stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_name = 'tax'");
$stmt->execute();
$tax_result = $stmt->get_result();

$tax_rate = 0;
if ($row = $tax_result->fetch_assoc()) {
    $tax_rate = (float)$row['setting_value'];
}
$tax_rate_percent = 100 * $tax_rate;

$stmt = $conn->prepare("SELECT * FROM suppliers WHERE supplier_id = ?");
$stmt->bind_param("s", $supplier_id);
$stmt->execute();
$supplier_result = $stmt->get_result();

// Get lab_id
$stmt = $conn->prepare("SELECT lab_id FROM lab_connection WHERE lab_name = ?");
$stmt->bind_param("s", $laboratory);
$stmt->execute();
$lab_id_result = $stmt->get_result();

$lab_id = 0;
if ($row = $lab_id_result->fetch_assoc()) {
    $lab_id = $row['lab_id'];
}

$stmt = $conn->prepare("SELECT lower_bound_weight_kg, lower_bound_price FROM suppliers_shipping_rates WHERE supplier_id = ?");
$stmt->bind_param("s", $supplier_id);
$stmt->execute();
$suppliers_shipping_rates_result = $stmt->get_result();

$subtotal = 0;
$grand_total_weight = 0;

foreach ($selected_items as &$item) {
    $total_weight = $item['unit_weight'] * $item['quantity'];
    $total_price = $item['unit_price'] * $item['quantity'];

    $item['total_weight'] = $total_weight;
    $item['total_price'] = sprintf('%.2f', $total_price);
    
    $subtotal += $total_price;
    $grand_total_weight += $total_weight;
}
unset($item); // best practice to unset reference variable
$_SESSION['selected_items'] = $selected_items;

// Calculatation

$shipping_cost = 0;
while ($row = $suppliers_shipping_rates_result->fetch_assoc()) {
    if ($row['lower_bound_weight_kg'] <= $grand_total_weight) {
        $shipping_cost = $row['lower_bound_price'];
    } else {
        
    }
}

$tax = (float)($subtotal * $tax_rate);
$grand_total = $subtotal + $tax + $shipping_cost;

// Prepare array for purchase_order db
//
// Table needs:
// PO_no, supplier_id, lab_id, user_id, status, 
// subtotal, tax, shipping_cost, grand_total, grand_total_weight, 
// admin_id, head_admin_id

$purchase_order = [
    'PO_no' => null,
    'supplier_id' => $supplier_id,
    'lab_id' => $lab_id,
    'user_id' => $user_id,
    'status' => "Submitted",
    'subtotal' => $subtotal,
    'tax' => $tax,
    'shipping_cost' => $shipping_cost,
    'grand_total' => $grand_total,
    'grand_total_weight' => $grand_total_weight,
    'admin_id' => null,
    'head_admin_id' => null,
    'admin_status' => null,
    'head_admin_status' => null,
    'final_status' => null,
];

$_SESSION['purchase_order'] = $purchase_order;

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
    
    <div class="content">
        <div class="add-container">
            <div class="information">
                <h2 id="add-user-header">PURCHASE ORDER DETAILS</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Item No.</th>
                            <th>Item Name</th>
                            <th>Item Description</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th style="width: 90px;">Unit Price (PHP)</th>
                            <th style="width: 90px;">Total Price (PHP)</th>
                            <th style="width: 90px;">Unit Weight (kg)</th>
                            <th style="width: 90px;">Total Weight (kg)</th>        
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($selected_items as $item) {
                                if ($item['is_selected'] === true) {
                                $item_id = htmlspecialchars($item['item_id'], ENT_QUOTES); // prevent JS injection
                                echo "
                                    <tr>
                                        <td>{$item['item_id']}</td>
                                        <td>{$item['item_name']}</td>
                                        <td>{$item['item_desc']}</td>
                                        <td>{$item['quantity']}</td>
                                        <td>{$item['unit_of_measure']}</td>
                                        <td>{$item['unit_price']}</td>
                                        <td>{$item['total_price']}</td>
                                        <td>" . rtrim(rtrim(sprintf('%.4f', $item['unit_weight']), '0'), '.') . "</td>
                                        <td>" . rtrim(rtrim(sprintf('%.4f', $item['total_weight']), '0'), '.') . "</td>
                                    </tr>
                                ";
                            }
                        }
                        ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><hr style="margin: auto; border: 1px solid black;"></td>
                            <td><hr style="margin: auto; border: 1px solid black;"></td>
                            <td><hr style="margin: auto; border: 1px solid black;"></td>
                            <td><hr style="margin: auto; border: 1px solid black;"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><strong>Subtotal:</strong></td>
                            <td><?php echo sprintf('%.2f', $subtotal) ?></td>
                            <td><strong>Net Weight:</strong></td>
                            <td><?php echo $grand_total_weight ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><strong>Tax (<?php echo $tax_rate_percent?>%):</strong></td>
                            <td><?php echo sprintf('%.2f', $tax) ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><strong>Shipping Cost:</strong></td>
                            <td><?php echo sprintf('%.2f', $shipping_cost) ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><strong>Grand Total:</strong></td>
                            <td><?php echo sprintf('%.2f', $grand_total) ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>

                <br>
                <!-- <div class="input-group">
                    <h3>COST BREAKDOWN</h3> 
                    <p><strong>Subtotal: </strong><?php echo sprintf('%.2f', $subtotal)?></p>
                    <p><strong>Tax (<?php echo $tax_rate_percent?>%): </strong><?php echo sprintf('%.2f', $tax)?></p>
                    <p><strong>Shipping Cost: </strong><?php echo sprintf('%.2f', $shipping_cost)?></p>
                    <p><strong>Grand Total: </strong><?php echo sprintf('%.2f', $grand_total)?></p>
                </div> -->

                <div class="button-container">
                    <button type="button" class="save-button" onclick="confirmOrder()">ORDER</button>
                    <button type="button" class="cancel-button" onclick="window.location.href='purchase order shopping cart.php';">GO BACK</button>
                </div>

            </div>
        </div>

        <div class="modal" id="confirmOrderModal" style="display: none;">
            <div class="modal-content">
                <h2>Confirm Order</h2>
                <p>Are you sure you want to place this order?</p>
                <form id="deleteForm" action="purchase order add PO.php" method="POST">
                    <!-- <input type="hidden" name="item_id" id="modalItemId" required> -->

                    <div class="modal-buttons">
                        <button type="submit" class="restore-button">Confirm</button>
                        <button type="button" class="delete-button" onclick="closeConfirmOrderModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>

    function confirmOrder() {
        document.getElementById('confirmOrderModal').style.display = 'flex';
    }

    function closeConfirmOrderModal() {
        // Hide the modal
        document.getElementById('confirmOrderModal').style.display = 'none';
    }


    </script>

</body>
</html>