<?php
require_once __DIR__ . '/../include/dbConfig.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('sendEmailNotification')) {
    function sendEmailNotification($toEmail, $subject, $body) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: AIMSA Association <noreply@zealeducation.com>\r\n";
        $mailSent = @mail($toEmail, $subject, $body, $headers);
        return $mailSent;
    }
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

/**
 * Helper function to generate recipient matching SQL for targeted notifications
 */
function buildRecipientWhereSQL($userEmail, $userRole, $userZprn, &$params) {
    $where = "(LOWER(`recipient`) IN ('all', 'all members', 'everyone', 'public')";
    
    if ($userEmail) {
        $where .= " OR LOWER(`recipient`) = LOWER(:userEmail)";
        $params[':userEmail'] = $userEmail;
    }
    if ($userZprn) {
        $where .= " OR LOWER(`recipient`) = LOWER(:userZprn)";
        $params[':userZprn'] = $userZprn;
    }
    if ($userRole) {
        $roleAliases = [strtolower($userRole)];
        if (stripos($userRole, 'student') !== false) {
            $roleAliases = array_merge($roleAliases, ['student member', 'students', 'student']);
        }
        if (stripos($userRole, 'faculty') !== false) {
            $roleAliases = array_merge($roleAliases, ['faculty coordinator', 'faculty']);
        }
        if (stripos($userRole, 'hod') !== false || stripos($userRole, 'head') !== false) {
            $roleAliases = array_merge($roleAliases, ['hod', 'head of department']);
        }
        if (stripos($userRole, 'president') !== false) {
            $roleAliases = array_merge($roleAliases, ['association president', 'president']);
        }
        if (stripos($userRole, 'committee') !== false) {
            $roleAliases = array_merge($roleAliases, ['committee member', 'committee', 'technical team']);
        }
        $roleAliases = array_unique($roleAliases);

        $rolePlaceholders = [];
        foreach (array_values($roleAliases) as $i => $alias) {
            $paramName = ":role_alias_" . $i;
            $rolePlaceholders[] = "LOWER(`recipient`) = {$paramName}";
            $params[$paramName] = $alias;
        }
        if (!empty($rolePlaceholders)) {
            $where .= " OR " . implode(" OR ", $rolePlaceholders);
        }
    }
    $where .= ")";
    return $where;
}

try {
    switch ($action) {
        case 'getNotifications':
            $userEmail = $_SESSION['user']['email'] ?? ($_REQUEST['email'] ?? '');
            $userRole = $_SESSION['user']['role'] ?? ($_REQUEST['role'] ?? '');
            $userZprn = $_SESSION['user']['zprn'] ?? ($_REQUEST['zprn'] ?? '');

            $params = [];
            $whereSQL = buildRecipientWhereSQL($userEmail, $userRole, $userZprn, $params);
            $sql = "SELECT * FROM `notifications` WHERE {$whereSQL} ORDER BY `created_at` DESC LIMIT 50";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $notifications = $stmt->fetchAll();

            echo json_encode(['status' => 'success', 'notifications' => $notifications]);
            break;

        case 'addNotification':
            $title = trim($_POST['title'] ?? '');
            $text = trim($_POST['text'] ?? $_POST['content'] ?? '');
            $indicator = trim($_POST['indicator'] ?? 'green');
            $recipient = trim($_POST['recipient'] ?? 'all');
            $emailSent = isset($_POST['email_sent']) ? (int)$_POST['email_sent'] : 1;
            $postedBy = $_SESSION['user']['name'] ?? ($_POST['posted_by'] ?? 'System Admin');

            if (!$title || !$text) {
                echo json_encode(['status' => 'error', 'message' => 'Title and text are required']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`, `email_sent`) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $text, $indicator, $recipient, $emailSent]);
            $notifId = $pdo->lastInsertId();

            // If broadcast notification, sync to announcements table as well
            if (in_array(strtolower($recipient), ['all', 'all members', 'everyone', 'public'])) {
                $priority = $indicator === 'red' ? 'Urgent' : ($indicator === 'yellow' ? 'Important' : 'Normal');
                $annStmt = $pdo->prepare("INSERT INTO `announcements` (`title`, `content`, `priority`, `posted_by`, `target_audience`, `views_count`, `pinned`) VALUES (?, ?, ?, ?, ?, 1, 0)");
                $annStmt->execute([$title, $text, $priority, $postedBy, 'All Members']);
            }

            // Send email notification if email_sent flag is set
            if ($emailSent && !empty($recipient)) {
                $subject = "[AIMSA] {$title}";
                $htmlBody = "<h2>{$title}</h2><p>{$text}</p><hr><p style='color:#666;font-size:12px;'>AIMSA Association - Zeal Education</p>";
                if (in_array(strtolower($recipient), ['all', 'all members', 'everyone', 'public'])) {
                    $userStmt = $pdo->query("SELECT email FROM users WHERE membershipStatus = 'Active'");
                    while ($u = $userStmt->fetch()) {
                        sendEmailNotification($u->email, $subject, $htmlBody);
                    }
                } elseif (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                    sendEmailNotification($recipient, $subject, $htmlBody);
                }
            }

            echo json_encode(['status' => 'success', 'message' => 'Notification added', 'id' => $notifId]);
            break;

        case 'getUnreadCount':
            $userEmail = $_SESSION['user']['email'] ?? ($_REQUEST['email'] ?? '');
            $userRole = $_SESSION['user']['role'] ?? ($_REQUEST['role'] ?? '');
            $userZprn = $_SESSION['user']['zprn'] ?? ($_REQUEST['zprn'] ?? '');

            $params = [];
            $whereSQL = buildRecipientWhereSQL($userEmail, $userRole, $userZprn, $params);
            $sql = "SELECT COUNT(*) FROM `notifications` WHERE {$whereSQL}";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $count = $stmt->fetchColumn();

            echo json_encode(['status' => 'success', 'count' => (int)$count]);
            break;

        case 'get_public_announcements':
            $sql = "SELECT `id`, `title`, `content`, `priority`, `posted_by`, `target_audience`, `views_count`, `pinned`, `created_at` 
                    FROM `announcements` 
                    WHERE LOWER(`target_audience`) IN ('all', 'all members', 'everyone', 'public', 'students') 
                    ORDER BY `pinned` DESC, `id` DESC LIMIT 20";
            $stmt = $pdo->query($sql);
            $announcements = $stmt->fetchAll();

            echo json_encode(['status' => 'success', 'announcements' => $announcements]);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
