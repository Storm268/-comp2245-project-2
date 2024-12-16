// Clear any invalid statuses
function clearInvalid() {
    // Remove invalid class
    document.querySelectorAll(".input-invalid").forEach((input) => {
        input.classList.remove("input-invalid"); 
    });

    // Remove any error messages
    document.querySelectorAll(".error-message").forEach((error) => {
        error.remove(); 
    });
}

// Add any invalid statuses 
function invalid(input, message) {
    input.classList.add("input-invalid"); 

    const errorMessage = document.createElement("div");
    errorMessage.classList.add("error-message");
    errorMessage.textContent = message;

    // Input error message after the input
    input.parentNode.appendChild(errorMessage);
}

// Event listener for the login form
document.getElementById("loginForm").addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent default form submission

    // Clear previous invalid status
    clearInvalid();

    // Get form fields
    const email = document.getElementById("email");
    const password = document.getElementById("password");

    let isValid = true;

    // Input Validation 
    if (!email.value.trim()) {
        isValid = false;
        invalid(email, "Email is required.");
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        isValid = false;
        invalid(email, "Invalid email format.");
    }

    if (!password.value.trim()) {
        isValid = false;
        invalid(password, "Password is required.");
    }

    // No submission with invalid form
    if (!isValid) {
        return;
    }

    //Submit data via AJAX once it is valid
    fetch("LogIn.php", {
        method: "POST",
        body: new URLSearchParams({
            email: email.value.trim(),
            password: password.value,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Redirect to the dashboard on success
                window.location.href = "dashboard.html";
            } else {
                //Error if login fails
                alert( "Invalid email or password.");
            }
        })
        .catch((error) => {
            // Handle any unexpected errors
            console.error("Error:", error);
            alert("An unexpected error occurred. Please try again.");
        });
});
