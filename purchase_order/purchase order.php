<!-- purchase order.php -->
<?php
// Start the session and include the necessary files
require '../header.php';
require '../db_connection.php';
require '../fpdf.php';


$session_user_id = $_SESSION['user_id'];

// User end view
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
$stmt = $conn->prepare("SELECT * FROM purchase_order WHERE status IN ('Delivered','Canceled') ORDER BY date_created DESC");
$stmt->execute();
$history_result = $stmt->get_result();

$history_rows = [];
while ($row = $history_result->fetch_assoc()) {
    $history_rows[] = $row;
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
        <h1 class="table-title"><?php echo $my ?>Purchase Order History (Delivered)</h1>
        <div class="add-item">
            <button onclick="window.location.href='purchase order shopping cart.php'">Place Order</button>
            <br>
            <button onclick="window.location.href='purchase order pendings.php'">Pending Forms</button>
        </div>
        <table class="table_PO">
            <thead>
                <tr>
                    <th>PO No.</th>
                    <th>Date</th>
                    <th>Grand Total Price(PHP)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history_rows as $row): 
                    if (
                        $row['status'] == 'Delivered' && (
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
                        <td><?php echo htmlspecialchars($row['grand_total']); ?></td>
                        <td>
                            <div class="actions" style="display: flex; justify-content: center;">
                                <button class="view-button" onclick="window.location.href='purchase order details.php?PO_no=<?php echo $row['PO_no']; ?>' ">View</button>
                                <button class="pdf-button" onclick="window.location.href='purchase_order_workflow/PO generate form.php?PO_no=<?php echo $row['PO_no']; ?>' ">&#128196; PDF</button>
                            </div>
                        </td>
                    </tr>
                <?php endif;
                endforeach; ?>
            </tbody>
        </table>

        <br><br>
        <h1 class="table-title"><?php echo $my ?>Purchase Order History (Canceled)</h1>
        <table class="table_PO">
            <thead>
                <tr>
                    <th>PO No.</th>
                    <th>Date</th>
                    <th style="width:18%">Grand Total Price(PHP)</th>
                    <th>Cancelation Reason</th>
                    <th style="width:20%">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history_rows as $row): 
                    if (
                        $row['status'] == 'Canceled' && (
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
                        <td><?php echo htmlspecialchars($row['grand_total']); ?></td>
                        <td>
                            <?php
                                if ($row['final_status'] == "Rejected") {
                                    echo "PO form was rejected by admins";
                                } else {
                                    echo "Order complications";
                                }
                            ?>
                        </td>
                        <td>
                            <div class="actions" style="display: flex; justify-content: center;">
                                <button class="view-button" onclick="window.location.href='purchase order details.php?PO_no=<?php echo $row['PO_no']; ?>' ">View</button>
                                <button class="pdf-button" onclick="window.location.href='purchase_order_workflow/PO generate form.php?PO_no=<?php echo $row['PO_no']; ?>' ">&#128196; PDF</button>
                            </div>
                        </td>
                    </tr>
                <?php endif;
                endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php include '../footer.php'; ?>

    <script>
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            // Page was loaded from back/forward cache
            window.location.reload();
        }
    });
    </script>

</body>
</html>