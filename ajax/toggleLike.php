<?php
require_once('../vendor/autoload.php');
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../ajax_errors.log');
try {
    $postsObj = new \App\Posts();
    
    // Check if user is logged in
    session_start();
    if (!isset($_SESSION['userId'])) {
        throw new Exception('You must be logged in to like posts');
    }
    
    // Check if post ID was provided
    if (!isset($_POST['postId']) || !is_numeric($_POST['postId'])) {
        throw new Exception('Invalid post ID');
    }
    
    $postId = (int)$_POST['postId'];
    $userId = (int)$_SESSION['userId'];
    
    // Toggle like status
    $action = $postsObj->toggleLike($postId, $userId);
    
    echo json_encode([
        'status' => 'success',
        'action' => $action
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
ob_end_clean();
echo json_encode([
    'status' => 'success',
    'message' => 'Post created successfully',
    'postId' => $result['postId'] // Fixed key
]);
