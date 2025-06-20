<?php
session_start();

$_SESSION['supplier_id'] = null;
$_SESSION['items_list'] = null;
$_SESSION['selected_items'] = null;
$_SESSION['laboratory_choice'] = null;
$_SESSION['purchase_order'] = null;

// Go back to purchase order
header("Location: purchase order.php");
exit();
?>