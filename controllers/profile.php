<?php
require_once 'includes/functions.php';

function showProfile() {
    requireLogin();

    $errors = [];
    $success = null;
    $userId = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? 'save_profile';

        if ($action === 'save_profile') {
            $name = sanitizeInput($_POST['name'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $phone = sanitizeInput($_POST['phone'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');

            if (empty($name) || empty($email) || empty($phone) || empty($address)) {
                $errors[] = 'All profile fields are required.';
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please enter a valid email address.';
            }

            $existingUser = getUserByEmail($email);
            if ($existingUser && $existingUser['id'] != $userId) {
                $errors[] = 'That email address is already registered with another account.';
            }

            if (empty($errors)) {
                if (updateUserProfile($userId, $name, $email, $phone, $address)) {
                    $success = 'Your profile has been updated successfully.';
                } else {
                    $errors[] = 'Unable to save your profile. Please try again later.';
                }
            }
        } elseif ($action === 'add_address') {
            $label = sanitizeInput($_POST['address_label'] ?? '');
            $addressText = sanitizeInput($_POST['address_text'] ?? '');

            if (empty($label) || empty($addressText)) {
                $errors[] = 'Both address label and delivery address are required.';
            }

            if (empty($errors)) {
                if (addUserAddress($userId, $label, $addressText)) {
                    $success = 'Delivery address saved successfully.';
                } else {
                    $errors[] = 'Unable to save the delivery address. Please try again.';
                }
            }
        } elseif ($action === 'delete_address') {
            $addressId = intval($_POST['address_id'] ?? 0);
            $address = getUserAddressById($addressId);
            if (!$address || $address['user_id'] != $userId) {
                $errors[] = 'Address not found or access denied.';
            } else {
                if (deleteUserAddress($addressId)) {
                    $success = 'Delivery address removed successfully.';
                } else {
                    $errors[] = 'Unable to delete the address. Please try again.';
                }
            }
        } elseif ($action === 'request_change_password_otp') {
            $user = getUserById($userId);
            $token = createPasswordResetToken($userId);
            if (sendPasswordResetOtp($user['email'], $user['name'], $token['otp'])) {
                $_SESSION['change_password_token'] = $token['token'];
                $success = 'A verification code has been sent to your email. Please enter it below to change your password.';
            } else {
                $errors[] = 'Unable to send verification code. Please try again later.';
            }
        } elseif ($action === 'confirm_change_password') {
            $otp = sanitizeInput($_POST['otp'] ?? '');
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $token = $_SESSION['change_password_token'] ?? '';

            if (empty($token)) {
                $errors[] = 'Password change session expired. Please request a new verification code.';
            } elseif (empty($otp)) {
                $errors[] = 'Please enter the verification code.';
            } elseif (empty($newPassword) || empty($confirmPassword)) {
                $errors[] = 'Please enter and confirm your new password.';
            } elseif (strlen($newPassword) < 6) {
                $errors[] = 'New password must be at least 6 characters long.';
            } elseif ($newPassword !== $confirmPassword) {
                $errors[] = 'New passwords do not match.';
            } else {
                $record = getPasswordResetRecord($token);
                if (!$record) {
                    $errors[] = 'Invalid verification code.';
                } elseif (!empty($record['used_at'])) {
                    $errors[] = 'This verification code has already been used.';
                } elseif (strtotime($record['expires_at']) < time()) {
                    $errors[] = 'This verification code has expired. Please request a new one.';
                } elseif ($record['otp'] !== $otp) {
                    $errors[] = 'The verification code is incorrect.';
                } else {
                    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                    if (updateUserPassword($userId, $passwordHash) && markPasswordResetTokenUsed($record['id'])) {
                        unset($_SESSION['change_password_token']);
                        $success = 'Your password has been changed successfully.';
                    } else {
                        $errors[] = 'Unable to change your password. Please try again later.';
                    }
                }
            }
        }
    }

    $user = getUserById($userId);
    $orders = getUserOrders($userId);
    $loyaltyTier = getLoyaltyTierByName($user['loyalty_tier']);
    $loyaltyTiers = getLoyaltyTiers();
    $addresses = getUserAddresses($userId);

    include 'views/profile.php';
}
?>