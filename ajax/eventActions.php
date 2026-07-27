<?php
require_once __DIR__ . '/../include/dbConfig.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'createEvent':
            $name = $_POST['name'] ?? '';
            $category = $_POST['category'] ?? '';
            $date = $_POST['date'] ?? '';
            $time = $_POST['time'] ?? '';
            $venue = $_POST['venue'] ?? '';
            $desc = $_POST['description'] ?? '';
            $max = $_POST['max_participants'] ?? 100;
            $dl = $_POST['registration_deadline'] ?? '';
            $coordinator = $_POST['coordinator'] ?? '';
            $status = $_POST['status'] ?? 'Pending';

            if (!$name || !$date || !$time || !$venue || !$desc || !$dl) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO events (name, category, date, time, venue, description, max_participants, registration_deadline, coordinator, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $category, $date, $time, $venue, $desc, $max, $dl, $coordinator, $status]);

            echo json_encode(['status' => 'success', 'message' => 'Event created successfully', 'id' => $pdo->lastInsertId()]);
            break;

        case 'getEvents':
            $statusFilter = $_GET['status'] ?? '';
            $sql = "SELECT * FROM events";
            $params = [];
            if ($statusFilter) {
                $sql .= " WHERE status = ?";
                $params[] = $statusFilter;
            }
            $sql .= " ORDER BY date ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $events = $stmt->fetchAll();

            echo json_encode(['status' => 'success', 'events' => $events]);
            break;

        case 'getPendingEvents':
            $stmt = $pdo->prepare("SELECT * FROM events WHERE status = 'Pending' ORDER BY date ASC");
            $stmt->execute();
            echo json_encode(['status' => 'success', 'events' => $stmt->fetchAll()]);
            break;

        case 'getApprovedEvents':
            $stmt = $pdo->prepare("SELECT * FROM events WHERE status = 'Approved' ORDER BY date ASC");
            $stmt->execute();
            echo json_encode(['status' => 'success', 'events' => $stmt->fetchAll()]);
            break;

        case 'approveEvent':
            $id = $_POST['id'] ?? 0;
            $stmt = $pdo->prepare("UPDATE events SET status = 'Approved' WHERE id = ?");
            $stmt->execute([$id]);

            $creator = $pdo->prepare("SELECT created_by FROM events WHERE id = ?");
            $creator->execute([$id]);
            $created_by = $creator->fetchColumn();

            $notif = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`) VALUES (?, ?, 'green', 'all')");
            $notif->execute(["Event Approved", "Event has been approved by faculty."]);

            if ($created_by) {
                $userStmt = $pdo->prepare("SELECT email FROM users WHERE LOWER(name) = LOWER(?) LIMIT 1");
                $userStmt->execute([$created_by]);
                $creatorEmail = $userStmt->fetchColumn();
                if ($creatorEmail) {
                    $notif2 = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`) VALUES (?, ?, 'green', ?)");
                    $notif2->execute(["Your Event Approved", "Your event proposal has been approved and is now live.", $creatorEmail]);
                }
            }

            echo json_encode(['status' => 'success', 'message' => 'Event approved']);
            break;

        case 'getLandingEvents':
            $items = [];
            $titleSeen = [];
            try {
                $stmt = $pdo->query("SELECT `id`, `title`, `description`, `event_date` as `date`, '10:00 AM' as `time`, `location` as `venue`, 'Departmental Event' as `category`, 'All Members' as `target_audience`, `created_by` 
                                    FROM `events` 
                                    WHERE `status` = 'Approved' OR `status` = 'Scheduled'
                                    ORDER BY `event_date` ASC");
                $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($events) {
                    foreach ($events as $ev) {
                        $normTitle = strtolower(trim($ev['title']));
                        if (!isset($titleSeen[$normTitle])) {
                            $titleSeen[$normTitle] = true;
                            $items[] = $ev;
                        }
                    }
                }
            } catch (Exception $e) {}

            try {
                $stmt2 = $pdo->query("SELECT `id`, `title`, CONCAT(`title`, ' — Official department meeting scheduled for ', `target_audience`, '.') as `description`, `meeting_date` as `date`, `meeting_time` as `time`, `venue`, `category`, `target_audience`, COALESCE(`verified_by`, 'Faculty Coordinator') as `created_by` 
                                     FROM `meetings` 
                                     WHERE `status` != 'Completed' AND `status` != 'Cancelled'
                                     ORDER BY `meeting_date` ASC");
                $meetings = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                if ($meetings) {
                    foreach ($meetings as $mt) {
                        $normTitle = strtolower(trim($mt['title']));
                        if (!isset($titleSeen[$normTitle])) {
                            $titleSeen[$normTitle] = true;
                            $items[] = $mt;
                        }
                    }
                }
            } catch (Exception $e) {}

            usort($items, function($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });

            echo json_encode(['status' => 'success', 'events' => array_values($items)]);
            break;

        case 'rejectEvent':
            $id = $_POST['id'] ?? 0;
            $stmt = $pdo->prepare("UPDATE events SET status = 'Rejected' WHERE id = ?");
            $stmt->execute([$id]);

            $creator = $pdo->prepare("SELECT created_by FROM events WHERE id = ?");
            $creator->execute([$id]);
            $created_by = $creator->fetchColumn();

            $notif = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`) VALUES (?, ?, 'red', 'all')");
            $notif->execute(["Event Rejected", "An event proposal has been rejected."]);

            if ($created_by) {
                $userStmt = $pdo->prepare("SELECT email FROM users WHERE LOWER(name) = LOWER(?) LIMIT 1");
                $userStmt->execute([$created_by]);
                $creatorEmail = $userStmt->fetchColumn();
                if ($creatorEmail) {
                    $notif2 = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`) VALUES (?, ?, 'red', ?)");
                    $notif2->execute(["Your Event Rejected", "Your event proposal has been rejected. Please contact faculty for details.", $creatorEmail]);
                }
            }

            echo json_encode(['status' => 'success', 'message' => 'Event rejected']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
