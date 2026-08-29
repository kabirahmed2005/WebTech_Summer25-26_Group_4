<?php
session_start();
header('Content-Type: application/json');
include "../../Common/model/DatabaseConnection.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    echo json_encode(["success" => false, "message" => "Not authorized"]);
    exit();
}

$teacherId = $_SESSION['user_id'];

$quizTitle = isset($_POST['quiz_title']) ? trim($_POST['quiz_title']) : "";
$questionText = isset($_POST['question_text']) ? trim($_POST['question_text']) : "";
$optionA = isset($_POST['option_a']) ? trim($_POST['option_a']) : "";
$optionB = isset($_POST['option_b']) ? trim($_POST['option_b']) : "";
$optionC = isset($_POST['option_c']) ? trim($_POST['option_c']) : "";
$optionD = isset($_POST['option_d']) ? trim($_POST['option_d']) : "";
$correctOption = isset($_POST['correct_option']) ? $_POST['correct_option'] : "A";
$marks = isset($_POST['marks']) ? intval($_POST['marks']) : 1;

if ($quizTitle == "" || $questionText == "" || $optionA == "" || $optionB == "" || $optionC == "" || $optionD == "") {
    echo json_encode(["success" => false, "message" => "Please fill in all fields"]);
    exit();
}

$connection = new DatabaseConnection();
$conn = $connection->OpenCon();

// Find or create the quiz for this teacher
$stmt = $conn->prepare("SELECT id FROM quizzes WHERE title = ? AND teacher_id = ?");
$stmt->bind_param("si", $quizTitle, $teacherId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $quizId = $result->fetch_assoc()['id'];
} else {
    $insertQuiz = $conn->prepare("INSERT INTO quizzes (title, teacher_id) VALUES (?, ?)");
    $insertQuiz->bind_param("si", $quizTitle, $teacherId);
    $insertQuiz->execute();
    $quizId = $insertQuiz->insert_id;
    $insertQuiz->close();
}
$stmt->close();

$insertQ = $conn->prepare("INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option, marks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$insertQ->bind_param("issssssi", $quizId, $questionText, $optionA, $optionB, $optionC, $optionD, $correctOption, $marks);

if ($insertQ->execute()) {
    $newId = $insertQ->insert_id;
    echo json_encode([
        "success" => true,
        "question" => [
            "id" => $newId,
            "question_text" => $questionText,
            "option_a" => $optionA,
            "option_b" => $optionB,
            "option_c" => $optionC,
            "option_d" => $optionD,
            "correct_option" => $correctOption,
            "marks" => $marks
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Could not save question: " . $conn->error]);
}

$insertQ->close();
$connection->CloseCon($conn);
