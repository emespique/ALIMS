<!-- header.php -->
<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="navbar">
        <div class="logo-container">
            <img src="lab-icon.png" alt="Lab Icon">
            <span>ALIMS</span>
        </div>

        <div class="nav-links">
            <div class="dropdown">
                <a href="#">LABORATORIES</a>
                <div class="dropdown-content">
                    <a href="#">Pathology</a>
                    <a href="#">Immunology</a>
                    <a href="#">Microbiology</a>
                </div>
            </div>
            <a href="purchase order.php">PURCHASE ORDER</a>
            <a href="stock level report.php">STOCK LEVEL REPORT</a>
            <a href="disposition.php">DISPOSITION</a>
            <?php if ($role === 'admin'): ?>
                <a href="accounts.php">ACCOUNTS</a> <!-- Show "Accounts" for admin -->
            <?php else: ?>
                <a href="account.php">ACCOUNT</a> <!-- Show "Account" for user -->
            <?php endif; ?>
            <a href="login.php">SIGN OUT</a>
        </div>
    </div>
</body>
</html>