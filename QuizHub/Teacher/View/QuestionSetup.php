<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: ../../Auth/View/Login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>QuizHub - Question Setup</title>
<link rel="stylesheet" href="../css/teacher.css">
</head>
<body>

<div class="navbar">
    <div class="left">
        <span class="brand-name">QuizHub</span>
        <div class="nav-tabs">
            <a href="QuestionSetup.php" class="active">Question Setup</a>
            <a href="MyQuizzes.php">My Quizzes</a>
        </div>
    </div>
    <div class="right">
        <span class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
        <a href="../../Auth/Control/Logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="container">

    <div class="card">
        <h2>Add New Question</h2>
        <form id="questionForm">
            <input type="hidden" id="questionId" value="">

            <label for="quizTitle">Quiz Title</label>
            <input type="text" id="quizTitle" placeholder="e.g. Web Programming Quiz" required>

            <label for="questionText">Question</label>
            <textarea id="questionText" rows="2" placeholder="Type your question here..." required></textarea>

            <div class="form-row">
                <div>
                    <label for="optionA">Option A</label>
                    <input type="text" id="optionA" placeholder="First option" required>
                </div>
                <div>
                    <label for="optionB">Option B</label>
                    <input type="text" id="optionB" placeholder="Second option" required>
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="optionC">Option C</label>
                    <input type="text" id="optionC" placeholder="Third option" required>
                </div>
                <div>
                    <label for="optionD">Option D</label>
                    <input type="text" id="optionD" placeholder="Fourth option" required>
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="correctAnswer">Correct Answer</label>
                    <select id="correctAnswer">
                        <option value="A">Option A</option>
                        <option value="B">Option B</option>
                        <option value="C">Option C</option>
                        <option value="D">Option D</option>
                    </select>
                </div>
                <div>
                    <label for="marks">Marks</label>
                    <input type="number" id="marks" value="1" min="1" required>
                </div>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn-primary" id="addBtn">Add Question</button>
                <button type="button" class="btn-secondary" id="clearBtn">Clear</button>
            </div>
            <p id="formMsg" class="msg" style="color:#dc2626;font-size:13px;"></p>
        </form>
    </div>

    <div class="card">
        <div class="section-header">
            <div>
                <h2>Added Questions</h2>
                <p id="questionCountText">0 questions added</p>
            </div>
            <span class="badge" id="totalMarksBadge">0 total marks</span>
        </div>

        <div id="questionsList">
            <p class="empty-note">No questions added yet. Enter a Quiz Title above and add your first question.</p>
        </div>
    </div>

</div>

<script src="../js/questionSetup.js"></script>
</body>
</html>
