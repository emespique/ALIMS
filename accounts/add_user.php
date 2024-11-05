<!-- add_user.php -->
<?php
// Start the session and include the database connection
require '../header.php';
require '../db_connection.php';

// Initialize variables and error messages
$first_name = $middle_initial = $last_name = $designation = $laboratory = $username = $email = $password = $confirm_password = "";
$username_error = $email_error = $password_error = $confirm_password_error = "";

// Handle form submission when the form is posted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve and sanitize the form data
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $middle_initial = htmlspecialchars(trim($_POST['middle_initial']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $designation = htmlspecialchars(trim($_POST['designation']));
    $laboratory = htmlspecialchars(trim($_POST['laboratory']));
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));
    $email = htmlspecialchars(trim($_POST['email']));

    // Validate the email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Invalid email format";
    }

    // Validate the username
    if (strlen($username) < 8 || strlen($username) > 15 || !preg_match("/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z0-9]+$/", $username)) {
        $username_error = "Username must be 8-15 alphanumeric characters";
    }

    // Validate the password
    if (strlen($password) < 8 || strlen($password) > 15 || !preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,15}$/", $password)) {
        $password_error = "Password must be 8-15 characters composed of letters, numbers, and special characters";
    }

    if ($password !== $confirm_password) {
        $confirm_password_error = "Passwords do not match";
    }

    // If there are no errors, proceed to add the user
    if (empty($username_error) && empty($email_error) && empty($password_error) && empty($confirm_password_error)) {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare an insert statement
        $stmt = $conn->prepare("INSERT INTO users (first_name, middle_initial, last_name, designation, laboratory, username, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $first_name, $middle_initial, $last_name, $designation, $laboratory, $username, $email, $hashed_password);

        // Execute the statement and check for errors
        if ($stmt->execute()) {
            // Redirect back to the accounts page if the insertion is successful
            header("Location: accounts.php");
            exit();
        } else {
            echo "Error adding user: " . $conn->error;
        }

        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <link rel="stylesheet" type="text/css" href="../css/accounts.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script defer src="../js/user_validation.js"></script>
</head>
<body>
    <?php include '../header.php'; ?> 
    
    <div class="content">
        <div class="add-container">
            <div class="information">
                <h2 id="add-user-header">ADD USER</h2>
                <form action="add_user.php" method="POST">
                    <div class="input-group">
                        <label for="last_name">Name</label>
                        <input type="text" class="name-input" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" placeholder="Last Name" required>
                        <input type="text" class="name-input" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" placeholder="First Name" required>
                        <input type="text" class="name-input" name="middle_initial" value="<?php echo htmlspecialchars($middle_initial); ?>" placeholder="Middle Initial">
                    </div>
                    
                    <div class="input-group">
                        <label for="designation">Designation</label>
                        <select class="info-dropdown" name="designation" required>
                            <option value="">Select Designation</option>
                            <option value="Medical Technologist" <?php if ($designation == "Medical Technologist") echo "selected"; ?>>Medical Technologist</option>
                            <option value="Researcher" <?php if ($designation == "Researcher") echo "selected"; ?>>Researcher</option>
                            <option value="Lab Manager" <?php if ($designation == "Lab Manager") echo "selected"; ?>>Lab Manager</option>
                            <option value="Student" <?php if ($designation == "Student") echo "selected"; ?>>Student</option>
                            <option value="Technician" <?php if ($designation == "Technician") echo "selected"; ?>>Technician</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="laboratory">Laboratory</label>
                        <select class="info-dropdown" name="laboratory" required>
                            <option value="">Select Laboratory</option>
                            <option value="Pathology" <?php if ($laboratory == "Pathology") echo "selected"; ?>>Pathology</option>
                            <option value="Immunology" <?php if ($laboratory == "Immunology") echo "selected"; ?>>Immunology</option>
                            <option value="Microbiology" <?php if ($laboratory == "Microbiology") echo "selected"; ?>>Microbiology</option>
                        </select>
                    </div>

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
                            <input type="password" id="password" class="name-input" name="password" value="" placeholder="Password" required>
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
                            <input type="password" id="reenteredpassword" class="name-input" name="confirm_password" value="" placeholder="Re-enter Password" required>
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
                        <button type="button" class="cancel-button" onclick="window.location.href='accounts.php';">CANCEL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include '../footer.php'; ?>
</body>
</html>