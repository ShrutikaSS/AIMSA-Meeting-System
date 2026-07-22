<?php
require_once __DIR__ . '/../include/dbConfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!$pdo) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection unavailable']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

$roleDashboards = [
    'HOD' => 'hod_dashboard.php',
    'Faculty Coordinator' => 'faculty_dashboard.php',
    'Association President' => 'president_dashboard.php',
    'Committee Member' => 'committee_dashboard.php',
    'Student Member' => 'student_dashboard.php'
];

try {
    switch ($action) {
        case 'login':
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = trim($_POST['role'] ?? 'Student Member');

            if (empty($email) || empty($password)) {
                echo json_encode(['status' => 'error', 'message' => 'Please enter college email ID and password.']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE LOWER(`email`) = LOWER(:email) AND `role` = :role");
            $stmt->execute([':email' => $email, ':role' => $role]);
            $user = $stmt->fetch();

            if (!$user) {
                // Try fallback search without role filter to give clearer error
                $stmtAlt = $pdo->prepare("SELECT * FROM `users` WHERE LOWER(`email`) = LOWER(:email)");
                $stmtAlt->execute([':email' => $email]);
                $altUser = $stmtAlt->fetch();

                if ($altUser) {
                    echo json_encode(['status' => 'error', 'message' => "This email belongs to role '{$altUser->role}'. Please select '{$altUser->role}' role card."]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Invalid Email ID or Password for the selected role.']);
                }
                exit;
            }

            // Check password (supports plain text or password_hash)
            $pwdMatch = ($user->password === $password) || password_verify($password, $user->password);
            if (!$pwdMatch) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid Email ID or Password.']);
                exit;
            }

            if ($user->membershipStatus === 'Inactive') {
                echo json_encode(['status' => 'error', 'message' => 'Your account is inactive. Please contact the administrator.']);
                exit;
            }

            // Store in session
            $_SESSION['user'] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'branch' => $user->branch,
                'batch' => $user->batch,
                'membershipStatus' => $user->membershipStatus,
                'committeeDesignation' => $user->committeeDesignation
            ];

            $redirect = $roleDashboards[$user->role] ?? 'student_dashboard.php';

            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful',
                'user' => $_SESSION['user'],
                'redirect' => $redirect
            ]);
            break;

        case 'register':
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = trim($_POST['role'] ?? 'Student Member');

            if (empty($name) || empty($email) || empty($password)) {
                echo json_encode(['status' => 'error', 'message' => 'Please fill out all required fields.']);
                exit;
            }

            // Check if user already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE LOWER(`email`) = LOWER(:email)");
            $stmt->execute([':email' => $email]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['status' => 'error', 'message' => 'A user with this Email ID already exists.']);
                exit;
            }

            // Insert new user into DB as Active
            $insert = $pdo->prepare("INSERT INTO `users` (`name`, `email`, `password`, `role`, `branch`, `batch`, `membershipStatus`) VALUES (?, ?, ?, ?, 'AI & ML', '2026', 'Active')");
            $insert->execute([$name, $email, $password, $role]);
            $newId = $pdo->lastInsertId();

            $newUser = [
                'id' => $newId,
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'branch' => 'AI & ML',
                'batch' => '2026',
                'membershipStatus' => 'Active',
                'committeeDesignation' => NULL
            ];

            $_SESSION['user'] = $newUser;
            $redirect = $roleDashboards[$role] ?? 'student_dashboard.php';

            echo json_encode([
                'status' => 'success',
                'message' => 'Registration successful! Your account is now active.',
                'user' => $newUser,
                'redirect' => $redirect
            ]);
            break;

        case 'forgot_password':
            $email = trim($_POST['email'] ?? '');
            $newPassword = $_POST['new_password'] ?? '';

            if (empty($email) || empty($newPassword)) {
                echo json_encode(['status' => 'error', 'message' => 'Please enter your email and new password.']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE LOWER(`email`) = LOWER(:email)");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if (!$user) {
                echo json_encode(['status' => 'error', 'message' => 'No registered account found with that email address.']);
                exit;
            }

            $update = $pdo->prepare("UPDATE `users` SET `password` = :password WHERE `id` = :id");
            $update->execute([':password' => $newPassword, ':id' => $user->id]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Password reset successfully! You can now log in with your new password.'
            ]);
            break;

        case 'get_current_user':
            echo json_encode([
                'status' => 'success',
                'user' => $_SESSION['user'] ?? null
            ]);
            break;

        case 'logout':
            unset($_SESSION['user']);
            session_destroy();
            echo json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action specified']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
