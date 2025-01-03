<!-- stock level report.php -->
<?php
// Start the session and include the necessary files
require '../header.php';
require '../db_connection.php';

// Connect to the user_management database
$conn = connectToDatabase('form_data');

// show all items
$stmt = $conn->prepare("SELECT * FROM stock_level");
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Level Report</title>
    <link rel="stylesheet" type="text/css" href="../css/stocklevelreport.css?version=51">
    <link rel="stylesheet" type="text/css" href="../css/accounts.css?version=51">
    <link rel="stylesheet" type="text/css" href="../css/main.css?version=51">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <script defer src="../js/user_validation.js"></script>
</head>
<body>
    <?php include '../header.php'; ?>

    <div class="content">
        <h1 class="table-title">Stock Level Report</h1>
        <div class="add-item">
            <button onclick="window.location.href='add_item_stock.php'">Add Item</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Item No.</th>
                    <th>Item Description</th>
                    <th>Stock on Hand</th>
                    <th>Minimum Stock Level</th>
                    <th>Maximum Stock Level</th>
                    <th>Status</th>
                    <th>Action Required</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_desc']); ?></td>
                        <td><?php echo htmlspecialchars($row['stock_on_hand']); ?></td>
                        <td><?php echo htmlspecialchars($row['min_stock']); ?></td>
                        <td><?php echo htmlspecialchars($row['max_stock']); ?></td>
                        <td><?php echo htmlspecialchars($row['stock_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['action_req']); ?></td>
                        <td>
                            <div class="actions">
                                <button class="delete-button" onclick="deleteUser(<?php echo $row['id']; ?>)">Delete</button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="modal" id="deleteModal" style="display: none;">
        <div class="modal-content">
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete this item?</p>
            <form id="deleteForm" action="delete_stock_item.php" method="POST">
                <input type="hidden" name="user_id" id="deleteUserId">
                <div class="modal-buttons">
                    <button type="submit" class="delete-button">Delete</button>
                    <button type="button" class="cancel-button" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

        <div class="button-container">
            <button class="generate-btn">Generate</button>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>
