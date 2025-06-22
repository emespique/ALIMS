<!-- stock level report.php -->
<?php
// Start the session and include the necessary files
require '../header.php';
require '../db_connection.php';

// Connect to the form database
$conn = connectToDatabase('form_data');

$user_id = $_SESSION['user_id'];
$session_laboratory = $_SESSION['laboratory'];

// Get all items
$stmt = $conn->prepare("SELECT *, (pathology_stock + immunology_stock + microbiology_stock) AS total_stock FROM items WHERE is_deleted = 0 ORDER BY item_id");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Level Report</title>
    <link rel="stylesheet" type="text/css" href="../css/stocklevelreport.css?version=<?php echo time() - 1000000000; ?>">
    <link rel="stylesheet" type="text/css" href="../css/accounts.css?version=<?php echo time() - 1000000000; ?>">
    <link rel="stylesheet" type="text/css" href="../css/main.css?version=<?php echo time() - 1000000000; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <script defer src="../js/user_validation.js"></script>
</head>
<body>
    <?php include '../header.php'; ?>

    <div class="content">
        <h1 class="table-title">Stock Level Report</h1>
         
        <?php if ($user_id == 1): ?>
        <div class="add-item" style="display: flex; gap: 20px;">
            <button onclick="window.location.href='stock level report add.php'">Add Item</button>
            <button onclick="window.location.href='stock level report view deleted.php'">View Deleted Items</button>
        </div>
        <?php endif; ?>
        <table <?php ($_SESSION['role'] == 'user') ? 'style="width:50%"' : ''; ?>>
            <thead>
                <tr>
                    <th>Item No.</th>
                    <th>Item Name</th>
                    <th>Item Description</th>
                    <th><?php if ($user_id == 1) echo "Total"; ?> Stock on Hand</th>
                    <?php if ($user_id == 2 || $user_id == 3 || $user_id == 4): ?>
                        <th>Minimum Stock <br> Level</th>
                        <th>Maximum Stock <br> Level</th>
                        <th>Status</th>
                        <th>Action <br> Required</th>
                    <?php endif; ?>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['item_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_desc']); ?></td>
                        <?php if ($user_id == 1): ?>
                            <td class="stock-cell">
                                <div class="stock-wrapper">
                                    <span><?php echo htmlspecialchars($row['total_stock']); ?></span>
                                    <button class="toggle-details" onclick="toggleDetails(this)">&#x25B6;</button>

                                    <div class="details-flyout" style="display: none;">
                                        <h4><u>Stock Details</u></h4>
                                        <p><strong>Pathology:</strong> <?php echo htmlspecialchars($row['pathology_stock']); ?></p>
                                        <p><strong>Immunology:</strong> <?php echo htmlspecialchars($row['immunology_stock']); ?></p>
                                        <p><strong>Microbiology:</strong> <?php echo htmlspecialchars($row['microbiology_stock']); ?></p>
                                    </div>
                                </div>
                            </td>
                        <?php elseif ($session_laboratory == "Pathology"): ?>
                            <td><?php $stock_in_hand = $row['pathology_stock']; echo htmlspecialchars($stock_in_hand); ?></td>
                        <?php elseif ($session_laboratory == "Immunology"): ?>
                            <td><?php $stock_in_hand = $row['immunology_stock']; echo htmlspecialchars($stock_in_hand); ?></td>
                        <?php elseif ($session_laboratory == "Microbiology"): ?>
                            <td><?php $stock_in_hand = $row['microbiology_stock']; echo htmlspecialchars($stock_in_hand); ?></td>
                        <?php endif; ?>
                        <?php if ($user_id == 2): ?>
                            <td><?php $min_stock = $row['pathology_min_stock']; echo htmlspecialchars($min_stock); ?></td>
                            <td><?php $max_stock = $row['pathology_max_stock']; echo htmlspecialchars($max_stock); ?></td>
                        <?php elseif ($user_id == 3): ?>
                            <td><?php $min_stock = $row['immunology_min_stock']; echo htmlspecialchars($min_stock); ?></td>
                            <td><?php $max_stock = $row['immunology_max_stock']; echo htmlspecialchars($max_stock); ?></td>
                        <?php elseif ($user_id == 4): ?>
                            <td><?php $min_stock = $row['microbiology_min_stock']; echo htmlspecialchars($min_stock); ?></td>
                            <td><?php $max_stock = $row['microbiology_max_stock']; echo htmlspecialchars($max_stock); ?></td>
                        <?php endif; ?>
                        <?php if ($user_id == 2 || $user_id == 3 || $user_id == 4): ?>
                            <td>
                                <?php if ($stock_in_hand >= $min_stock && $stock_in_hand != 0): ?>
                                    <span class="stock-green"><b>Sufficient</b></span>
                                <?php elseif ($stock_in_hand > 0): ?>
                                    <span class="stock-orange"><b>Below Reorder Level</b></span>
                                <?php else: ?>
                                    <span class="stock-red"><b>Critical Stockout</b></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($stock_in_hand >= $min_stock + floor((0.1 * ($max_stock - $min_stock))) && $stock_in_hand != 0): ?>
                                    <span class="stock-green"><b>None</b></span>
                                <?php elseif ($stock_in_hand >= $min_stock && $stock_in_hand != 0): ?>
                                    <span class="stock-yellow"><b>Reorder Soon</b></span>
                                <?php elseif ($stock_in_hand >= floor((0.25 * $min_stock)) && $stock_in_hand != 0): ?>
                                    <span class="stock-orange"><b>Reorder Immediately</b></span>
                                <?php elseif ($stock_in_hand >= 0): ?>
                                    <span class="stock-red"><b>Urgent Order Needed</b></span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                        <td>
                            <div class="actions" style="display: flex; justify-content: center;">
                                <button class="view-button" onclick="window.location.href='stock level report view.php?item_id=<?php echo $row['item_id']; ?>'">View</button>
                                <button class="edit-button" onclick="window.location.href='stock level report edit.php?item_id=<?php echo $row['item_id']; ?>'">Edit</button>
                                <?php if ($user_id == 1): ?>
                                    <button class="delete-button" onclick="deleteUser('<?php echo $row['item_id']; ?>')">Delete</button>
                                <?php endif; ?>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="modal" id="deleteModal" style="display: none;">
            <div class="modal-content">
                <h2>Confirm Deletion</h2>
                <p>Are you sure you want to delete this item?</p>
                <form id="deleteForm" action="stock level report delete.php" method="POST">
                    <input type="hidden" name="item_id" id="deleteUserId">
                    <div class="modal-buttons">
                        <button type="submit" class="delete-button">Delete</button>
                        <button type="button" class="cancel-button" onclick="closeModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br><br><br><br>

    <?php include '../footer.php'; ?>
    
    <script>
    let openFlyout = null;

    function toggleDetails(button) {
        const wrapper = button.closest('.stock-wrapper');
        const flyout = wrapper.querySelector('.details-flyout');

        // Hide other flyouts
        document.querySelectorAll('.details-flyout').forEach(d => {
            if (d !== flyout) d.style.display = 'none';
        });

        // Toggle current flyout
        const isVisible = flyout.style.display === 'block';
        flyout.style.display = isVisible ? 'none' : 'block';
        openFlyout = isVisible ? null : flyout;

        // Match parent bg color
        const bgColor = getComputedStyle(button.closest('tr')).backgroundColor;
        flyout.style.backgroundColor = bgColor;
    }

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.stock-wrapper')) {
            document.querySelectorAll('.details-flyout').forEach(f => f.style.display = 'none');
            openFlyout = null;
        }
    });
    </script>

</body>
</html>
