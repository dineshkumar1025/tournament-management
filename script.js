document.addEventListener("DOMContentLoaded", function () {
    const userOption = document.getElementById("userOption");
    const userForm = document.getElementById("userForm");
    const registerUserButton = document.getElementById("registerUser");
    const userRegistrationForm = document.getElementById("userRegistrationForm");
    const resultContainer = document.getElementById("result");

    userOption.addEventListener("click", () => {
        hideForms();
        userForm.style.display = "block";
    });

    registerUserButton.addEventListener("click", () => {
        const userId = document.getElementById("userId").value;
        const userName = document.getElementById("userName").value;
        const userEmail = document.getElementById("userEmail").value;

      
        // Example using fetch:
        fetch("api/registerUser.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ userId, userName, userEmail }),
        })
            .then((response) => response.json())
            .then((data) => {
                resultContainer.style.display = "block";
                resultContainer.textContent = data.message;
            })
            .catch((error) => {
                console.error("Error:", error);
            });
    });

    function hideForms() {
        const forms = document.querySelectorAll(".form-container");
        forms.forEach((form) => {
            form.style.display = "none";
        });
    }
});
