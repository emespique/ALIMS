<!-- delete_user.php -->
<?php
require '../db_connection.php'; // Adjust the path as necessary

// Connect to the user_management database
$conn = connectToDatabase('form_data');

session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $laboratory_choice = $_POST['laboratory_choice'];
    $_SESSION['laboratory_choice'] = $laboratory_choice;
    

    header("Location: purchase order shopping cart.php");
    exit();

}
$conn->close();
?>