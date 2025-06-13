<?php
ob_start();
require_once('../vendor/autoload.php');

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['userID'])) {
        throw new Exception('You must be logged in to create posts');
    }
    
    // Validate inputs
    if (!isset($_POST['title']) || empty(trim($_POST['title']))) {
        throw new Exception('Post title cannot be empty');
    }
    
    $content = trim($_POST['content'] ?? '');
    $imageProvided = !empty($_FILES['image']['name']);
    
    if (empty($content) && !$imageProvided) {
        throw new Exception('Post content or image is required');
    }
    
    $postsObj = new \App\Posts();
    $title = trim($_POST['title']);
    
    // Handle image upload if present
    $imagePath = null;
    if ($imageProvided && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = new \App\FileUpload($_FILES['image']);
        $upload->setAllowedTypes(['image/jpeg', 'image/png', 'image/gif']);
        $upload->setMaxSize(5 * 1024 * 1024); // 5MB
        $relativePath = $upload->save('assets/img/posts/');
        $imagePath = '/Socicuos/' . $relativePath; // Full path
    }
    
    // Create the post
    $result = $postsObj->createPost($_SESSION['userID'], $title, $content, $imagePath); 
    var_dump($result); // Debugging output
    $unexpectedOutput = ob_get_contents();
    if (!empty($unexpectedOutput)) {
        error_log("Unexpected output detected: " . $unexpectedOutput);
        ob_end_clean();
    } else {
        ob_end_clean();
    }

echo json_encode([
    'status' => 'success',
    'message' => 'Post created successfully',
    'postId' => $result['postId'],
    'postHtml' => $result['postHtml'],
    'unexpectedOutput' => $unexpectedOutput ? $unexpectedOutput : null

]);
    
} catch (Exception $e) {
    // Log detailed error
    error_log("Create Post Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}