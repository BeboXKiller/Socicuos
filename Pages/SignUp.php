<?php
    require_once('../vendor/autoload.php');
    use App\Authenticate;
    $authObj = new Authenticate();
    $authObj->redirectIfAuth();
    $authObj->signUp(); 

    use App\FormUtility;


    
?>
    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Socicuos - Sign Up</title>      
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="../assets/js/usernameValidator.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new UsernameValidator('username');
        });
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <?php require($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/Pages/Layout/Navbar.php'); ?>

    <div class="flex min-h-screen">
        <!-- Left Side - Form -->
        <div class="w-1/2 p-8 flex items-center justify-center">
            <form method="post" class="bg-white p-8 mt-20 rounded-lg shadow-lg max-w-md w-full">
                <div class="space-y-6">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-900">Sign Up</p>
                        <p class="text-sm text-gray-600 mt-2">Create your account</p>
                    </div>
                    
                    <div class="space-y-4">
                        <?php 
                            echo FormUtility::textField('username', 'Username', 'text', true);
                            echo FormUtility::textField('email', 'Email', 'email', true);
                            echo FormUtility::textField('confirm_email', 'Confirm Email', 'email', true);
                            echo FormUtility::textField('password', 'Password', 'password', true);
                            echo FormUtility::textField('confirm_password', 'Confirm Password', 'password', true);
                        ?>

                        <button 
                            type="submit" 
                            name="signUpBtn"
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                        >
                            Sign Up
                        </button>

                        <p class="text-sm text-gray-600 text-center">
                            Already have an account?
                            <a href="SignIn.php" class="text-blue-600 hover:text-blue-700 font-medium"> Sign In</a>
                        </p>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right Side - Image and Footer -->
        <div class="w-5/6 bg-zinc-900 flex flex-col">
            <!-- Image Section -->
            <div class="flex-1 p-0.5 py-0">
                <div class="h-full bg-gray-100 overflow-hidden">
                    <img 
                        src="../assets/img/pic1.jpeg" 
                        alt="Connect with friends" 
                        class="w-full h-full object-cover"
                    >
                </div>
            </div>
            
            <!-- Footer Section -->
            <div class="h-1/6 flex">
            <?php require($_SERVER[ 'DOCUMENT_ROOT' ] . '/Socicuos/Pages/Layout/Footer.php'); ?>
            </div>
        </div>
        </div>
    </div>
</body>
</html>


