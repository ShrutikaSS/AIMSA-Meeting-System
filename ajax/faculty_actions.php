<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once __DIR__ . '/../include/dbConfig.php';

$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'get_faculty_stats':
            // Total Student Members
            $stmt = $pdo->query("SELECT COUNT(*) FROM `users` WHERE `role` = 'Student Member' OR `role` = 'Committee Member'");
            $totalMembers = (int)$stmt->fetchColumn();

            // Approved Events Count
            $stmt = $pdo->query("SELECT COUNT(*) FROM `events` WHERE `status` = 'Approved'");
            $approvedEventsCount = (int)$stmt->fetchColumn();

            // Pending Events Count
            $stmt = $pdo->query("SELECT COUNT(*) FROM `events` WHERE `status` = 'Pending'");
            $pendingEventsCount = (int)$stmt->fetchColumn();

            // Attendance Rate
            $stmt = $pdo->query("SELECT SUM(`present_count`) as total_present, SUM(`present_count` + `absent_count`) as total_all FROM `meetings` WHERE `status` = 'Completed'");
            $attRow = $stmt->fetch();
            $avgAttendance = 89; // Default fallback
            if ($attRow && $attRow->total_all > 0) {
                $avgAttendance = round(($attRow->total_present / $attRow->total_all) * 100);
            }

            echo json_encode([
                'status' => 'success',
                'stats' => [
                    'approved_events' => $approvedEventsCount,
                    'pending_events' => $pendingEventsCount,
                    'total_members' => $totalMembers,
                    'avg_attendance' => $avgAttendance . '%'
                ]
            ]);
            break;

        case 'get_member_statistics':
            // Branch Breakdown
            $stmt = $pdo->query("SELECT `branch`, COUNT(*) as count FROM `users` GROUP BY `branch` ORDER BY count DESC");
            $branchData = $stmt->fetchAll();

            // Batch / Year Breakdown
            $stmt = $pdo->query("SELECT `batch`, COUNT(*) as count FROM `users` GROUP BY `batch` ORDER BY `batch` ASC");
            $batchData = $stmt->fetchAll();

            // Role Distribution
            $stmt = $pdo->query("SELECT `role`, COUNT(*) as count FROM `users` GROUP BY `role` ORDER BY count DESC");
            $roleData = $stmt->fetchAll();

            // Active Ratio
            $stmt = $pdo->query("SELECT `membershipStatus`, COUNT(*) as count FROM `users` GROUP BY `membershipStatus`");
            $statusData = $stmt->fetchAll();

            // Full Roster
            $stmt = $pdo->query("SELECT `id`, `name`, `email`, `role`, `branch`, `batch`, `membershipStatus`, `zprn` FROM `users` ORDER BY `id` ASC");
            $membersList = $stmt->fetchAll();

            echo json_encode([
                'status' => 'success',
                'branch_breakdown' => $branchData,
                'batch_breakdown' => $batchData,
                'role_distribution' => $roleData,
                'status_breakdown' => $statusData,
                'members' => $membersList
            ]);
            break;

        case 'get_attendance_summary':
            // Meetings List
            $stmt = $pdo->query("SELECT `id`, `title`, `meeting_date`, `meeting_time`, `venue`, `category`, `status`, `present_count`, `absent_count`, `verified_by` FROM `meetings` ORDER BY `meeting_date` DESC");
            $meetings = $stmt->fetchAll();

            // Students Attendance Roster
            $stmt = $pdo->query("SELECT a.id, a.meeting_id, a.user_id, a.student_name, a.student_email, a.zprn, a.branch, a.batch, a.status, a.marked_by, m.title as event_name, m.meeting_date 
                                FROM `attendance` a 
                                LEFT JOIN `meetings` m ON a.meeting_id = m.id 
                                ORDER BY a.id DESC");
            $roster = $stmt->fetchAll();

            // Calculate overall attendance metrics
            $totalPresent = 0;
            $totalRecorded = 0;
            foreach ($meetings as $m) {
                $totalPresent += $m->present_count;
                $totalRecorded += ($m->present_count + $m->absent_count);
            }
            $overallRate = $totalRecorded > 0 ? round(($totalPresent / $totalRecorded) * 100) : 89;

            echo json_encode([
                'status' => 'success',
                'overall_rate' => $overallRate . '%',
                'total_meetings' => count($meetings),
                'meetings' => $meetings,
                'roster' => $roster
            ]);
            break;

        case 'update_faculty_profile':
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $zprn = trim($_POST['zprn'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $staffId = trim($_POST['staff_id'] ?? '');

            if (empty($name) || empty($email)) {
                echo json_encode(['status' => 'error', 'message' => 'Name and Email are required.']);
                exit;
            }

            $userEmail = $_SESSION['user']['email'] ?? 'faculty@zealeducation.com';

            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE LOWER(`email`) = LOWER(:email)");
            $stmt->execute([':email' => $userEmail]);
            $user = $stmt->fetch();

            if ($user) {
                $update = $pdo->prepare("UPDATE `users` SET `name` = :name, `email` = :email, `zprn` = :zprn WHERE `id` = :id");
                $update->execute([':name' => $name, ':email' => $email, ':zprn' => $zprn, ':id' => $user->id]);
            }

            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['zprn'] = $zprn;

            echo json_encode([
                'status' => 'success',
                'message' => 'Faculty profile updated successfully!',
                'user' => [
                    'name' => $name,
                    'email' => $email,
                    'zprn' => $zprn,
                    'role' => 'Faculty Coordinator'
                ]
            ]);
            break;

        case 'verify_meeting_attendance':
            $meetingId = (int)($_POST['meeting_id'] ?? 0);
            $verifiedBy = trim($_POST['verified_by'] ?? 'Prof. Manisha Devgunde');

            if ($meetingId > 0) {
                $stmt = $pdo->prepare("UPDATE `meetings` SET `verified_by` = :v WHERE `id` = :id");
                $stmt->execute([':v' => $verifiedBy, ':id' => $meetingId]);
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Attendance sign-off & verification recorded successfully!'
            ]);
            break;

        case 'schedule_meeting':
            $title = trim($_POST['title'] ?? '');
            $meetingDate = trim($_POST['meeting_date'] ?? '');
            $meetingTime = trim($_POST['meeting_time'] ?? '10:00 AM');
            $venue = trim($_POST['venue'] ?? 'AIML Seminar Hall');
            $category = trim($_POST['category'] ?? 'General Body');
            $targetAudience = trim($_POST['target_audience'] ?? 'All Members');
            $agenda = trim($_POST['agenda'] ?? '');
            $createdBy = $_SESSION['user']['name'] ?? 'Prof. Manisha Devgunde (Faculty)';

            if (empty($title) || empty($meetingDate) || empty($venue)) {
                echo json_encode(['status' => 'error', 'message' => 'Meeting Title, Date, and Venue are required.']);
                exit;
            }

            $conflictCheck = $pdo->prepare("SELECT COUNT(*) FROM `meetings` WHERE `venue` = ? AND `meeting_date` = ? AND `meeting_time` = ? AND `status` != 'Cancelled' AND `id` != 0");
            $conflictCheck->execute([$venue, $meetingDate, $meetingTime]);
            if ($conflictCheck->fetchColumn() > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Slot already booked: A meeting is already scheduled at this venue, date and time.']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO `meetings` (`title`, `meeting_date`, `meeting_time`, `venue`, `category`, `target_audience`, `agenda`, `created_by`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Scheduled')");
            $stmt->execute([$title, $meetingDate, $meetingTime, $venue, $category, $targetAudience, $agenda, $createdBy]);

            try {
                $notifText = "Faculty Coordinator scheduled a meeting: {$title} on {$meetingDate} at {$meetingTime} in {$venue}. Target: {$targetAudience}";
                $notifStmt = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`) VALUES (?, ?, 'blue', ?)");
                $notifStmt->execute(["Meeting Scheduled: {$title}", $notifText, $targetAudience]);
            } catch (Exception $ne) {
                error_log('Faculty Meeting notification error: ' . $ne->getMessage());
            }

            echo json_encode(['status' => 'success', 'message' => 'Meeting scheduled successfully and notifications broadcasted!']);
            break;

        case 'cancel_meeting':
            $meetingId = (int)($_POST['meeting_id'] ?? 0);
            if ($meetingId <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid meeting ID provided.']);
                exit;
            }

            $stmtFetch = $pdo->prepare("SELECT * FROM `meetings` WHERE `id` = ?");
            $stmtFetch->execute([$meetingId]);
            $meeting = $stmtFetch->fetch();

            if (!$meeting) {
                echo json_encode(['status' => 'error', 'message' => 'Meeting record not found.']);
                exit;
            }

            $stmtUpd = $pdo->prepare("UPDATE `meetings` SET `status` = 'Cancelled' WHERE `id` = ?");
            $stmtUpd->execute([$meetingId]);

            try {
                $cancelText = "The meeting '{$meeting->title}' scheduled for {$meeting->meeting_date} at {$meeting->meeting_time} has been cancelled by Faculty Coordinator.";
                $notifStmt = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`) VALUES (?, ?, 'red', ?)");
                $notifStmt->execute(["Meeting Cancelled: {$meeting->title}", $cancelText, $meeting->target_audience ?? 'All Members']);
            } catch (Exception $ne) {
                error_log('Faculty Cancel notification error: ' . $ne->getMessage());
            }

            echo json_encode(['status' => 'success', 'message' => "Meeting '{$meeting->title}' was cancelled and participants notified!"]);
            break;

        case 'update_meeting':
            $meetingId = (int)($_POST['meeting_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $meetingDate = trim($_POST['meeting_date'] ?? '');
            $meetingTime = trim($_POST['meeting_time'] ?? '10:00 AM');
            $venue = trim($_POST['venue'] ?? 'AIML Seminar Hall');
            $category = trim($_POST['category'] ?? 'General Body');
            $targetAudience = trim($_POST['target_audience'] ?? 'All Members');
            $agenda = trim($_POST['agenda'] ?? '');

            if ($meetingId <= 0 || empty($title) || empty($meetingDate) || empty($venue)) {
                echo json_encode(['status' => 'error', 'message' => 'Meeting ID, Title, Date, and Venue are required.']);
                exit;
            }

            $stmtFetch = $pdo->prepare("SELECT * FROM `meetings` WHERE `id` = ?");
            $stmtFetch->execute([$meetingId]);
            $existing = $stmtFetch->fetch();
            if (!$existing) {
                echo json_encode(['status' => 'error', 'message' => 'Meeting record not found.']);
                exit;
            }

            $conflictCheck = $pdo->prepare("SELECT COUNT(*) FROM `meetings` WHERE `venue` = ? AND `meeting_date` = ? AND `meeting_time` = ? AND `status` != 'Cancelled' AND `id` != ?");
            $conflictCheck->execute([$venue, $meetingDate, $meetingTime, $meetingId]);
            if ($conflictCheck->fetchColumn() > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Slot already booked: Another meeting is already scheduled at this venue, date and time.']);
                exit;
            }

            $stmtUpd = $pdo->prepare("UPDATE `meetings` SET `title` = ?, `meeting_date` = ?, `meeting_time` = ?, `venue` = ?, `category` = ?, `target_audience` = ?, `agenda` = ? WHERE `id` = ?");
            $stmtUpd->execute([$title, $meetingDate, $meetingTime, $venue, $category, $targetAudience, $agenda, $meetingId]);

            try {
                $notifText = "Faculty Coordinator rescheduled '{$title}' to {$meetingDate} at {$meetingTime} in {$venue}. Target: {$targetAudience}";
                $notifStmt = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`) VALUES (?, ?, 'blue', ?)");
                $notifStmt->execute(["Meeting Rescheduled: {$title}", $notifText, $targetAudience]);
            } catch (Exception $ne) {
                error_log('Faculty Meeting update notification error: ' . $ne->getMessage());
            }

            echo json_encode(['status' => 'success', 'message' => 'Meeting rescheduled successfully and notifications broadcasted!']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid faculty action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
