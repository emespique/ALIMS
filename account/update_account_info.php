<!-- update_account_info.php -->
<?php
// Start the session and include the database connection
require '../header.php';
require '../db_connection.php';

// Check if the user ID is set in the session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the user's ID from the session
$user_id = $_SESSION['user_id'];

// Initialize variables for form submission and error messages
$username = $email = $password = $confirm_password = "";
$username_error = $email_error = $password_error = $confirm_password_error = "";

// Handle form submission when the form is posted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve and sanitize the form data
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));

    // Validate username (min 8 characters, max 15 alphanumeric)
    if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,15}$/", $username)) {
        $username_error = "Username must be 8-15 alphanumeric characters";
    }

    // Validate the email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Invalid email format";
    }

    // Validate password (min 8 characters, max 15 characters, must contain letters, numbers, and special characters)
    if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,15}$/", $password)) {
        $password_error = "Password must be 8-15 characters composed of letters, numbers, and special characters";
    }

    // Validate confirm password
    if ($password !== $confirm_password) {
        $confirm_password_error = "Passwords do not match";
    }

    // If no errors, update the database
    if (empty($username_error) && empty($email_error) && empty($password_error) && empty($confirm_password_error)) {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare an update statement
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssi", $username, $email, $hashed_password, $user_id);
        
        // Execute the statement and check for errors
        if ($stmt->execute()) {
            // Redirect back to the account page if the update is successful
            header("Location: account.php");
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }

        $stmt->close();
    }
} else {
    // Fetch existing user information to populate the form if it's a GET request
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username, $email);
    $stmt->fetch();
    $stmt->close();
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Account Information</title>
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <link rel="stylesheet" type="text/css" href="../css/account.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script defer src="../js/user_validation.js"></script>
</head>
<body>
    <?php include '../header.php'; ?> <!-- Include header if necessary -->
    
    <div class="content">
        <div class="update-container">
            <div class="information">
                <h2 id="update-account-info-header">UPDATE ACCOUNT INFORMATION</h2>
                <form action="update_account_info.php" method="POST" onsubmit="return validateForm()">
                    <div class="input-group" id="username_group">
                        <label for="username">Username</label>
                        <input type="text" class="name-input" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Username" required>
                        <?php if (!empty($username_error)): ?>
                            <span class="error" id="username_error"><?php echo $username_error; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="input-group" id="email_group">
                        <label for="email">Email</label>
                        <input type="email" class="name-input" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Email" required>
                        <?php if (!empty($email_error)): ?>
                            <span class="error" id="email_error"><?php echo $email_error; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="input-group" id="password_group">
                        <label for="password">Password</label>
                        <div class="password-container">
                            <input type="password" id="password" class="name-input" name="password" placeholder="Password">
                            <span class="eye-icon" onclick="togglePassword()">
                                <i id="eye-icon-password" class="fas fa-eye"></i>
                            </span>
                        </div>
                        <?php if (!empty($password_error)): ?>
                            <span class="error" id="password_error"><?php echo $password_error; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="input-group" id="matching_group">
                        <label for="confirm_password">Re-enter Password</label>
                        <div class="password-container">
                            <input type="password" id="reenteredpassword" class="name-input" name="confirm_password" placeholder="Re-enter Password">
                            <span class="eye-icon" onclick="toggleReenteredPassword()">
                                <i id="eye-icon-reentered" class="fas fa-eye"></i>
                            </span>
                        </div>
                        <?php if (!empty($confirm_password_error)): ?>
                            <span class="error" id="matching_error"><?php echo $confirm_password_error; ?></span>
                        <?php endif; ?>
                    </div>
                                        
                    <div class="button-container">
                        <button type="submit" class="save-button">SAVE</button>
                        <button type="button" class="cancel-button" onclick="window.location.href='account.php';">CANCEL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include '../footer.php'; ?>
</body>
</html>