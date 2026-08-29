<?php
session_start();
header('Content-Type: application/json');
include "../../Common/model/DatabaseConnection.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    echo json_encode(["success" => false, "message" => "Not authorized"]);
    exit();
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$questionText = isset($_POST['question_text']) ? trim($_POST['question_text']) : "";
$optionA = isset($_POST['option_a']) ? trim($_POST['option_a']) : "";
$optionB = isset($_POST['option_b']) ? trim($_POST['option_b']) : "";
$optionC = isset($_POST['option_c']) ? trim($_POST['option_c']) : "";
$optionD = isset($_POST['option_d']) ? trim($_POST['option_d']) : "";
$correctOption = isset($_POST['correct_option']) ? $_POST['correct_option'] : "A";
$marks = isset($_POST['marks']) ? intval($_POST['marks']) : 1;

if ($id <= 0 || $questionText == "" || $optionA == "" || $optionB == "" || $optionC == "" || $optionD == "") {
    echo json_encode(["success" => false, "message" => "Please fill in all fields"]);
    exit();
}

$connection = new DatabaseConnection();
$conn = $connection->OpenCon();

$stmt = $conn->prepare("UPDATE questions SET question_text=?, option_a=?, option_b=?, option_c=?, option_d=?, correct_option=?, marks=? WHERE id=?");
$stmt->bind_param("ssssssii", $questionText, $optionA, $optionB, $optionC, $optionD, $correctOption, $marks, $id);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "question" => [
            "id" => $id,
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
    echo json_encode(["success" => false, "message" => "Could not update question"]);
}

$stmt->close();
$connection->CloseCon($conn);
