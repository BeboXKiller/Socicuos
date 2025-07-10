<?php
session_start();
require_once('../vendor/autoload.php');

use App\Posts;
use App\UserInfoUtility;

header('Content-Type: application/json');

try {
    // Check if user is logged in
    if (!isset($_SESSION['userID'])) {
        throw new Exception('You must be logged in to view posts');
    }
    
    // Get parameters
    $userId = isset($_POST['userId']) ? (int)$_POST['userId'] : null;
    $userTo = isset($_POST['userTo']) ? $_POST['userTo'] : null;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $offset = isset($_POST['offset']) ? (int)$_POST['offset'] : 0;
    
    if (!$userId) {
        throw new Exception('User ID is required');
    }
    
    // If userTo is a username, get the user ID
    $userToId = null;
    if ($userTo && $userTo !== 'None') {
        $userInfoUtility = new UserInfoUtility();
        $userInfo = $userInfoUtility->getUserInfo($userTo);
        $userToId = $userInfo['id'] ?? null;
    }
    
    $postsObj = new Posts();
    
    // Get posts for the user's wall
    $posts = $postsObj->getUserWall($userId, $userToId, $limit, $offset);
    
    // Convert posts to HTML
    $postsHtml = '';
    $userInfoUtility = new UserInfoUtility();
    
    foreach ($posts as $post) {
        // Skip post if either the author or target user has a closed account
        if ($userInfoUtility->isClosed($post['added_by']) || $userInfoUtility->isClosed($post['user_to'])) {
            continue; // Skip this post and continue searching
        }
        
        $isLiked = $postsObj->isLikedByUser($post['id'], $_SESSION['userID']);
        $likeCount = $postsObj->getLikeCount($post['id']);
        
        // Get the target user's username if this post was posted to someone
        $targetUsername = 'None';
        if ($post['user_to'] != $post['added_by']) {
            $targetUserInfo = $userInfoUtility->getUserInfoById($post['user_to']);
            $targetUsername = $targetUserInfo['username'] ?? 'Unknown';
        }
        
        $postsHtml .= \App\PostsUtility::UserPost(
            $post['id'],
            $post['username'],
            $targetUsername,
            $post['title'],
            $post['content'],
            $post['profile_pic'] ?? null,
            $post['created_at'] ?? null,
            $isLiked,
            $likeCount,
            $post['image'] ?? null
        );
    }
    
    echo json_encode([
        'status' => 'success',
        'html' => $postsHtml,
        'hasMore' => count($posts) >= $limit,
        'nextOffset' => $offset + $limit
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 