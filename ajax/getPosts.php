<?php

require_once('../vendor/autoload.php');

use App\Posts;
// Initialize variables
$userId = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;
$limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 5; // Default limit
$offset = isset($_POST['offset']) ? (int)$_POST['offset'] : 0; // Default offset
try {

    // Check if user is logged in
    if (!isset($_SESSION['userID'])) {
        throw new Exception('You must be logged in to view posts');
    }
    
    $postsObj = new Posts();
    // Get posts
    $posts = $postsObj-> getNewsFeed($userId, $limit, $offset);

    // Return posts as JSON
    echo json_encode([
        'status' => 'success',
        'posts' => $posts
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}