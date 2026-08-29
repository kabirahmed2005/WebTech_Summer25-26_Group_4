<?php
session_start();
header('Content-Type: application/json');
include "../../Common/model/DatabaseConnection.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    echo json_encode(["questions" => []]);
    exit();
}

$teacherId = $_SESSION['user_id'];
$quizTitle = isset($_POST['quiz_title']) ? trim($_POST['quiz_title']) : "";

$connection = new DatabaseConnection();
$conn = $connection->OpenCon();

$questions = [];

if ($quizTitle != "") {
    $stmt = $conn->prepare("SELECT id FROM quizzes WHERE title = ? AND teacher_id = ?");
    $stmt->bind_param("si", $quizTitle, $teacherId);
    $stmt->execute();
    $quizResult = $stmt->get_result();

    if ($quizResult->num_rows > 0) {
        $quiz = $quizResult->fetch_assoc();
        $quizId = $quiz['id'];

        $qStmt = $conn->prepare("SELECT id, question_text, option_a, option_b, option_c, option_d, correct_option, marks FROM questions WHERE quiz_id = ? ORDER BY id ASC");
        $qStmt->bind_param("i", $quizId);
        $qStmt->execute();
        $qResult = $qStmt->get_result();

        while ($row = $qResult->fetch_assoc()) {
            $questions[] = $row;
        }
        $qStmt->close();
    }
    $stmt->close();
}

echo json_encode(["questions" => $questions]);

$connection->CloseCon($conn);
