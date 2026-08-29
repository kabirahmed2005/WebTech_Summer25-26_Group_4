<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>QuizHub - Register</title>
<link rel="stylesheet" href="../css/auth.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="brand">
        <h1>Create an Account</h1>
        <p>QuizHub &ndash; Simple Online Quiz System</p>
    </div>

    <div class="card">
        <form id="registerForm">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" placeholder="e.g. Ali Hassan" required>
            <p id="nameMsg" class="msg"></p>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Create a password" required>

            <label for="confirmPassword">Confirm Password</label>
            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Repeat your password" required>

            <label>Register as</label>
            <div class="role-toggle">
                <button type="button" class="role-btn active" data-role="student">Student</button>
                <button type="button" class="role-btn" data-role="teacher">Teacher</button>
            </div>
            <input type="hidden" id="role" name="role" value="student">

            <button type="submit" class="primary-btn">Register</button>
            <p id="registerMsg" class="msg"></p>
        </form>
    </div>

    <p class="footer-text">Already have an account? <a href="Login.php" class="link">Login</a></p>
    <p class="copyright">&copy; 2025 QuizHub &middot; University Project</p>
</div>

<script src="../js/RegisterValidation.js"></script>
</body>
</html>
