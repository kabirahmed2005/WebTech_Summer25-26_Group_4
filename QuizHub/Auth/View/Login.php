<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'student') header("Location: ../../Student/View/QuizList.php");
    elseif ($_SESSION['role'] == 'teacher') header("Location: ../../Teacher/View/QuestionSetup.php");
    elseif ($_SESSION['role'] == 'admin') header("Location: ../../Admin/View/Dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>QuizHub - Login</title>
<link rel="stylesheet" href="../css/auth.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="brand">
        <h1>Welcome to QuizHub</h1>
        <p>Simple Online Quiz System</p>
    </div>

    <div class="card">
        <form id="loginForm">
            <label for="username">Username or Email</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <label>Login as</label>
            <div class="role-toggle">
                <button type="button" class="role-btn active" data-role="student">Student</button>
                <button type="button" class="role-btn" data-role="teacher">Teacher</button>
                <button type="button" class="role-btn" data-role="admin">Admin</button>
            </div>
            <input type="hidden" id="role" name="role" value="student">

            <button type="submit" class="primary-btn">Login</button>
            <p id="loginMsg" class="msg"></p>

            <a href="#" class="link">Forgot password?</a>
        </form>
    </div>

    <p class="footer-text">Don't have an account? <a href="Register.php" class="link">Register</a></p>
    <p class="copyright">&copy; 2025 QuizHub &middot; University Project</p>
</div>

<script src="../js/LoginValidation.js"></script>
</body>
</html>
