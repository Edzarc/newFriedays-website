<?php
require_once 'includes/functions.php';

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'register':
        require_once 'controllers/auth.php';
        register();
        break;
    case 'login':
        require_once 'controllers/auth.php';
        login();
        break;
    case 'logout':
        require_once 'controllers/auth.php';
        logout();
        break;
    case 'verify_email':
        require_once 'controllers/auth.php';
        verifyEmail();
        break;
    case 'resend_verification':
        require_once 'controllers/auth.php';
        resendVerification();
        break;
    case 'forgot_password':
        require_once 'controllers/auth.php';
        forgotPassword();
        break;
    case 'menu':
        require_once 'controllers/menu.php';
        showMenu();
        break;
    case 'checkout':
        require_once 'controllers/checkout.php';
        showCheckout();
        break;
    case 'queue':
        require_once 'controllers/queue.php';
        showQueue();
        break;
    case 'profile':
        require_once 'controllers/profile.php';
        showProfile();
        break;
    case 'admin':
        require_once 'admin/controllers/dashboard.php';
        showAdminDashboard();
        break;
    case 'staff':
        require_once 'staff/controllers/dashboard.php';
        showStaffDashboard();
        break;
    case 'admin_users':
        require_once 'admin/controllers/users.php';
        showUsers();
        break;
    case 'admin_orders':
        require_once 'admin/controllers/orders.php';
        showOrders();
        break;
    case 'admin_analytics':
        require_once 'admin/controllers/analytics.php';
        showAnalytics();
        break;
    case 'admin_products':
        require_once 'admin/controllers/products.php';
        showProducts();
        break;
    case 'payment_success':
        require_once 'controllers/payment.php';
        handlePaymentSuccess();
        break;
    case 'payment_failed':
        require_once 'controllers/payment.php';
        handlePaymentFailed();
        break;
    default:
        showHome();
        break;
}

function showHome() {
    include 'views/home.php';
}
?>