<!-- dashboard.php -->
<?php
require 'header.php';
require 'db_connection.php';

// Connect to the user_management database
$conn = connectToDatabase('user_management');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Fetch the username from the database based on the logged-in user's ID
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="content">
        <h1>Welcome to the Dashboard, <?php echo htmlspecialchars($username); ?>!</h1>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>