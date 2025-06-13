<?php
require_once('../vendor/autoload.php');

header('Content-Type: application/json');

try {
    if (!isset($_POST['username'])) {
        throw new Exception('Username is required');
    }

    $username = trim($_POST['username']);
    
    if (empty($username)) {
        throw new Exception('Username cannot be empty');
    }

    if (strlen($username) < 3) {
        throw new Exception('Username must be at least 3 characters');
    }

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        throw new Exception('Username can only contain letters, numbers, and underscores');
    }

    $auth = new App\Authenticate();
    $exists = $auth->usernameExists($username);
    
    echo json_encode([
        'exists' => $exists,
        'message' => $exists ? 'Username already taken' : 'Username available'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}