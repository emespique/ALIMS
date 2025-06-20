<!-- header.php -->
<?php

// To remove "ignoring session_start()" warning
if(!isset($_SESSION)){
    session_start();
}


$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

// Use the root-relative URL to point directly to the image
$basePath = '/ALIMS/';

// initiate session variables
$_SESSION['supplier_id'] = null;
$_SESSION['items_list'] = null;
$_SESSION['selected_items'] = null;
$_SESSION['laboratory_choice'] = null;
$_SESSION['purchase_order'] = null;

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
            <img src="<?php echo $basePath; ?>lab-icon.png" alt="Lab Icon">
            <span>ALIMS</span>
        </div>

        <div class="nav-links">
        <a href="/ALIMS/dashboard.php">DASHBOARD</a>
            <div class="dropdown">
                <a href="#">LABORATORIES</a>
                <div class="dropdown-content">
                    <a href="/ALIMS/pathology.php">Pathology</a>
                    <a href="/ALIMS/immunology.php">Immunology</a>
                    <a href="/ALIMS/microbiology.php">Microbiology</a>
                </div>
            </div>
            <a href="/ALIMS/purchase_order/purchase order.php">PURCHASE ORDER</a>
            <a href="/ALIMS/stock_level_report/stock level report.php">STOCK LEVEL REPORT</a>
            <a href="/ALIMS/disposition/disposition.php">DISPOSITION</a>
            <?php if ($role === 'admin'): ?>
                <a href="/ALIMS/accounts/accounts.php">ACCOUNTS</a> <!-- Show "Accounts" for admin -->
            <?php else: ?>
                <a href="/ALIMS/account/account.php">ACCOUNT</a> <!-- Show "Account" for user -->
            <?php endif; ?>
            <a href="/ALIMS/login.php">SIGN OUT</a>
        </div>
    </div>
</body>
</html>