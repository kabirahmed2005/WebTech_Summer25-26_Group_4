<?php
session_start();
header('Content-Type: application/json');
include "../../Common/model/DatabaseConnection.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    echo json_encode(["success" => false, "message" => "Not authorized"]);
    exit();
}

$studentId = $_SESSION['user_id'];
$quizId = isset($_POST['quiz_id']) ? intval($_POST['quiz_id']) : 0;
$answersJson = isset($_POST['answers']) ? $_POST['answers'] : "{}";
$answers = json_decode($answersJson, true);

if (!is_array($answers)) $answers = [];

$connection = new DatabaseConnection();
$conn = $connection->OpenCon();

$stmt = $conn->prepare("SELECT id, correct_option, marks FROM questions WHERE quiz_id = ?");
$stmt->bind_param("i", $quizId);
$stmt->execute();
$result = $stmt->get_result();

$score = 0;
$totalMarks = 0;

while ($row = $result->fetch_assoc()) {
    $totalMarks += intval($row['marks']);
    $questionId = strval($row['id']);
    if (isset($answers[$questionId]) && $answers[$questionId] === $row['correct_option']) {
        $score += intval($row['marks']);
    }
}
$stmt->close();

$insert = $conn->prepare("INSERT INTO results (student_id, quiz_id, score, total_marks) VALUES (?, ?, ?, ?)");
$insert->bind_param("iiii", $studentId, $quizId, $score, $totalMarks);

if ($insert->execute()) {
    echo json_encode(["success" => true, "score" => $score, "total_marks" => $totalMarks]);
} else {
    echo json_encode(["success" => false, "message" => "Could not submit quiz"]);
}

$insert->close();
$connection->CloseCon($conn);
