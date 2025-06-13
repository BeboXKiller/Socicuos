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
    <script src="https://kit.fontawesome.com/c68c5c4d75.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        // Debug: Check jQuery before loading plugin
        console.log('jQuery loaded:', typeof jQuery !== 'undefined');
        console.log('jQuery version:', jQuery.fn.jquery);
    </script>
    <script src="../assets/js/usernamevalidator.js"></script>
    <script>
        // Debug: Check if plugin is available
        console.log('Plugin available:', typeof $.fn.usernameValidator !== 'undefined');
    </script>
    <script src="../assets/js/alert.js"></script>
    <script src="../assets/js/passwordValidator.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new UsernameValidator('username');
            new PasswordValidator('password');
        });;
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <?php require($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/Pages/Layout/Navbar.php'); ?>

    <div class="flex flex-col lg:flex-row min-h-screen">
        <!-- Left Side - Form -->
        <div class="w-full lg:w-1/2 p-4 lg:p-8 flex items-center justify-center">
            <form method="post" class="bg-white p-6 lg:p-8 mt-15 lg:mt-20 rounded-lg shadow-lg w-full max-w-md">
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
        <div class=" lg:block lg:w-2/3 lg:h-screen bg-zinc-900 flex flex-col">
            <!-- Image Section -->
            <div class="flex-1 p-0.5 py-0">
                    <div class="lg:h-2/3 bg-gray-100 overflow-hidden">
                        <img 
                            src="../assets/img/pic4.AVIF" 
                            alt="Connect with friends" 
                            class="w-full h-full object-cover"
                        >
                    </div>
                </div>
                
                <!-- Footer Section -->
                <div class="lg:h-0.5 flex ">
                    <?php require($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/Pages/Layout/Footer.php'); ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


