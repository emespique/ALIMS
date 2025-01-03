<!-- purchase order.php -->
<?php
// Start the session and include the necessary files
require '../header.php';
require '../db_connection.php';

// Connect to the user_management database
$conn = connectToDatabase('form_data');


// show all items
$stmt = $conn->prepare("SELECT * FROM purchase_order");
$stmt->execute();
$result = $stmt->get_result();

$stmt = $conn->prepare("SELECT * FROM purchase_order_items WHERE PO_no = ?");
$stmt->bind_param("s", $_GET['id']); // "s" means the parameter is a string
$stmt->execute();
$result_items = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order</title>
    <link rel="stylesheet" type="text/css" href="../css/purchaseorder.css?version=51">
    <link rel="stylesheet" type="text/css" href="../css/accounts.css?version=51">
    <link rel="stylesheet" type="text/css" href="../css/main.css?version=51">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <script defer src="../js/user_validation.js"></script>
</head>
<body>
    <?php include '../header.php'; ?>

    <div class="content">
        <h1 class="table-title">Purchase Order Form (details)</h1>
        <div class="add-item">
            <button onclick="window.location.href='purchase order.php'">Go Back ←</button>
        </div>
        <table class="table_PO">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Price (PHP)</th>
                    <th>Total Price (PHP)</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result_items->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['item_desc']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['unit_price']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_price']); ?></td>
                        <td>
                            <div class="actions">
                                <button class="delete-button">Delete</button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>

    <?php include '../footer.php'; ?>
</body>
</html>