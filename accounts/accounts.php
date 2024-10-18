<!-- accounts.php -->
<?php
// Start the session and include the necessary files
require '../header.php';
require '../db_connection.php';

// Check if the user ID is set in the session and if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch user data from the database
$stmt = $conn->prepare("SELECT id, first_name, last_name, designation, laboratory, username FROM users");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts</title>
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <link rel="stylesheet" type="text/css" href="../css/admin_accounts_list.css"> <!-- Admin-specific CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../header.php'; ?> <!-- Include header if necessary -->
    
    <div class="content">
        <h2>Accounts</h2>
        <table class="user-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Laboratory</th>
                    <th>Username</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['designation']); ?></td>
                        <td><?php echo htmlspecialchars($row['laboratory']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td>
                            <div class="actions">
                                <button class="edit-button" onclick="window.location.href='edit_user.php?id=<?php echo $row['id']; ?>'">Edit</button>
                                <button class="delete-button" onclick="deleteUser(<?php echo $row['id']; ?>)">Delete</button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button class="add-button" onclick="window.location.href='add_user.php'">Add User</button>
    </div>

    <?php include '../footer.php'; ?>
    <script>
        // JavaScript function to confirm deletion
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                window.location.href = 'delete_user.php?id=' + userId;
            }
        }
    </script>
</body>
</html>
