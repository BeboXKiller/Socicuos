<?php
require_once('../vendor/autoload.php');

use App\Authenticate;
$authObj = new Authenticate();
$authObj->redirectIfNotAuth();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile - Socicuos</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="/Socicuos/assets/css/fonts.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php require($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/Pages/Layout/Navbar.php'); ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-16">
        <div class="bg-white shadow rounded-lg">
            <!-- Profile Header -->
            <div class="p-6 border-b">
                <div class="flex items-center space-x-4">                  
                    <div class="w-20 h-20 rounded-full bg-blue-600 flex items-center justify-center">
                        <span class="text-white text-4xl noto-serif-dives-akuru-regular flex mt-3 items-center justify-center">
                            <?php echo strtoupper(substr($_SESSION['userName'], 0, 1)); ?>
                        </span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900"><?php echo ucfirst(htmlspecialchars($_SESSION['userName'])); ?></h1>
                        <p class="text-gray-500">Member since <?php echo date('F Y'); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Profile Content -->
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h2>
                <!-- Add more profile content here -->
            </div>
        </div>
    </div>

    <?php require($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/Pages/Layout/Footer.php'); ?>
</body>
</html>