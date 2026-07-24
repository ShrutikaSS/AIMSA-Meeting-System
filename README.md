# AIMSA — Artificial Intelligence & Machine Learning Student Association Portal

Welcome to the official web application repository for **AIMSA (AI & ML Student Association)** at **Zeal College of Engineering and Research, Pune**.

---

## 🚀 Overview & Key Features

The AIMSA Portal is a full-stack PHP/MySQL platform providing role-based dashboards, event management, digital membership passes, and dynamic public landing page feeds.

### 🌟 Key Capabilities:
- **Role-Based Access Control (RBAC)**: Custom interactive dashboards tailored for:
  - 🎓 **Student Members** (`student_dashboard.php`)
  - ⚡ **Committee Members** (`committee_dashboard.php`)
  - 👔 **Association President** (`president_dashboard.php`)
  - 👩‍🏫 **Faculty Coordinators** (`faculty_dashboard.php`)
  - 🏛️ **Head of Department (HOD)** (`hod_dashboard.php`)
- **Targeted Notifications & Announcements**: Dual broadcast & targeted visibility per recipient email, ZPRN, or role alias.
- **Dynamic Event Publishing**: Real-time event scheduling, meeting tracking, attendance management, and interactive detail modals.
- **Digital Membership Cards**: Verified student digital membership pass with live QR/barcodes and renewal tracking.
- **Certificate Verification & Downloads**: Instant PDF verification for workshop attendees and event participants.

---

## 🛠️ Technology Stack

- **Backend**: PHP 8.x, PDO Prepared Statements, MySQL 8.0 / MariaDB
- **Frontend**: Native HTML5, CSS3 Custom Properties, Vanilla JavaScript (ES6+)
- **Security**: Password Hashing (`PASSWORD_DEFAULT`), Prepared SQL Statements, Input Sanitation (`htmlspecialchars`), Role-Based Auth Guards
- **Design System**: Responsive glassmorphism typography (`Sora`, `Inter`, `JetBrains Mono`)

---

## 📂 Project Structure

```
AIMSA-Meeting-System/
├── index.php                 # Public Landing Page & Interactive Portal Hub
├── event_calendar.php        # Departmental Interactive Calendar & Timeline
├── student_dashboard.php     # Student Member Dashboard
├── committee_dashboard.php   # Committee Member Dashboard
├── president_dashboard.php   # Association President Dashboard
├── faculty_dashboard.php     # Faculty Coordinator Dashboard
├── hod_dashboard.php         # Head of Department (HOD) Dashboard
├── navbar.php                # Global Navigation Bar with Multi-role Login Modal
├── ajax/                     # Async API Endpoints
│   ├── auth.php              # Login, Registration, Logout & Role Validation
│   ├── eventActions.php      # Landing Events & Event Calendar API
│   ├── hod_actions.php       # HOD Dashboard Data & Event Approval API
│   ├── president_actions.php # President Dashboard & Announcement API
│   ├── student_actions.php   # Student Registration & Certificate API
│   ├── faculty_actions.php   # Faculty Coordinator Data & Verification API
│   ├── notificationActions.php # Targeted Recipient Notification Engine
│   └── file_handler.php      # Secure Gallery & Document Upload Handler
├── include/                  # Layout Includes & Configurations
│   ├── dbConfig.php          # Database PDO Connection Config
│   ├── dbSetup.php           # Automatic Database Schema & Seed Script
│   ├── header.php            # Meta & Head Include
│   └── footer.php            # Global Footer & Modal Include
└── assets/                   # Static Frontend Assets
    ├── css/landing.css       # Core Design System Tokens & Styles
    └── js/landing.js         # Interactive Frontend Scripts
```

---

## ⚙️ Installation & Setup

1. **Prerequisites**: XAMPP / WAMP / LAMP stack running **PHP 8.0+** and **MySQL 5.7+**.
2. **Setup Workspace**: Clone or copy this repository into your web root (e.g., `C:/xampp/htdocs/AIMSA-Meeting-System`).
3. **Database Setup**:
   - Start Apache and MySQL in XAMPP Control Panel.
   - Open your browser and navigate to `http://localhost/AIMSA-Meeting-System/include/dbSetup.php`.
   - The setup script will automatically create the database `aimsa_db`, all required tables, and initial default users.
4. **Default Test Accounts**:
   - **HOD**: `hod.aiml@zealeducation.com` | Password: `password123`
   - **Faculty Coordinator**: `faculty.coordinator@zealeducation.com` | Password: `password123`
   - **Association President**: `president.aimsa@zealeducation.com` | Password: `password123`
   - **Student Member**: `student@zealeducation.com` | Password: `password123`

---

## 🔒 Security Best Practices

- All database queries use PDO prepared statements with bound parameters (`?` or `:name`).
- Frontend inputs are escaped via `escapeHtml()` prior to DOM insertion to prevent XSS.
- Uploaded media extensions are strictly validated against a safe whitelist (`jpg`, `jpeg`, `png`, `pdf`, `mp4`).

---

## 🌐 Production Readiness

Tested & audited across modern browsers (Chrome, Firefox, Edge, Safari) and screen sizes (Mobile 375px, Tablet 768px, Desktop 1440px+).
