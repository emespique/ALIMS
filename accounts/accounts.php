<!-- accounts.php -->
<?php 
/*
    // Start the session and include the necessary files
    require '../header.php';
    require '../db_connection.php';
    // Check if the user ID is set in the session and if the user is an admin
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: login.php");
        exit();
    }
    // Fetch user data from the database
    $stmt = $conn->prepare("SELECT id, first_name, middle_initial, last_name, designation, laboratory, username FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
*/

// Start the session and include the necessary files
require '../header.php';
require '../db_connection.php';

// Check if the user ID is set in the session and if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Initialize search variables
$search = isset($_GET['search']) ? htmlspecialchars(trim($_GET['search'])) : '';
$option = isset($_GET['option']) ? htmlspecialchars(trim($_GET['option'])) : '';

// Prepare SQL query based on search option
if ($search && $option) {
    if ($option === 'delete') {
        $stmt = $conn->prepare("SELECT id, first_name, middle_initial, last_name, designation, laboratory, username, role, email FROM users WHERE first_name LIKE ? OR last_name LIKE ? OR username LIKE ?");
        $searchTerm = "%" . $search . "%";
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    } elseif ($option === 'single') {
        $stmt = $conn->prepare("SELECT id, first_name, middle_initial, last_name, designation, laboratory, username, role, email FROM users WHERE first_name LIKE ? OR last_name LIKE ? OR username LIKE ?");
        $searchTerm = "%" . $search . "%";
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    } else { // View all users
        $stmt = $conn->prepare("SELECT id, first_name, middle_initial, last_name, designation, laboratory, username, role, email FROM users");
    }
} else {
    // Default view: show all users
    $stmt = $conn->prepare("SELECT id, first_name, middle_initial, last_name, designation, laboratory, username, role, email FROM users");
}

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
    <link rel="stylesheet" type="text/css" href="../css/accounts.css"> <!-- Admin-specific CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <script defer src="../js/user_validation.js"></script>
</head>
<body>
    <?php include '../header.php'; ?> 
    
    <div class="table-content">
        <h2 id="accounts-header">Accounts</h2>

        <div class="search-and-add-container">
            <!-- Search Form -->
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search by name or username">
                <select id="searchOption">
                    <option value="all">View All Users</option>
                    <option value="single">View Single User</option>
                    <option value="delete">Delete User</option>
                </select>
                <button class="search-button" onclick="searchUser()">Search</button>
            </div>
            <div class="add-button-container">
                <button class="add-button" onclick="window.location.href='add_user.php'">Add User</button>
            </div>
        </div>
        
        <table class="user-table">
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Laboratory</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                        <td>
                            <?php echo htmlspecialchars(
                                $row['first_name'] . 
                                (!empty($row['middle_initial']) ? ' ' . $row['middle_initial'] . '. ' : ' ') . 
                                $row['last_name']
                            ); 
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['designation']); ?></td>
                        <td><?php echo htmlspecialchars($row['laboratory']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td>
                            <div class="actions">
                                <?php if ($_SESSION['user_id'] == 1 || ($_SESSION['user_id'] == $row['id']) || ($row['role'] == 'user' && $_SESSION['role'] == 'admin')): ?>
                                    <button class="edit-button" onclick="window.location.href='edit_user.php?id=<?php echo $row['id']; ?>'">Edit</button>
                                <?php endif; ?>
                                
                                <?php if ($_SESSION['user_id'] == 1 || ($_SESSION['user_id'] == $row['id']) || ($row['role'] == 'user' && $_SESSION['role'] == 'admin')): ?>
                                    <button class="delete-button" onclick="deleteUser(<?php echo $row['id']; ?>)">Delete</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="modal" id="deleteModal" style="display: none;">
        <div class="modal-content">
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete this user?</p>
            <form id="deleteForm" action="delete_user.php" method="POST">
                <input type="hidden" name="user_id" id="deleteUserId">
                <div class="modal-buttons">
                    <button type="submit" class="delete-button">Delete</button>
                    <button type="button" class="cancel-button" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>