<?php
require_once 'C:\xampp\htdocs\newFriedays-website\includes\functions.php';

function adminLogin() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];

        $user = getUserByEmail($email);

        if ($user && $user['role'] === 'admin' && password_verify($password, $user['password_hash'])) {
            $_SESSION['role'] = 'admin';
            $_SESSION['user_id'] = $user['id'];
            header('Location: index.php?page=admin');
            exit();
        } else {
            $error = "Invalid admin credentials.";
            include '../views/login.php';
        }
    } else {
        include 'views\login.php';
    }
}
?>