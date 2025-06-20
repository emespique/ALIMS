<!-- delete_user.php -->
<?php
require '../db_connection.php'; // Adjust the path as necessary

// Connect to the user_management database
$conn = connectToDatabase('form_data');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];

    $is_deleted = 1;

    // Prepare and execute the delete query
    $stmt = $conn->prepare("UPDATE items SET is_deleted = ? WHERE item_id = ?");
    $stmt->bind_param("is", $is_deleted, $item_id);

    if ($stmt->execute()) {
        // Redirect back to stock level report.php after deletion
        header("Location: stock level report.php");
        exit();
    } else {
        echo "Error deleting user: " . $conn->error;
    }

    $stmt->close();
}
$conn->close();
?>