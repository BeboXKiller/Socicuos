<?php

    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/vendor/autoload.php');
    
    use App\Authenticate;
    $authObj = new Authenticate();
    $authObj->logOut();
   
    // Get current page name
    $currentPage = basename($_SERVER['PHP_SELF']);
?>
<head>
    <link rel="stylesheet" href="/Socicuos/assets/css/fonts.css">
    <link rel="stylesheet" href="/Socicuos/assets/css/tailwind.css">    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" 
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
    crossorigin="anonymous"></script>
    <!-- <script src="https://kit.fontawesome.com/c68c5c4d75.js" crossorigin="anonymous"></script> -->
    <script src="/Socicuos/assets/js/fontawesome.js"></script>
    <script src="/Socicuos/assets/js/solid.js"></script>
    <script src="/Socicuos/assets/js/brands.js"></script>
    <script src="/Socicuos/assets/js/search.js"></script>
</head>
<nav class="bg-white shadow-sm fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo/Brand -->
            <div class="flex pb-2 items-center">                
                <a href="index.php" class="text-2xl sm:text-3xl font-bold text-blue-600">
                    Socicuos
                </a>
            </div>            <!-- Navigation Items -->
            <div class="flex-1 flex justify-center px-2 sm:px-4">
                <?php if(!$authObj->isAuth()): ?>
                    <!-- Center space holder when not authenticated -->
                    <div class="flex-1"></div>
                    <!-- Non-authenticated user navigation moved to right -->
                    <div class="flex items-center space-x-2 sm:space-x-4 ml-auto">
                        <a 
                            href="SignIn.php" 
                            class="<?= $currentPage === 'SignIn.php' 
                                ? 'bg-blue-700 text-white' 
                                : 'bg-blue-600 hover:bg-blue-700 text-white' ?> px-3 sm:px-4 py-2 text-sm sm:text-base rounded-md transition-colors"
                        >
                            Sign In
                        </a>
                        <a 
                            href="SignUp.php" 
                            class="<?= $currentPage === 'SignUp.php' 
                                ? 'bg-blue-700 text-white' 
                                : 'bg-blue-600 hover:bg-blue-700 text-white' ?> px-3 sm:px-4 py-2 text-sm sm:text-base rounded-md transition-colors"
                        >
                            Sign Up
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Navigation Buttons for Authenticated Users -->
                    <div class="flex items-center justify-center space-x-4 sm:space-x-6">
                        <a href="index.php?section=feed" class="nav-link <?= (!isset($_GET['section']) || $_GET['section'] === 'feed') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600 flex items-center justify-center' ?>">
                            <i class="fas fa-newspaper flex items-center text-xl sm:text-2xl"></i>
                            <!-- <span class="hidden sm:inline-block ml-2 text-sm sm:text-base">News Feed</span> -->
                        </a>
                        <a href="index.php?section=notifications" class="nav-link <?= (isset($_GET['section']) && $_GET['section'] === 'notifications') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' ?>">
                            <div class="relative">
                                <i class="fas fa-bell text-xl sm:text-2xl"></i>
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">3</span>
                            </div>
                            <!-- <span class="hidden sm:inline-block ml-2 text-sm sm:text-base">Notifications</span> -->
                        </a>
                        <a href="index.php?section=friends" class="nav-link <?= (isset($_GET['section']) && $_GET['section'] === 'friends') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' ?>">
                            <div class="relative">
                                <i class="fas fa-user-friends text-xl sm:text-2xl"></i>
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">2</span>
                            </div>
                        </a>
                        <a href="index.php?section=messages" class="nav-link <?= (isset($_GET['section']) && $_GET['section'] === 'messages') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' ?>">
                            <div class="relative">
                                <i class="fas fa-envelope text-xl sm:text-2xl"></i>
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">2</span>
                            </div>
                            <!-- <span class="hidden sm:inline-block ml-2 text-sm sm:text-base">Messages</span> -->
                        </a>
                    </div>

                    <!-- Search Bar -->
                    <div class="max-w-xl w-full ml-4">
                        <div class="relative">
                            <input 
                                id="searchInput"
                                type="text" 
                                placeholder="Search people..." 
                                class="w-full my-2 bg-gray-50 border border-gray-300 rounded-full px-3 sm:px-5 py-2 sm:py-2.5 pr-10 sm:pr-12 
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                                       transition-colors text-sm sm:text-base"
                                autocomplete="off"
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 sm:pr-4 pointer-events-none">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            
                            <!-- Search Results -->
                            <div id="searchResults" class="hidden absolute w-full bg-white mt-1 rounded-md shadow-lg border border-gray-200 max-h-96 overflow-y-auto z-50"></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($authObj->isAuth()): ?>
            <!-- User Menu -->
            <div class="flex items-center space-x-2 sm:space-x-4">
                <!-- Profile Icon -->
                <a href="<?php echo $_SESSION['userName'] ?>" class="profile-link relative group ">                            
                    <?php echo \App\UserInfoUtility::userIcon($_SESSION['userName'], 'w-12 h-12 sm:text-xl'); ?>
                </a>
                <!-- Sign Out Button -->
                <a 
                    href="?logout=true"
                    class="text-gray-600 hover:text-gray-900 bg-gray-100 px-3 sm:px-4 py-2 text-sm sm:text-base rounded-md hover:bg-gray-200 transition-colors"
                >
                    Sign Out
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
.nav-link {
    display: flex;
    align-items: center;
    transition-property: color;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}
</style>