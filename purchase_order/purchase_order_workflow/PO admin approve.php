<?php
require '../../db_connection.php'; // Adjust the path as necessary

// Connect to the user_management database
$conn = connectToDatabase('form_data');

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_SESSION['user_id'];
    $PO_no = $_POST['PO_no']; // the item_id you're targeting

    // Prepare and execute the delete query
    $stmt = $conn->prepare("UPDATE purchase_order SET admin_status = 'Approved', admin_id = ?
    WHERE PO_no = ?");
    $stmt->bind_param("ss", $user_id, $PO_no);

    if ($stmt->execute()) {
        // Redirect back to accounts.php after deletion
        header("Location: ../purchase order pendings.php");
        exit();
    } else {
        echo "Error deleting user: " . $conn->error;
    }

    $stmt->close();

}
$conn->close();
?>