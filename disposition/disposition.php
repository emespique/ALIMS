<!-- purchase order.php -->
<?php
// Start the session and include the necessary files
require '../header.php';
require '../db_connection.php';


$session_user_id = $_SESSION['user_id'];

$my = $status = "";
if (in_array($session_user_id, [2,3,4])) {
    $my = "My ";
}

// Connect to the user_management database
$conn = connectToDatabase('form_data');

$session_laboratory = $_SESSION['laboratory'];
$session_role = $_SESSION['role'];

$stock = "";
if ($session_laboratory == "Pathology") {
    $stock = "pathology_stock";
} else if ($session_laboratory == "Immunology") {
    $stock = "immunology_stock";
} else if ($session_laboratory == "Microbiology") {
    $stock = "microbiology_stock";
}

if ($session_user_id == 1) {
    $stmt = $conn->prepare("SELECT item_id, item_name, item_desc, unit_of_measure,
    pathology_stock, immunology_stock, microbiology_stock FROM items WHERE 
    pathology_stock > 0 OR immunology_stock > 0 OR microbiology_stock > 0");
    $stmt->execute();
    $items_result = $stmt->get_result();
} else {
    $stmt = $conn->prepare("SELECT item_id, item_name, item_desc, unit_of_measure,
    `$stock` FROM items WHERE `$stock` > 0");
    $stmt->execute();
    $items_result = $stmt->get_result();
}

$items = [];
while ($row = $items_result->fetch_assoc()) {
    $items[] = $row;
}


// $stock should be a validated string like 'pathology_stock'
$sql = "SELECT d.*, i.item_name, i.item_desc, i.unit_of_measure
        FROM disposition d
        INNER JOIN items i ON d.item_id = i.item_id";

$stmt = $conn->prepare($sql);
$stmt->execute();
$disposition_result = $stmt->get_result();


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disposition</title>
    <link rel="stylesheet" type="text/css" href="../css/stocklevelreport.css?version=<?php echo time() - 1000000000; ?>">
    <link rel="stylesheet" type="text/css" href="../css/input_design.css?version=<?php echo time() - 1000000000; ?>"> 
    <link rel="stylesheet" type="text/css" href="../css/accounts.css?version=<?php echo time() - 1000000000; ?>">   <!-- "echo time()" is only for development, not for production -->
    <link rel="stylesheet" type="text/css" href="../css/main.css?version=<?php echo time() - 1000000000; ?>">   <!-- "echo time()" is only for development, not for production -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <script defer src="../js/user_validation.js"></script>
</head>
<body>
    <?php include '../header.php'; ?>

    <div class="content">
        <h1 class="table-title"><?php echo $my ?>Disposition Forms</h1>
        <?php if (in_array($session_user_id, [2,3,4])): ?>
        <div class="add-item">
            <button type="button" onclick="addItem()">Dispose</button>
        </div>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item No.</th>
                    <th>Item Name</th>
                    <th>Item Description</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Reason for Disposition</th>
                    <th>Disposition Method</th>
                    <th>Date of Disposition</th>
                    <th>Dispositioned by</th>
                    <?php if ($session_user_id == 1): ?>
                        <th>Laboratory</th>
                    <?php endif; ?>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $disposition_result->fetch_assoc()): 
                    if ($row['lab_disposed_by'] == $session_laboratory || $session_user_id == 1): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_desc']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['unit_of_measure']); ?></td>
                        <td><?php echo htmlspecialchars($row['disposal_reason']); ?></td>
                        <td><?php echo htmlspecialchars($row['disposal_method']); ?></td>
                        <td>
                            <?php
                                $date = new DateTime($row['disposal_date']);
                                echo $date->format('m/d/y'); 
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['disposed_by']); ?></td>
                        <?php if ($session_user_id == 1): ?>
                            <td><?php echo htmlspecialchars($row['lab_disposed_by']); ?></td>
                        <?php endif; ?>
                        <td><?php echo htmlspecialchars($row['comments']); ?></td>
                    </tr>
                <?php endif;
            endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php 
    $is_admin = ($session_user_id == 1);
    ?>

    <div class="modal" id="shoppingCartModal" style="display: none;">
        <div class="modal-content" style="min-width: 600px;">
            <h3>Dispose item</h3>
            <form id="deleteForm" action="disposition dispose item.php" method="POST">
                <div class="custom-dropdown">
                    <input type="hidden" name="item_id" id="selectedItemId" required>
                    <div class="dropdown-list" id="dropdownList">
                        <?php foreach ($items as $item): ?>
                            <?php
                                $i_id   = $item['item_id'];
                                $i_name = $item['item_name'];
                                $i_desc = $item['item_desc'];
                                $i_unit = $item['unit_of_measure'];

                                $p_stock = $item['pathology_stock']     ?? 0;
                                $i_stock = $item['immunology_stock']    ?? 0;
                                $m_stock = $item['microbiology_stock']  ?? 0;
                            ?>
                            <div class="dropdown-item" data-id="<?= $i_id ?>" 
                                style="display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid #ccc;">
                                
                                <!-- LEFT SIDE: Item Info -->
                                <div style="flex: 1;">
                                    <strong><?= htmlspecialchars($i_id) ?> - <?= htmlspecialchars($i_name) ?></strong><br>
                                    <small><?= htmlspecialchars($i_desc) ?></small>
                                </div>

                                <!-- RIGHT SIDE: Stock Info -->
                                <div style="flex: 0 0 auto; display: flex; align-items: center; border-left: 1px solid #ccc; padding-left: 12px;">
                                    <?php if ($is_admin): ?>
                                        <!-- Stock columns -->
                                        <div style="text-align: right;">
                                            <div>Path: <?= htmlspecialchars($p_stock) ?></div>
                                            <div>Immu: <?= htmlspecialchars($i_stock) ?></div>
                                            <div>Micro: <?= htmlspecialchars($m_stock) ?></div>
                                        </div>
                                        <!-- Divider -->
                                        <div style="height: 60px; width: 1px; background-color: #ccc; margin: 0 8px;"></div>
                                        <!-- Unit vertically centered -->
                                        <div style="display: flex; align-items: center; height: 60px;">
                                            <?= htmlspecialchars($i_unit) ?>
                                        </div>
                                    <?php else: ?>
                                        <div style="display: flex; align-items: center;">
                                            <!-- Quantity -->
                                            <div style="text-align: right;">
                                                <?= htmlspecialchars($item[$stock] ?? 0) ?>
                                            </div>
                                            <!-- Divider -->
                                            <div style="height: 24px; width: 1px; background-color: #ccc; margin: 0 8px;"></div>
                                            <!-- Unit -->
                                            <div>
                                                <?= htmlspecialchars($i_unit) ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="modal-buttons">
                    <button type="submit" class="restore-button">Select</button>
                    <button type="button" class="delete-button" onclick="closeAddItemModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>






    <?php include '../footer.php'; ?>

    <script>
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            // Page was loaded from back/forward cache
            window.location.reload();
        }
    });


    function addItem() {
        document.getElementById('shoppingCartModal').style.display = 'flex';
    }

    function closeAddItemModal() {
        // Hide the modal
        document.getElementById('shoppingCartModal').style.display = 'none';
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