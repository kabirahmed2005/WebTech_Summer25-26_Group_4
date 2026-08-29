<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../../Auth/View/Login.php");
    exit();
}
include "../../Common/model/DatabaseConnection.php";

$connection = new DatabaseConnection();
$conn = $connection->OpenCon();

$sql = "SELECT q.id, q.title, u.full_name AS teacher_name, COUNT(qs.id) AS question_count, COALESCE(SUM(qs.marks),0) AS total_marks
        FROM quizzes q
        JOIN users u ON u.id = q.teacher_id
        LEFT JOIN questions qs ON qs.quiz_id = q.id
        GROUP BY q.id, q.title, u.full_name
        ORDER BY q.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>QuizHub - Available Quizzes</title>
<link rel="stylesheet" href="../css/student.css">
</head>
<body>

<div class="navbar">
    <div class="left">
        <span class="brand-name">QuizHub</span>
    </div>
    <div class="right">
        <span class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
        <a href="../../Auth/Control/Logout.php" class="exit-btn">Logout</a>
    </div>
</div>

<div class="container">
    <h1>Available Quizzes</h1>
    <p class="subtitle">Pick a quiz below to get started.</p>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="list-card">
                <div>
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><?php echo $row['question_count']; ?> Questions &middot; <?php echo $row['total_marks']; ?> Marks &middot; by <?php echo htmlspecialchars($row['teacher_name']); ?></p>
                </div>
                <a class="start-btn" href="TakeQuiz.php?quiz_id=<?php echo $row['id']; ?>">Start Quiz</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="subtitle">No quizzes available right now.</p>
    <?php endif; ?>
</div>

</body>
</html>
<?php $connection->CloseCon($conn); ?>
