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
            echo json_encode(['status' => 'success', 'message' => 'Event approved']);
            break;

        case 'rejectEvent':
            $id = $_POST['id'] ?? 0;
            $stmt = $pdo->prepare("UPDATE events SET status = 'Rejected' WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success', 'message' => 'Event rejected']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
