<!-- delete_user.php -->
<?php
require '../db_connection.php'; // Adjust the path as necessary

// Connect to the user_management database
$conn = connectToDatabase('form_data');

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    $item_id = $_POST['item_id']; // the item_id you're targeting
    $quantity = (int) $_POST['quantity'];
    $is_selected = true;

    foreach ($_SESSION['items_list'] as &$item) {
        if ($item['item_id'] === $item_id) {
            $item['quantity'] = $quantity;
            $item['is_selected'] = $is_selected;
        }
    }
    unset($item); // best practice to unset reference variable

    header("Location: purchase order shopping cart.php");
    exit();

}
$conn->close();
?>