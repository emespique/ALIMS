<?php
require '../../db_connection.php'; // Adjust the path as necessary

// Connect to the user_management database
$conn = connectToDatabase('form_data');

session_start();

function updateLabStock(mysqli $conn, mysqli_result $result, string $laboratory, string $status, string $PO_no) {

    // Validate column name
    $allowed_columns = ['pathology_stock', 'immunology_stock', 'microbiology_stock'];
    if (!in_array($laboratory, $allowed_columns)) {
        die("Invalid column name");
    }

    $stmt = $conn->prepare("UPDATE purchase_order SET status = ? WHERE PO_no = ?");
    $stmt->bind_param("ss", $status, $PO_no);
    $stmt->execute();

    // Update stock
    while ($row = $result->fetch_assoc()) {
        $item_id = $row['item_id'];
        $quantity_to_add = $row['quantity'];

        $sql = "UPDATE items SET was_purchased = TRUE, $laboratory = $laboratory + ? WHERE item_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("is", $quantity_to_add, $item_id);
        $stmt->execute();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $PO_no = $_POST['PO_no'];
    $status = $_POST['status']; 

    // Prepare and execute the delete query
    if ($status == "Delivered") {

        // Get all item_id and quantity from the given PO_no
        $stmt = $conn->prepare("SELECT item_id, quantity FROM purchase_order_items WHERE PO_no = ?");
        $stmt->bind_param("s", $PO_no);
        $stmt->execute();
        $PO_items_result = $stmt->get_result();

        $stmt = $conn->prepare("SELECT lab_id FROM purchase_order WHERE PO_no = ?");
        $stmt->bind_param("s", $PO_no);
        $stmt->execute();
        $lab_id_result = $stmt->get_result();

        $lab_id = 0;
        if ($row = $lab_id_result->fetch_assoc()) {
            $lab_id = $row['lab_id'];
        }
        
        if ($lab_id == 1) {
            updateLabStock($conn, $PO_items_result, "pathology_stock", $status, $PO_no);
        } else if ($lab_id == 2) {
            updateLabStock($conn, $PO_items_result, "immunology_stock", $status, $PO_no);
        } else {
            updateLabStock($conn, $PO_items_result, "microbiology_stock", $status, $PO_no);
        }
        
    } else {
        $stmt = $conn->prepare("UPDATE purchase_order SET status = ? WHERE PO_no = ?");
        $stmt->bind_param("ss", $status, $PO_no);
        $stmt->execute();
    }

    header("Location: ../purchase order pendings.php");
    exit();

    $stmt->close();

}
$conn->close();
?>