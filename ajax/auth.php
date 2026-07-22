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

            $roleAliases = [$role];
            if ($role === 'Association President' || $role === 'President') {
                $roleAliases = ['Association President', 'President', 'Vice President'];
            }

            $placeholders = implode(',', array_fill(0, count($roleAliases), '?'));
            $params = array_merge([$email], $roleAliases);

            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE LOWER(`email`) = LOWER(?) AND `role` IN ($placeholders)");
            $stmt->execute($params);
            $user = $stmt->fetch();

            if (!$user) {
                // Try fallback search without role filter to give clearer error
                $stmtAlt = $pdo->prepare("SELECT * FROM `users` WHERE LOWER(`email`) = LOWER(?)");
                $stmtAlt->execute([$email]);
                $altUser = $stmtAlt->fetch();

                if ($altUser) {
                    echo json_encode(['status' => 'error', 'message' => "This email belongs to role '{$altUser->role}'. Please select the '{$altUser->role}' role card."]);
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
                'committeeDesignation' => $user->committeeDesignation,
                'zprn' => $user->zprn ?? ''
            ];

            // Add login-time notification for the user (avoid duplicates within last 1 hour)
            try {
                $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM `notifications` WHERE `title` = :title AND `recipient` = :recipient AND `created_at` > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
                $checkStmt->execute([
                    ':title' => 'Welcome back, ' . $user->name,
                    ':recipient' => $user->role
                ]);
                if ($checkStmt->fetchColumn() == 0) {
                    $loginNotifStmt = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`, `email_sent`) VALUES (?, ?, 'green', ?, 1)");
                    $loginNotifStmt->execute([
                        'Welcome back, ' . $user->name,
                        'You have successfully logged into the AIMSA Portal. You have new updates waiting for you.',
                        $user->role
                    ]);
                }
            } catch (Exception $e) {
                error_log("Login notification failed: " . $e->getMessage());
            }

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
            $zprn = trim($_POST['zprn'] ?? '');
            $role = trim($_POST['role'] ?? 'Student Member');

            if (empty($name) || empty($email) || empty($password) || empty($zprn)) {
                echo json_encode(['status' => 'error', 'message' => 'Please fill out all required fields including your unique Student ZPRN.']);
                exit;
            }

            // Check if email already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE LOWER(`email`) = LOWER(:email)");
            $stmt->execute([':email' => $email]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['status' => 'error', 'message' => 'A user with this Email ID already exists.']);
                exit;
            }

            // Check if ZPRN already exists
            $stmtZ = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE LOWER(`zprn`) = LOWER(:zprn)");
            $stmtZ->execute([':zprn' => $zprn]);
            if ($stmtZ->fetchColumn() > 0) {
                echo json_encode(['status' => 'error', 'message' => 'A user with this ZPRN (Student ID) is already registered.']);
                exit;
            }

            // Insert new user into DB as Active
            $insert = $pdo->prepare("INSERT INTO `users` (`name`, `email`, `password`, `role`, `branch`, `batch`, `membershipStatus`, `zprn`) VALUES (?, ?, ?, ?, 'AI & ML', '2026', 'Active', ?)");
            $insert->execute([$name, $email, $password, $role, $zprn]);
            $newId = $pdo->lastInsertId();

            $newUser = [
                'id' => $newId,
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'branch' => 'AI & ML',
                'batch' => '2026',
                'membershipStatus' => 'Active',
                'committeeDesignation' => NULL,
                'zprn' => $zprn
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

        case 'verify_zprn':
            $email = trim($_POST['email'] ?? '');
            $zprn = trim($_POST['zprn'] ?? '');

            if (empty($email) && empty($zprn)) {
                echo json_encode(['status' => 'error', 'message' => 'Please enter your registered College Email ID and/or ZPRN.']);
                exit;
            }

            // Flexible query to find user record
            $user = null;
            if (!empty($email) && !empty($zprn)) {
                $stmt = $pdo->prepare("SELECT * FROM `users` WHERE LOWER(`email`) = LOWER(:email) OR LOWER(`zprn`) = LOWER(:zprn)");
                $stmt->execute([':email' => $email, ':zprn' => $zprn]);
                $user = $stmt->fetch();
            } else if (!empty($zprn)) {
                $stmt = $pdo->prepare("SELECT * FROM `users` WHERE LOWER(`zprn`) = LOWER(:zprn)");
                $stmt->execute([':zprn' => $zprn]);
                $user = $stmt->fetch();
            } else {
                $stmt = $pdo->prepare("SELECT * FROM `users` WHERE LOWER(`email`) = LOWER(:email)");
                $stmt->execute([':email' => $email]);
                $user = $stmt->fetch();
            }

            if (!$user) {
                echo json_encode(['status' => 'error', 'message' => 'No registered account found matching that Email ID or ZPRN.']);
                exit;
            }

            if (!empty($zprn) && (!isset($user->zprn) || strtolower(trim($user->zprn)) !== strtolower(trim($zprn)))) {
                echo json_encode(['status' => 'error', 'message' => 'Security Answer (ZPRN) does not match our records for this account.']);
                exit;
            }

            $_SESSION['forgot_zprn_verified'] = [
                'email' => strtolower($user->email),
                'zprn' => strtolower($user->zprn ?? ''),
                'verified_at' => time()
            ];

            echo json_encode([
                'status' => 'success',
                'message' => 'Security Question (ZPRN) verified successfully! You can now set your new password.',
                'email' => $user->email,
                'name' => $user->name
            ]);
            break;

        case 'forgot_password':
            $email = trim($_POST['email'] ?? '');
            $newPassword = $_POST['new_password'] ?? '';

            if (empty($newPassword)) {
                echo json_encode(['status' => 'error', 'message' => 'Please enter your new secure password.']);
                exit;
            }

            // Require Security Question ZPRN verification before allowing password reset
            if (!isset($_SESSION['forgot_zprn_verified'])) {
                echo json_encode(['status' => 'error', 'message' => 'Security Question (ZPRN) verification required before resetting password.']);
                exit;
            }

            $targetEmail = !empty($_SESSION['forgot_zprn_verified']['email']) ? $_SESSION['forgot_zprn_verified']['email'] : strtolower($email);

            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE LOWER(`email`) = LOWER(:email) OR LOWER(`email`) = LOWER(:posted_email)");
            $stmt->execute([':email' => $targetEmail, ':posted_email' => strtolower($email)]);
            $user = $stmt->fetch();

            if (!$user) {
                echo json_encode(['status' => 'error', 'message' => 'No registered account found for password reset.']);
                exit;
            }

            // Save new reset password to database
            $update = $pdo->prepare("UPDATE `users` SET `password` = :password WHERE `id` = :id");
            $update->execute([':password' => $newPassword, ':id' => $user->id]);

            // Create notification record
            try {
                $notifStmt = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`) VALUES (?, ?, 'green', ?)");
                $notifStmt->execute(['Password Updated', "Password for {$user->email} was reset via ZPRN security verification.", $user->email]);
            } catch (Exception $e) {}

            // Clear session verification
            unset($_SESSION['forgot_zprn_verified']);

            echo json_encode([
                'status' => 'success',
                'message' => 'Password reset successfully! Your new password has been saved to the database. You can now log in.'
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
