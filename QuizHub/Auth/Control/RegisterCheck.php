<?php
header('Content-Type: application/json');
include "../../Common/model/DatabaseConnection.php";

$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";
$role = isset($_POST['role']) ? $_POST['role'] : "student";

if ($fullname == "" || $password == "") {
    echo json_encode(["success" => false, "message" => "All fields are required"]);
    exit();
}

if ($role != "student" && $role != "teacher") {
    echo json_encode(["success" => false, "message" => "Invalid role"]);
    exit();
}

$connection = new DatabaseConnection();
$conn = $connection->OpenCon();

// Username uniqueness check (Full Name is used as the username)
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $fullname);
$stmt->execute();
$check = $stmt->get_result();

if ($check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "This name is already registered"]);
    $stmt->close();
    $connection->CloseCon($conn);
    exit();
}
$stmt->close();

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$status = "pending"; // Every new student / teacher must be approved by the admin

$stmt = $conn->prepare("INSERT INTO users (full_name, username, password, role, status) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $fullname, $fullname, $hashedPassword, $role, $status);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registered! Please wait for admin approval."]);
} else {
    echo json_encode(["success" => false, "message" => "Registration failed: " . $conn->error]);
}

$stmt->close();
$connection->CloseCon($conn);
