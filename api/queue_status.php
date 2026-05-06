<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$currentServing = getCurrentServing();
$userQueue = getUserQueuePosition($_SESSION['user_id']);

echo json_encode([
    'current_serving' => $currentServing,
    'user_queue' => $userQueue
]);
?>