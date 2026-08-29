<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../../Auth/View/Login.php");
    exit();
}

$score = isset($_GET['score']) ? intval($_GET['score']) : 0;
$total = isset($_GET['total']) ? intval($_GET['total']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>QuizHub - Result</title>
<link rel="stylesheet" href="../css/student.css">
</head>
<body>

<div class="navbar">
    <div class="left"><span class="brand-name">QuizHub</span></div>
    <div class="right">
        <span class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
        <a href="../../Auth/Control/Logout.php" class="exit-btn">Logout</a>
    </div>
</div>

<div class="container">
    <div class="result-card">
        <h1>Quiz Completed</h1>
        <p class="subtitle">Here is your result</p>
        <p class="result-score"><?php echo $score; ?> / <?php echo $total; ?></p>
        <a class="start-btn" href="QuizList.php">Back to Quizzes</a>
    </div>
</div>

</body>
</html>
