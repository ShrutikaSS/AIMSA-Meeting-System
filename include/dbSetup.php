<?php
// Automatic Database Setup & Seed Migration Script for AIMSA System
require_once __DIR__ . '/dbConfig.php';

function setupDatabaseTables($pdo) {
    try {
        // 1. Users Table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL DEFAULT 'password123',
            `role` VARCHAR(100) NOT NULL DEFAULT 'Student Member',
            `branch` VARCHAR(100) DEFAULT 'AI & ML',
            `batch` VARCHAR(50) DEFAULT '2026',
            `membershipStatus` VARCHAR(50) DEFAULT 'Active',
            `committeeDesignation` VARCHAR(150) NULL,
            `committeeResponsibility` TEXT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Seed / Update initial HOD, Faculty, President, Committee & Students according to exact role credentials
        $defaultUsers = [
            ['Dr. Dipali Shende', 'hod@zealeducation.com', 'hod123', 'HOD', 'AI & ML', 'Faculty', 'Active', 'Head of Department', 'Departmental Leadership & Guidance'],
            ['Prof. Meera Nair', 'faculty@zealeducation.com', 'faculty123', 'Faculty Coordinator', 'AI & ML', 'Faculty', 'Active', 'Faculty Coordinator', 'Event Oversight & Advisory'],
            ['Karan Mehta', 'president@zealeducation.com', 'president123', 'Association President', 'AI & ML', '2024', 'Active', 'President', 'Association Roadmap & Execution'],
            ['Riya Desai', 'committee@zealeducation.com', 'committee123', 'Committee Member', 'AI & ML', '2025', 'Active', 'Technical Committee', 'Technical Event Organizer'],
            ['Arjun Patil', 'student@zealeducation.com', 'student123', 'Student Member', 'AI & ML', '2026', 'Active', NULL, NULL],
            ['Aarav Sharma', 'aarav.sharma@zealeducation.com', 'president123', 'Association President', 'AI & ML', '2024', 'Active', 'President', 'Association Roadmap & Execution'],
            ['Neha Verma', 'neha.verma@zealeducation.com', 'password123', 'Association President', 'AI & ML', '2024', 'Active', 'Vice President', 'Cross-committee coordination'],
            ['Rohan Kulkarni', 'rohan.k@zealeducation.com', 'committee123', 'Committee Member', 'AI & ML', '2025', 'Active', 'Technical Head', 'Technical Workshops Lead'],
            ['Vikram Salunkhe', 'vikram.s@zealeducation.com', 'student123', 'Student Member', 'AI & ML', '2025', 'Active', NULL, NULL]
        ];

        $upsertUser = $pdo->prepare("INSERT INTO `users` (`name`, `email`, `password`, `role`, `branch`, `batch`, `membershipStatus`, `committeeDesignation`, `committeeResponsibility`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            `name` = VALUES(`name`),
            `password` = VALUES(`password`),
            `role` = VALUES(`role`),
            `branch` = VALUES(`branch`),
            `batch` = VALUES(`batch`),
            `membershipStatus` = VALUES(`membershipStatus`),
            `committeeDesignation` = VALUES(`committeeDesignation`),
            `committeeResponsibility` = VALUES(`committeeResponsibility`)");

        foreach ($defaultUsers as $u) {
            $upsertUser->execute($u);
        }

        // 2. Events Table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `events` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `description` TEXT NULL,
            `event_date` DATE NOT NULL,
            `location` VARCHAR(255) DEFAULT 'Main Auditorium',
            `status` VARCHAR(50) DEFAULT 'Approved',
            `created_by` VARCHAR(255) NOT NULL,
            `registrations_count` INT DEFAULT 0,
            `report_file` VARCHAR(255) NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $stmt = $pdo->query("SELECT COUNT(*) FROM `events`");
        if ($stmt->fetchColumn() == 0) {
            $defaultEvents = [
                ['Tech Symposium 2026', 'Annual departmental tech flagship event featuring paper presentations and code hackathons.', '2026-07-28', 'Main Auditorium', 'Approved', 'Dr. Dipali Shende', 148, NULL],
                ['AI Workshop Series', 'Hands-on Machine Learning & Deep Learning session for 2nd and 3rd year students.', '2026-08-03', 'Lab 402', 'Approved', 'Prof. Rahul Patil', 86, NULL],
                ['Campus Hackathon 2026', '24-hour innovation sprint on real-world industry AI challenges.', '2026-08-15', 'Online + Campus', 'Pending', 'Aarav Sharma', 62, NULL],
                ['Guest Lecture: ML in Healthcare', 'Special expert lecture by industry leaders on AI applications in medical diagnosis.', '2026-08-22', 'Seminar Hall', 'Approved', 'Dr. Dipali Shende', 45, NULL],
                ['Orientation & Induction 2025', 'Welcome event for first year AI & ML students.', '2025-09-10', 'Seminar Hall', 'Completed', 'Dr. Dipali Shende', 190, NULL]
            ];

            $insertEvent = $pdo->prepare("INSERT INTO `events` (`title`, `description`, `event_date`, `location`, `status`, `created_by`, `registrations_count`, `report_file`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($defaultEvents as $e) {
                $insertEvent->execute($e);
            }
        }

        // 3. Notifications Table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `notifications` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `text` TEXT NOT NULL,
            `indicator` VARCHAR(20) DEFAULT 'green',
            `recipient` VARCHAR(255) DEFAULT 'all',
            `email_sent` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $stmt = $pdo->query("SELECT COUNT(*) FROM `notifications`");
        if ($stmt->fetchColumn() == 0) {
            $defaultNotifs = [
                ['New membership applications', '3 pending approval from new students.', 'yellow', 'HOD', 1],
                ['Tech Symposium 2026 approved', 'Event goes live for registrations.', 'green', 'all', 1],
                ['Monthly report generated', 'June 2026 analytics ready.', 'green', 'HOD', 1],
                ['5 certificates awaiting issuance', 'Review required before releasing PDFs.', 'red', 'HOD', 1]
            ];

            $insertNotif = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`, `email_sent`) VALUES (?, ?, ?, ?, ?)");
            foreach ($defaultNotifs as $n) {
                $insertNotif->execute($n);
            }
        }

        // 4. Certificates Table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `certificates` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `cert_code` VARCHAR(100) NOT NULL UNIQUE,
            `type` VARCHAR(100) NOT NULL,
            `event_name` VARCHAR(255) NOT NULL,
            `student_name` VARCHAR(255) NOT NULL,
            `student_email` VARCHAR(255) NOT NULL,
            `pdf_path` VARCHAR(255) NULL,
            `issued_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $stmt = $pdo->query("SELECT COUNT(*) FROM `certificates`");
        if ($stmt->fetchColumn() == 0) {
            $defaultCerts = [
                ['CERT-2026-001', 'Participation Certificate', 'Tech Symposium 2025', 'Vikram Salunkhe', 'vikram.s@zealeducation.com', NULL],
                ['CERT-2026-002', 'Volunteer Certificate', 'Orientation & Induction 2025', 'Rohan Kulkarni', 'rohan.k@zealeducation.com', NULL],
                ['CERT-2026-003', 'Winner Certificate', 'Tech Symposium 2025', 'Neha Verma', 'neha.verma@zealeducation.com', NULL]
            ];

            $insertCert = $pdo->prepare("INSERT INTO `certificates` (`cert_code`, `type`, `event_name`, `student_name`, `student_email`, `pdf_path`) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($defaultCerts as $c) {
                $insertCert->execute($c);
            }
        }

        // 5. Reports Table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `reports` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `category` VARCHAR(100) NOT NULL,
            `summary` TEXT NULL,
            `format` VARCHAR(20) DEFAULT 'PDF',
            `file_path` VARCHAR(255) NULL,
            `created_by` VARCHAR(255) DEFAULT 'Dr. Dipali Shende',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $stmt = $pdo->query("SELECT COUNT(*) FROM `reports`");
        if ($stmt->fetchColumn() == 0) {
            $defaultReports = [
                ['Monthly Analytics — June 2026', 'Member Report', '247 members · 8 events · 96% attendance overview', 'PDF', NULL, 'Dr. Dipali Shende'],
                ['Semester Membership Report', 'Member Report', 'Batch-wise breakdown for Sem II 2025-26', 'PDF', NULL, 'Dr. Dipali Shende'],
                ['Event Completion Report - Tech Symposium', 'Event Report', '24 events · 1,284 registrations overview', 'PDF', NULL, 'Dr. Dipali Shende']
            ];

            $insertRep = $pdo->prepare("INSERT INTO `reports` (`title`, `category`, `summary`, `format`, `file_path`, `created_by`) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($defaultReports as $r) {
                $insertRep->execute($r);
            }
        }

        // 6. Gallery Table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `gallery` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `item_type` VARCHAR(50) DEFAULT 'Photo',
            `album` VARCHAR(100) DEFAULT 'General',
            `file_path` VARCHAR(255) NOT NULL,
            `file_name` VARCHAR(255) NOT NULL,
            `file_size` INT DEFAULT 0,
            `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $stmt = $pdo->query("SELECT COUNT(*) FROM `gallery`");
        if ($stmt->fetchColumn() == 0) {
            $defaultGallery = [
                ['ML Bootcamp Highlights', 'Photo', 'ML Bootcamp 2026', 'uploads/gallery/bootcamp_pic.jpg', 'bootcamp_pic.jpg', 1048576],
                ['Orientation Ceremony', 'Photo', 'Orientation Day', 'uploads/gallery/orientation_pic.jpg', 'orientation_pic.jpg', 2097152]
            ];

            $insertGal = $pdo->prepare("INSERT INTO `gallery` (`title`, `item_type`, `album`, `file_path`, `file_name`, `file_size`) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($defaultGallery as $g) {
                $insertGal->execute($g);
            }
        }

        return true;
    } catch (PDOException $e) {
        error_log("Database Table Setup Failed: " . $e->getMessage());
        return false;
    }
}

if (isset($pdo) && $pdo) {
    setupDatabaseTables($pdo);
}
?>
