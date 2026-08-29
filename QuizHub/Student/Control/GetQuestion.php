<?php
session_start();
header('Content-Type: application/json');
include "../../Common/model/DatabaseConnection.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    echo json_encode(["success" => false, "message" => "Not authorized"]);
    exit();
}

$quizId = isset($_POST['quiz_id']) ? intval($_POST['quiz_id']) : 0;
$index = isset($_POST['index']) ? intval($_POST['index']) : 1;

$connection = new DatabaseConnection();
$conn = $connection->OpenCon();

$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM questions WHERE quiz_id = ?");
$countStmt->bind_param("i", $quizId);
$countStmt->execute();
$total = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();

if ($total == 0) {
    echo json_encode(["success" => false, "message" => "This quiz has no questions yet."]);
    $connection->CloseCon($conn);
    exit();
}

$offset = $index - 1;
$stmt = $conn->prepare("SELECT id, question_text, option_a, option_b, option_c, option_d FROM questions WHERE quiz_id = ? ORDER BY id ASC LIMIT 1 OFFSET ?");
$stmt->bind_param("ii", $quizId, $offset);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "Question not found."]);
} else {
    echo json_encode(["success" => true, "total" => intval($total), "question" => $result->fetch_assoc()]);
}

$stmt->close();
$connection->CloseCon($conn);
