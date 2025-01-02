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
        <h1 class="table-title">Purchase Order Form</h1>
        <table class="user-table">
            <thead>
                <tr>
                    <th>PO No.</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Total Price(PHP)</th>
                    <th>Details</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['PO_no']); ?></td>
                        <td><?php echo htmlspecialchars($row['PO_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['PO_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['grand_total']); ?></td>
                        <td>click here</td>
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