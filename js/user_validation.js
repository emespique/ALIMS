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