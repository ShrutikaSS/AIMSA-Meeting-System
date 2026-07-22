<?php
require_once __DIR__ . '/../include/dbConfig.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'getNotifications':
            $userEmail = $_SESSION['user']['email'] ?? ($_GET['email'] ?? '');
            $userRole = $_SESSION['user']['role'] ?? ($_GET['role'] ?? '');

            $sql = "SELECT * FROM `notifications` WHERE 1=1";
            $params = [];

            if ($userEmail) {
                $sql .= " AND (`recipient` = 'all' OR `recipient` = :email OR `recipient` = :role)";
                $params[':email'] = $userEmail;
                $params[':role'] = $userRole;
            }

            $sql .= " ORDER BY `created_at` DESC LIMIT 50";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $notifications = $stmt->fetchAll();

            echo json_encode(['status' => 'success', 'notifications' => $notifications]);
            break;

        case 'addNotification':
            $title = trim($_POST['title'] ?? '');
            $text = trim($_POST['text'] ?? '');
            $indicator = trim($_POST['indicator'] ?? 'green');
            $recipient = trim($_POST['recipient'] ?? 'all');
            $emailSent = isset($_POST['email_sent']) ? (int)$_POST['email_sent'] : 1;

            if (!$title || !$text) {
                echo json_encode(['status' => 'error', 'message' => 'Title and text are required']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`, `email_sent`) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $text, $indicator, $recipient, $emailSent]);

            echo json_encode(['status' => 'success', 'message' => 'Notification added', 'id' => $pdo->lastInsertId()]);
            break;

        case 'getUnreadCount':
            $userEmail = $_SESSION['user']['email'] ?? ($_GET['email'] ?? '');
            $userRole = $_SESSION['user']['role'] ?? ($_GET['role'] ?? '');

            $sql = "SELECT COUNT(*) FROM `notifications` WHERE 1=1";
            $params = [];

            if ($userEmail) {
                $sql .= " AND (`recipient` = 'all' OR `recipient` = :email OR `recipient` = :role)";
                $params[':email'] = $userEmail;
                $params[':role'] = $userRole;
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $count = $stmt->fetchColumn();

            echo json_encode(['status' => 'success', 'count' => (int)$count]);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
