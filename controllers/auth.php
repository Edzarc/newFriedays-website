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
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, phone, address, role) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $passwordHash, $phone, $address, 'customer'])) {
                $userId = $pdo->lastInsertId();
                addUserAddress($userId, 'Home', $address);

                $_SESSION['user_id'] = $userId;
                $_SESSION['role'] = 'customer';
                header('Location: index.php?page=menu');
                exit();
            } else {
                $errors[] = "Registration failed. Please try again.";
            }
        }

        // Show form with errors
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
?>