<!-- delete_user.php -->
<?php
require '../db_connection.php'; // Adjust the path as necessary

// Connect to the user_management database
$conn = connectToDatabase('form_data');

session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'];
    $_SESSION['supplier_id'] = $supplier_id;

    $stmt = $conn->prepare("SELECT item_id, item_name, item_desc, unit_of_measure, 
    weight_per_unit, price_per_unit, is_deleted FROM items WHERE supplier_id = ?");
    $stmt->bind_param("s", $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // part of purchase order details: PO_no, item_id, quantity, unit_weight, total_weight, unit_price, total_price

    $_SESSION['items_list'] = [];

    while ($row = $result->fetch_assoc()) {
        if ($row['is_deleted'] === 0) {
            $_SESSION['items_list'][] = [
                'item_id' => $row['item_id'],
                'item_name' => $row['item_name'],
                'item_desc' => $row['item_desc'],
                'quantity' => 0,
                'unit_of_measure' => $row['unit_of_measure'],
                'unit_weight' => $row['weight_per_unit'],
                'total_weight' => 0,
                'unit_price' => $row['price_per_unit'],
                'total_price' => 0,
                'is_selected' => false // default value
            ];
        }
    }

    header("Location: purchase order shopping cart.php");
    exit();

}
$conn->close();
?>