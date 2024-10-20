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
$username = $password = $confirm_password = "";
$username_error = $password_error = $confirm_password_error = "";

// Handle form submission when the form is posted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve and sanitize the form data
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));

    // Validate username (min 8 characters, max 15 alphanumeric)
    if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,15}$/", $username)) {
        $username_error = "Username must be 8-15 alphanumeric characters";
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
    if (empty($username_error) && empty($password_error) && empty($confirm_password_error)) {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare an update statement
        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $hashed_password, $user_id);
        
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
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username);
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
                    
                    <div class="input-group" id="password_group">
                        <label for="password">Password</label>
                        <input type="password" class="name-input" name="password" value="" placeholder="Password" required>
                        <?php if (!empty($password_error)): ?>
                            <span class="error" id="password_error"><?php echo $password_error; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="input-group" id="matching_group">
                        <label for="confirm_password">Re-enter Password</label>
                        <input type="password" class="name-input" name="confirm_password" value="" placeholder="Re-enter Password" required>
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

    <script>
        function validateForm() {
            let username = document.querySelector('[name="username"]').value;
            let password = document.querySelector('[name="password"]').value;
            let confirmPassword = document.querySelector('[name="confirm_password"]').value;
            let isValid = true;

            // Reset error messages
            document.querySelectorAll('.error').forEach(el => el.textContent = '');

            // Username validation
            if (!/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,15}$/.test(username)) {
                document.querySelector('[name="username"]').nextElementSibling.textContent = 'Username must be 8-15 alphanumeric characters';
                isValid = false;
            }

            // Password validation
            if (!/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,15}$/.test(password)) {
                document.querySelector('[name="password"]').nextElementSibling.textContent = 'Password must be 8-15 characters composed of letters, numbers, and special characters';
                isValid = false;
            }

            // Confirm password validation
            if (password !== confirmPassword) {
                document.querySelector('[name="confirm_password"]').nextElementSibling.textContent = 'Passwords do not match';
                isValid = false;
            }

            return isValid;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Get all input fields with the class 'name-input'
            const inputs = document.querySelectorAll('.name-input');

            // Add a focus event listener to each input field
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    // Find all error messages on the page
                    const errorSpans = document.querySelectorAll('.error');

                    // Clear the content and hide all error messages
                    errorSpans.forEach(errorSpan => {
                        errorSpan.textContent = ''; // Remove the text content
                        errorSpan.classList.add('hidden'); // Add hidden class to hide the element and its arrow

                        // Additionally, ensure any inline styles that might keep the arrow visible are removed
                        errorSpan.style.display = 'none';
                    });
                });
            });
        });
    </script>
</body>
</html>