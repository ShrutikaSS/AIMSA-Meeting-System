-- AIMSA Meeting System Database Schema
CREATE DATABASE IF NOT EXISTS `aimsa_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `aimsa_db`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL,
  `committee_role` VARCHAR(100) DEFAULT NULL,
  `department` VARCHAR(100) DEFAULT 'AI & ML Department',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Assigned Events Table
CREATE TABLE IF NOT EXISTS `assigned_events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `event_name` VARCHAR(150) NOT NULL,
  `event_date` VARCHAR(50) NOT NULL,
  `role` VARCHAR(50) NOT NULL,
  `tasks_summary` VARCHAR(255) DEFAULT NULL,
  `status` VARCHAR(50) DEFAULT 'Confirmed',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Committee Tasks Table
CREATE TABLE IF NOT EXISTS `committee_tasks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `task_title` VARCHAR(255) NOT NULL,
  `due_date` VARCHAR(50) DEFAULT NULL,
  `priority` VARCHAR(20) DEFAULT 'Medium',
  `status` VARCHAR(20) DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Attendance Records Table
CREATE TABLE IF NOT EXISTS `attendance_records` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `event_name` VARCHAR(150) NOT NULL,
  `student_name` VARCHAR(100) NOT NULL,
  `student_email` VARCHAR(100) DEFAULT NULL,
  `status` VARCHAR(20) NOT NULL,
  `marked_by` VARCHAR(100) DEFAULT NULL,
  `marked_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Event Reports Table
CREATE TABLE IF NOT EXISTS `event_reports` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `event_name` VARCHAR(150) NOT NULL,
  `report_date` VARCHAR(50) NOT NULL,
  `attendance_pct` INT DEFAULT 0,
  `summary` TEXT,
  `status` VARCHAR(50) DEFAULT 'Report Ready',
  `submitted_by` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Notifications Table
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `text` TEXT NOT NULL,
  `indicator` VARCHAR(20) DEFAULT 'blue',
  `recipient_email` VARCHAR(100) NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Data (Default Committee Member Riya Desai)
INSERT IGNORE INTO `users` (`id`, `name`, `email`, `password`, `role`, `committee_role`, `department`, `avatar`, `phone`) VALUES
(1, 'Riya Desai', 'committee@zealeducation.com', 'committee123', 'Committee Member', 'Technical Committee Head', 'AI & ML Department', 'RD', '+91 9876543210'),
(2, 'Aarav Sharma', 'aarav.sharma@zealeducation.com', 'student123', 'Student Member', 'Member', 'AI & ML Department', 'AS', '+91 9876543211'),
(3, 'Ananya Patel', 'ananya.patel@zealeducation.com', 'student123', 'Student Member', 'Member', 'AI & ML Department', 'AP', '+91 9876543212'),
(4, 'Rohan Verma', 'rohan.verma@zealeducation.com', 'student123', 'Student Member', 'Member', 'AI & ML Department', 'RV', '+91 9876543213');

INSERT IGNORE INTO `assigned_events` (`id`, `user_id`, `event_name`, `event_date`, `role`, `tasks_summary`, `status`) VALUES
(1, 1, 'Tech Symposium 2026', 'Jul 28, 2026', 'Organiser', 'Setup, Registration', 'Confirmed'),
(2, 1, 'AI Workshop Series', 'Aug 03, 2026', 'Volunteer', 'Venue arrangement', 'Confirmed'),
(3, 1, 'Hackathon 2026', 'Aug 15, 2026', 'Participant', 'Team formation', 'Registered'),
(4, 1, 'ML Guest Lecture', 'Sep 05, 2026', 'Attendee', 'Registration desk', 'Pending');

INSERT IGNORE INTO `committee_tasks` (`id`, `user_id`, `task_title`, `due_date`, `priority`, `status`) VALUES
(1, 1, 'Finalize Tech Symposium stage setup plan', 'Jul 25, 2026', 'High', 'Pending'),
(2, 1, 'Collect registration forms — AI Workshop', 'Jul 20, 2026', 'Medium', 'Completed'),
(3, 1, 'Submit Hackathon team details', 'Aug 10, 2026', 'Medium', 'Pending');

INSERT IGNORE INTO `attendance_records` (`id`, `event_name`, `student_name`, `student_email`, `status`, `marked_by`) VALUES
(1, 'Tech Symposium 2026', 'Aarav Sharma', 'aarav.sharma@zealeducation.com', 'Present', 'Riya Desai'),
(2, 'Tech Symposium 2026', 'Ananya Patel', 'ananya.patel@zealeducation.com', 'Present', 'Riya Desai'),
(3, 'AI Workshop Series', 'Rohan Verma', 'rohan.verma@zealeducation.com', 'Absent', 'Riya Desai');

INSERT IGNORE INTO `event_reports` (`id`, `event_name`, `report_date`, `attendance_pct`, `summary`, `status`, `submitted_by`) VALUES
(1, 'Tech Symposium 2025', 'Dec 2025', 96, 'Tech Symposium 2025 organized with 96% attendance across all labs.', 'Report Ready', 'Riya Desai'),
(2, 'AI Workshop Q1 2026', 'Apr 2026', 88, 'Hands-on AI workshop conducted for 120+ participants.', 'Report Ready', 'Riya Desai'),
(3, 'Hackathon 2025', 'Aug 2025', 91, '24-hour hackathon with 35 project submissions.', 'Report Ready', 'Riya Desai'),
(4, 'AI Workshop Q2 2026', 'Jul 2026', 0, 'Pending final review and attendance compilation.', 'Pending Submission', 'Riya Desai');

INSERT IGNORE INTO `notifications` (`id`, `title`, `text`, `indicator`, `recipient_email`, `is_read`) VALUES
(1, 'Tech Symposium briefing tomorrow', '10:00 AM, Lab 402', 'blue', 'committee@zealeducation.com', 0),
(2, 'Certificate issued for Hackathon 2025', 'Download available for team members', 'green', 'committee@zealeducation.com', 0),
(3, 'Event report submission reminder', 'AI Workshop Q2 report due Jul 28', 'yellow', 'committee@zealeducation.com', 0),
(4, 'New task assigned by President', 'Finalize stage setup plan for Symposium', 'blue', 'committee@zealeducation.com', 0);
