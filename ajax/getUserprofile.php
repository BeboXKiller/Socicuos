<?php
require_once('../vendor/autoload.php');

header('Content-Type: application/json');


    
$current_user = $_SESSION['userName'] ?? null;
$requested_username = $_GET['username'] ?? $current_user;

// Validate username
if (empty($requested_username) || !preg_match('/^[a-zA-Z0-9_]+$/', $requested_username)) {
    echo json_encode(['error' => 'Invalid username']);
    exit;
}

use App\UserInfoUtility;
$userInfoUtility = new UserInfoUtility();

try {
    $userInfo = $userInfoUtility->getUserInfo($requested_username);
    
    if (!$userInfo) {
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    // Return user data as JSON
    echo json_encode([
        'username' => $userInfo['username'],
        'email' => $userInfo['email'],
        'profilePictureUrl' => $userInfo['profile_picture'],
        'bio' => $userInfo['bio']
    ]);
    
} catch (\Exception $e) {
    error_log("Error in UserProfile: " . $e->getMessage());
    echo json_encode(['error' => 'Server error']);
}