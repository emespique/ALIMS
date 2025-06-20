<!-- purchase order.php -->
<?php
// Start the session and include the necessary files
require '../header.php';
require '../db_connection.php';


$session_user_id = $_SESSION['user_id'];


$my = "";
if ($_SESSION['role'] === 'user') {
    $my = "My ";
}

// Connect to the user_management database
$conn = connectToDatabase('form_data');

$session_laboratory = $_SESSION['laboratory'];
$stmt = $conn->prepare("SELECT lab_id FROM lab_connection WHERE lab_name = ?");
$stmt->bind_param("s", $session_laboratory);
$stmt->execute();
$lab_id_result = $stmt->get_result();

$session_lab_id = 0;
if ($row = $lab_id_result->fetch_assoc()) {
    $session_lab_id = $row['lab_id'];
}

// show all items
$stmt = $conn->prepare("SELECT * FROM purchase_order WHERE 
status IN ('Submitted', 'Procurement Office', 'Accounting Office')
ORDER BY date_created DESC");
$stmt->execute();
$pending_result = $stmt->get_result();

$pending_rows = [];
while ($row = $pending_result->fetch_assoc()) {
    $pending_rows[] = $row;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order</title>
    <link rel="stylesheet" type="text/css" href="../css/purchaseorder.css?version=<?php echo time() - 1000000000; ?>">  <!-- "echo time()" is only for development, not for production -->
    <link rel="stylesheet" type="text/css" href="../css/stocklevelreport.css?version=<?php echo time() - 1000000000; ?>">
    <link rel="stylesheet" type="text/css" href="../css/accounts.css?version=<?php echo time() - 1000000000; ?>">   <!-- "echo time()" is only for development, not for production -->
    <link rel="stylesheet" type="text/css" href="../css/main.css?version=<?php echo time() - 1000000000; ?>">   <!-- "echo time()" is only for development, not for production -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <script defer src="../js/user_validation.js"></script>
</head>
<body>
    <?php include '../header.php'; ?>

    <div class="content">
        <h1 class="table-title"><?php echo $my ?>Purchase Order (For Approval)</h1>
        <div class="add-item">
            <button onclick="window.location.href='purchase order.php'">Go Back</button>
        </div>
        <table class="table_PO">
            <thead>
                <tr>
                    <th>PO No.</th>
                    <th>Date</th>
                    <th>Grand Total Price(PHP)</th>
                    <?php if (!in_array($session_user_id, [1, 2, 3, 4])): ?>
                        <th>Admin</th>
                    <?php endif; ?>
                    <?php if ($session_user_id != 1): ?>
                        <th>Head Admin</th>
                    <?php endif; ?>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_rows as $row): 
                    $current_PO_no = $row['PO_no'];
                    if (
                        $row['final_status'] == 'Pending' && (
                        ($session_user_id == 1 && $row["admin_status"] == "Approved" && $row["head_admin_status"] != "Approved") || // Head Admin sees all
                        (in_array($session_user_id, [2, 3, 4]) && $row["admin_status"] != "Approved") && $session_lab_id == $row['lab_id'] ) || // Lab admins see lab rows
                        ($session_user_id == $row['user_id'] && $session_user_id != 1) // Regular users see their own rows
                    ): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($current_PO_no); ?></td>
                        <td>
                            <?php
                                $date = new DateTime($row['date_created']);
                                echo $date->format('m/d/y g:i A'); 
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['grand_total']); ?></td>
                        <?php if (!in_array($session_user_id, [1, 2, 3, 4])): ?>
                            <td><?php echo htmlspecialchars($row['admin_status']); ?></td>
                        <?php endif; ?>
                        <?php if ($session_user_id != 1): ?>
                            <td><?php echo htmlspecialchars($row['head_admin_status']); ?></td>
                        <?php endif; ?>
                        <td>
                            <div class="actions" style="display: flex; justify-content: center;">
                                <button class="view-button" onclick="window.location.href='purchase order details.php?PO_no=<?php echo $row['PO_no']; ?>' ">View</button>
                                <?php if ($session_user_id == 1 && $row["admin_status"] == "Approved"): ?>
                                    <button class="edit-button" onclick="headAdminApprove('<?= $current_PO_no; ?>')">Approve</button>
                                    <button class="delete-button" onclick="headAdminReject('<?= $current_PO_no; ?>')">Reject</button>
                                <?php endif; ?>
                                <?php if (in_array($session_user_id, [2, 3, 4]) && $row["admin_status"] != "Approved"): ?>
                                    <button class="edit-button" onclick="adminApprove('<?= $current_PO_no; ?>')">Approve</button>
                                    <button class="delete-button" onclick="adminReject('<?= $current_PO_no; ?>')">Reject</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif;
                endforeach; ?>
            </tbody>
        </table>

        <br><br><br>
        <h1 class="table-title"><?php echo $my ?>Purchase Order (Processing)</h1>
        <table class="table_PO">
            <thead>
                <tr>
                    <th>PO No.</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Grand Total Price(PHP)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_rows as $row): 
                    if (
                        $row['final_status'] == 'Approved' && (
                        $session_user_id == 1 || // Head Admin sees all
                        (in_array($session_user_id, [2, 3, 4]) && $session_lab_id == $row['lab_id']) || // Lab admins see lab rows
                        ($session_user_id == $row['user_id']) ) // Regular users see their own rows
                    ): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['PO_no']); ?></td>
                        <td>
                            <?php
                                $date = new DateTime($row['date_created']);
                                echo $date->format('m/d/y g:i A'); 
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['grand_total']); ?></td>
                        <td>
                            <div class="actions" style="display: flex; justify-content: center;">
                                <button class="view-button" onclick="window.location.href='purchase order details.php?PO_no=<?php echo $row['PO_no']; ?>' ">View</button>
                                <?php if ($session_user_id == 1): ?>
                                    <button class="edit-button" onclick="updateStatus('<?= $current_PO_no; ?>')">Update</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif;
                endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- For admin -->
    <div class="modal" id="adminApproveModal" style="display: none;">
        <div class="modal-content">
            <h2>Confirm Approval</h2>
            <p>Are you sure you want to approve this item?</p>
            <form id="deleteForm" action="purchase_order_workflow/PO admin approve.php" method="POST">
                <input type="hidden" name="PO_no" id="modalItemIdAdminApprove" required>

                <div class="modal-buttons">
                    <button type="submit" class="confirm-button">Confirm</button>
                    <button type="button" class="cancel-button" onclick="closeAdminApproveModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="adminRejectModal" style="display: none;">
        <div class="modal-content">
            <h2>Confirm Rejection</h2>
            <p>Are you sure you want to reject this item?</p>
            <form id="deleteForm" action="purchase_order_workflow/PO admin reject.php" method="POST">
                <input type="hidden" name="PO_no" id="modalItemIdAdminReject" required>

                <div class="modal-buttons">
                    <button type="submit" class="confirm-button">Confirm</button>
                    <button type="button" class="cancel-button" onclick="closeAdminRejectModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- For head admin -->
    <div class="modal" id="headAdminApproveModal" style="display: none;">
        <div class="modal-content">
            <h2>Confirm Approval</h2>
            <p>Are you sure you want to approve this item?</p>
            <form id="deleteForm" action="purchase_order_workflow/PO head admin approve.php" method="POST">
                <input type="hidden" name="PO_no" id="modalItemIdHeadAdminApprove" required>

                <div class="modal-buttons">
                    <button type="submit" class="confirm-button">Confirm</button>
                    <button type="button" class="cancel-button" onclick="closeHeadAdminApproveModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="headAdminRejectModal" style="display: none;">
        <div class="modal-content">
            <h2>Confirm Rejection</h2>
            <p>Are you sure you want to reject this item?</p>
            <form id="deleteForm" action="purchase_order_workflow/PO head admin reject.php" method="POST">
                <input type="hidden" name="PO_no" id="modalItemIdHeadAdminReject" required>

                <div class="modal-buttons">
                    <button type="submit" class="confirm-button">Confirm</button>
                    <button type="button" class="cancel-button" onclick="closeHeadadminRejectModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update status (head admin only) -->
    <div class="modal" id="updateStatusModal" style="display: none;">
        <div class="modal-content">
            <h2>Update Status</h2>
            <form id="deleteForm" action="purchase_order_workflow/PO update status.php" method="POST">
                <input type="hidden" name="PO_no" id="modalItemIdUpdateStatus" required>
                    <select class="info-dropdown" name="status" required>
                        <option value="">Select Status</option>
                        <option value="Submitted" <?php if ($status == "Submitted") echo "selected"; ?> >Submitted</option>
                        <option value="Procurement Office" <?php if ($status == "Procurement Office") echo "selected"; ?>>Procurement Office</option>
                        <option value="Accounting Office" <?php if ($status == "Accounting Office") echo "selected"; ?>>Accounting Office</option>
                        <option value="Delivered" <?php if ($status == "Delivered") echo "selected"; ?> style="color: green;">Delivered</option>
                        <option value="Canceled" <?php if ($status == "Canceled") echo "selected"; ?> style="color: red;">Canceled</option>
                    </select>
                <div class="modal-buttons">
                    <button type="submit" class="confirm-button">Update</button>
                    <button type="button" class="cancel-button" onclick="closeUpdateStatusModal()">Cancel</button>
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

    // For head admin
    function headAdminApprove(itemId) {
        document.getElementById('modalItemIdHeadAdminApprove').value = itemId;
        document.getElementById('headAdminApproveModal').style.display = 'flex';
    }

    function closeHeadAdminApproveModal() {
        // Hide the modal
        document.getElementById('headAdminApproveModal').style.display = 'none';
    }

    function headAdminReject(itemId) {
        document.getElementById('modalItemIdHeadAdminReject').value = itemId;
        document.getElementById('headAdminRejectModal').style.display = 'flex';
    }

    function closeHeadAdminRejectModal() {
        // Hide the modal
        document.getElementById('headAdminRejectModal').style.display = 'none';
    }

    // For admin
    function adminApprove(itemId) {
        document.getElementById('modalItemIdAdminApprove').value = itemId;
        document.getElementById('adminApproveModal').style.display = 'flex';
    }

    function closeAdminApproveModal() {
        // Hide the modal
        document.getElementById('adminApproveModal').style.display = 'none';
    }

    function adminReject(itemId) {
        document.getElementById('modalItemIdAdminReject').value = itemId;
        document.getElementById('adminRejectModal').style.display = 'flex';
    }

    function closeAdminRejectModal() {
        // Hide the modal
        document.getElementById('adminRejectModal').style.display = 'none';
    }

    // Update status
    function updateStatus(itemId) {
        document.getElementById('modalItemIdUpdateStatus').value = itemId;
        document.getElementById('updateStatusModal').style.display = 'flex';
    }

    function closeUpdateStatusModal() {
        // Hide the modal
        document.getElementById('updateStatusModal').style.display = 'none';
    }

    </script>

</body>
</html>