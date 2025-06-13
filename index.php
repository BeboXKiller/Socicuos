<?php

    require_once('vendor/autoload.php');
        use App\Authenticate;
        use App\PostsUtility;
        // Get current section
        $section = $_GET['section'] ?? 'feed';

        $authObj = new Authenticate();
        $authObj->redirectIfNotAuth(); 
        $authObj->logOut();

        $userId = $_SESSION['userID'];
        $postManager = new PostsUtility();
        $initialPosts = $postManager->getNewsFeed($userId, 10, 0);
        
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Socicuos</title>      
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" 
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
    crossorigin="anonymous"></script>
    <script src="/Socicuos/assets/js/fontawesome.js"></script>
    <script src="/Socicuos/assets/js/solid.js"></script>
    <script src="/Socicuos/assets/js/brands.js"></script>
    <script src="/Socicuos/assets/js/alert.js"></script>
    <script src="/Socicuos/assets/js/newsFeed.js"></script>]
    <script src="/Socicuos/assets/js/createPosts.js"></script>
    <script src="/Socicuos/assets/js/getUserInfo.js"></script>

    
</head>



<body class="bg-gray-50">
   
    <?php require($_SERVER[ 'DOCUMENT_ROOT' ] . '/Socicuos/Pages/Layout/Navbar.php');  ?>
    

    <!-- Main Content -->
    <div class="pt-16 min-h-screen">
        <?php if ($section === 'feed'): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Left Sidebar -->
                    <div class="w-full lg:w-1/4 space-y-6">
                        <!-- User Greeting Card -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center space-x-4">
                                <?php echo \App\UserInfoUtility::userIcon($_SESSION['userName'], 'w-16 h-16 text-2xl'); ?>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">Welcome back,</h2>
                                    <p class="text-gray-600"><?php echo htmlspecialchars($_SESSION['userName']); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Friend Suggestions Card -->
                        <div class="bg-white rounded-lg shadow">
                            <div class="p-4 border-b">
                                <h3 class="font-semibold text-gray-900">Friend Suggestions</h3>
                            </div>
                            <div class="divide-y">
                                <!-- Suggestion 1 -->
                                <div class="p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <?php echo \App\UserInfoUtility::userIcon('John Doe', 'w-10 h-10 text-sm'); ?>
                                            <div>
                                                <p class="font-medium text-gray-900">John Doe</p>
                                                <p class="text-sm text-gray-500">5 mutual friends</p>
                                            </div>
                                        </div>
                                        <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                            Add Friend
                                        </button>
                                    </div>
                                </div>
                                <!-- Suggestion 2 -->
                                <div class="p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <?php echo \App\UserInfoUtility::userIcon('Sarah Smith', 'w-10 h-10 text-sm'); ?>
                                            <div>
                                                <p class="font-medium text-gray-900">Sarah Smith</p>
                                                <p class="text-sm text-gray-500">3 mutual friends</p>
                                            </div>
                                        </div>
                                        <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                            Add Friend
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="w-full lg:w-2/4">                       
                         <!-- Create Post -->
                        <div class="bg-white rounded-lg shadow p-4 mb-6">
                            <form id="createPostForm" class="space-y-4" enctype="multipart/form-data" method="POST" >
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

                        <!-- Posts Feed -->
                        <div id="news-feed-container" class="news-feed-container space-y-4">
                            <?= $initialPosts['html'] ?>
                        </div>
                        
                        <!-- End of posts indicator -->
                        <div id="end-of-posts" class="text-center text-gray-500 py-4" style="display: none;">
                            <p>You've reached the end of your feed</p>
                        </div>
                        
                        <!-- Loading indicator -->
                        <div id="loading" class="loading" style="display: none;">
                            <p>Loading posts...</p>
                        </div>
                        
                        <!-- Scroll loading indicator -->
                        <div id="scroll-loading" class="scroll-loading">
                            Loading more posts...
                        </div>
                    </div>

                    <!-- Right Sidebar - Quick Access -->
                    <div class="w-full lg:w-1/4">
                        <div class="bg-white rounded-lg shadow">
                            <div class="p-4 border-b">
                                <h3 class="font-semibold text-gray-900">Quick Access</h3>
                            </div>
                            <div class="p-4 space-y-4">
                                <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 transition-colors">
                                    <i class="fas fa-photo-video text-gray-500"></i>
                                    <span>Photos & Videos</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 transition-colors">
                                    <i class="fas fa-calendar-alt text-gray-500"></i>
                                    <span>Events</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 transition-colors">
                                    <i class="fas fa-users text-gray-500"></i>
                                    <span>Groups</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 transition-colors">
                                    <i class="fas fa-bookmark text-gray-500"></i>
                                    <span>Saved Posts</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 transition-colors">
                                    <i class="fas fa-cog text-gray-500"></i>
                                    <span>Settings</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($section === 'notifications'): ?>
            <!-- Notifications Section -->
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <h4 class="text-xl font-bold text-gray-900 mb-6">Notifications</h4>
                
                <!-- Notifications List -->
                <div class="bg-white rounded-lg flex-1 shadow divide-y">
                    <!-- Like Notification -->
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start space-x-4">
                            <?php echo \App\UserInfoUtility::userIcon('John Doe', 'w-10 h-10 text-lg flex-shrink-0'); ?>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-gray-900">
                                        <span class="font-semibold">John Doe</span> liked your post
                                    </p>
                                    <span class="text-sm text-gray-500">2m ago</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">"This is a sample post content..."</p>
                            </div>
                        </div>
                    </div>

                    <!-- Comment Notification -->
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start space-x-4">
                            <?php echo \App\UserInfoUtility::userIcon('Sarah Smith', 'w-10 h-10 text-lg flex-shrink-0'); ?>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-gray-900">
                                        <span class="font-semibold">Sarah Smith</span> commented on your post
                                    </p>
                                    <span class="text-sm text-gray-500">15m ago</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">"Great post! Thanks for sharing..."</p>
                            </div>
                        </div>
                    </div>

                    <!-- Friend Request Notification -->
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start space-x-4">
                            <?php echo \App\UserInfoUtility::userIcon('Mike Johnson', 'w-10 h-10 text-lg flex-shrink-0'); ?>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-gray-900">
                                        <span class="font-semibold">Mike Johnson</span> sent you a friend request
                                    </p>
                                    <span class="text-sm text-gray-500">1h ago</span>
                                </div>
                                <div class="flex space-x-2 mt-2">
                                    <button class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm">
                                        Accept
                                    </button>
                                    <button class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors text-sm">
                                        Decline
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($section === 'friends'): ?>
        <!-- Messages Section -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Friends</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Friend Requests Card -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b">
                        <h3 class="font-semibold text-gray-900">Friend Requests</h3>
                    </div>
                    <div class="divide-y">
                        <!-- Placeholder Request 1 -->
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <?php echo \App\UserInfoUtility::userIcon('Mike Johnson', 'w-10 h-10 text-sm'); ?>
                                <div>
                                    <p class="font-medium text-gray-900">Mike Johnson</p>
                                    <p class="text-sm text-gray-500">1 mutual friend</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm">Accept</button>
                                <button class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors text-sm">Decline</button>
                            </div>
                        </div>
                        <!-- Placeholder Request 2 -->
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <?php echo \App\UserInfoUtility::userIcon('Alice Brown', 'w-10 h-10 text-sm'); ?>
                                <div>
                                    <p class="font-medium text-gray-900">Alice Brown</p>
                                    <p class="text-sm text-gray-500">2 mutual friends</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm">Accept</button>
                                <button class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors text-sm">Decline</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Friend Suggestions Card -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b">
                        <h3 class="font-semibold text-gray-900">Friend Suggestions</h3>
                    </div>
                    <div class="divide-y">
                        <!-- Placeholder Suggestion 1 -->
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <?php echo \App\UserInfoUtility::userIcon('Sarah Smith', 'w-10 h-10 text-sm'); ?>
                                <div>
                                    <p class="font-medium text-gray-900">Sarah Smith</p>
                                    <p class="text-sm text-gray-500">3 mutual friends</p>
                                </div>
                            </div>
                            <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">Add Friend</button>
                        </div>
                        <!-- Placeholder Suggestion 2 -->
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <?php echo \App\UserInfoUtility::userIcon('John Doe', 'w-10 h-10 text-sm'); ?>
                                <div>
                                    <p class="font-medium text-gray-900">John Doe</p>
                                    <p class="text-sm text-gray-500">5 mutual friends</p>
                                </div>
                            </div>
                            <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">Add Friend</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php elseif ($section === 'messages'): ?>
            <!-- Messages Section -->
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Messages</h2>
                
                <!-- Messages List -->
                <div class="bg-white rounded-lg shadow">
                    <!-- Sample Message -->
                    <div class="p-4 border-b hover:bg-gray-50 cursor-pointer">
                        <div class="flex items-center space-x-4">
                            <?php echo \App\UserInfoUtility::userIcon('John Doe', 'w-12 h-12 text-xl'); ?>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <h3 class="font-semibold text-gray-900">John Doe</h3>
                                    <span class="text-sm text-gray-500">2m ago</span>
                                </div>
                                <p class="text-sm text-gray-600 truncate">Hey, how are you doing?</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
     <?php require($_SERVER[ 'DOCUMENT_ROOT' ] . '/Socicuos/Pages/Layout/Footer.php'); ?>
</body>
</html>
