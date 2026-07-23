<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../include/dbConfig.php';

session_start();

// Enable CORS/Error reporting for clean JSON
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$action = $_REQUEST['action'] ?? '';
$user_id = $_SESSION['user_id'] ?? 13; // Default committee member Riya Desai
$user_email = $_SESSION['user_email'] ?? 'committee@zealeducation.com';

try {
    switch ($action) {
        case 'get_dashboard_summary':
            // 1. Assigned events count
            $stmtAssigned = $pdo->prepare("SELECT COUNT(*) FROM assigned_events WHERE user_id = ?");
            $stmtAssigned->execute([$user_id]);
            $assignedCount = $stmtAssigned->fetchColumn();

            // 2. Attendance count and rate calculation
            $stmtAttTotal = $pdo->query("SELECT COUNT(*) FROM attendance");
            $totalAtt = $stmtAttTotal->fetchColumn() ?: 1;
            $stmtAttPres = $pdo->query("SELECT COUNT(*) FROM attendance WHERE status = 'Present'");
            $presAtt = $stmtAttPres->fetchColumn();
            $attRate = round(($presAtt / $totalAtt) * 100);

            // 3. Event reports count
            $stmtReports = $pdo->query("SELECT COUNT(*) FROM reports");
            $reportsCount = $stmtReports->fetchColumn();

            // 4. Notifications count
            $stmtNotif = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE recipient IN ('all', 'committee@zealeducation.com', ?) AND (email_sent = 0 OR is_read = 0)");
            $stmtNotif->execute([$user_email]);
            $notifCount = $stmtNotif->fetchColumn();

            // 5. Fetch assigned events list
            $stmtEventsList = $pdo->prepare("SELECT * FROM assigned_events WHERE user_id = ? ORDER BY id DESC");
            $stmtEventsList->execute([$user_id]);
            $assignedEvents = $stmtEventsList->fetchAll();

            // 6. Fetch tasks list
            $stmtTasks = $pdo->prepare("SELECT * FROM committee_tasks WHERE user_id = ? ORDER BY id DESC");
            $stmtTasks->execute([$user_id]);
            $tasks = $stmtTasks->fetchAll();

            // 7. Fetch attendance records
            $stmtAttendance = $pdo->query("SELECT * FROM attendance ORDER BY id DESC");
            $attendanceRecords = $stmtAttendance->fetchAll();

            // 8. Fetch student members for dropdown
            $stmtStudents = $pdo->query("SELECT id, name, email, branch FROM users WHERE role LIKE '%Student%' ORDER BY name ASC");
            $studentsList = $stmtStudents->fetchAll();

            // 9. Fetch event reports list
            $stmtReportsList = $pdo->query("SELECT * FROM reports ORDER BY id DESC");
            $reportsList = $stmtReportsList->fetchAll();

            // 10. Fetch notifications list
            $stmtNotifList = $pdo->prepare("SELECT * FROM notifications WHERE recipient IN ('all', 'committee@zealeducation.com', ?) ORDER BY id DESC LIMIT 10");
            $stmtNotifList->execute([$user_email]);
            $notifications = $stmtNotifList->fetchAll();

            // 11. Fetch user profile
            $stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = ? OR email = ?");
            $stmtUser->execute([$user_id, $user_email]);
            $profile = $stmtUser->fetch();

            echo json_encode([
                'status' => 'success',
                'stats' => [
                    'assigned_events' => $assignedCount,
                    'attendance_rate' => $attRate . '%',
                    'reports_filed' => $reportsCount,
                    'notifications' => $notifCount
                ],
                'assigned_events' => $assignedEvents,
                'tasks' => $tasks,
                'attendance' => $attendanceRecords,
                'students' => $studentsList,
                'reports' => $reportsList,
                'notifications' => $notifications,
                'profile' => $profile
            ]);
            break;

        case 'save_attendance':
            $event_name = trim($_POST['event_name'] ?? '');
            $student_name = trim($_POST['student_name'] ?? '');
            $status = trim($_POST['status'] ?? 'Present');
            $record_id = $_POST['record_id'] ?? null;

            if (empty($event_name) || empty($student_name)) {
                echo json_encode(['status' => 'error', 'message' => 'Event and Student Name are required.']);
                exit;
            }

            if (!empty($record_id)) {
                $stmt = $pdo->prepare("UPDATE attendance SET meeting_id=0, student_name=?, status=?, marked_by='Riya Desai' WHERE id=?");
                $stmt->execute([$student_name, $status, $record_id]);
                $msg = 'Attendance record updated successfully.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO attendance (meeting_id, user_id, student_name, student_email, status, marked_by) VALUES (0, 0, ?, ?, ?, 'Riya Desai')");
                $stmt->execute([$student_name, strtolower(str_replace(' ', '.', $student_name)) . '@zealeducation.com', $status]);
                $msg = 'Attendance record logged successfully.';
            }

            // Create confirmation notification
            $stmtN = $pdo->prepare("INSERT INTO notifications (title, text, indicator, recipient) VALUES (?, ?, 'green', 'all')");
            $stmtN->execute(["Attendance Recorded", "Attendance for {$student_name} in {$event_name} marked as {$status}."]);

            echo json_encode(['status' => 'success', 'message' => $msg]);
            break;

        case 'toggle_task':
            $task_id = $_POST['task_id'] ?? 0;
            if (!$task_id) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid Task ID']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT status FROM committee_tasks WHERE id = ?");
            $stmt->execute([$task_id]);
            $curr = $stmt->fetchColumn();

            $newStatus = ($curr === 'Completed') ? 'Pending' : 'Completed';
            $stmtUp = $pdo->prepare("UPDATE committee_tasks SET status = ? WHERE id = ?");
            $stmtUp->execute([$newStatus, $task_id]);

            echo json_encode(['status' => 'success', 'new_status' => $newStatus]);
            break;

        case 'add_assigned_event':
            $event_name = trim($_POST['event_name'] ?? '');
            $event_date = trim($_POST['event_date'] ?? '');
            $role = trim($_POST['role'] ?? 'Organiser');
            $tasks_summary = trim($_POST['tasks_summary'] ?? '');
            $status = trim($_POST['status'] ?? 'Confirmed');

            if (empty($event_name) || empty($event_date)) {
                echo json_encode(['status' => 'error', 'message' => 'Event Name and Date are required']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO assigned_events (user_id, event_name, event_date, role, tasks_summary, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $event_name, $event_date, $role, $tasks_summary, $status]);

            echo json_encode(['status' => 'success', 'message' => 'New event assigned successfully']);
            break;

        case 'submit_report':
            $title = trim($_POST['title'] ?? '');
            $category = trim($_POST['category'] ?? 'Event Report');
            $summary = trim($_POST['summary'] ?? '');
            $format = trim($_POST['format'] ?? 'PDF');

            if (empty($title) || empty($summary)) {
                echo json_encode(['status' => 'error', 'message' => 'Title and Summary are required']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO reports (title, category, summary, format, created_by) VALUES (?, ?, ?, ?, 'Riya Desai')");
            $stmt->execute([$title, $category, $summary, $format]);

            echo json_encode(['status' => 'success', 'message' => 'Event report submitted successfully']);
            break;

        case 'clear_notifications':
            $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE recipient IN ('all', 'committee@zealeducation.com', ?)");
            $stmt->execute([$user_email]);

            echo json_encode(['status' => 'success', 'message' => 'All notifications marked as read']);
            break;

        case 'update_profile':
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $committeeDesignation = trim($_POST['committeeDesignation'] ?? '');
            $branch = trim($_POST['branch'] ?? '');

            if (empty($name) || empty($email)) {
                echo json_encode(['status' => 'error', 'message' => 'Name and Email are required']);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, committeeDesignation=?, branch=? WHERE id=?");
            $stmt->execute([$name, $email, $committeeDesignation, $branch, $user_id]);

            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
            break;

        case 'change_password':
            $currPassword = $_POST['currPassword'] ?? '';
            $newPassword = $_POST['newPassword'] ?? '';

            if (empty($currPassword) || empty($newPassword)) {
                echo json_encode(['status' => 'error', 'message' => 'Please provide both current and new passwords']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ? OR email = ?");
            $stmt->execute([$user_id, $user_email]);
            $realPass = $stmt->fetchColumn();

            if ($realPass !== $currPassword) {
                echo json_encode(['status' => 'error', 'message' => 'Incorrect current password.']);
                exit;
            }

            $stmtUp = $pdo->prepare("UPDATE users SET password = ? WHERE id = ? OR email = ?");
            $stmtUp->execute([$newPassword, $user_id, $user_email]);

            echo json_encode(['status' => 'success', 'message' => 'Password changed successfully']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action parameter']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
