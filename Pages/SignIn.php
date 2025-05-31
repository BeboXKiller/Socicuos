<?php

    require_once('../vendor/autoload.php');
        use App\Authenticate;
            $authObj = new Authenticate();
            $authObj->signIn();
            $authObj->redirectIfAuth();
        use App\Alert;
            $alertObj = new Alert();
            $alertObj->alertAfterSignUp();
            

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Socicuos - Sign In</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <?php require($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/Pages/Layout/Navbar.php'); ?>

    <div class="flex min-h-screen">
        <!-- Left Side - Form -->
        <div class="w-1/2 p-8 flex items-center justify-center">
            <form method="post" class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
                <div class="space-y-6">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-900">Sign In</p>
                        <p class="text-sm text-gray-600 mt-2">Welcome back</p>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="space-y-4">
                            <p class="text-sm text-gray-600 text-center">Sign in to your account</p> 
                            
                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                > 
                            </div>
                            
                            <div class="space-y-2">
                                <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    value="<?php echo isset($_SESSION['passowrd']) ? htmlspecialchars($_SESSION['password']) : ''; ?>"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                            </div>
                            
                            <button 
                                type="submit" 
                                name="signInBtn"
                                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                            >
                                Sign In
                            </button>

                            <p class="text-sm text-gray-600 text-center">
                                Don't have an account?
                                <a href="SignUp.php" class="text-blue-600 hover:text-blue-700 font-medium"> Sign Up</a>  
                            </p>
                        </div>
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
                        src="../assets/img/pic4.AVIF" 
                        alt="Connect with friends" 
                        class="w-full h-full object-cover"
                    >
                </div>
            </div>
            
            <!-- Footer Section -->
            <div class="h-2/6 flex">
            <?php require($_SERVER[ 'DOCUMENT_ROOT' ] . '/Socicuos/Pages/Layout/Footer.php'); ?>
            </div>
        </div>
        </div>
    </div>        
</body>
</html>