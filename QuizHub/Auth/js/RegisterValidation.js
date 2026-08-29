// RegisterValidation.js
// Handles the Student/Teacher toggle, live full-name availability check (AJAX)
// and submits the registration form using AJAX.

document.addEventListener("DOMContentLoaded", function () {
    const roleButtons = document.querySelectorAll(".role-btn");
    const roleInput = document.getElementById("role");
    const registerForm = document.getElementById("registerForm");
    const registerMsg = document.getElementById("registerMsg");
    const fullnameInput = document.getElementById("fullname");
    const nameMsg = document.getElementById("nameMsg");

    roleButtons.forEach(function (btn) {
        btn.addEventListener("click", function () {
            roleButtons.forEach(function (b) { b.classList.remove("active"); });
            btn.classList.add("active");
            roleInput.value = btn.getAttribute("data-role");
        });
    });

    // Live check (AJAX) whenever the user leaves the Full Name field
    fullnameInput.addEventListener("blur", function () {
        const fullname = fullnameInput.value.trim();
        nameMsg.textContent = "";
        nameMsg.className = "msg";

        if (fullname === "") return;

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../Control/CheckUsername.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                const response = JSON.parse(xhr.responseText);
                nameMsg.textContent = response.message;
                nameMsg.classList.add(response.available ? "success" : "error");
            }
        };

        xhr.send("fullname=" + encodeURIComponent(fullname));
    });

    registerForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const fullname = fullnameInput.value.trim();
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("confirmPassword").value;
        const role = roleInput.value;

        registerMsg.textContent = "";
        registerMsg.className = "msg";

        if (fullname === "" || password === "" || confirmPassword === "") {
            registerMsg.textContent = "Please fill in all fields.";
            registerMsg.classList.add("error");
            return;
        }

        if (password !== confirmPassword) {
            registerMsg.textContent = "Passwords do not match.";
            registerMsg.classList.add("error");
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../Control/RegisterCheck.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                const response = JSON.parse(xhr.responseText);
                registerMsg.textContent = response.message;
                registerMsg.classList.add(response.success ? "success" : "error");

                if (response.success) {
                    registerForm.reset();
                    setTimeout(function () {
                        window.location.href = "Login.php";
                    }, 1500);
                }
            }
        };

        const params = "fullname=" + encodeURIComponent(fullname) +
            "&password=" + encodeURIComponent(password) +
            "&role=" + encodeURIComponent(role);

        xhr.send(params);
    });
});
