<?php
require_once 'includes/functions.php';

function register() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = sanitizeInput($_POST['name']);
        $email = sanitizeInput($_POST['email']);
        $phone = sanitizeInput($_POST['phone']);
        $address = sanitizeInput($_POST['address']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        $errors = [];
        $verificationMessage = '';

        // Validation checks
        if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($password)) {
            $errors[] = "All fields are required.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        if (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters long.";
        }

        if ($password !== $confirmPassword) {
            $errors[] = "Passwords do not match.";
        }

        if (getUserByEmail($email)) {
            $errors[] = "Email already registered.";
        }

        if (empty($errors)) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            global $pdo;
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, phone, address, role, email_verified) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $passwordHash, $phone, $address, 'customer', 0])) {
                $userId = $pdo->lastInsertId();
                addUserAddress($userId, 'Home', $address);

                $token = createEmailVerificationToken($userId);
                if (sendVerificationEmail($email, $name, $token)) {
                    updateLastVerificationEmailSent($userId);
                    $_SESSION['pending_verification_email'] = $email;
                    header('Location: index.php?page=resend_verification');
                    exit();
                } else {
                    $errors[] = 'Registration succeeded but verification email could not be sent. Please contact support.';
                }
            } else {
                $errors[] = "Registration failed. Please try again.";
            }
        }

        include 'views/register.php';
    } else {
        include 'views/register.php';
    }
}

function login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];

        $user = getUserByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            if (empty($user['email_verified'])) {
                $_SESSION['pending_verification_email'] = $email;
                header('Location: index.php?page=resend_verification');
                exit();
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header('Location: index.php?page=menu&clear_cart=1');
            exit();
        } else {
            $error = "Invalid email or password.";
            include 'views/login.php';
        }
    } else {
        include 'views/login.php';
    }
}

function logout() {
    session_destroy();
    header('Location: index.php?clear_cart=1');
    exit();
}

function verifyEmail() {
    $token = $_GET['token'] ?? '';
    $verificationError = '';
    $verificationSuccess = false;

    if (empty($token)) {
        $verificationError = 'Verification token is required.';
    } else {
        $record = getEmailVerificationRecord($token);

        if (!$record) {
            $verificationError = 'Invalid or expired verification token.';
        } elseif (!empty($record['used_at'])) {
            $verificationError = 'This verification link has already been used.';
        } elseif (strtotime($record['expires_at']) < time()) {
            $verificationError = 'This verification link has expired.';
        } elseif (!empty($record['email_verified'])) {
            $verificationSuccess = true;
        } else {
            if (markEmailVerified($record['id'], $record['user_id'])) {
                $verificationSuccess = true;
            } else {
                $verificationError = 'Unable to verify your account at this time. Please try again later.';
            }
        }
    }

    include 'views/verify_email.php';
}

function resendVerification() {
    $email = $_SESSION['pending_verification_email'] ?? '';
    $message = '';
    $error = '';

    if (empty($email)) {
        $error = 'No pending verification email found. Please register or log in again to continue.';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
        $user = getUserByEmail($email);
        if (!$user) {
            $error = 'No account found with this email address.';
        } elseif (!empty($user['email_verified'])) {
            $message = 'This email address is already verified. You can now <a href="index.php?page=login">log in</a>.';
        } elseif (!canResendVerificationEmail($user['id'])) {
            $error = 'Please wait 30 seconds before requesting another verification email.';
        } else {
            $token = createEmailVerificationToken($user['id']);
            if (sendVerificationEmail($email, $user['name'], $token)) {
                updateLastVerificationEmailSent($user['id']);
                $message = 'Verification email sent successfully. Please check your inbox.';
            } else {
                $error = 'Failed to send verification email. Please try again later.';
            }
        }
    }

    include 'views/resend_verification.php';
}
?>