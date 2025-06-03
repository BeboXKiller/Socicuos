<?php
require_once('../vendor/autoload.php');

if (isset($_POST['search'])) {
    $userObj = new App\User();
    $results = $userObj->searchUsers($_POST['search'], $_SESSION['userID'] ?? 0);
    
    header('Content-Type: application/json');
    echo json_encode($results);
    exit();
}
