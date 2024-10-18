<!-- update_personal_info.php -->

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

// Initialize variables for form submission
$first_name = $middle_initial = $last_name = $designation = $laboratory = "";

// Handle form submission when the form is posted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve and sanitize the form data
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $middle_initial = htmlspecialchars(trim($_POST['middle_initial']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $designation = htmlspecialchars(trim($_POST['designation']));
    $laboratory = htmlspecialchars(trim($_POST['laboratory']));
    
    // Prepare an update statement
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, middle_initial = ?, last_name = ?, designation = ?, laboratory = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $first_name, $middle_initial, $last_name, $designation, $laboratory, $user_id);
    
    // Execute the statement and check for errors
    if ($stmt->execute()) {
        // Redirect back to the account page if the update is successful
        header("Location: account.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
    
    $stmt->close();
} else {
    // Fetch existing user information to populate the form if it's a GET request
    $stmt = $conn->prepare("SELECT first_name, middle_initial, last_name, designation, laboratory FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($first_name, $middle_initial, $last_name, $designation, $laboratory);
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
    <title>Update Personal Information</title>
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <link rel="stylesheet" type="text/css" href="../css/account.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../header.php'; ?> 
    
    <div class="content">
        <div class="update-container">
            <div class="information">
                <h2 id="update-personal-info-header">UPDATE PERSONAL INFORMATION</h2>
                <form action="update_personal_info.php" method="POST">
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