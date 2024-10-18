<!-- update_personal_info.php -->

<?php
// Include database connection
require 'db_connection.php';

// Retrieve the user's current information (assuming you have stored user ID in the session)
$user_id = $_SESSION['user_id'];

// Fetch user information from the database
$stmt = $conn->prepare("SELECT first_name, middle_initial, last_name, designation, laboratory FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $middle_initial, $last_name, $designation, $laboratory);
$stmt->fetch();
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Personal Information</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" type="text/css" href="css/account.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?> <!-- Include header if necessary -->
    
    <div class="content">
        <h2>UPDATE PERSONAL INFORMATION</h2>
        <form action="process_update_personal_info.php" method="POST">
            <div class="input-group">
                <label for="last_name">Name</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" placeholder="Last Name" required>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" placeholder="First Name" required>
                <input type="text" name="middle_initial" value="<?php echo htmlspecialchars($middle_initial); ?>" placeholder="Middle Initial">
            </div>
            
            <div class="input-group">
                <label for="designation">Designation</label>
                <select name="designation" required>
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
                <select name="laboratory" required>
                    <option value="">Select Laboratory</option>
                    <option value="Pathology" <?php if ($laboratory == "Pathology") echo "selected"; ?>>Pathology</option>
                    <option value="Immunology" <?php if ($laboratory == "Immunology") echo "selected"; ?>>Immunology</option>
                    <option value="Microbiology" <?php if ($laboratory == "Microbiology") echo "selected"; ?>>Microbiology</option>
                </select>
            </div>
            
            <button type="submit" class="save-button">SAVE</button>
        </form>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>