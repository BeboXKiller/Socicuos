<?php

    require_once('../vendor/autoload.php');
        use App\Authenticate;
        $authObj = new Authenticate();
        $authObj->redirectIfNotAuth(); 
        $authObj->logOut();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Socicuos</title>      
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js" 
    integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" 
    crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"></script>
    <script src="https://kit.fontawesome.com/c68c5c4d75.js" crossorigin="anonymous"></script>
    
</head>



<body class="bg-gray-50">
   
    <?php require($_SERVER[ 'DOCUMENT_ROOT' ] . '/Socicuos/Pages/Layout/Navbar.php'); ?>

    <!-- Hero Section -->
    <section class="bg-blue-600 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">Welcome to Socicuos</h1>
                <p class="text-xl text-blue-100">Connect with friends and share your moments</p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Features</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold mb-2">Connect</h3>
                    <p class="text-gray-600">Connect with friends and family from around the world</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold mb-2">Share</h3>
                    <p class="text-gray-600">Share your favorite moments and experiences</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold mb-2">Engage</h3>
                    <p class="text-gray-600">Engage with your community through likes and comments</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
     <?php require($_SERVER[ 'DOCUMENT_ROOT' ] . '/Socicuos/Pages/Layout/Footer.php'); ?>
</body>
</html>