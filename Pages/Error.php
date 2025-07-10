<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header('Location: /Socicuos/Pages/SignIn.php');
    exit();
}

$error = $_GET['error'] ?? 'Unknown';
$errorMessages = [
    'UserNotFound' => 'The user you are looking for does not exist.',
    'AccountClosed' => 'This account has been closed and is no longer accessible.',
    'Unauthorized' => 'You are not authorized to access this page.',
    'Unknown' => 'An unknown error occurred.'
];

$message = $errorMessages[$error] ?? $errorMessages['Unknown'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Socicuos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/Socicuos/assets/css/output.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php require($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/Pages/Layout/Navbar.php');  ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6 mt-12">
            <div class="text-center">
                <div class="text-red-500 text-6xl mb-4">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Oops!</h1>
                <p class="text-gray-600 mb-6"><?php echo htmlspecialchars($message); ?></p>
                <a href="/Socicuos/index.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Go Home
                </a>
            </div>
        </div>
    </div>
    
    <?php include 'Layout/Footer.php'; ?>
    <script src="/Socicuos/assets/js/fontawesome.js"></script>
    <script src="/Socicuos/assets/js/solid.js"></script>
    <script src="/Socicuos/assets/js/brands.js"></script>
</body>
</html> 