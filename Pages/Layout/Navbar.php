<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/vendor/autoload.php');
    use App\Authenticate;
    $authObj = new Authenticate();
   
    // Get current page name
    $currentPage = basename($_SERVER['PHP_SELF']);
?>
<head>
    <link rel="stylesheet" href="/Socicuos/assets/css/fonts.css">
</head>
<nav class="bg-white shadow-sm fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo/Brand -->
            <div class="flex items-center">                
                <a href="index.php" class="text-3xl font-bold text-blue-600">
                    Socicuos
                </a>
            </div>            <!-- Navigation Items -->
            <div class="flex-1 flex justify-center px-4">
                <?php if(!$authObj->isAuth()): ?>
                    <!-- Center space holder when not authenticated -->
                    <div class="flex-1"></div>
                    <!-- Non-authenticated user navigation moved to right -->
                    <div class="flex items-center space-x-4 ml-auto">
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
                    <!-- Search Bar -->
                    <div class="max-w-xl w-full">
                        <div class="relative">
                            <input 
                                type="text" 
                                placeholder="Search people or posts..." 
                                class="w-full my-2 bg-gray-50 border border-gray-300 rounded-full px-5 py-2.5 pr-12 
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                                       transition-colors"
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
            </div>

            <!-- User Menu -->
            <div class="flex items-center space-x-4">
                <!-- Profile Icon -->
                <a href="UserProfile.php" class="relative group">                            
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center transition-colors group-hover:bg-blue-700">
                        <span class="text-white text-lg items-center justify-center noto-serif-dives-akuru-regular mt-2 mx-auto">
                            <?php echo strtoupper(substr($_SESSION['userName'], 0, 1)); ?>
                        </span>
                        <span class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                    </div>
                </a>
                        <!-- Sign Out Button -->
                        <a 
                            href="?logout=true"
                            class="text-gray-600 hover:text-gray-900 bg-gray-100 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors"
                        >
                            Sign Out
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>