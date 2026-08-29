<?php
session_start();
header('Content-Type: application/json');
include "../../Common/model/DatabaseConnection.php";

$username = isset($_POST['username']) ? trim($_POST['username']) : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";
$role = isset($_POST['role']) ? $_POST['role'] : "student";

if ($username == "" || $password == "") {
    echo json_encode(["success" => false, "message" => "Please fill in both fields"]);
    exit();
}

$connection = new DatabaseConnection();
$conn = $connection->OpenCon();

// Look up the account by username AND the role selected on the login form
$stmt = $conn->prepare("SELECT id, full_name, username, password, role, status FROM users WHERE username = ? AND role = ?");
$stmt->bind_param("ss", $username, $role);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "No account found for this role"]);
    $stmt->close();
    $connection->CloseCon($conn);
    exit();
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    echo json_encode(["success" => false, "message" => "Incorrect password"]);
    $stmt->close();
    $connection->CloseCon($conn);
    exit();
}

if ($user['status'] == 'pending') {
    echo json_encode(["success" => false, "message" => "Your account is awaiting admin approval"]);
    $stmt->close();
    $connection->CloseCon($conn);
    exit();
}

if ($user['status'] == 'rejected') {
    echo json_encode(["success" => false, "message" => "Your registration was rejected"]);
    $stmt->close();
    $connection->CloseCon($conn);
    exit();
}

// Login successful
$_SESSION['user_id'] = $user['id'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['role'] = $user['role'];

$redirect = "../../Student/View/QuizList.php";
if ($user['role'] == 'teacher') $redirect = "../../Teacher/View/QuestionSetup.php";
if ($user['role'] == 'admin') $redirect = "../../Admin/View/Dashboard.php";

echo json_encode(["success" => true, "message" => "Login successful", "redirect" => $redirect]);

$stmt->close();
$connection->CloseCon($conn);
