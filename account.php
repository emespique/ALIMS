<!-- account.php -->
<?php 
session_start();
require 'db_connection.php'; // Ensure this file path is correct

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user's information
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

$stmt->close();
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" type="text/css" href="css/account.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <title>Account</title>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="content">
        <div class="account-container">
            <div class="information">
                <div class="personal-section">
                    <h2>PERSONAL INFORMATION</h2>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['middle_initial'] . '.' . ' ' . $user['last_name']); ?></p>
                        <p><strong>Designation:</strong> <?php echo htmlspecialchars($user['designation']); ?></p>
                        <p><strong>Laboratory:</strong> <?php echo htmlspecialchars($user['laboratory']); ?></p>
                    <button class="update-button" id="personal-button" onclick="location.href='update_personal_info.php';">
                        🖊  Update Personal Information
                    </button>
                </div>

                <div class="account-section">
                    <h2>ACCOUNT INFORMATION</h2>
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                        <p><strong>Password:</strong> **********</p> <!-- Masked password for security -->
                    </div>
                    <button class="update-button" id="account-button" onclick="location.href='update_account_info.php';">
                        🖊  Update Account Information
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>