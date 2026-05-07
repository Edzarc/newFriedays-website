<?php
require_once 'includes/functions.php';

function showProfile() {
    requireLogin();

    $errors = [];
    $success = null;
    $userId = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $address = sanitizeInput($_POST['address'] ?? '');

        if (empty($name) || empty($email) || empty($phone) || empty($address)) {
            $errors[] = 'All fields are required.';
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
    }

    $user = getUserById($userId);
    $orders = getUserOrders($userId);
    $loyaltyTier = getLoyaltyTierByName($user['loyalty_tier']);
    $loyaltyTiers = getLoyaltyTiers();

    include 'views/profile.php';
}
?>