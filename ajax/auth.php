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

        case 'send_otp':
            $email = trim($_POST['email'] ?? '');

            if (empty($email)) {
                echo json_encode(['status' => 'error', 'message' => 'Please enter your college email ID.']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE LOWER(`email`) = LOWER(:email)");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if (!$user) {
                echo json_encode(['status' => 'error', 'message' => 'No registered account found with that email address.']);
                exit;
            }

            $otp = sprintf("%06d", rand(100000, 999999));
            $_SESSION['forgot_otp'] = [
                'email' => strtolower($email),
                'otp' => $otp,
                'expires' => time() + 300 // 5 minutes validity
            ];

            // Dispatch OTP email to user's registered email
            $subject = "AIMSA Portal - Password Reset OTP Code";
            $message = "Hello {$user->name},\n\nYour 6-digit OTP code to reset your AIMSA Portal password is: {$otp}\n\nThis code is valid for 5 minutes.\nIf you did not request a password reset, please ignore this email.\n\nDepartment of AIML\nZeal College of Engineering and Research, Pune";
            $headers = "From: AIMSA Helpdesk <aimsa.helpdesk@zealeducation.com>\r\n" .
                       "Reply-To: support.aimsa@zealeducation.com\r\n" .
                       "X-Mailer: PHP/" . phpversion() . "\r\n" .
                       "Content-Type: text/plain; charset=UTF-8";

            @mail($user->email, $subject, $message, $headers);

            // Log email dispatch in notifications table
            try {
                $notifStmt = $pdo->prepare("INSERT INTO `notifications` (`title`, `text`, `indicator`, `recipient`, `email_sent`) VALUES (?, ?, 'green', ?, 1)");
                $notifStmt->execute(['Password Reset OTP', "A 6-digit verification code was sent to {$user->email}.", $user->email]);
            } catch (Exception $e) {
                // Ignore database logging exceptions if schema differs
            }

            // Create masked email string for user feedback (e.g. ad***h@zealeducation.com)
            $emailParts = explode('@', $user->email);
            $uname = $emailParts[0];
            $domain = $emailParts[1] ?? 'zealeducation.com';
            $maskedName = strlen($uname) > 2 ? substr($uname, 0, 2) . '***' . substr($uname, -1) : $uname . '***';
            $maskedEmail = $maskedName . '@' . $domain;

            echo json_encode([
                'status' => 'success',
                'message' => "OTP sent successfully to {$maskedEmail}",
                'masked_email' => $maskedEmail
            ]);
            break;

        case 'verify_otp':
            $email = trim($_POST['email'] ?? '');
            $otp = trim($_POST['otp'] ?? '');

            if (empty($email) || empty($otp)) {
                echo json_encode(['status' => 'error', 'message' => 'Please enter the 6-digit OTP sent to your email.']);
                exit;
            }

            if (!isset($_SESSION['forgot_otp']) || strtolower($_SESSION['forgot_otp']['email']) !== strtolower($email)) {
                echo json_encode(['status' => 'error', 'message' => 'No OTP request found for this email. Please request a new OTP.']);
                exit;
            }

            if (time() > $_SESSION['forgot_otp']['expires']) {
                echo json_encode(['status' => 'error', 'message' => 'OTP has expired (valid for 5 minutes). Please request a new OTP.']);
                exit;
            }

            if ($_SESSION['forgot_otp']['otp'] !== $otp) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid OTP code. Please enter the correct 6-digit OTP.']);
                exit;
            }

            // Mark OTP verified for this session
            $_SESSION['forgot_otp_verified'] = [
                'email' => strtolower($email),
                'verified_at' => time()
            ];

            echo json_encode([
                'status' => 'success',
                'message' => 'OTP verified successfully! You can now set your new password.'
            ]);
            break;

        case 'forgot_password':
            $email = trim($_POST['email'] ?? '');
            $newPassword = $_POST['new_password'] ?? '';

            if (empty($email) || empty($newPassword)) {
                echo json_encode(['status' => 'error', 'message' => 'Please fill out all required fields.']);
                exit;
            }

            // Require OTP verification before allowing password reset
            if (!isset($_SESSION['forgot_otp_verified']) || strtolower($_SESSION['forgot_otp_verified']['email']) !== strtolower($email)) {
                echo json_encode(['status' => 'error', 'message' => 'OTP verification required before resetting password.']);
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

            // Clear OTP session variables
            unset($_SESSION['forgot_otp']);
            unset($_SESSION['forgot_otp_verified']);

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
