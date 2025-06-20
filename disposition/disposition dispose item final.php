<?php
require '../db_connection.php'; // Adjust the path as necessary

// Connect to the user_management database
$conn = connectToDatabase('form_data');

session_start();


$quantity = $_GET['qty'];
$lab_stock = $_GET['lab_stock'];
$item_id = $_GET['item_id'];


$sql = "UPDATE items SET $lab_stock = $lab_stock - ? WHERE item_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("is", $quantity, $item_id);
$stmt->execute();
    

header("Location: disposition.php");
exit();

$stmt->close();

$conn->close();
?>