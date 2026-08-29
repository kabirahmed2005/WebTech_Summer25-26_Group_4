<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: ../../Auth/View/Login.php");
    exit();
}
include "../../Common/model/DatabaseConnection.php";

$teacherId = $_SESSION['user_id'];
$connection = new DatabaseConnection();
$conn = $connection->OpenCon();

$stmt = $conn->prepare(
    "SELECT q.id, q.title, COUNT(qs.id) AS question_count, COALESCE(SUM(qs.marks),0) AS total_marks
     FROM quizzes q
     LEFT JOIN questions qs ON qs.quiz_id = q.id
     WHERE q.teacher_id = ?
     GROUP BY q.id, q.title
     ORDER BY q.created_at DESC"
);
$stmt->bind_param("i", $teacherId);
$stmt->execute();
$quizzes = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>QuizHub - My Quizzes</title>
<link rel="stylesheet" href="../css/teacher.css">
</head>
<body>

<div class="navbar">
    <div class="left">
        <span class="brand-name">QuizHub</span>
        <div class="nav-tabs">
            <a href="QuestionSetup.php">Question Setup</a>
            <a href="MyQuizzes.php" class="active">My Quizzes</a>
        </div>
    </div>
    <div class="right">
        <span class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
        <a href="../../Auth/Control/Logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="container">
    <div class="card">
        <h2>My Quizzes</h2>
        <table class="quiz-table">
            <tr>
                <th>Quiz Title</th>
                <th>Questions</th>
                <th>Total Marks</th>
            </tr>
            <?php if ($quizzes->num_rows > 0): ?>
                <?php while ($row = $quizzes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo $row['question_count']; ?></td>
                        <td><?php echo $row['total_marks']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3" class="empty-note">No quizzes created yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

</body>
</html>
<?php
$stmt->close();
$connection->CloseCon($conn);
?>
