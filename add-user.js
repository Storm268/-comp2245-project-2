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

document.addEventListener("DOMContentLoaded", function () {
    const addUserForm = document.getElementById("addUserForm");
    const contentArea = document.querySelector(".content-area");

    // Hide addUserForm
    contentArea.style.display = "none";

    // Checks if the user is already an admin    
    fetch("auth.php", { credentials: "same-origin" })
        .then((response) => response.json())
        .then((data) => {
            if (data.authenticated && data.user.is_admin) {
                // If admin, show the form
                contentArea.style.display = "block";
            } else {
                // If not an admin, redirect to dashboard
                alert("Access Denied: Only administrators can add users.");
                window.location.href = "dashboard.html";
            }
        })
        .catch((error) => {
            // Handle errors
            console.error("Error checking admin status:", error);
            alert("An unexpected error occurred. Please try again.");
            window.location.href = "dashboard.html";
        });

    // Form Submission Handler
    if (addUserForm) {
        addUserForm.addEventListener("submit", function (e) {
            e.preventDefault(); // Prevent default form submission

            clearInvalid(); // Clear any previous invalid statuses

            //get input data from fields
            const firstname = document.getElementById("firstname");
            const lastname = document.getElementById("lastname");
            const email = document.getElementById("email");
            const password = document.getElementById("password");

            let isValid = true;

            // Input Validation
            if (firstname.value.trim() === "") {
                isValid = false;
                invalid(firstname, "First Name is required.");
            }

            if (lastname.value.trim() === "") {
                isValid = false;
                invalid(lastname, "Last Name is required.");
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value.trim())) {
                isValid = false;
                invalid(email, "Invalid email format.");
            }

            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
            if (!passwordRegex.test(password.value.trim())) {
                isValid = false;
                invalid(
                    password,
                    "Password must have a minimum of 8 characters with the use of uppercase and lowercase letters along with any numbers or special signs"
                );
            }

            // No submission with invalid form
            if (!isValid) {
                return;
            }

            //Submit data via AJAX once it is valid 
            const formData = new FormData(this);
            fetch("add-user.php", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                         // If the user was added successfully, show confirmation
                        alert(data.message || "User added successfully!"); 
                        this.reset(); // Reset the form
                        clearInvalid();

                    } else {
                        alert(data.error || "An error occurred while submitting the form.");
                    }
                })
                .catch((error) => {
                    // Handle unexpected errors
                    console.error("Error:", error);
                    alert("An unexpected error occurred. Please try again.");
                });
        });
    }
});

// Function to add invalid status
function invalid(input, message) {
    input.classList.add("input-invalid");

    const errorMessage = document.createElement("div");
    errorMessage.classList.add("error-message");
    errorMessage.textContent = message;

     // Add error message below input field
    input.parentNode.appendChild(errorMessage);
}
