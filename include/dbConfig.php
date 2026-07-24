<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'aimsa_db');

$pdo = null;

try {
    // Attempt connection directly to database
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (PDOException $e) {
    // If database does not exist (1049 Unknown database), create it dynamically
    try {
        $pdoServer = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdoServer->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    } catch (PDOException $e2) {
        error_log("Database connection failed: " . $e2->getMessage());
    }
}

if ($pdo) {
    require_once __DIR__ . '/dbSetup.php';
}

// ── MYSQL DATE & TIME MODULE ──
$sqlCurrentDateFormatted = date('F j, Y');
$sqlFullDateFormatted = date('l, F j, Y');

if ($pdo) {
    try {
        $stmtDate = $pdo->query("SELECT DATE_FORMAT(NOW(), '%W, %M %e, %Y') AS `full_date`, DATE_FORMAT(NOW(), '%M %e, %Y') AS `std_date`");
        $sqlDateRow = $stmtDate->fetch(PDO::FETCH_ASSOC);
        if ($sqlDateRow && !empty($sqlDateRow['std_date'])) {
            $sqlCurrentDateFormatted = $sqlDateRow['std_date'];
            $sqlFullDateFormatted = $sqlDateRow['full_date'];
        }
    } catch (Exception $e) {
        error_log("SQL Date Module query error: " . $e->getMessage());
    }
}
?>
