<!-- stock level report.php -->
<?php
// Start the session and include the necessary files
require '../header.php';
require '../db_connection.php';

// Connect to the form database
$conn = connectToDatabase('form_data');

// Get all items
$stmt = $conn->prepare("SELECT * FROM items WHERE is_deleted = 1 ORDER BY item_id");
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Deleted Items</title>
    <link rel="stylesheet" type="text/css" href="../css/stocklevelreport.css?version=<?php echo time() - 1000000000; ?>">
    <link rel="stylesheet" type="text/css" href="../css/accounts.css?version=<?php echo time() - 1000000000; ?>">
    <link rel="stylesheet" type="text/css" href="../css/main.css?version=<?php echo time() - 1000000000; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <script defer src="../js/user_validation.js"></script>
</head>
<body>
    <?php include '../header.php'; ?>

    <div class="content">
        <h1 class="table-title">Deleted Stock Items</h1>
        <div class="add-item">
            <button onclick="window.location.href='stock level report.php'">Go back ←</button>
        </div> 
        <table>
            <thead>
                <tr>
                    <th>Item No.</th>
                    <th>Item Name</th>
                    <th>Item Description</th>
                    <th>Item Type</th>
                    <th>Unit of Measure</th>
                    <th>Price Per Unit</th>
                    <th>Weight Per Unit (kg)</th>
                    <th>Expiry Date</th>
                    <th>Min. Stock</th>
                    <th>Max. Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['item_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_desc']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['unit_of_measure']); ?></td>
                        <td><?php echo htmlspecialchars($row['price_per_unit']); ?></td>
                        <td><?php echo htmlspecialchars($row['weight_per_unit']); ?></td>
                        <td>
                        <?php
                            if ($row['expiry_date'] == null) {
                            echo('N.A.');
                            } else {
                            $date = new DateTime($row['expiry_date']);
                            echo $date->format('m/d/y'); 
                            }
                        ?>
                        </td>
                        <td>
                            <div class="stock-wrapper">
                                <span>Click here →</span>
                                <button class="toggle-details" onclick="toggleDetails(this)">&#x25B6;</button>
                                <div class="details-flyout" style="display: none;">
                                    <h4><u>Min. Stock Details</u></h4>
                                    <p><strong>Pathology:</strong> <?php echo htmlspecialchars($row['pathology_min_stock']); ?></p>
                                    <p><strong>Immunology:</strong> <?php echo htmlspecialchars($row['immunology_min_stock']); ?></p>
                                    <p><strong>Microbiology:</strong> <?php echo htmlspecialchars($row['microbiology_min_stock']); ?></p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="stock-wrapper">
                                <span>Click here →</span>
                                <button class="toggle-details" onclick="toggleDetails(this)">&#x25B6;</button>
                                <div class="details-flyout" style="display: none;">
                                    <h4><u>Max. Stock Details</u></h4>
                                    <p><strong>Pathology:</strong> <?php echo htmlspecialchars($row['pathology_max_stock']); ?></p>
                                    <p><strong>Immunology:</strong> <?php echo htmlspecialchars($row['immunology_max_stock']); ?></p>
                                    <p><strong>Microbiology:</strong> <?php echo htmlspecialchars($row['microbiology_max_stock']); ?></p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <button class="edit-button" onclick="deleteUser('<?php echo $row['item_id']; ?>')">Restore</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="modal" id="deleteModal" style="display: none;">
            <div class="modal-content">
                <h2>Confirm Restoration</h2>
                <p>Are you sure you want to restore this item?</p>
                <form id="deleteForm" action="stock level report restore.php" method="POST">
                    <input type="hidden" name="item_id" id="deleteUserId">
                    <div class="modal-buttons">
                        <button type="submit" class="restore-button">Restore</button>
                        <button type="button" class="cancel-button" onclick="closeModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

    <br><br><br><br><br><br><br><br>

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
