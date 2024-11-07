<!-- account.php -->
<?php
// Start the session and include the database connection
session_start();
require '../db_connection.php';

// Check if the user ID is set in the session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the user's ID from the session
$user_id = $_SESSION['user_id'];

// Initialize variables
$first_name = $middle_initial = $last_name = $designation = $laboratory = $email = "";

// Fetch the latest user information from the database
$stmt = $conn->prepare("SELECT first_name, middle_initial, last_name, designation, laboratory, email, username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $middle_initial, $last_name, $designation, $laboratory, $email, $username);
$stmt->fetch();
$stmt->close();

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <link rel="stylesheet" type="text/css" href="../css/account.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <title>Account</title>
</head>
<body>
    <?php include '../header.php'; ?>

    <div class="content">
        <div class="account-container">
            <div class="information">
                <div class="personal-section">
                    <h2>PERSONAL INFORMATION</h2>
                    <p><strong>Name:</strong> 
                        <?php echo htmlspecialchars($first_name) . 
                            (!empty($middle_initial) ? ' ' . htmlspecialchars($middle_initial) . '. ' : ' ') . 
                            htmlspecialchars($last_name); 
                        ?>
                    </p>
                    <p><strong>Designation:</strong> <?php echo htmlspecialchars($designation); ?></p>
                    <p><strong>Laboratory:</strong> <?php echo htmlspecialchars($laboratory); ?></p>
                    <button class="update-button" id="personal-button" onclick="location.href='update_personal_info.php';">
                        🖊  Update Personal Information
                    </button>
                </div>

                <div class="account-section">
                    <h2>ACCOUNT INFORMATION</h2>
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                        <p><strong>Password:</strong> **********</p> <!-- Masked password for security -->
                    </div>
                    <button class="update-button" id="account-button" onclick="location.href='update_account_info.php';">
                        🖊  Update Account Information
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>