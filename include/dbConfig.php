<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'aimsa_db');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `" . DB_NAME . "`");
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    // Auto-create tables if missing
    initDatabaseIfNeeded($pdo);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
}

function initDatabaseIfNeeded($pdo) {
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `assigned_events` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `user_id` INT NOT NULL DEFAULT 13,
              `event_name` VARCHAR(150) NOT NULL,
              `event_date` VARCHAR(50) NOT NULL,
              `role` VARCHAR(50) NOT NULL,
              `tasks_summary` VARCHAR(255) DEFAULT NULL,
              `status` VARCHAR(50) DEFAULT 'Confirmed',
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `committee_tasks` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `user_id` INT NOT NULL DEFAULT 13,
              `task_title` VARCHAR(255) NOT NULL,
              `due_date` VARCHAR(50) DEFAULT NULL,
              `priority` VARCHAR(20) DEFAULT 'Medium',
              `status` VARCHAR(20) DEFAULT 'Pending',
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        try {
            $pdo->exec("ALTER TABLE `notifications` ADD COLUMN `is_read` TINYINT(1) DEFAULT 0");
        } catch (PDOException $ex) {
            // Column already exists
        }

        // Seed default assigned events if empty
        $checkEvents = $pdo->query("SELECT COUNT(*) FROM assigned_events");
        if ($checkEvents->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO `assigned_events` (`user_id`, `event_name`, `event_date`, `role`, `tasks_summary`, `status`) VALUES
                (13, 'Tech Symposium 2026', 'Jul 28, 2026', 'Organiser', 'Setup, Registration', 'Confirmed'),
                (13, 'AI Workshop Series', 'Aug 03, 2026', 'Volunteer', 'Venue arrangement', 'Confirmed'),
                (13, 'Hackathon 2026', 'Aug 15, 2026', 'Participant', 'Team formation', 'Registered'),
                (13, 'ML Guest Lecture', 'Sep 05, 2026', 'Attendee', 'Registration desk', 'Pending')
            ");
        }

        // Seed default committee tasks if empty
        $checkTasks = $pdo->query("SELECT COUNT(*) FROM committee_tasks");
        if ($checkTasks->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO `committee_tasks` (`user_id`, `task_title`, `due_date`, `priority`, `status`) VALUES
                (13, 'Finalize Tech Symposium stage setup plan', 'Jul 25, 2026', 'High', 'Pending'),
                (13, 'Collect registration forms — AI Workshop', 'Jul 20, 2026', 'Medium', 'Completed'),
                (13, 'Submit Hackathon team details', 'Aug 10, 2026', 'Medium', 'Pending')
            ");
        }
    } catch (PDOException $e) {
        error_log("Failed to initialize database schema: " . $e->getMessage());
    }
}
?>
