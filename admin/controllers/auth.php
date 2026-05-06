<?php
require_once '../../includes/functions.php';

function adminLogin() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];

        // Check admin credentials
        if ($email === 'admin@friedays.com' && password_verify($password, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')) {
            $_SESSION['admin'] = true;
            $_SESSION['user_id'] = 1; // Admin user ID
            header('Location: index.php?page=admin');
            exit();
        } else {
            $error = "Invalid admin credentials.";
            include '../views/login.php';
        }
    } else {
        include '../views/login.php';
    }
}
?>