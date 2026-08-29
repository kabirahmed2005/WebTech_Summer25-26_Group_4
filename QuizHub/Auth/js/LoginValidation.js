// LoginValidation.js
// Handles the Student/Teacher toggle and submits the login form using AJAX.

document.addEventListener("DOMContentLoaded", function () {
    const roleButtons = document.querySelectorAll(".role-btn");
    const roleInput = document.getElementById("role");
    const loginForm = document.getElementById("loginForm");
    const loginMsg = document.getElementById("loginMsg");

    roleButtons.forEach(function (btn) {
        btn.addEventListener("click", function () {
            roleButtons.forEach(function (b) { b.classList.remove("active"); });
            btn.classList.add("active");
            roleInput.value = btn.getAttribute("data-role");
        });
    });

    loginForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value;
        const role = roleInput.value;

        loginMsg.textContent = "";
        loginMsg.className = "msg";

        if (username === "" || password === "") {
            loginMsg.textContent = "Please fill in both fields.";
            loginMsg.classList.add("error");
            return;
        }

        // AJAX request to the Control layer
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../Control/LoginCheck.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                let response;
                try {
                    response = JSON.parse(xhr.responseText);
                } catch (err) {
                    loginMsg.textContent = "Something went wrong. Please try again.";
                    loginMsg.classList.add("error");
                    return;
                }

                if (response.success) {
                    loginMsg.textContent = response.message;
                    loginMsg.classList.add("success");
                    window.location.href = response.redirect;
                } else {
                    loginMsg.textContent = response.message;
                    loginMsg.classList.add("error");
                }
            }
        };

        const params = "username=" + encodeURIComponent(username) +
            "&password=" + encodeURIComponent(password) +
            "&role=" + encodeURIComponent(role);

        xhr.send(params);
    });
});
