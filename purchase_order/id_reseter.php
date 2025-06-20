<?php

require '../db_connection.php';
$conn = connectToDatabase('form_data');

// $conn->query("ALTER TABLE id_counter_pathology AUTO_INCREMENT = 1");
// $conn->query("ALTER TABLE id_counter_immunology AUTO_INCREMENT = 1");
// $conn->query("ALTER TABLE id_counter_microbiology AUTO_INCREMENT = 1");

$conn->query("ALTER TABLE purchase_order_items AUTO_INCREMENT = 1");

echo "Done!"

?>