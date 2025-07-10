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
            
            // Check if the user's account is closed
            if ($userInfoUtility->isClosed($profile_data['id'])) {
                header('Location: /Socicuos/Pages/Error.php?error=AccountClosed');
                exit();
            }

            // Use profile data instead of session data
            $bio = $profile_data['bio'] ?? '';
            $join_date = $profile_data['join_date'] ?? date('Y-m-d');
            
            // Get initial posts for the user's wall using getUserWall
            $postManager = new \App\Posts();
            $initialPosts = $postManager->getUserWall($profile_data['id'], null, 10, 0);
            
            // Convert posts to HTML for initial display
            $postsHtml = '';
            $userInfoUtility = new UserInfoUtility();
            
            foreach ($initialPosts as $post) {
                $isLiked = $postManager->isLikedByUser($post['id'], $_SESSION['userID']);
                $likeCount = $postManager->getLikeCount($post['id']);
                
                // Get the target user's username if this post was posted to someone
                $targetUsername = 'None';
                if ($post['user_to'] != $post['added_by']) {
                    $targetUserInfo = $userInfoUtility->getUserInfoById($post['user_to']);
                    $targetUsername = $targetUserInfo['username'] ?? 'Unknown';
                }
                
                $postsHtml .= \App\PostsUtility::UserPost(
                    $post['id'],
                    $post['username'],
                    $targetUsername,
                    $post['title'],
                    $post['content'],
                    $post['profile_pic'] ?? null,
                    $post['created_at'] ?? null,
                    $isLiked,
                    $likeCount,
                    $post['image'] ?? null
                );
            }

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
    <script src="/Socicuos/assets/js/createPosts.js"></script>
    <script src="/Socicuos/assets/js/userWall.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Hidden data container for JavaScript -->
    <div id="userWallData" 
         data-user-id="<?php echo htmlspecialchars($profile_data['id']); ?>" 
         data-user-to="<?php echo htmlspecialchars($requested_username); ?>"
         style="display: none;">
    </div>
    
    <?php require($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/Pages/Layout/Navbar.php'); ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-16">
        <div class="bg-white shadow rounded-lg">
            <!-- Profile Header -->
            <div class="p-4 sm:p-6 border-b">
                <?php if ($is_own_profile): ?>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Your Profile</h1>
                <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-4">                  
                    <?php echo UserInfoUtility::userIcon($_SESSION['userName'],
                     'w-20 h-20 sm:text-4xl ', 'text-base sm:text-4xl sm:mt-4'); ?>
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
                     <!-- Create Post -->
                        <div class="bg-white rounded-lg shadow p-4 mb-6">
                            <form id="createPostForm" class="space-y-4" enctype="multipart/form-data" method="POST" >
                                <input type="hidden" id="userTo" name="userTo" value="<?php echo htmlspecialchars($requested_username); ?>">
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-4">
                                        <?php echo \App\UserInfoUtility::userIcon($_SESSION['userName'], 'w-10 h-10 text-lg'); ?>
                                        <input 
                                            id="postTitle"
                                            type="text"
                                            name="title" 
                                            placeholder="Post title"
                                            class="flex-1 bg-gray-50 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500 text-gray-900 text-sm transition-colors hover:bg-gray-100"
                                        />
                                    </div>
                                    <!-- userTo is handled via the hidden input above -->
                                    <div class="pl-14">
                                        <textarea 
                                            id="postContent" 
                                            name="content" 
                                            placeholder="What's on your mind?"
                                            class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none placeholder-gray-500 text-gray-900 text-sm min-h-[70px] transition-colors hover:bg-gray-100"
                                            rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-2 border-t">
                                    <div class="flex items-center space-x-2">
                                        <label class="cursor-pointer text-gray-500 hover:text-blue-600 flex items-center space-x-2">
                                            <i class="fas fa-image"></i>
                                            <span>Add Photo</span>
                                            <input type="file" name="image" class="hidden" accept="image/*">
                                        </label>
                                        <div id="imagePreview" class="hidden ml-4 relative">
                                            <img src="" alt="Preview" class="h-20 w-20 object-cover rounded">
                                            <button type="button" class="remove-image absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 w-6 h-6 flex items-center justify-center">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button id="submit" type="submit" name="createPostBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        Post
                                    </button>
                                </div>
                            </form>
                        </div>

                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Posts</h2>
                    <div id="posts-container" class="space-y-4">
                        <!-- Posts will be dynamically loaded here -->
                        <?= $postsHtml ?>
                    </div>
                    
                    <!-- End of posts indicator -->
                    <div id="end-of-posts" class="text-center text-gray-500 py-4" style="display: none;">
                        <p>You've reached the end of the posts</p>
                    </div>
                    
                    <!-- Loading indicator -->
                    <div id="loading" class="loading" style="display: none;">
                        <p>Loading posts...</p>
                    </div>
                    
                    <!-- Scroll loading indicator -->
                    <div id="scroll-loading" class="scroll-loading" style="display: none;">
                        Loading more posts...
                    </div>
                <?php else: ?>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2" ><?php echo htmlspecialchars($requested_username); ?>'s Profile</h1>
                    <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-4 ">
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
                <div class="p-4 sm:p-6 border-t">
                     <!-- Create Post -->
                        <div class="bg-white rounded-lg shadow p-4 mb-6">
                            <form id="createPostForm" class="space-y-4" enctype="multipart/form-data" method="POST" >
                                <input type="hidden" id="userTo" name="userTo" value="<?php echo htmlspecialchars($requested_username); ?>">
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-4">
                                        <?php echo \App\UserInfoUtility::userIcon($_SESSION['userName'], 'w-10 h-10 text-lg'); ?>
                                        <input 
                                            id="postTitle"
                                            type="text"
                                            name="title" 
                                            placeholder="Post title"
                                            class="flex-1 bg-gray-50 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500 text-gray-900 text-sm transition-colors hover:bg-gray-100"
                                        />
                                    </div>
                                    <div class="pl-14">
                                        <textarea 
                                            id="postContent" 
                                            name="content" 
                                            placeholder="What's on your mind?"
                                            class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none placeholder-gray-500 text-gray-900 text-sm min-h-[70px] transition-colors hover:bg-gray-100"
                                            rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-2 border-t">
                                    <div class="flex items-center space-x-2">
                                        <label class="cursor-pointer text-gray-500 hover:text-blue-600 flex items-center space-x-2">
                                            <i class="fas fa-image"></i>
                                            <span>Add Photo</span>
                                            <input type="file" name="image" class="hidden" accept="image/*">
                                        </label>
                                        <div id="imagePreview" class="hidden ml-4 relative">
                                            <img src="" alt="Preview" class="h-20 w-20 object-cover rounded">
                                            <button type="button" class="remove-image absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 w-6 h-6 flex items-center justify-center">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button id="submit" type="submit" name="createPostBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        Post
                                    </button>
                                </div>
                            </form>
                        </div>

                <!-- Posts Section -->
                <div class="p-4 sm:p-6 border-t">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Posts</h2>
                    <div id="posts-container" class="space-y-4">
                        <!-- Posts will be dynamically loaded here -->
                        <?= $postsHtml ?>
                    </div>
                    
                    <!-- End of posts indicator -->
                    <div id="end-of-posts" class="text-center text-gray-500 py-4" style="display: none;">
                        <p>You've reached the end of the posts</p>
                    </div>
                    
                    <!-- Loading indicator -->
                    <div id="loading" class="loading" style="display: none;">
                        <p>Loading posts...</p>
                    </div>
                    
                    <!-- Scroll loading indicator -->
                    <div id="scroll-loading" class="scroll-loading" style="display: none;">
                        Loading more posts...
                    </div>
                <?php endif; ?>
            </div>
            
            
        </div> 
    </div>
   

    <?php require($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/Pages/Layout/Footer.php'); ?>
</body>
</html>