<?php
require_once('../vendor/autoload.php');

    use App\Authenticate;
    use App\UserInfoUtility;

        $authObj = new Authenticate();
        $authObj->redirectIfNotAuth();

            $current_user = $_SESSION['userName'] ?? null;
            $requested_username = $_GET['username'] ?? $current_user;

            // Validate requested username
            if (empty($requested_username) || !preg_match('/^[a-zA-Z0-9_]+$/', $requested_username)) {
                header('Location: /Socicuos/Pages/Error.php?error=InvalidUsername');
                exit();
            }

            $userInfoUtility = new UserInfoUtility();
            $is_own_profile = ($current_user === $requested_username);

            // Fetch profile data (for both own and others' profiles)
            $profile_data = $userInfoUtility->getUserInfo($requested_username);

            if (!$profile_data) {
                header('Location: /Socicuos/Pages/Error.php?error=UserNotFound');
                exit();
            }

            // Use profile data instead of session data
            $bio = $profile_data['bio'] ?? '';
            $join_date = $profile_data['join_date'] ?? date('Y-m-d');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile - Socicuos</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="/Socicuos/assets/css/fonts.css">
    <script 
    src="https://code.jquery.com/jquery-3.7.1.slim.js" 
    integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" 
    crossorigin="anonymous"></script>
     <script src="/Socicuos/assets/js/fontawesome.js"></script>
    <script src="/Socicuos/assets/js/solid.js"></script>
    <script src="/Socicuos/assets/js/brands.js"></script>
    <script src="/Socicuos/assets/js/getUserProfile.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    
    <?php require($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/Pages/Layout/Navbar.php'); ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-16">
        <div class="bg-white shadow rounded-lg">
            <!-- Profile Header -->
            <div class="p-4 sm:p-6 border-b">
                <?php if ($is_own_profile): ?>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Your Profile</h1>
                <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-4">                  
                    <?php echo UserInfoUtility::userIcon($_SESSION['userName'], 'w-20 h-20 sm:text-4xl ', 'text-base sm:text-4xl sm:mt-4'); ?>
                    <div class="flex-1 text-center sm:text-left">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <span class="text-gray-500">
                                    <i class="fas fa-user"></i> 
                                    <?php echo htmlspecialchars($_SESSION['userName']); ?>
                                </span>
                                <p class="text-gray-500">Member since <?php echo date('F Y'); ?></p>
                            </div>
                            <div class="mt-4 sm:mt-0">
                                <a href="EditProfile.php" class="text-blue-600 hover:text-blue-800 font-semibold">Edit Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Profile Content -->
                    <div class="p-4 sm:p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">About</h2>
                        <div class="space-y-2">
                            <span class="text-zinc-600">
                                <?php echo htmlspecialchars($_SESSION['bio']) ?>
                            </span>
                        </div>
                    </div>
                <!-- Posts Section -->
                <div class="p-4 sm:p-6 border-t">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Posts</h2>
                    <div id="posts-container" class="space-y-4">
                        <!-- Posts will be dynamically loaded here -->
                    </div>
                <?php else: ?>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($requested_username); ?>'s Profile</h1>
                    <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-4">
                        <?php echo UserInfoUtility::userIcon($requested_username, 'w-20 h-20 text-3xl'); ?>
                        <div class="flex-1 text-center sm:text-left">
                            <span class="text-gray-500">
                                <i class="fas fa-user"></i>
                                <?php echo htmlspecialchars($requested_username); ?>
                            </span>
                            <p class="text-gray-500">Member since <?php echo date('F Y'); ?></p>
                        </div>

                    </div>
                    <!-- Profile Content -->
                    <div class="p-4 sm:p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">About</h2>
                    <div class="space-y-2">
                        <span id="profileBio"class="text-zinc-600">
                            <?php echo htmlspecialchars($bio); ?>
                        </span>
                    </div>
                </div>
                <!-- Posts Section -->
                <div class="p-4 sm:p-6 border-t">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Posts</h2>
                    <div id="posts-container" class="space-y-4">
                        <!-- Posts will be dynamically loaded here -->
                    </div>
                <?php endif; ?>
            </div>
            
            
        </div> 
    </div>
   

    <?php require($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/Pages/Layout/Footer.php'); ?>
</body>
</html>