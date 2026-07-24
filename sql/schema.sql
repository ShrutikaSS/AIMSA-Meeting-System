-- Events table for AIMSA Portal
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    date DATE,
    time TIME,
    venue VARCHAR(255),
    description TEXT,
    max_participants INT DEFAULT 100,
    registration_deadline DATE,
    coordinator VARCHAR(255),
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_date (date)
);

-- Seed sample events
INSERT INTO events (name, category, date, time, venue, description, max_participants, registration_deadline, coordinator, status) VALUES
('Robotics Workshop', 'Workshop', '2026-08-10', '10:00:00', 'Lab 3', 'Hands-on robotics workshop for beginners.', 100, '2026-08-05', 'Tech Committee', 'Pending'),
('Cultural Night 2026', 'Symposium', '2026-08-20', '18:00:00', 'Auditorium', 'Annual cultural night with performances.', 200, '2026-08-15', 'Cultural Committee', 'Pending'),
('Alumni Connect 2026', 'Symposium', '2026-09-05', '14:00:00', 'Seminar Hall 1', 'Networking event with alumni.', 150, '2026-08-30', 'Outreach Committee', 'Pending'),
('Tech Symposium 2026', 'Symposium', '2026-07-28', '09:00:00', 'Main Hall', 'Annual technical symposium.', 300, '2026-07-20', 'Prof. Manisha Devgunde', 'Approved'),
('AI Workshop Series', 'Workshop', '2026-08-03', '11:00:00', 'Lab 2', 'AI/ML workshop series for students.', 80, '2026-07-28', 'Prof. Manisha Devgunde', 'Approved'),
('Hackathon 2026', 'Hackathon', '2026-08-15', '08:00:00', 'Lab 1 & 2', '24-hour coding hackathon.', 200, '2026-08-10', 'Prof. Manisha Devgunde', 'Approved'),
('ML Guest Lecture', 'Guest Lecture', '2026-08-22', '15:00:00', 'Seminar Hall 2', 'Guest lecture on ML trends.', 120, '2026-08-17', 'Prof. Manisha Devgunde', 'Approved');
