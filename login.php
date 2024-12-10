<!-- login.php -->

<?php 
session_start();
require 'db_connection.php'; // Ensure the path to db_connection.php is correct

// Connect to the user_management database
$conn = connectToDatabase('user_management');

$username_error = '';
$password_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare a MySQLi statement to check if the username exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the hashed password using password_verify
        if (password_verify($password, $user['password'])) {
            // Login successful, store user information in session and redirect
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Store the role ('admin' or 'user')
            $_SESSION['user_id'] = $user['id']; // Store the user ID in the session
            header("Location: dashboard.php");
            exit();
        } else {
            // Password is incorrect
            $password_error = "Incorrect password";
        }
    } else {
        // Username not found
        $username_error = "No user found with that username";
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Istok+Web:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
</head>
<body>
    <div class="container">
        <!-- Logo Section -->
        <div class="logo-container">
            <img src="lab-icon.png" alt="Lab Flask Logo" class="logo">
        </div>

        <!-- Text Section (ALIMS and Subtitle) -->
        <div class="text-container">
            <h1>ALIMS</h1>
            <h2>Automated Laboratory Inventory Management System</h2>
        </div>

        <!-- Login Form Section -->
        <div class="login-container">

            <form class="login-form" action="login.php" method="POST">
                <div class="interactive">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                    <?php if (!empty($username_error)): ?>
                        <span class="error-message"><?php echo $username_error; ?></span>
                    <?php endif; ?>
                </div>

                <div class="interactive">
                    <label for="password">Password:</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" required>
                        <span class="eye-icon" onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <?php if (!empty($password_error)): ?>
                        <span class="error-message"><?php echo $password_error; ?></span>
                    <?php endif; ?>
                </div>

                <div class="signin-button">
                    <button type="submit">Sign in</button>
                </div>

                <div class="forgotpassword">
                    <p><a href="#">Forgot password?</a></p>
                </div>
            </form>

        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById("password");
            const eyeIcon = document.querySelector(".eye-icon i");

            // Toggle the type attribute
            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash"); // Switch to eye-slash icon
            } else {
                passwordField.type = "password";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye"); // Switch back to eye icon
            }
        }
    </script>

</body>
</html>