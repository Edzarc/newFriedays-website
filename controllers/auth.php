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
                    $_SESSION['register_success_message'] = 'Your account was successfully created. Please check your email for verification instructions.';
                    header('Location: index.php?page=register');
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
        $errors = [];
        $verificationMessage = '';
        if (isset($_SESSION['register_success_message'])) {
            $verificationMessage = $_SESSION['register_success_message'];
            unset($_SESSION['register_success_message']);
        }
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

function forgotPassword() {
    $errors = [];
    $message = '';
    $showOtpForm = false;
    $email = '';
    $resetToken = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? 'send_otp';

        if ($action === 'send_otp') {
            $email = sanitizeInput($_POST['email'] ?? '');

            if (empty($email)) {
                $errors[] = 'Please enter your email address.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please enter a valid email address.';
            } else {
                $user = getUserByEmail($email);

                if ($user) {
                    $token = createPasswordResetToken($user['id']);
                    if (sendPasswordResetOtp($email, $user['name'], $token['otp'])) {
                        $message = 'An OTP has been sent. Please check your inbox.';
                        $showOtpForm = true;
                        $resetToken = $token['token'];
                    } else {
                        $errors[] = 'Unable to send the password reset code. Please try again later.';
                    }
                } else {
                    $message = 'An OTP has been sent. Please check your inbox.';
                }
            }
        } elseif ($action === 'reset_password') {
            $resetToken = $_POST['reset_token'] ?? '';
            $otp = sanitizeInput($_POST['otp'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($resetToken)) {
                $errors[] = 'Password reset token is missing.';
            }
            if (empty($otp)) {
                $errors[] = 'Please enter the OTP sent to your email.';
            }
            if (empty($password)) {
                $errors[] = 'Please choose a new password.';
            }
            if (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters long.';
            }
            if ($password !== $confirmPassword) {
                $errors[] = 'Passwords do not match.';
            }

            if (empty($errors)) {
                $record = getPasswordResetRecord($resetToken);
                if (!$record) {
                    $errors[] = 'Invalid or expired reset request.';
                } elseif (!empty($record['used_at'])) {
                    $errors[] = 'This password reset request has already been used.';
                } elseif (strtotime($record['expires_at']) < time()) {
                    $errors[] = 'This password reset code has expired. Please request a new one.';
                } elseif ($record['otp'] !== $otp) {
                    $errors[] = 'The OTP code is incorrect. Please try again.';
                    $showOtpForm = true;
                    $resetToken = $resetToken;
                } else {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    if (updateUserPassword($record['user_id'], $passwordHash) && markPasswordResetTokenUsed($record['id'])) {
                        $message = 'Your password has been updated successfully. You may now log in.';
                    } else {
                        $errors[] = 'Unable to update your password at this time. Please try again later.';
                        $showOtpForm = true;
                        $resetToken = $resetToken;
                    }
                }
            } else {
                $showOtpForm = true;
            }
        }
    }

    include 'views/forgot_password.php';
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
    $isVerified = false;

    if (empty($email)) {
        $error = 'No pending verification email found. Please register or log in again to continue.';
    } else {
        $user = getUserByEmail($email);
        if (!$user) {
            $error = 'No account found with this email address.';
        } elseif (!empty($user['email_verified'])) {
            $isVerified = true;
            $message = 'Your email has already been verified. You can now <a href="index.php?page=login">log in</a>.';
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error) && !$isVerified) {
        $user = getUserByEmail($email);
        if (!$user) {
            $error = 'No account found with this email address.';
        } elseif (!empty($user['email_verified'])) {
            $isVerified = true;
            $message = 'Your email has already been verified. You can now <a href="index.php?page=login">log in</a>.';
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