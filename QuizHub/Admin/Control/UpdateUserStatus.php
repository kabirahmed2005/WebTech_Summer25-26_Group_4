<?php
session_start();
header('Content-Type: application/json');
include "../../Common/model/DatabaseConnection.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo json_encode(["success" => false, "message" => "Not authorized"]);
    exit();
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : "";

if ($id <= 0 || ($status != "approved" && $status != "rejected")) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit();
}

$connection = new DatabaseConnection();
$conn = $connection->OpenCon();

$stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ? AND status = 'pending'");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Could not update user status"]);
}

$stmt->close();
$connection->CloseCon($conn);
