<?php
header('Content-Type: application/json');
include "../../Common/model/DatabaseConnection.php";

$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : "";

if ($fullname == "") {
    echo json_encode(["available" => false, "message" => "Name cannot be empty"]);
    exit();
}

$connection = new DatabaseConnection();
$conn = $connection->OpenCon();

$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $fullname);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["available" => false, "message" => "This name is already registered"]);
} else {
    echo json_encode(["available" => true, "message" => "Name is available"]);
}

$stmt->close();
$connection->CloseCon($conn);
