<!-- purchase order.php -->
<?php
// Start the session and include the necessary files
require '../db_connection.php';

session_start();

// Connect to the user_management database
$conn = connectToDatabase('form_data');
$conn2 = connectToDatabase('user_management');

$PO_no = $_GET['PO_no'];

$session_user_id = $_SESSION['user_id'];

// Get specific lab_id
$stmt = $conn->prepare("SELECT lab_id FROM purchase_order WHERE PO_no = ?");
$stmt->bind_param("s", $PO_no);
$stmt->execute();
$lab_id_result = $stmt->get_result();

$lab_id = 0;
if ($row = $lab_id_result->fetch_assoc()) {
    $lab_id = $row['lab_id'];
}

$stmt = $conn->prepare("SELECT lab_name FROM lab_connection WHERE lab_id = ?");
$stmt->bind_param("s", $lab_id);
$stmt->execute();
$lab_name_result = $stmt->get_result();

$lab_name = "";
if ($row = $lab_name_result->fetch_assoc()) {
    $lab_name = $row['lab_name'];
}

// User end view
$my = "";
if ($session_user_id != 1) {
    $my = "My ";
}

// Get supplier details
$sql = "
    SELECT p.PO_no, s.*
    FROM purchase_order p
    JOIN suppliers s ON p.supplier_id = s.supplier_id
    WHERE p.PO_no = ?;
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $PO_no);
$stmt->execute();
$suppliers_result = $stmt->get_result();
$suppliers_row = $suppliers_result->fetch_assoc();

// Get lab details
$sql = "
    SELECT p.PO_no, l.*
    FROM purchase_order p
    JOIN laboratories l ON p.lab_id = l.lab_id
    WHERE p.PO_no = ?;
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $PO_no);
$stmt->execute();
$lab_result = $stmt->get_result();
$lab_row = $lab_result->fetch_assoc();

// Get user details
$user_id = null;
$stmt = $conn->prepare("SELECT user_id FROM purchase_order WHERE PO_no = ?");
$stmt->bind_param("s", $PO_no);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();

$stmt = $conn2->prepare("SELECT first_name, last_name, middle_initial FROM users WHERE id = ?");
$stmt->bind_param("s", $user_id); // "s" because item_id is a string
$stmt->execute();
$users_result = $stmt->get_result();
$users_row = $users_result->fetch_assoc();

// Get tax value
$stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_name = 'tax'");
$stmt->execute();
$tax_result = $stmt->get_result();

$tax_rate = 0;
if ($row = $tax_result->fetch_assoc()) {
    $tax_rate = (float)$row['setting_value'];
}
$tax_rate_percent = 100 * $tax_rate;

// Get PO details
$stmt = $conn->prepare("SELECT * FROM purchase_order WHERE PO_no = ?");
$stmt->bind_param("s", $PO_no);
$stmt->execute();
$PO_result = $stmt->get_result();
$PO_row = $PO_result->fetch_assoc();

// Get PO items details
$sql = "
    SELECT poi.item_id, poi.quantity, poi.unit_price, poi.total_price,
           i.item_id, i.item_name, i.item_desc
    FROM purchase_order_items poi
    JOIN items i ON poi.item_id = i.item_id
    WHERE poi.PO_no = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $PO_no);
$stmt->execute();
$PO_result_items = $stmt->get_result();

$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order</title>
    <link rel="stylesheet" type="text/css" href="../css/stocklevelreport.css?version=<?php echo time() - 1000000000; ?>">
    <!-- <link rel="stylesheet" type="text/css" href="../css/purchaseorder.css?version=<?php echo time() - 1000000000; ?>"> -->
    <link rel="stylesheet" type="text/css" href="../css/accounts.css?version=<?php echo time() - 1000000000; ?>">
    <link rel="stylesheet" type="text/css" href="../css/main.css?version=<?php echo time() - 1000000000; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <script defer src="../js/user_validation.js"></script>
</head>
<body>
    <?php include '../header.php'; ?>

    <div class="content">
        <h1 class="table-title"><?php echo $my ?>Purchase Order (Details)</h1>
        <div class="add-item">
            <button onclick="window.location.href='purchase order.php'">Go Back ←</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Item no.</th>
                    <th>Item name</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Price (PHP)</th>
                    <th>Total Price (PHP)</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $PO_result_items->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['item_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_desc']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_desc']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['unit_price']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_price']); ?></td>
                    </tr>
                <?php endwhile; ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><hr style="margin: auto; border: 1px solid black;"></td>
                    <td><hr style="margin: auto; border: 1px solid black;"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><strong>Subtotal: </strong></td>
                    <td><?php echo sprintf('%.2f', $PO_row['subtotal']); ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><strong>Tax (<?php echo $tax_rate_percent?>%): </strong></td>
                    <td><?php echo sprintf('%.2f', $PO_row['tax']); ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><strong>Shipping Cost: </strong></td>
                    <td><?php echo sprintf('%.2f', $PO_row['shipping_cost']); ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><strong>Grand Total: </strong></td>
                    <td><?php echo sprintf('%.2f', $PO_row['grand_total']); ?></td>
                </tr>
            </tbody>
        </table>


        <div class="info-columns-view-stock">
            <div class="column-view-stock">
                <h2 class="table-title">Supplier Details</h2>
                <p><strong>ID: </strong><?php echo htmlspecialchars($suppliers_row['supplier_id']); ?></p>
                <p><strong>Name: </strong><?php echo htmlspecialchars($suppliers_row['supplier_name']); ?></p>
                <p><strong>Address: </strong><?php echo htmlspecialchars($suppliers_row['supplier_address']); ?></p>
                <p><strong>Phone Number: </strong><?php echo htmlspecialchars($suppliers_row['supplier_phone_no']); ?></p>
                <p><strong>Email: </strong><?php echo htmlspecialchars($suppliers_row['supplier_email']); ?></p>
                <p><strong>Contact Person: </strong><?php echo htmlspecialchars($suppliers_row['supplier_contact_person']); ?></p>
            </div>

            <div class="column-view-stock">
                <h2 class="table-title">Laboratory Details</h2>
                <p><strong>ID: </strong><?php echo htmlspecialchars($lab_row['lab_id']); ?></p>
                <p><strong>Name: </strong><?php echo htmlspecialchars($lab_row['lab_name']); ?></p>
                <p><strong>Address: </strong><?php echo htmlspecialchars($lab_row['lab_address']); ?></p>
                <p><strong>Phone Number: </strong><?php echo htmlspecialchars($lab_row['lab_phone_no']); ?></p>
                <p><strong>Email: </strong><?php echo htmlspecialchars($lab_row['lab_email']); ?></p>
                <p><strong>Contact Person: </strong><?php echo htmlspecialchars($lab_row['lab_contact_person']); ?></p>
            </div>
        </div>
        <br>
        <p><strong>Purchase initiated by: </strong>
            <?php 
            echo htmlspecialchars($users_row['first_name']) . ' ' .
            htmlspecialchars($users_row['middle_initial']) . ' ' .
            htmlspecialchars($users_row['last_name']);
            if (in_array($user_id, [2,3,4])) {
                echo ' (' . htmlspecialchars($lab_name) . ' Admin)';
            } else if ($user_id == 1) {
                echo " (Head Admin)";
            }
            ?>
        </p>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>