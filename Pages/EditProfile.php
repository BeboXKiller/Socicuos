<?php
require_once('../vendor/autoload.php');

    use App\Authenticate;
        $authObj = new Authenticate();
        $authObj->redirectIfNotAuth();
    use App\FormUtility;
        $formUtility = new FormUtility();
    use App\User;      
        $userObj = new User;
        $userProfile = $userObj->getUserProfile($_SESSION['userID']);
    use App\Alert;
    use App\UserInfoUtility;

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Socicuos</title>
    <link rel="stylesheet" href="/Socicuos/assets/css/fonts.css">
    <link href="./output.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js" 
    integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" 
    crossorigin="anonymous"></script>
    <script src="../assets/js/fontawesome.js"></script>
    <script src="../assets/js/solid.js"></script>
    <script src="../assets/js/alert.js"></script>
    <script src="../assets/js/usernameValidator.js"></script>
    <script src="../assets/js/userProfileUpdate.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new UsernameValidator('username');
        });
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    
    <?php require($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/Pages/Layout/Navbar.php'); ?>  
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex my-15 flex-col lg:flex-row items-center justify-center gap-8" id="updateProfileForm" method="POST">
            <!-- Profile Picture Section -->
            <div class="w-full lg:w-auto flex flex-col items-center">    
                <div class="w-48 h-48 lg:w-80 lg:h-80 rounded-full bg-blue-600 flex items-center justify-center">
                    <span id="profileImage" class="text-white text-6xl lg:text-9xl/4 pt-8 noto-serif-dives-akuru-regular flex space-between">
                        <?php echo UserInfoUtility::userIcon($_SESSION['userName'], "", "") ?>
                    </span>
                </div>
                <span  class="space-y-6" >
                    <div class="group relative flex justify-center items-center h-16 w-16 lg:h-20 left-20 bottom-20 lg:w-20 cursor-pointer">
                        <!-- Hidden file input -->
                        <input 
                            type="file" 
                            id="profilePictureInput" 
                            name="profilePicture" 
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            accept="image/png, image/jpeg, image/jpg, image/webp"
                            onchange="previewProfilePicture(event)"
                        >
                        
                        <!-- Styled camera icon -->
                        <div class="flex justify-center items-center h-full w-full text-3xl lg:text-4xl text-white bg-zinc-700 group-hover:bg-zinc-800 group-active:bg-zinc-600 rounded-full transition-colors duration-200">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                    </div>
                </span>
            </div>
            
            <!-- Edit Form Section -->
            <div class="w-full lg:w-2/6">
                <div class="bg-white p-6 lg:p-8 rounded-lg shadow-lg">
                    <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">Edit Profile</h1>     
                    <form method="post" enctype="multipart/form-data" class="space-y-6" name="editProfileForm">

                        <?php echo $formUtility->textFieldUpdate('username', 'Username', 'text', false, $_SESSION['userName']) ?>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Bio</label>
                            <textarea
                                name="bio" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                rows="4"
                                placeholder="Tell us about yourself..."
                            ><?php echo htmlspecialchars($_SESSION['bio'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" id="updateProfileBtn" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php var_dump($_SESSION); require($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/Pages/Layout/Footer.php'); ?>
</body>
</html>