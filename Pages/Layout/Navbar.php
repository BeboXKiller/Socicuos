<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/vendor/autoload.php');
    use App\Authenticate;
    $authObj = new Authenticate();
   
    // Get current page name
    $currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="bg-white shadow-sm fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo/Brand -->
            <div class="flex items-center">
                <a href="index.php" class="text-2xl font-bold text-blue-600 hover:text-blue-700">
                    Socicuos
                </a>
            </div>

            <!-- Navigation Items -->
            <div class="flex items-center space-x-4">
                <?php if(!$authObj->isAuth()): ?>
                    <!-- Non-authenticated user navigation -->
                    <div class="flex items-center space-x-4">
                        <a 
                            href="SignIn.php" 
                            class="<?= $currentPage === 'SignIn.php' 
                                ? 'bg-blue-700 text-white' 
                                : 'bg-blue-600 hover:bg-blue-700 text-white' ?> px-4 py-2 rounded-md transition-colors"
                        >
                            Sign In
                        </a>
                        <a 
                            href="SignUp.php" 
                            class="<?= $currentPage === 'SignUp.php' 
                                ? 'bg-blue-700 text-white' 
                                : 'bg-blue-600 hover:bg-blue-700 text-white' ?> px-4 py-2 rounded-md transition-colors"
                        >
                            Sign Up
                        </a>
                    </div>
                <?php else: ?>
                    
                    <!-- Authenticated user navigation -->
                    <a 
                        
                        href="?logout=true"
                        class="text-gray-600 hover:text-gray-900 bg-gray-100 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors"
                    >
                        Sign Out
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>