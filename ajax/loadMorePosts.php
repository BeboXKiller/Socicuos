<?php

require_once('../vendor/autoload.php');
use App\PostsUtility;
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['userID'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['userID'];
$postManager = new PostsUtility();

// Get parameters from request
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

try {
    $result = $postManager->getNewsFeed($userId, $limit, $offset);
    echo json_encode([
        'html' => $result['html'],
        'hasMore' => $result['hasMore'],
        'nextOffset' => $result['nextOffset']
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

