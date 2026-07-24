<?php
require_once __DIR__ . '/../include/dbConfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!$pdo) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$sessionUser = $_SESSION['user'] ?? null;
$userEmail = trim($_POST['email'] ?? $_GET['email'] ?? ($sessionUser['email'] ?? 'student@zealeducation.com'));

try {
    switch ($action) {

        // 1. Get Dashboard Summary Data
        case 'getDashboardData':
            // Fetch User Details
            $stmtUser = $pdo->prepare("SELECT * FROM `users` WHERE LOWER(`email`) = LOWER(?) LIMIT 1");
            $stmtUser->execute([$userEmail]);
            $user = $stmtUser->fetch();

            if (!$user) {
                // Fallback to first student member
                $stmtFallback = $pdo->query("SELECT * FROM `users` WHERE `role` = 'Student Member' LIMIT 1");
                $user = $stmtFallback->fetch();
            }

            if (!$user) {
                echo json_encode(['status' => 'error', 'message' => 'Student user not found']);
                exit;
            }

            // Sync session if matching
            if (!isset($_SESSION['user']) || $_SESSION['user']['email'] === $user->email) {
                $_SESSION['user'] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'branch' => $user->branch,
                    'batch' => $user->batch,
                    'membershipStatus' => $user->membershipStatus,
                    'zprn' => $user->zprn ?? '',
                    'phone' => $user->phone ?? '',
                    'photograph' => $user->photograph ?? ''
                ];
            }

            // Fetch Registered Events
            $stmtRegs = $pdo->prepare("SELECT er.*, e.event_date, e.location, e.description 
                FROM `event_registrations` er 
                LEFT JOIN `events` e ON er.event_id = e.id 
                WHERE LOWER(er.student_email) = LOWER(?) ORDER BY er.registered_at DESC");
            $stmtRegs->execute([$user->email]);
            $registeredEvents = $stmtRegs->fetchAll();

            // Fetch Upcoming Approved Events
            $stmtUpcoming = $pdo->query("SELECT * FROM `events` WHERE `status` = 'Approved' ORDER BY `event_date` ASC");
            $upcomingEvents = $stmtUpcoming->fetchAll();

            // Fetch Certificates strictly issued to this student
            $stmtCerts = $pdo->prepare("SELECT * FROM `certificates` 
                WHERE LOWER(`student_email`) = LOWER(?) 
                   OR (LOWER(`student_name`) = LOWER(?) AND (`student_email` IS NULL OR `student_email` = '')) 
                ORDER BY `issued_at` DESC");
            $stmtCerts->execute([$user->email, $user->name]);
            $certificates = $stmtCerts->fetchAll();

            // Fetch Achievements
            $stmtAch = $pdo->prepare("SELECT * FROM `user_achievements` WHERE LOWER(`student_email`) = LOWER(?) ORDER BY `submitted_at` DESC");
            $stmtAch->execute([$user->email]);
            $achievements = $stmtAch->fetchAll();

            // Fetch Notifications
            $studentZprn = $user->zprn ?? '';
            $stmtNotifs = $pdo->prepare("SELECT * FROM `notifications` 
                WHERE LOWER(`recipient`) IN ('all', 'all members', 'everyone', 'public', 'student member', 'students', 'student') 
                OR LOWER(`recipient`) = LOWER(?) 
                OR ( ? <> '' AND LOWER(`recipient`) = LOWER(?) )
                ORDER BY `created_at` DESC LIMIT 10");
            $stmtNotifs->execute([$user->email, $studentZprn, $studentZprn]);
            $notifications = $stmtNotifs->fetchAll();

            // Calculate Stats
            $eventsAttendedCount = count($registeredEvents) + 5; // Base 5 past + registered
            $upcomingCount = count($upcomingEvents);
            $certCount = count($certificates);
            $achieveCount = count($achievements);

            echo json_encode([
                'status' => 'success',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'branch' => $user->branch,
                    'batch' => $user->batch,
                    'membershipStatus' => $user->membershipStatus,
                    'zprn' => $user->zprn ?? '125UAM1005',
                    'phone' => $user->phone ?? '',
                    'photograph' => $user->photograph ?? '',
                    'studentId' => 'AIMSA-' . ($user->batch ?? '2026') . '-' . sprintf('%04d', $user->id)
                ],
                'registered_events' => $registeredEvents,
                'upcoming_events' => $upcomingEvents,
                'certificates' => $certificates,
                'achievements' => $achievements,
                'notifications' => $notifications,
                'stats' => [
                    'events_attended' => $eventsAttendedCount,
                    'upcoming_events' => count($registeredEvents),
                    'certificates' => $certCount,
                    'achievements' => $achieveCount
                ]
            ]);
            break;

        // 2. Update Student Profile
        case 'updateProfile':
            $name = trim($_POST['name'] ?? '');
            $zprn = trim($_POST['zprn'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $batch = trim($_POST['batch'] ?? '2026');
            $branch = trim($_POST['branch'] ?? 'AI & ML');
            $photograph = trim($_POST['photograph'] ?? '');

            // Handle Photo File Upload if uploaded
            if (isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/profiles/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileExt = strtolower(pathinfo($_FILES['profilePhoto']['name'], PATHINFO_EXTENSION));
                $fileName = 'profile_' . time() . '_' . rand(1000, 9999) . '.' . $fileExt;
                $targetFile = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['profilePhoto']['tmp_name'], $targetFile)) {
                    $photograph = 'uploads/profiles/' . $fileName;
                }
            }

            if (empty($name)) {
                echo json_encode(['status' => 'error', 'message' => 'Full Name cannot be empty']);
                exit;
            }

            $stmtUpdate = $pdo->prepare("UPDATE `users` SET `name` = ?, `zprn` = ?, `phone` = ?, `batch` = ?, `branch` = ?" . 
                ($photograph ? ", `photograph` = ?" : "") . 
                " WHERE LOWER(`email`) = LOWER(?)");

            $params = [$name, $zprn, $phone, $batch, $branch];
            if ($photograph) {
                $params[] = $photograph;
            }
            $params[] = $userEmail;

            $stmtUpdate->execute($params);

            // Also update attendance records and event registrations to reflect new name
            $pdo->prepare("UPDATE `event_registrations` SET `student_name` = ?, `zprn` = ? WHERE LOWER(`student_email`) = LOWER(?)")->execute([$name, $zprn, $userEmail]);
            $pdo->prepare("UPDATE `user_achievements` SET `student_name` = ? WHERE LOWER(`student_email`) = LOWER(?)")->execute([$name, $userEmail]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Profile details updated successfully in database',
                'photograph' => $photograph
            ]);
            break;

        // 3. Renew Membership
        case 'renewMembership':
            $stmtRenew = $pdo->prepare("UPDATE `users` SET `membershipStatus` = 'Active' WHERE LOWER(`email`) = LOWER(?)");
            $stmtRenew->execute([$userEmail]);

            // Add notification
            $stmtNotif = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`, `email_sent`) VALUES (?, ?, 'green', ?, 1)");
            $stmtNotif->execute([
                'Membership Renewed Successfully',
                'Your AIMSA student membership has been renewed for the academic year 2026-27.',
                $userEmail
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Membership status updated to Active']);
            break;

        // 4. Register for an Event
        case 'registerEvent':
            $eventId = (int)($_POST['event_id'] ?? 0);
            $eventName = trim($_POST['event_name'] ?? '');

            if (!$eventId && $eventName) {
                $stmtEvt = $pdo->prepare("SELECT id, title FROM `events` WHERE LOWER(`title`) = LOWER(?) LIMIT 1");
                $stmtEvt->execute([$eventName]);
                $evtRow = $stmtEvt->fetch();
                if ($evtRow) {
                    $eventId = $evtRow->id;
                    $eventName = $evtRow->title;
                }
            }

            if (!$eventId) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid Event selected']);
                exit;
            }

            // Fetch Student Details
            $stmtStu = $pdo->prepare("SELECT * FROM `users` WHERE LOWER(`email`) = LOWER(?) LIMIT 1");
            $stmtStu->execute([$userEmail]);
            $stu = $stmtStu->fetch();

            $studentName = $stu ? $stu->name : 'Student Member';
            $zprn = $stu ? $stu->zprn : '125UAM1005';
            $userId = $stu ? $stu->id : 0;

            // Check if already registered
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM `event_registrations` WHERE `event_id` = ? AND LOWER(`student_email`) = LOWER(?)");
            $stmtCheck->execute([$eventId, $userEmail]);
            if ($stmtCheck->fetchColumn() > 0) {
                echo json_encode(['status' => 'error', 'message' => 'You are already registered for this event.']);
                exit;
            }

            // Insert Registration
            $stmtIns = $pdo->prepare("INSERT INTO `event_registrations` (`event_id`, `event_name`, `user_id`, `student_name`, `student_email`, `zprn`, `role`, `status`) VALUES (?, ?, ?, ?, ?, ?, 'Attendee', 'Registered')");
            $stmtIns->execute([$eventId, $eventName, $userId, $studentName, $userEmail, $zprn]);

            // Increment event registration count
            $pdo->prepare("UPDATE `events` SET `registrations_count` = `registrations_count` + 1 WHERE `id` = ?")->execute([$eventId]);

            // Notification
            $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`, `email_sent`) VALUES (?, ?, 'green', ?, 1)")
               ->execute(['Registration Confirmed', "Successfully registered for event: {$eventName}.", $userEmail]);

            echo json_encode(['status' => 'success', 'message' => "Successfully registered for {$eventName}."]);
            break;

        // 5. Cancel Registration
        case 'cancelRegistration':
            $eventId = (int)($_POST['event_id'] ?? 0);
            $eventName = trim($_POST['event_name'] ?? '');

            if (!$eventId && $eventName) {
                $stmtEvt = $pdo->prepare("SELECT id FROM `events` WHERE LOWER(`title`) = LOWER(?) LIMIT 1");
                $stmtEvt->execute([$eventName]);
                $evtRow = $stmtEvt->fetch();
                if ($evtRow) $eventId = $evtRow->id;
            }

            $stmtDel = $pdo->prepare("DELETE FROM `event_registrations` WHERE (`event_id` = ? OR LOWER(`event_name`) = LOWER(?)) AND LOWER(`student_email`) = LOWER(?)");
            $stmtDel->execute([$eventId, $eventName, $userEmail]);

            if ($eventId) {
                $pdo->prepare("UPDATE `events` SET `registrations_count` = GREATEST(0, `registrations_count` - 1) WHERE `id` = ?")->execute([$eventId]);
            }

            $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`, `email_sent`) VALUES (?, ?, 'red', ?, 1)")
               ->execute(['Registration Cancelled', "Your registration for {$eventName} was cancelled.", $userEmail]);

            echo json_encode(['status' => 'success', 'message' => "Cancelled registration for {$eventName}."]);
            break;

        // 6. Submit Achievement
        case 'submitAchievement':
            $category = trim($_POST['category'] ?? 'Competition Certificate');
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (empty($title) || empty($description)) {
                echo json_encode(['status' => 'error', 'message' => 'Please provide title and details for the achievement.']);
                exit;
            }

            // Fetch Student Details
            $stmtStu = $pdo->prepare("SELECT * FROM `users` WHERE LOWER(`email`) = LOWER(?) LIMIT 1");
            $stmtStu->execute([$userEmail]);
            $stu = $stmtStu->fetch();

            $studentName = $stu ? $stu->name : 'Student Member';
            $userId = $stu ? $stu->id : 0;
            $filePath = null;

            if (isset($_FILES['achFile']) && $_FILES['achFile']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/achievements/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $ext = strtolower(pathinfo($_FILES['achFile']['name'], PATHINFO_EXTENSION));
                $fileName = 'ach_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $targetFile = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['achFile']['tmp_name'], $targetFile)) {
                    $filePath = 'uploads/achievements/' . $fileName;
                }
            }

            $stmtInsAch = $pdo->prepare("INSERT INTO `user_achievements` (`user_id`, `student_name`, `student_email`, `category`, `title`, `description`, `file_path`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
            $stmtInsAch->execute([$userId, $studentName, $userEmail, $category, $title, $description, $filePath]);

            // Add notification
            $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`, `email_sent`) VALUES (?, ?, 'yellow', ?, 1)")
               ->execute(['Achievement Submitted', "Your achievement nomination '{$title}' is under faculty review.", $userEmail]);

            echo json_encode(['status' => 'success', 'message' => 'Achievement submitted successfully for review.']);
            break;

        // 7. Change Password
        case 'changePassword':
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';

            if (empty($currentPassword) || empty($newPassword)) {
                echo json_encode(['status' => 'error', 'message' => 'Please enter all password fields']);
                exit;
            }

            $stmtPass = $pdo->prepare("SELECT `password` FROM `users` WHERE LOWER(`email`) = LOWER(?)");
            $stmtPass->execute([$userEmail]);
            $userPass = $stmtPass->fetch();

            if (!$userPass) {
                echo json_encode(['status' => 'error', 'message' => 'User account not found']);
                exit;
            }

            $pwdMatch = ($userPass->password === $currentPassword) || password_verify($currentPassword, $userPass->password);
            if (!$pwdMatch) {
                echo json_encode(['status' => 'error', 'message' => 'Incorrect current password']);
                exit;
            }

            $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmtUpdatePass = $pdo->prepare("UPDATE `users` SET `password` = ? WHERE LOWER(`email`) = LOWER(?)");
            $stmtUpdatePass->execute([$hashedNewPassword, $userEmail]);

            echo json_encode(['status' => 'success', 'message' => 'Password updated successfully in database']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid student action request']);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
