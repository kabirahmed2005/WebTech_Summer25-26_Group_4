<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../../Auth/View/Login.php");
    exit();
}
include "../../Common/model/DatabaseConnection.php";

$quizId = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;

$connection = new DatabaseConnection();
$conn = $connection->OpenCon();

$stmt = $conn->prepare("SELECT title FROM quizzes WHERE id = ?");
$stmt->bind_param("i", $quizId);
$stmt->execute();
$quizResult = $stmt->get_result();

if ($quizResult->num_rows == 0) {
    header("Location: QuizList.php");
    exit();
}
$quiz = $quizResult->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>QuizHub - <?php echo htmlspecialchars($quiz['title']); ?></title>
<link rel="stylesheet" href="../css/student.css">
</head>
<body>

<div class="navbar">
    <div class="left">
        <span class="brand-name">QuizHub</span>
        <span class="page-title">&middot; <?php echo htmlspecialchars($quiz['title']); ?></span>
    </div>
    <div class="right">
        <span class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
        <a href="QuizList.php" class="exit-btn">Exit Quiz</a>
    </div>
</div>

<div class="container">
    <h1><?php echo htmlspecialchars($quiz['title']); ?></h1>
    <p class="subtitle" id="questionOfText">Question 1 of &hellip;</p>

    <div class="progress-bar-track">
        <div class="progress-bar-fill" id="progressFill"></div>
    </div>
    <p class="progress-label" id="progressLabel">1 / &hellip; Questions</p>

    <div class="q-card" id="quizCard">
        <p class="empty-note">Loading question...</p>
    </div>

    <div class="quiz-nav">
        <button class="nav-btn" id="prevBtn">&lsaquo; Previous</button>
        <div class="dots" id="dotsWrap"></div>
        <button class="nav-btn next" id="nextBtn">Next &rsaquo;</button>
    </div>
</div>

<script>
    const QUIZ_ID = <?php echo $quizId; ?>;
</script>
<script src="../js/quiz.js"></script>
</body>
</html>
<?php
$stmt->close();
$connection->CloseCon($conn);
?>
