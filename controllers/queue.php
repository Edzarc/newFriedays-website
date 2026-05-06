<?php
require_once 'includes/functions.php';

function showQueue() {
    requireLogin();

    $currentServing = getCurrentServing();
    $userQueue = getUserQueuePosition($_SESSION['user_id']);

    include 'views/queue.php';
}
?>