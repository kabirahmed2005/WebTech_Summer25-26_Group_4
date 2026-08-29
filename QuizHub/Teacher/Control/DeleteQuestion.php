<?php
session_start();
header('Content-Type: application/json');
include "../../Common/model/DatabaseConnection.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    echo json_encode(["success" => false, "message" => "Not authorized"]);
    exit();
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

$connection = new DatabaseConnection();
$conn = $connection->OpenCon();

$stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Could not delete question"]);
}

$stmt->close();
$connection->CloseCon($conn);
