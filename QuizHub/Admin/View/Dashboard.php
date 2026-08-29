<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../Auth/View/Login.php");
    exit();
}
include "../../Common/model/DatabaseConnection.php";

$connection = new DatabaseConnection();
$conn = $connection->OpenCon();

// Pending registrations
$pending = $conn->query("SELECT id, full_name, role FROM users WHERE status = 'pending' ORDER BY created_at ASC");

// Student marks
$marks = $conn->query(
    "SELECT u.full_name AS student_name, q.title AS quiz_name, r.score, r.total_marks
     FROM results r
     JOIN users u ON u.id = r.student_id
     JOIN quizzes q ON q.id = r.quiz_id
     ORDER BY r.taken_at DESC"
);

// Quizzes conducted per teacher
$teacherSummary = $conn->query(
    "SELECT u.full_name AS teacher_name, COUNT(q.id) AS quiz_count
     FROM users u
     LEFT JOIN quizzes q ON q.teacher_id = u.id
     WHERE u.role = 'teacher' AND u.status = 'approved'
     GROUP BY u.id, u.full_name
     ORDER BY u.full_name ASC"
);

$pendingCount = $pending->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>QuizHub - Admin Dashboard</title>
<link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<div class="navbar">
    <div class="left">
        <span class="brand-name">QuizHub</span>
        <span class="page-title">&middot; Admin Dashboard</span>
    </div>
    <div class="right">
        <span class="user-name">Admin</span>
        <a href="../../Auth/Control/Logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="container">

    <div class="card">
        <div class="section-header">
            <div>
                <h2>Pending User Registrations</h2>
                <p>Review and approve or reject new user registrations.</p>
            </div>
            <span class="badge" id="pendingBadge"><?php echo $pendingCount; ?> pending</span>
        </div>

        <table id="pendingTable">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
            <?php if ($pendingCount > 0): $i = 1; ?>
                <?php while ($row = $pending->fetch_assoc()): ?>
                    <tr data-id="<?php echo $row['id']; ?>">
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><span class="role-tag <?php echo $row['role']; ?>"><?php echo ucfirst($row['role']); ?></span></td>
                        <td>
                            <button class="action-btn accept-btn" data-action="approved">Accept</button>
                            <button class="action-btn reject-btn" data-action="rejected">Reject</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </table>
        <p class="empty-note" id="pendingEmptyNote" style="<?php echo $pendingCount > 0 ? 'display:none;' : ''; ?>">No pending registrations.</p>
    </div>

    <div class="card">
        <div class="section-header">
            <div>
                <h2>Student Marks</h2>
                <p>Quiz results for all students.</p>
            </div>
        </div>
        <table>
            <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Quiz Name</th>
                <th>Marks</th>
            </tr>
            <?php if ($marks->num_rows > 0): $i = 1; ?>
                <?php while ($row = $marks->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['quiz_name']); ?></td>
                        <td><span class="marks-pill"><?php echo $row['score']; ?> / <?php echo $row['total_marks']; ?></span></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" class="empty-note">No results yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="card">
        <div class="section-header">
            <div>
                <h2>Teacher Quiz Summary</h2>
                <p>Number of quizzes conducted per teacher.</p>
            </div>
        </div>
        <table>
            <tr>
                <th>#</th>
                <th>Teacher Name</th>
                <th>Quizzes Conducted</th>
            </tr>
            <?php if ($teacherSummary->num_rows > 0): $i = 1; ?>
                <?php while ($row = $teacherSummary->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                        <td><?php echo $row['quiz_count']; ?> quizzes</td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3" class="empty-note">No approved teachers yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>

</div>

<script src="../js/dashboard.js"></script>
</body>
</html>
<?php $connection->CloseCon($conn); ?>
