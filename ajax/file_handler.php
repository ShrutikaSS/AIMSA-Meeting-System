<?php
require_once __DIR__ . '/../include/dbConfig.php';

header('Content-Type: application/json');

if (!$pdo) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'upload':
            $item_type = trim($_POST['item_type'] ?? 'Photo');
            $album = trim($_POST['album'] ?? 'General');
            $title = trim($_POST['title'] ?? '');

            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['status' => 'error', 'message' => 'No file uploaded or upload failed.']);
                exit;
            }

            $file = $_FILES['file'];
            $fileName = basename($file['name']);
            $fileSize = $file['size'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'mp4', 'avi', 'mov', 'doc', 'docx'];
            if (!in_array($fileExt, $allowed)) {
                echo json_encode(['status' => 'error', 'message' => 'File extension not allowed. Only images, PDFs, videos & docs allowed.']);
                exit;
            }

            $uniqueName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);
            $uploadDir = __DIR__ . '/../uploads/gallery/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $targetPath = $uploadDir . $uniqueName;
            $relativePath = 'uploads/gallery/' . $uniqueName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                if (empty($title)) {
                    $title = pathinfo($fileName, PATHINFO_FILENAME);
                }

                $stmt = $pdo->prepare("INSERT INTO `gallery` (`title`, `item_type`, `album`, `file_path`, `file_name`, `file_size`) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $item_type, $album, $relativePath, $fileName, $fileSize]);

                echo json_encode([
                    'status' => 'success',
                    'message' => "File '{$fileName}' uploaded & stored successfully!",
                    'file_path' => $relativePath,
                    'item_id' => $pdo->lastInsertId()
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to save uploaded file on server.']);
            }
            break;

        case 'get_gallery':
            $stmt = $pdo->query("SELECT * FROM `gallery` ORDER BY `id` DESC");
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
            break;

        case 'delete_gallery':
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $pdo->prepare("SELECT file_path FROM `gallery` WHERE `id` = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();

            if ($item) {
                $fullPath = __DIR__ . '/../' . $item->file_path;
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }

                $delStmt = $pdo->prepare("DELETE FROM `gallery` WHERE `id` = ?");
                $delStmt->execute([$id]);
            }

            echo json_encode(['status' => 'success', 'message' => 'File deleted successfully.']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid file operation']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
