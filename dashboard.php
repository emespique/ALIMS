<!-- dashboard.php -->
<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // User is not logged in, redirect to login page
    header("Location: login.php"); 
    exit();
}
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
        <h1>Welcome to the Dashboard, <?php echo $_SESSION['username']; ?>!</h1>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
