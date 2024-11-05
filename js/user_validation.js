// Handles the removal of error messages when the user interacts with the input fields
document.addEventListener('DOMContentLoaded', function() {
    // Get all input fields and error spans
    const inputs = document.querySelectorAll('.name-input');
    const errorSpans = document.querySelectorAll('.error');

    // Add a focus event listener to each input field
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            // Clear the content and hide all error messages
            errorSpans.forEach(errorSpan => {
                errorSpan.textContent = ''; // Remove the text content
                errorSpan.classList.add('hidden'); // Add hidden class to hide the element and its arrow
                
                // Additionally, ensure any inline styles that might keep the arrow visible are removed
                errorSpan.style.display = 'none';
                const arrow = errorSpan.querySelector('::before'); // Try targeting the arrow directly if styled separately
                if (arrow) {
                    arrow.style.display = 'none';
                }
            });
        });
    });
});

// Function to toggle the visibility of the password field
function togglePassword() {
    const passwordField = document.getElementById("password");
    const eyeIcon = document.getElementById("eye-icon-password");

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

// Function to toggle the visibility of the re-entered password field
function toggleReenteredPassword() {
    const passwordField = document.getElementById("reenteredpassword");
    const eyeIcon = document.getElementById("eye-icon-reentered");

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

// Function to search a user
function searchUser() {
    // Get search value and option
    const searchInput = document.getElementById('searchInput').value.trim();
    const searchOption = document.getElementById('searchOption').value;

    if (searchInput === '') {
        alert("Please enter a search term");
        return;
    }

    // Redirect to the search page with the search parameters
    window.location.href = `accounts.php?search=${searchInput}&option=${searchOption}`;
}

// Function to delete the selected user in the accounts table
function deleteUser(userId) {
    // Set the hidden input value in the form
    document.getElementById('deleteUserId').value = userId;
    // Show the modal
    document.getElementById('deleteModal').style.display = 'flex';
}

// Function to close the Delete modal
function closeModal() {
    // Hide the modal
    document.getElementById('deleteModal').style.display = 'none';
}