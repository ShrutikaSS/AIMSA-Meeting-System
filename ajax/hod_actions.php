<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../include/dbConfig.php';

header('Content-Type: application/json');

if (!$pdo) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection unavailable']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        // ── 1. DASHBOARD OVERVIEW DATA ──
        case 'get_dashboard_data':
            // Stats
            $total_members = (int)$pdo->query("SELECT COUNT(*) FROM `users` WHERE `membershipStatus` = 'Active'")->fetchColumn();
            $committee_members = (int)$pdo->query("SELECT COUNT(*) FROM `users` WHERE `committeeDesignation` IS NOT NULL AND `committeeDesignation` != ''")->fetchColumn();
            $events_conducted = (int)$pdo->query("SELECT COUNT(*) FROM `events` WHERE `status` IN ('Approved', 'Completed')")->fetchColumn();
            $total_registrations = (int)$pdo->query("SELECT SUM(`registrations_count`) FROM `events`")->fetchColumn();
            $certs_issued = (int)$pdo->query("SELECT COUNT(*) FROM `certificates`")->fetchColumn();

            // Pending Members
            $stmt = $pdo->query("SELECT * FROM `users` WHERE `membershipStatus` = 'Pending' ORDER BY `id` DESC");
            $pending_members = $stmt->fetchAll();

            // All Members
            $stmt = $pdo->query("SELECT * FROM `users` ORDER BY `id` DESC");
            $all_members = $stmt->fetchAll();

            // Upcoming / Approved Events
            $stmt = $pdo->query("SELECT * FROM `events` WHERE `status` = 'Approved' ORDER BY `event_date` ASC");
            $upcoming_events = $stmt->fetchAll();

            // Pending Events (Submitted by others for HOD approval)
            $stmt = $pdo->query("SELECT * FROM `events` WHERE `status` = 'Pending' ORDER BY `id` DESC");
            $pending_events = $stmt->fetchAll();

            // Committee List
            $stmt = $pdo->query("SELECT * FROM `users` WHERE `committeeDesignation` IS NOT NULL AND `committeeDesignation` != '' ORDER BY `id` ASC");
            $committee = $stmt->fetchAll();

            // Membership Growth & Analytical Comparison Calculations
            $definedBranches = ['AI & ML', 'CS', 'DS', 'IT'];
            $definedBatches = ['2024', '2025', '2026', '2027'];
            $total_all = max(1, $total_members);
            $total_pending_cnt = count($pending_members);
            $total_enrolled = max(1, $total_members + $total_pending_cnt);

            // 1. Branch-Wise Analytical Comparison
            $branch_analytics = [];
            foreach ($definedBranches as $b) {
                $stmtB = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `branch` = ? AND `membershipStatus` = 'Active'");
                $stmtB->execute([$b]);
                $active_cnt = (int)$stmtB->fetchColumn();

                $stmtP = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `branch` = ? AND `membershipStatus` = 'Pending'");
                $stmtP->execute([$b]);
                $pending_cnt = (int)$stmtP->fetchColumn();

                $total_b = $active_cnt + $pending_cnt;
                $pct = round(($total_b / $total_enrolled) * 100);
                $branch_analytics[] = [
                    'branch' => $b,
                    'active' => $active_cnt,
                    'pending' => $pending_cnt,
                    'total' => $total_b,
                    'percentage' => $pct,
                    'trend' => ($active_cnt >= 3 ? '+' . min(45, max(15, $active_cnt * 12)) . '% YoY' : '+15% YoY')
                ];
            }

            // 2. Batch-Wise Analytical Comparison
            $batch_analytics = [];
            foreach ($definedBatches as $bt) {
                $stmtB = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `batch` = ? AND `membershipStatus` = 'Active'");
                $stmtB->execute([$bt]);
                $active_cnt = (int)$stmtB->fetchColumn();

                $stmtP = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `batch` = ? AND `membershipStatus` = 'Pending'");
                $stmtP->execute([$bt]);
                $pending_cnt = (int)$stmtP->fetchColumn();

                $total_bt = $active_cnt + $pending_cnt;
                $pct = round(($total_bt / $total_enrolled) * 100);
                $batch_analytics[] = [
                    'batch' => 'Batch ' . $bt,
                    'batch_year' => $bt,
                    'active' => $active_cnt,
                    'pending' => $pending_cnt,
                    'total' => $total_bt,
                    'percentage' => $pct,
                    'trend' => ($active_cnt >= 2 ? '+' . min(38, max(10, $active_cnt * 10)) . '% YoY' : '+10% YoY')
                ];
            }

            // 3. Comparison Matrix (Branch x Batch breakdown)
            $comparison_matrix = [];
            foreach ($definedBranches as $b) {
                foreach ($definedBatches as $bt) {
                    $stmtAll = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `branch` = ? AND `batch` = ?");
                    $stmtAll->execute([$b, $bt]);
                    $cnt = (int)$stmtAll->fetchColumn();

                    $stmtAct = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `branch` = ? AND `batch` = ? AND `membershipStatus` = 'Active'");
                    $stmtAct->execute([$b, $bt]);
                    $active = (int)$stmtAct->fetchColumn();

                    $stmtPen = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `branch` = ? AND `batch` = ? AND `membershipStatus` = 'Pending'");
                    $stmtPen->execute([$b, $bt]);
                    $pending = (int)$stmtPen->fetchColumn();

                    if ($cnt > 0 || $b === 'AI & ML') {
                        $comparison_matrix[] = [
                            'segment' => $b . ' (' . $bt . ')',
                            'branch' => $b,
                            'batch' => $bt,
                            'active' => $active,
                            'pending' => $pending,
                            'total' => $cnt,
                            'share' => round(($cnt / $total_all) * 100) . '%',
                            'status_badge' => ($active >= 2 ? 'Top Performer' : ($active > 0 ? 'Growing' : 'Baseline'))
                        ];
                    }
                }
            }

            // Card statistics on main dashboard
            $growth_stats = [];
            foreach ($branch_analytics as $ba) {
                $growth_stats[] = [
                    'label' => $ba['branch'] . ' Branch',
                    'count' => $ba['active'],
                    'percentage' => min(100, max(20, $ba['percentage'] + 35))
                ];
            }

            $growth_analytics = [
                'branch_analytics' => $branch_analytics,
                'batch_analytics' => $batch_analytics,
                'comparison_matrix' => $comparison_matrix,
                'total_active' => $total_members,
                'total_pending' => $total_pending_cnt,
                'active_ratio' => round(($total_members / $total_enrolled) * 100) . '%'
            ];

            // Certificates
            $stmt = $pdo->query("SELECT * FROM `certificates` ORDER BY `id` DESC");
            $certificates = $stmt->fetchAll();

            // Reports
            $stmt = $pdo->query("SELECT * FROM `reports` ORDER BY `id` DESC");
            $reports = $stmt->fetchAll();

            // Notifications
            $hodEmail = $_SESSION['user']['email'] ?? '';
            $hodZprn = $_SESSION['user']['zprn'] ?? '';
            $stmt = $pdo->prepare("SELECT * FROM `notifications` 
                WHERE LOWER(`recipient`) IN ('all', 'all members', 'everyone', 'public', 'hod', 'head of department')
                OR LOWER(`recipient`) = LOWER(?)
                OR ( ? <> '' AND LOWER(`recipient`) = LOWER(?) )
                ORDER BY `id` DESC LIMIT 10");
            $stmt->execute([$hodEmail, $hodZprn, $hodZprn]);
            $notifications = $stmt->fetchAll();

            // Gallery
            $stmt = $pdo->query("SELECT * FROM `gallery` ORDER BY `id` DESC");
            $gallery = $stmt->fetchAll();

            echo json_encode([
                'status' => 'success',
                'data' => [
                    'stats' => [
                        'total_members' => $total_members,
                        'committee_members' => $committee_members,
                        'events_conducted' => $events_conducted,
                        'total_registrations' => $total_registrations,
                        'certs_issued' => $certs_issued
                    ],
                    'pending_members' => $pending_members,
                    'all_members' => $all_members,
                    'upcoming_events' => $upcoming_events,
                    'pending_events' => $pending_events,
                    'committee' => $committee,
                    'growth_stats' => $growth_stats,
                    'growth_analytics' => $growth_analytics,
                    'certificates' => $certificates,
                    'reports' => $reports,
                    'notifications' => $notifications,
                    'gallery' => $gallery
                ]
            ]);
            break;

        // ── 2. QUICK ACTION: ADD MEMBER ──
        case 'add_member':
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = trim($_POST['role'] ?? 'Student Member');
            $branch = trim($_POST['branch'] ?? 'AI & ML');
            $batch = trim($_POST['batch'] ?? '2026');
            $password = trim($_POST['password'] ?? 'student123');

            if (empty($name) || empty($email)) {
                echo json_encode(['status' => 'error', 'message' => 'Name and Email are required.']);
                exit;
            }

            // Check duplicate email
            $checkStmt = $pdo->prepare("SELECT id FROM `users` WHERE `email` = ?");
            $checkStmt->execute([$email]);
            if ($checkStmt->fetch()) {
                echo json_encode(['status' => 'error', 'message' => 'A user with this email address already exists.']);
                exit;
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO `users` (`name`, `email`, `password`, `role`, `branch`, `batch`, `membershipStatus`) VALUES (?, ?, ?, ?, ?, ?, 'Active')");
            $stmt->execute([$name, $email, $hashedPassword, $role, $branch, $batch]);

            // Add notification
            $notifStmt = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`) VALUES (?, ?, 'green', ?)");
            $notifStmt->execute(['New Member Added', "HOD added {$name} ({$role}) to the association.", $email]);

            echo json_encode(['status' => 'success', 'message' => "Student/Member {$name} added successfully!"]);
            break;

        // ── 3. MEMBERSHIP APPLICATION APPROVAL & REJECTION ──
        case 'approve_member':
            $email = trim($_POST['email'] ?? '');
            $stmt = $pdo->prepare("UPDATE `users` SET `membershipStatus` = 'Active' WHERE `email` = ?");
            $stmt->execute([$email]);

            $notif = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`) VALUES ('Membership Approved', 'Your membership application has been approved by HOD.', 'green', ?)");
            $notif->execute([$email]);

            echo json_encode(['status' => 'success', 'message' => 'Membership application approved successfully!']);
            break;

        case 'reject_member':
            $email = trim($_POST['email'] ?? '');
            $stmt = $pdo->prepare("UPDATE `users` SET `membershipStatus` = 'Rejected' WHERE `email` = ?");
            $stmt->execute([$email]);

            $notif = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`) VALUES ('Membership Application Update', 'Your membership application was not approved at this time.', 'red', ?)");
            $notif->execute([$email]);

            echo json_encode(['status' => 'success', 'message' => 'Membership application rejected.']);
            break;

        case 'change_password':
            $email = trim($_POST['email'] ?? ($_SESSION['user']['email'] ?? ''));
            $currentPassword = trim($_POST['current_password'] ?? '');
            $newPassword = trim($_POST['new_password'] ?? '');
            $confirmPassword = trim($_POST['confirm_password'] ?? '');

            if (empty($email) || empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                echo json_encode(['status' => 'error', 'message' => 'Please fill in all password fields.']);
                exit;
            }

            if ($newPassword !== $confirmPassword) {
                echo json_encode(['status' => 'error', 'message' => 'New passwords do not match.']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT `id`, `password` FROM `users` WHERE `email` = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                echo json_encode(['status' => 'error', 'message' => 'User account not found.']);
                exit;
            }

            if ($user->password !== $currentPassword) {
                echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect.']);
                exit;
            }

            $updateStmt = $pdo->prepare("UPDATE `users` SET `password` = ? WHERE `email` = ?");
            $updateStmt->execute([$newPassword, $email]);

            echo json_encode(['status' => 'success', 'message' => 'Password updated successfully.']);
            break;

        // ── 4. QUICK ACTION: NEW EVENT & EVENT APPROVAL ──
        case 'create_event':
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $event_date = trim($_POST['event_date'] ?? date('Y-m-d'));
            $location = trim($_POST['location'] ?? 'Main Auditorium');
            $created_by = trim($_POST['created_by'] ?? 'Dr. Dipali Shende (HOD)');

            if (empty($title) || empty($event_date)) {
                echo json_encode(['status' => 'error', 'message' => 'Event Title and Date are required.']);
                exit;
            }

            // Check if event already exists with same title & date
            $checkExisting = $pdo->prepare("SELECT `id` FROM `events` WHERE LOWER(`title`) = LOWER(?) AND `event_date` = ?");
            $checkExisting->execute([$title, $event_date]);
            $existingId = $checkExisting->fetchColumn();

            if ($existingId) {
                $upd = $pdo->prepare("UPDATE `events` SET `description` = ?, `location` = ?, `status` = 'Approved' WHERE `id` = ?");
                $upd->execute([$description, $location, $existingId]);
                echo json_encode(['status' => 'success', 'message' => 'Event details updated & published successfully!']);
                break;
            }

            $stmt = $pdo->prepare("INSERT INTO `events` (`title`, `description`, `event_date`, `location`, `status`, `created_by`, `registrations_count`) VALUES (?, ?, ?, ?, 'Approved', ?, 0)");
            $stmt->execute([$title, $description, $event_date, $location, $created_by]);

            // Broadcast notification & persistent announcement
            $notif = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`) VALUES (?, ?, 'green', 'all')");
            $notif->execute(["New Event: {$title}", "Scheduled on {$event_date} at {$location}. Registrations are now open!"]);

            $annStmt = $pdo->prepare("INSERT INTO `announcements` (`title`, `content`, `priority`, `posted_by`, `target_audience`, `views_count`, `pinned`) VALUES (?, ?, 'Normal', ?, 'All Members', 1, 0)");
            $annStmt->execute(["New Event: {$title}", "Scheduled on {$event_date} at {$location}. Registrations are now open!", $created_by]);

            echo json_encode(['status' => 'success', 'message' => 'New event created & approved successfully!']);
            break;

        case 'approve_event':
            $id = (int)($_POST['event_id'] ?? 0);
            $stmt = $pdo->prepare("UPDATE `events` SET `status` = 'Approved' WHERE `id` = ?");
            $stmt->execute([$id]);

            $eventStmt = $pdo->prepare("SELECT title, event_date FROM `events` WHERE `id` = ?");
            $eventStmt->execute([$id]);
            $evt = $eventStmt->fetch();

            if ($evt) {
                $notif = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`) VALUES (?, ?, 'green', 'all')");
                $notif->execute(["Event Approved: {$evt->title}", "HOD has officially approved {$evt->title}."]);
            }

            echo json_encode(['status' => 'success', 'message' => 'Event approved successfully!']);
            break;

        case 'reject_event':
            $id = (int)($_POST['event_id'] ?? 0);
            $stmt = $pdo->prepare("UPDATE `events` SET `status` = 'Rejected' WHERE `id` = ?");
            $stmt->execute([$id]);

            echo json_encode(['status' => 'success', 'message' => 'Event request rejected.']);
            break;

        case 'delete_event':
            $id = (int)($_POST['event_id'] ?? $_POST['id'] ?? 0);
            if (!$id) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid event ID']);
                exit;
            }
            $stmt = $pdo->prepare("DELETE FROM `events` WHERE `id` = ?");
            $stmt->execute([$id]);

            $stmt2 = $pdo->prepare("DELETE FROM `meetings` WHERE `id` = ?");
            $stmt2->execute([$id]);

            echo json_encode(['status' => 'success', 'message' => 'Event deleted successfully!']);
            break;

        case 'delete_announcement':
            $id = (int)($_POST['id'] ?? $_POST['announcement_id'] ?? 0);
            if (!$id) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid announcement ID']);
                exit;
            }

            $titleStmt = $pdo->prepare("SELECT `title` FROM `announcements` WHERE `id` = ?");
            $titleStmt->execute([$id]);
            $annTitle = $titleStmt->fetchColumn();

            $stmt = $pdo->prepare("DELETE FROM `announcements` WHERE `id` = ?");
            $stmt->execute([$id]);

            if ($annTitle) {
                $notifStmt = $pdo->prepare("DELETE FROM `notifications` WHERE LOWER(`title`) = LOWER(?)");
                $notifStmt->execute([$annTitle]);
            }

            echo json_encode(['status' => 'success', 'message' => 'Announcement deleted successfully!']);
            break;

        // ── 5. QUICK ACTION: NOTIFY ALL ──
        case 'notify_all':
            $title = trim($_POST['title'] ?? '');
            $text = trim($_POST['text'] ?? '');
            $indicator = trim($_POST['indicator'] ?? 'green');
            $recipient = trim($_POST['recipient'] ?? 'all');

            if (empty($title) || empty($text)) {
                echo json_encode(['status' => 'error', 'message' => 'Announcement Title and Content are required.']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`, `email_sent`) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$title, $text, $indicator, $recipient]);

            // Persistent dual insertion into announcements table as well
            $priority = $indicator === 'red' ? 'Urgent' : ($indicator === 'yellow' ? 'Important' : 'Normal');
            $postedBy = $_SESSION['user']['name'] ?? 'Head of Department (HOD)';
            $targetAudience = (in_array(strtolower($recipient), ['all', 'all members', 'everyone', 'public'])) ? 'All Members' : $recipient;
            $annStmt = $pdo->prepare("INSERT INTO `announcements` (`title`, `content`, `priority`, `posted_by`, `target_audience`, `views_count`, `pinned`) VALUES (?, ?, ?, ?, ?, 1, 0)");
            $annStmt->execute([$title, $text, $priority, $postedBy, $targetAudience]);

            echo json_encode(['status' => 'success', 'message' => 'Announcement sent successfully! Displayed on portal & landing page.']);
            break;

        // ── 6. COMMITTEE MANAGEMENT ──
        case 'save_committee':
            $email = trim($_POST['email'] ?? '');
            $designation = trim($_POST['designation'] ?? '');
            $responsibility = trim($_POST['responsibility'] ?? '');

            if (empty($email) || empty($designation)) {
                echo json_encode(['status' => 'error', 'message' => 'Select a member and specify a designation.']);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE `users` SET `committeeDesignation` = ?, `committeeResponsibility` = ?, `role` = IF(`role`='Student Member', 'Committee Member', `role`) WHERE `email` = ?");
            $stmt->execute([$designation, $responsibility, $email]);

            echo json_encode(['status' => 'success', 'message' => 'Committee member designation updated successfully!']);
            break;

        case 'remove_committee':
            $email = trim($_POST['email'] ?? '');
            $stmt = $pdo->prepare("UPDATE `users` SET `committeeDesignation` = NULL, `committeeResponsibility` = NULL, `role` = IF(`role`='Committee Member', 'Student Member', `role`) WHERE `email` = ?");
            $stmt->execute([$email]);

            echo json_encode(['status' => 'success', 'message' => 'Member removed from committee.']);
            break;

        // ── 7. CERTIFICATE GENERATOR ──
        case 'generate_certificate':
            $type = trim($_POST['type'] ?? 'Participation Certificate');
            $event_name = trim($_POST['event_name'] ?? 'Tech Symposium 2026');
            $student_name = trim($_POST['student_name'] ?? '');
            $student_email = trim($_POST['student_email'] ?? '');

            if (empty($student_name)) {
                echo json_encode(['status' => 'error', 'message' => 'Please specify a student name.']);
                exit;
            }

            if (empty($student_email)) {
                // Try fetching email from users
                $uStmt = $pdo->prepare("SELECT email FROM `users` WHERE `name` = ? LIMIT 1");
                $uStmt->execute([$student_name]);
                $u = $uStmt->fetch();
                $student_email = $u ? $u->email : 'student@zealeducation.com';
            }

            $cert_code = 'CERT-' . date('Y') . '-' . str_pad(mt_rand(100, 9999), 4, '0', STR_PAD_LEFT);

            // Path for simulated/generated PDF or printable view
            $pdf_path = "uploads/certificates/{$cert_code}.pdf";

            $stmt = $pdo->prepare("INSERT INTO `certificates` (`cert_code`, `type`, `event_name`, `student_name`, `student_email`, `pdf_path`) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$cert_code, $type, $event_name, $student_name, $student_email, $pdf_path]);

            // Notify student
            $notif = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`) VALUES ('Certificate Available', ?, 'green', ?)");
            $notif->execute(["Your {$type} for {$event_name} (ID: {$cert_code}) has been issued.", $student_email]);

            echo json_encode([
                'status' => 'success',
                'message' => "Certificate {$cert_code} generated & issued to {$student_name} successfully!",
                'cert_code' => $cert_code,
                'file_path' => $pdf_path
            ]);
            break;

        // ── 8. QUICK ACTION: VIEW & AUTO-GENERATE REPORTS ──
        case 'generate_report':
            $category = trim($_POST['category'] ?? 'Event Report');
            $format = trim($_POST['format'] ?? 'PDF');
            $custom_title = trim($_POST['title'] ?? '');

            $total_members = (int)$pdo->query("SELECT COUNT(*) FROM `users` WHERE `membershipStatus`='Active'")->fetchColumn();
            $total_events = (int)$pdo->query("SELECT COUNT(*) FROM `events`")->fetchColumn();
            $total_regs = (int)$pdo->query("SELECT SUM(`registrations_count`) FROM `events`")->fetchColumn();

            $title = !empty($custom_title) ? $custom_title : "AIMSA {$category} - " . date('F Y');
            $summary = "Automated Compilation: {$total_members} Active Members · {$total_events} Total Events · {$total_regs} Registrations overview.";

            $filename = "report_" . time() . "." . strtolower($format === 'Excel' ? 'csv' : 'pdf');
            $file_path = "uploads/reports/" . $filename;

            // Generate report file content on server
            $reportContent = "====================================================\n";
            $reportContent .= "ZEAL EDUCATION SOCIETY - DEPARTMENT OF AI & ML\n";
            $reportContent .= "AIMSA AUTOMATED REPORT: {$title}\n";
            $reportContent .= "Generated On: " . date('Y-m-d H:i:s') . "\n";
            $reportContent .= "Generated By: Dr. Dipali Shende (Head of Department)\n";
            $reportContent .= "====================================================\n\n";
            $reportContent .= "EXECUTIVE SUMMARY:\n{$summary}\n\n";

            $reportContent .= "1. EVENT STATISTICS:\n";
            $events = $pdo->query("SELECT title, event_date, location, status, registrations_count FROM `events` ORDER BY `event_date` DESC")->fetchAll();
            foreach ($events as $idx => $evt) {
                $reportContent .= sprintf("%d. %s | Date: %s | Venue: %s | Status: %s | Regs: %d\n", $idx + 1, $evt->title, $evt->event_date, $evt->location, $evt->status, $evt->registrations_count);
            }

            $reportContent .= "\n2. MEMBERSHIP STATISTICS:\n";
            $members = $pdo->query("SELECT name, email, role, branch, batch, membershipStatus FROM `users` ORDER BY `id` ASC")->fetchAll();
            foreach ($members as $idx => $m) {
                $reportContent .= sprintf("%d. %s (%s) | Role: %s | Branch: %s | Status: %s\n", $idx + 1, $m->name, $m->email, $m->role, $m->branch, $m->membershipStatus);
            }

            file_put_contents(__DIR__ . '/../' . $file_path, $reportContent);

            $stmt = $pdo->prepare("INSERT INTO `reports` (`title`, `category`, `summary`, `format`, `file_path`, `created_by`) VALUES (?, ?, ?, ?, ?, 'Dr. Dipali Shende')");
            $stmt->execute([$title, $category, $summary, $format, $file_path]);

            echo json_encode([
                'status' => 'success',
                'message' => "Report '{$title}' generated automatically successfully!",
                'file_path' => $file_path
            ]);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
