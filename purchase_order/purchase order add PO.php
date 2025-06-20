
<?php
// function findLowestMissing($arr) {
//     $left = 0;
//     $right = count($arr) - 1;

//     while ($left <= $right) {
//         $mid = intval(($left + $right) / 2);
//         $expected = $mid + 1;

//         if ($arr[$mid] == $expected) {
//             $left = $mid + 1;
//         } else {
//             $right = $mid - 1;
//         }
//     }

//     return $left + 1;
// }

// Connect to the user_management database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require '../db_connection.php'; // Adjust the path as necessary

    $conn = connectToDatabase('form_data');

    // Get all session variables
    session_start();
    $user_id = $_SESSION['user_id'];
    $purchase_order = $_SESSION['purchase_order'];
    $purchase_order_items = $_SESSION['selected_items'];
    $laboratory = "";
    if ($user_id == 1) {
        $laboratory = $_SESSION['laboratory_choice'];
    } else {
        $laboratory = $_SESSION['laboratory'];
    }

    // Initialize variables
    $PO_no = $PO_no_list = $PO_code = '';

    // Create PO_no
    if ($laboratory == 'Pathology') {
        $PO_no_list = "SELECT PO_no FROM purchase_order WHERE PO_no LIKE 'PTH%'";
        $PO_code = "PTH";
    } else if ($laboratory == 'Immunology') {
        $PO_no_list = "SELECT PO_no FROM purchase_order WHERE PO_no LIKE 'IMN%'";
        $PO_code = "IMN";
    } else if ($laboratory == 'Microbiology') {
        $PO_no_list = "SELECT PO_no FROM purchase_order WHERE PO_no LIKE 'MIC%'";
        $PO_code = "MIC";
    }

    $admin_id = $head_admin_id = null;
    $admin_status = $head_admin_status = $final_status = "Pending";
    if (in_array($user_id, [1, 2, 3, 4])) {
        $admin_id = $user_id;
        $admin_status = "Approved";
        if ($user_id == 1) {
            $head_admin_id = $user_id;
            $head_admin_status = "Approved";
            $final_status = "Approved";
        }
    }

    $PO_nos = [];
    if (!empty($PO_no_list)) {
        $stmt = $conn->prepare($PO_no_list);
        $stmt->execute();
        $PO_no_result = $stmt->get_result();

        while ($row = $PO_no_result->fetch_assoc()) {
            $PO_nos [] = $row['PO_no'];
        }
    }

    $PO_nos_int_only = [0];
    if (count($PO_nos) != 0) {
        foreach ($PO_nos as $code) {
            $PO_nos_int_only[] = intval(substr($code, 3));
        }
    }

    $new_PO_no = max($PO_nos_int_only) + 1;
    // sort($PO_nos_int_only);
    // $new_PO_no = findLowestMissing($PO_nos_int_only);
    $PO_no = $PO_code . str_pad($new_PO_no, 5, '0', STR_PAD_LEFT);

    
    // Get prepare all values needed for purchase_order
    $supplier_id = $purchase_order['supplier_id'];
    $lab_id = $purchase_order['lab_id'];
    $status = $purchase_order['status'];
    $subtotal = $purchase_order['subtotal'];
    $tax = $purchase_order['tax'];
    $shipping_cost = $purchase_order['shipping_cost'];
    $grand_total = $purchase_order['grand_total'];
    $grand_total_weight = $purchase_order['grand_total_weight'];

    // Prepare an insert statement for purchase_order
    $stmt = $conn->prepare("INSERT INTO purchase_order (PO_no, supplier_id, lab_id, user_id, status,
    subtotal, tax, shipping_cost, grand_total, grand_total_weight, 
    admin_id, head_admin_id, admin_status, head_admin_status, final_status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sssisdddddiisss",
        $PO_no, $supplier_id, $lab_id, $user_id, $status,
        $subtotal, $tax, $shipping_cost, $grand_total, $grand_total_weight, 
        $admin_id, $head_admin_id, $admin_status, $head_admin_status, $final_status
    );

    // Execute the statement and check for errors
    $purchase_order_error = false;
    if ($stmt->execute()) {
        // Success
    } else {
        $purchase_order_error = true;
        echo "Error adding item: " . $conn->error;
    }


    // Get prepare all values needed for purchase_order
    $values = [];
    $params = [];

    foreach ($purchase_order_items as $item) {
        // Collect raw values
        $values[] = "(?, ?, ?, ?, ?, ?, ?)";

        // Collect the parameters for binding
        $params[] = $PO_no;
        $params[] = $item['item_id'];
        $params[] = $item['quantity'];
        $params[] = $item['unit_weight'];
        $params[] = $item['total_weight'];
        $params[] = $item['unit_price'];
        $params[] = $item['total_price'];
    }

    // Prepare an insert statement for purchase_order_items with implode
    $sql = "INSERT INTO purchase_order_items (
        PO_no, item_id, quantity, unit_weight, total_weight, unit_price, total_price
    ) VALUES " . implode(", ", $values);

    // Prepare statement
    $stmt = $conn->prepare($sql);

    // Dynamically bind all collected params
    $types = str_repeat("ssidddd", count($purchase_order_items)); // repeated for each row
    $stmt->bind_param($types, ...$params);

    // Execute the statement and check for errors
    $purchase_order_items_error = false;
    if ($stmt->execute()) {
        // Success
    } else {
        $purchase_order_items_error = true;
        echo "Error adding item: " . $conn->error;
    }

    // If both statements don't have errors, proceed to clear variables
    if (!$purchase_order_error && !$purchase_order_items_error) {
        header("Location: purchase order clear variables.php");
        exit();
    }
    
    $stmt->close();
}
$conn->close();
?>