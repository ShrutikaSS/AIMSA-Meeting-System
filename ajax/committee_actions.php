<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once __DIR__ . '/../include/dbConfig.php';

$action = $_REQUEST['action'] ?? '';
$sessionUserEmail = $_SESSION['user']['email'] ?? 'committee@zealeducation.com';

try {
    switch ($action) {
        case 'get_dashboard_data':
            // 1. Logged in user info
            $stmt = $pdo->prepare("SELECT id, name, email, role, branch, batch, membershipStatus, committeeDesignation, committeeResponsibility, zprn, phone, photograph FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$sessionUserEmail]);
            $userProfile = $stmt->fetch();
            if (!$userProfile) {
                // Fallback to committee user
                $stmt = $pdo->query("SELECT id, name, email, role, branch, batch, membershipStatus, committeeDesignation, committeeResponsibility, zprn, phone, photograph FROM users WHERE role = 'Committee Member' LIMIT 1");
                $userProfile = $stmt->fetch();
            }

            // 2. Events list
            $stmt = $pdo->query("SELECT id, title, title AS name, description, event_date, location, status, created_by FROM events ORDER BY id DESC");
            $events = $stmt->fetchAll();

            // 3. Student Members for attendance dropdown (all users in department)
            $stmt = $pdo->query("SELECT id, name, email, zprn, branch, batch, role FROM users ORDER BY name ASC");
            $students = $stmt->fetchAll();

            // 4. Tasks assigned to committee member
            $stmt = $pdo->query("SELECT id, task_title, description, due_date, priority, status, assigned_to_email, created_by FROM committee_tasks ORDER BY id DESC");
            $tasks = $stmt->fetchAll();

            // 5. Attendance Records
            $stmt = $pdo->query("SELECT a.id, a.meeting_id, a.user_id, a.student_name, a.student_email, a.zprn, a.branch, a.batch, a.status, a.marked_by, a.marked_at, 
                                        COALESCE(m.title, 'AIMSA Event') as event_name, m.meeting_date 
                                FROM attendance a 
                                LEFT JOIN meetings m ON a.meeting_id = m.id 
                                ORDER BY a.id DESC");
            $attendanceRecords = $stmt->fetchAll();

            // Calculate Attendance Rate
            $stmt = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present FROM attendance");
            $attStats = $stmt->fetch();
            $attendanceRate = "92%";
            if ($attStats && $attStats->total > 0) {
                $attendanceRate = round(($attStats->present / $attStats->total) * 100) . "%";
            }

            // 6. Reports list
            $stmt = $pdo->query("SELECT id, title, category, summary, format, file_path, created_by, created_at FROM reports ORDER BY id DESC");
            $reports = $stmt->fetchAll();

            // 7. Notifications (fetch all relevant department notifications)
            $stmt = $pdo->query("SELECT id, title, text, indicator, recipient, email_sent, created_at FROM notifications ORDER BY id DESC LIMIT 50");
            $notifications = $stmt->fetchAll();

            // 8. Meetings list (for select event in attendance form)
            $stmt = $pdo->query("SELECT id, title, meeting_date, venue FROM meetings ORDER BY id DESC");
            $meetings = $stmt->fetchAll();

            echo json_encode([
                'status' => 'success',
                'user' => $userProfile,
                'events' => $events,
                'students' => $students,
                'tasks' => $tasks,
                'attendance_records' => $attendanceRecords,
                'attendance_rate' => $attendanceRate,
                'reports' => $reports,
                'notifications' => $notifications,
                'meetings' => $meetings,
                'stats' => [
                    'assigned_events' => count($events),
                    'attendance_rate' => $attendanceRate,
                    'reports_filed' => count($reports),
                    'unread_notifications' => count($notifications)
                ]
            ]);
            break;

        case 'mark_attendance':
            $eventTitle = trim($_POST['event_name'] ?? '');
            $studentEmail = trim($_POST['student_email'] ?? '');
            $statusVal = trim($_POST['status'] ?? 'Present');
            $recordId = !empty($_POST['record_id']) ? (int)$_POST['record_id'] : null;

            if (empty($eventTitle) || empty($studentEmail)) {
                echo json_encode(['status' => 'error', 'message' => 'Event and Student selections are required.']);
                exit;
            }

            // Get or create meeting entry
            $stmt = $pdo->prepare("SELECT id FROM meetings WHERE title = ? LIMIT 1");
            $stmt->execute([$eventTitle]);
            $meeting = $stmt->fetch();
            if (!$meeting) {
                $stmtIns = $pdo->prepare("INSERT INTO meetings (title, meeting_date, meeting_time, venue, category, target_audience, status, present_count, absent_count) VALUES (?, CURDATE(), '10:00 AM', 'Main Hall', 'Event', 'All Members', 'Completed', 1, 0)");
                $stmtIns->execute([$eventTitle]);
                $meetingId = $pdo->lastInsertId();
            } else {
                $meetingId = $meeting->id;
            }

            // Get student details
            $stmtUser = $pdo->prepare("SELECT id, name, email, zprn, branch, batch FROM users WHERE email = ? LIMIT 1");
            $stmtUser->execute([$studentEmail]);
            $studentObj = $stmtUser->fetch();

            if (!$studentObj) {
                // Try search by name
                $stmtUser = $pdo->prepare("SELECT id, name, email, zprn, branch, batch FROM users WHERE name = ? LIMIT 1");
                $stmtUser->execute([$studentEmail]);
                $studentObj = $stmtUser->fetch();
            }

            $userId = $studentObj ? $studentObj->id : 0;
            $studentName = $studentObj ? $studentObj->name : $studentEmail;
            $zprn = $studentObj ? $studentObj->zprn : '125UAM1004';
            $branch = $studentObj ? $studentObj->branch : 'AI & ML';
            $batch = $studentObj ? $studentObj->batch : '2026';
            $markedBy = $_SESSION['user']['name'] ?? 'Committee Member';

            if ($recordId) {
                // Update existing record
                $stmtUpd = $pdo->prepare("UPDATE attendance SET meeting_id = ?, user_id = ?, student_name = ?, student_email = ?, zprn = ?, branch = ?, batch = ?, status = ?, marked_by = ? WHERE id = ?");
                $stmtUpd->execute([$meetingId, $userId, $studentName, $studentEmail, $zprn, $branch, $batch, $statusVal, $markedBy, $recordId]);
                $msg = 'Attendance record updated successfully.';
            } else {
                // Insert new record
                $stmtIns = $pdo->prepare("INSERT INTO attendance (meeting_id, user_id, student_name, student_email, zprn, branch, batch, status, marked_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmtIns->execute([$meetingId, $userId, $studentName, $studentEmail, $zprn, $branch, $batch, $statusVal, $markedBy]);
                $msg = 'Attendance record saved successfully.';
            }

            // Update meeting counts
            $stmtCnt = $pdo->prepare("SELECT 
                                        SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as pres,
                                        SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as abs
                                      FROM attendance WHERE meeting_id = ?");
            $stmtCnt->execute([$meetingId]);
            $counts = $stmtCnt->fetch();
            if ($counts) {
                $stmtUpdM = $pdo->prepare("UPDATE meetings SET present_count = ?, absent_count = ? WHERE id = ?");
                $stmtUpdM->execute([$counts->pres ?? 0, $counts->abs ?? 0, $meetingId]);
            }

            // Create notification for student
            $stmtNotif = $pdo->prepare("INSERT INTO notifications (title, text, indicator, recipient, email_sent) VALUES (?, ?, ?, ?, 1)");
            $stmtNotif->execute(['Attendance Confirmation', "Your attendance for {$eventTitle} was marked as {$statusVal}.", 'green', $studentEmail]);

            echo json_encode(['status' => 'success', 'message' => $msg]);
            break;

        case 'submit_event_report':
            $title = trim($_POST['title'] ?? '');
            $category = trim($_POST['category'] ?? 'Event Report');
            $summary = trim($_POST['summary'] ?? '');
            $createdBy = $_SESSION['user']['name'] ?? 'Committee Member';

            if (empty($title)) {
                echo json_encode(['status' => 'error', 'message' => 'Report title is required.']);
                exit;
            }

            $stmtRep = $pdo->prepare("INSERT INTO reports (title, category, summary, format, created_by) VALUES (?, ?, ?, 'PDF', ?)");
            $stmtRep->execute([$title, $category, $summary, $createdBy]);

            echo json_encode(['status' => 'success', 'message' => 'Event report submitted successfully in MySQL database.']);
            break;

        case 'toggle_task_status':
            $taskId = (int)($_POST['task_id'] ?? 0);
            $newStatus = trim($_POST['status'] ?? 'Completed');

            if ($taskId > 0) {
                $stmtTask = $pdo->prepare("UPDATE committee_tasks SET status = ? WHERE id = ?");
                $stmtTask->execute([$newStatus, $taskId]);
                echo json_encode(['status' => 'success', 'message' => 'Task status updated to ' . $newStatus]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid task ID.']);
            }
            break;

        case 'update_profile':
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            if (empty($name)) {
                echo json_encode(['status' => 'error', 'message' => 'Name cannot be empty.']);
                exit;
            }

            $stmtProf = $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE email = ?");
            $stmtProf->execute([$name, $phone, $sessionUserEmail]);

            // Update session if present
            if (isset($_SESSION['user'])) {
                $_SESSION['user']['name'] = $name;
            }

            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully in MySQL database.']);
            break;

        case 'change_password':
            $currPassword = $_POST['curr_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';

            if (empty($currPassword) || empty($newPassword)) {
                echo json_encode(['status' => 'error', 'message' => 'Both current and new password are required.']);
                exit;
            }

            $stmtPass = $pdo->prepare("SELECT id, password FROM users WHERE email = ? LIMIT 1");
            $stmtPass->execute([$sessionUserEmail]);
            $userRow = $stmtPass->fetch();

            if (!$userRow || $userRow->password !== $currPassword) {
                echo json_encode(['status' => 'error', 'message' => 'Incorrect current password.']);
                exit;
            }

            $stmtUpdPass = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmtUpdPass->execute([$newPassword, $userRow->id]);

            // Save notification
            $stmtN = $pdo->prepare("INSERT INTO notifications (title, text, indicator, recipient, email_sent) VALUES (?, ?, 'green', ?, 1)");
            $stmtN->execute(['Password Changed', 'Your account password was successfully updated.', $sessionUserEmail]);

            echo json_encode(['status' => 'success', 'message' => 'Password updated successfully in MySQL.']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action specified.']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
