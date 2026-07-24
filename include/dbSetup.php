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
            `zprn` VARCHAR(100) NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        try {
            $pdo->exec("ALTER TABLE `users` ADD COLUMN `zprn` VARCHAR(100) NULL AFTER `committeeResponsibility`;");
        } catch (Exception $e) {
            // Column already exists
        }

        // Seed / Update initial HOD, Faculty, President, Committee & Students according to exact role credentials and ZPRNs
        $defaultUsers = [
            ['Dr. Dipali Shende', 'hod@zealeducation.com', 'hod123', 'HOD', 'AI & ML', 'Faculty', 'Active', 'Head of Department', 'Departmental Leadership & Guidance', '125UAM1001'],
            ['Prof. Manisha Devgunde', 'faculty@zealeducation.com', 'faculty123', 'Faculty Coordinator', 'AI & ML', 'Faculty', 'Active', 'Faculty Coordinator', 'Event Oversight & Advisory', '125UAM1002'],
            ['Varad', 'president@zealeducation.com', 'president123', 'Association President', 'AI & ML', '2024', 'Active', 'President', 'Association Roadmap & Execution', '125UAM1003'],
            ['Riya Desai', 'committee@zealeducation.com', 'committee123', 'Committee Member', 'AI & ML', '2025', 'Active', 'Technical Committee', 'Technical Event Organizer', '125UAM1004'],
            ['Piyush Sharma', 'piyush@zealeducation.com', 'student123', 'Student Member', 'AI & ML', '2026', 'Active', NULL, NULL, '125UAM1137'],
            ['Arjun Patil', 'student@zealeducation.com', 'student123', 'Student Member', 'AI & ML', '2026', 'Active', NULL, NULL, '125UAM1005'],
            ['Varad', 'aarav.sharma@zealeducation.com', 'president123', 'Association President', 'AI & ML', '2024', 'Active', 'President', 'Association Roadmap & Execution', '125UAM1006'],
            ['Neha Verma', 'neha.verma@zealeducation.com', 'password123', 'Association President', 'AI & ML', '2024', 'Active', 'Vice President', 'Cross-committee coordination', '125UAM1007'],
            ['Rohan Kulkarni', 'rohan.k@zealeducation.com', 'committee123', 'Committee Member', 'AI & ML', '2025', 'Active', 'Technical Head', 'Technical Workshops Lead', '125UAM1008'],
            ['Vikram Salunkhe', 'vikram.s@zealeducation.com', 'student123', 'Student Member', 'AI & ML', '2025', 'Active', NULL, NULL, '125UAM1009']
        ];

        $upsertUser = $pdo->prepare("INSERT INTO `users` (`name`, `email`, `password`, `role`, `branch`, `batch`, `membershipStatus`, `committeeDesignation`, `committeeResponsibility`, `zprn`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            `name` = VALUES(`name`),
            `password` = VALUES(`password`),
            `role` = VALUES(`role`),
            `branch` = VALUES(`branch`),
            `batch` = VALUES(`batch`),
            `membershipStatus` = VALUES(`membershipStatus`),
            `committeeDesignation` = VALUES(`committeeDesignation`),
            `committeeResponsibility` = VALUES(`committeeResponsibility`),
            `zprn` = VALUES(`zprn`)");

        foreach ($defaultUsers as $u) {
            $upsertUser->execute($u);
        }

        // Auto-assign ZPRN to any existing users without a ZPRN
        $unassignedUsers = $pdo->query("SELECT `id`, `name`, `branch` FROM `users` WHERE `zprn` IS NULL OR `zprn` = ''")->fetchAll();
        if ($unassignedUsers) {
            $updateZprn = $pdo->prepare("UPDATE `users` SET `zprn` = ? WHERE `id` = ?");
            foreach ($unassignedUsers as $row) {
                if (stripos($row->name, 'piyush') !== false) {
                    $zprnVal = '125UAM1137';
                } else {
                    $prefix = '125UAM';
                    if (strpos($row->branch, 'CS') !== false) $prefix = '125UCS';
                    else if (strpos($row->branch, 'DS') !== false) $prefix = '125UDS';
                    else if (strpos($row->branch, 'IT') !== false) $prefix = '125UIT';
                    $zprnVal = $prefix . sprintf('%04d', 1000 + (int)$row->id);
                }
                $updateZprn->execute([$zprnVal, $row->id]);
            }
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
                ['AI Workshop Series', 'Hands-on Machine Learning & Deep Learning session for 2nd and 3rd year students.', '2026-08-03', 'Lab 402', 'Approved', 'Prof. Manisha Devgunde', 86, NULL],
                ['Campus Hackathon 2026', '24-hour innovation sprint on real-world industry AI challenges.', '2026-08-15', 'Online + Campus', 'Pending', 'Varad', 62, NULL],
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

        // 7. Meetings Table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `meetings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `meeting_date` DATE NOT NULL,
            `meeting_time` VARCHAR(100) DEFAULT '10:00 AM',
            `venue` VARCHAR(255) DEFAULT 'AIML Seminar Hall',
            `category` VARCHAR(100) DEFAULT 'General Body',
            `target_audience` VARCHAR(100) DEFAULT 'All Members',
            `status` VARCHAR(50) DEFAULT 'Completed',
            `present_count` INT DEFAULT 0,
            `absent_count` INT DEFAULT 0,
            `verified_by` VARCHAR(255) NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $stmt = $pdo->query("SELECT COUNT(*) FROM `meetings`");
        if ($stmt->fetchColumn() == 0) {
            $defaultMeetings = [
                ['Tech Symposium 2026', '2026-07-28', '09:30 AM', 'Main Auditorium', 'Event', 'All Members', 'Completed', 148, 12, 'Prof. Manisha Devgunde'],
                ['AI Workshop Series', '2026-08-03', '11:00 AM', 'Lab 402', 'Workshop', 'Technical Team', 'Completed', 86, 8, 'Prof. Manisha Devgunde'],
                ['Semester Kickoff General Body Sync', '2026-08-10', '03:30 PM', 'Seminar Hall', 'General Body', 'All Members', 'Completed', 210, 22, 'Prof. Manisha Devgunde'],
                ['Executive Committee Planning Sync', '2026-08-18', '02:00 PM', 'Faculty Coordination Room', 'Committee Sync', 'Committee Only', 'Scheduled', 18, 2, NULL]
            ];

            $insertMeet = $pdo->prepare("INSERT INTO `meetings` (`title`, `meeting_date`, `meeting_time`, `venue`, `category`, `target_audience`, `status`, `present_count`, `absent_count`, `verified_by`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($defaultMeetings as $m) {
                $insertMeet->execute($m);
            }
        }

        // 8. Attendance Roster Table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `attendance` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `meeting_id` INT NOT NULL,
            `user_id` INT NOT NULL,
            `student_name` VARCHAR(255) NOT NULL,
            `student_email` VARCHAR(255) NOT NULL,
            `zprn` VARCHAR(100) NULL,
            `branch` VARCHAR(100) DEFAULT 'AI & ML',
            `batch` VARCHAR(50) DEFAULT '2026',
            `status` VARCHAR(50) NOT NULL DEFAULT 'Present',
            `marked_by` VARCHAR(255) DEFAULT 'Faculty Coordinator',
            `marked_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $stmt = $pdo->query("SELECT COUNT(*) FROM `attendance`");
        if ($stmt->fetchColumn() == 0) {
            $defaultAtt = [
                [1, 5, 'Piyush Sharma', 'piyush@zealeducation.com', '125UAM1137', 'AI & ML', '2026', 'Present', 'Prof. Manisha Devgunde'],
                [1, 6, 'Arjun Patil', 'student@zealeducation.com', '125UAM1005', 'AI & ML', '2026', 'Present', 'Prof. Manisha Devgunde'],
                [1, 9, 'Vikram Salunkhe', 'vikram.s@zealeducation.com', '125UAM1009', 'AI & ML', '2025', 'Present', 'Prof. Manisha Devgunde'],
                [1, 8, 'Siddharth Pawar', 'siddharth.p@zealeducation.com', '125UAM1008', 'AI & ML', '2025', 'Absent', 'Prof. Manisha Devgunde'],
                [2, 5, 'Piyush Sharma', 'piyush@zealeducation.com', '125UAM1137', 'AI & ML', '2026', 'Present', 'Prof. Manisha Devgunde'],
                [2, 6, 'Arjun Patil', 'student@zealeducation.com', '125UAM1005', 'AI & ML', '2026', 'Present', 'Prof. Manisha Devgunde'],
                [2, 9, 'Vikram Salunkhe', 'vikram.s@zealeducation.com', '125UAM1009', 'AI & ML', '2025', 'Absent', 'Prof. Manisha Devgunde']
            ];

            $insertAtt = $pdo->prepare("INSERT INTO `attendance` (`meeting_id`, `user_id`, `student_name`, `student_email`, `zprn`, `branch`, `batch`, `status`, `marked_by`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($defaultAtt as $a) {
                $insertAtt->execute($a);
            }
        }

        // 9. Announcements Table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `announcements` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `content` TEXT NOT NULL,
            `priority` VARCHAR(50) DEFAULT 'Normal',
            `posted_by` VARCHAR(255) DEFAULT 'Association President',
            `target_audience` VARCHAR(100) DEFAULT 'All Members',
            `views_count` INT DEFAULT 0,
            `pinned` TINYINT(1) DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $stmt = $pdo->query("SELECT COUNT(*) FROM `announcements`");
        if ($stmt->fetchColumn() == 0) {
            $defaultAnn = [
                ['🏆 Hackathon 2026 registrations open!', 'Official registration portal is now live for all AI & ML department students.', 'Normal', 'Varad (President)', 'All Members', 203, 0],
                ['⚡ Deadline: Club fee payment — Jul 31', 'All committee members and active members are requested to complete dues.', 'Urgent', 'Finance Committee', 'All Members', 312, 1],
                ['✅ Tech Symposium venue confirmed', 'Main Auditorium and Lab 402 reserved for July 28 event.', 'Important', 'Varad (President)', 'All Members', 187, 0],
                ['📢 New committee vacancies — Apply now', 'Applications open for Finance & Outreach committee positions.', 'Normal', 'Outreach Committee', 'Students', 92, 0]
            ];

            $insertAnn = $pdo->prepare("INSERT INTO `announcements` (`title`, `content`, `priority`, `posted_by`, `target_audience`, `views_count`, `pinned`) VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($defaultAnn as $an) {
                $insertAnn->execute($an);
            }
        }

        // Alter users table to ensure phone and photograph columns exist
        try {
            $pdo->exec("ALTER TABLE `users` ADD COLUMN `phone` VARCHAR(50) NULL AFTER `zprn`;");
        } catch (Exception $e) {}
        try {
            $pdo->exec("ALTER TABLE `users` ADD COLUMN `photograph` VARCHAR(255) NULL AFTER `phone`;");
        } catch (Exception $e) {}

        // 10. Event Registrations Table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `event_registrations` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `event_id` INT NOT NULL,
            `event_name` VARCHAR(255) NOT NULL,
            `user_id` INT NOT NULL,
            `student_name` VARCHAR(255) NOT NULL,
            `student_email` VARCHAR(255) NOT NULL,
            `zprn` VARCHAR(100) NULL,
            `role` VARCHAR(100) DEFAULT 'Attendee',
            `status` VARCHAR(50) DEFAULT 'Registered',
            `registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY `unique_event_user` (`event_id`, `student_email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $stmt = $pdo->query("SELECT COUNT(*) FROM `event_registrations`");
        if ($stmt->fetchColumn() == 0) {
            $defaultRegs = [
                [1, 'Tech Symposium 2026', 6, 'Arjun Patil', 'student@zealeducation.com', '125UAM1005', 'Attendee', 'Registered'],
                [3, 'Campus Hackathon 2026', 6, 'Arjun Patil', 'student@zealeducation.com', '125UAM1005', 'Participant', 'Registered'],
                [1, 'Tech Symposium 2026', 5, 'Piyush Sharma', 'piyush@zealeducation.com', '125UAM1137', 'Attendee', 'Registered'],
                [2, 'AI Workshop Series', 5, 'Piyush Sharma', 'piyush@zealeducation.com', '125UAM1137', 'Attendee', 'Registered']
            ];

            $insertReg = $pdo->prepare("INSERT INTO `event_registrations` (`event_id`, `event_name`, `user_id`, `student_name`, `student_email`, `zprn`, `role`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($defaultRegs as $r) {
                $insertReg->execute($r);
            }
        }

        // 11. User Achievements Table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `user_achievements` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `student_name` VARCHAR(255) NOT NULL,
            `student_email` VARCHAR(255) NOT NULL,
            `category` VARCHAR(100) NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `description` TEXT NULL,
            `file_path` VARCHAR(255) NULL,
            `status` VARCHAR(50) DEFAULT 'Pending',
            `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $stmt = $pdo->query("SELECT COUNT(*) FROM `user_achievements`");
        if ($stmt->fetchColumn() == 0) {
            $defaultAch = [
                [6, 'Arjun Patil', 'student@zealeducation.com', 'Hackathon Achievement', 'Active Participant', 'Attended 5+ departmental technical events and workshops.', NULL, 'Approved'],
                [6, 'Arjun Patil', 'student@zealeducation.com', 'Competition Certificate', 'Workshop Graduate', 'Successfully completed AI & ML hands-on bootcamp.', NULL, 'Approved'],
                [6, 'Arjun Patil', 'student@zealeducation.com', 'Research Publication', 'Loyal Member', '1 year active AIMSA member with 90%+ attendance.', NULL, 'Approved'],
                [6, 'Arjun Patil', 'student@zealeducation.com', 'Hackathon Achievement', 'Hackathon Finalist', 'Top 10 finalist in Inter-College AI Hackathon 2025.', NULL, 'Approved']
            ];

            $insertAch = $pdo->prepare("INSERT INTO `user_achievements` (`user_id`, `student_name`, `student_email`, `category`, `title`, `description`, `file_path`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($defaultAch as $a) {
                $insertAch->execute($a);
            }
        }

        // 12. Committee Tasks Table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `committee_tasks` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT DEFAULT 0,
            `task_title` VARCHAR(255) NOT NULL,
            `description` TEXT NULL,
            `due_date` VARCHAR(50) NULL,
            `priority` VARCHAR(50) DEFAULT 'Medium',
            `status` VARCHAR(50) DEFAULT 'Pending',
            `assigned_to_email` VARCHAR(255) DEFAULT 'committee@zealeducation.com',
            `assigned_to_name` VARCHAR(255) DEFAULT 'Riya Desai',
            `created_by` VARCHAR(255) DEFAULT 'Varad (President)',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Alter table if created with older schema
        try { $pdo->exec("ALTER TABLE `committee_tasks` ADD COLUMN `description` TEXT NULL AFTER `task_title`;"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE `committee_tasks` ADD COLUMN `assigned_to_email` VARCHAR(255) DEFAULT 'committee@zealeducation.com' AFTER `status`;"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE `committee_tasks` ADD COLUMN `assigned_to_name` VARCHAR(255) DEFAULT 'Riya Desai' AFTER `assigned_to_email`;"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE `committee_tasks` ADD COLUMN `created_by` VARCHAR(255) DEFAULT 'Varad (President)' AFTER `assigned_to_name`;"); } catch (Exception $e) {}

        $stmt = $pdo->query("SELECT COUNT(*) FROM `committee_tasks`");
        if ($stmt->fetchColumn() == 0) {
            $defaultTasks = [
                [13, 'Finalize Tech Symposium stage setup plan', 'Coordinate stage lighting, sound system and banner setup.', '2026-07-25', 'High Priority', 'Pending', 'committee@zealeducation.com', 'Riya Desai', 'Varad (President)'],
                [13, 'Collect registration forms — AI Workshop', 'Verify Google form entries and compile list of participants.', '2026-07-20', 'Medium Priority', 'Completed', 'committee@zealeducation.com', 'Riya Desai', 'Prof. Manisha Devgunde'],
                [13, 'Submit Hackathon team details', 'Gather hackathon team lists and submit to core committee.', '2026-08-10', 'Medium Priority', 'Pending', 'committee@zealeducation.com', 'Riya Desai', 'Varad (President)'],
                [13, 'Prepare attendance sheets for ML Guest Lecture', 'Print attendance rosters for Seminar Hall 2 entrance.', '2026-08-20', 'Normal Priority', 'Pending', 'committee@zealeducation.com', 'Riya Desai', 'Prof. Manisha Devgunde']
            ];

            $insertTask = $pdo->prepare("INSERT INTO `committee_tasks` (`user_id`, `task_title`, `description`, `due_date`, `priority`, `status`, `assigned_to_email`, `assigned_to_name`, `created_by`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($defaultTasks as $t) {
                $insertTask->execute($t);
            }
        }

        // Migrate any pre-existing database records to updated names
        try {
            $pdo->exec("UPDATE `users` SET `name` = 'Varad' WHERE `role` = 'Association President' OR `email` IN ('president@zealeducation.com', 'aarav.sharma@zealeducation.com');");
            $pdo->exec("UPDATE `users` SET `name` = 'Prof. Manisha Devgunde' WHERE `role` = 'Faculty Coordinator' OR `email` = 'faculty@zealeducation.com';");
            $pdo->exec("UPDATE `events` SET `created_by` = 'Prof. Manisha Devgunde' WHERE `created_by` IN ('Prof. Meera Nair', 'Prof. Rahul Patil');");
            $pdo->exec("UPDATE `events` SET `created_by` = 'Varad' WHERE `created_by` IN ('Karan Mehta', 'Aarav Sharma');");
            $pdo->exec("UPDATE `meetings` SET `verified_by` = 'Prof. Manisha Devgunde' WHERE `verified_by` IN ('Prof. Meera Nair', 'Prof. Rahul Patil');");
            $pdo->exec("UPDATE `attendance` SET `marked_by` = 'Prof. Manisha Devgunde' WHERE `marked_by` IN ('Prof. Meera Nair', 'Prof. Rahul Patil');");
            $pdo->exec("UPDATE `announcements` SET `posted_by` = 'Varad (President)' WHERE `posted_by` LIKE '%Karan Mehta%' OR `posted_by` LIKE '%Aarav Sharma%';");
            $pdo->exec("UPDATE `committee_tasks` SET `created_by` = 'Varad (President)' WHERE `created_by` LIKE '%Karan Mehta%';");
            $pdo->exec("UPDATE `committee_tasks` SET `created_by` = 'Prof. Manisha Devgunde' WHERE `created_by` LIKE '%Meera Nair%' OR `created_by` LIKE '%Rahul Patil%';");
        } catch (Exception $e) {}

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
