<?php
require_once('../vendor/autoload.php');

use App\Authenticate;
use App\FormUtility;
use App\User;
use App\Alert;

// Check authentication
    $authObj = new Authenticate();
        $authObj->redirectIfNotAuth();

    // Initialize objects
    $formUtility = new FormUtility();
        $userObj = new User();

        // Handle form submission
        if (isset($_POST['editProfileBtn'])) {
            $updatedData = $userObj->updateProfileInfo($_SESSION['userID'], $_POST);
            
            if ($updatedData === false) {
                Alert::PrintMessage("Error updating profile", 'Error');
            } elseif ($updatedData === true) {
                Alert::PrintMessage("No changes were made to your profile", 'Info');
            } elseif (is_array($updatedData)) {
                // Update session with new values
                if (isset($updatedData['username'])) {
                    $_SESSION['userName'] = $updatedData['username'];
                }
                if (isset($updatedData['bio'])) {
                    $_SESSION['bio'] = $updatedData['bio'];
                }
                if (isset($updatedData['email'])) {
                    $_SESSION['email'] = $updatedData['email'];
                }
                if (isset($updatedData['profile_pic'])) {
                    $_SESSION['profile_pic'] = $updatedData['profile_pic'];
                }
                Alert::PrintMessage("Profile updated successfully", 'Success');
            }
        }

// Get current profile data
$userProfile = $userObj->getUserProfile($_SESSION['userID']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Socicuos</title>
    <link rel="stylesheet" href="/Socicuos/assets/css/fonts.css">
    <link rel="stylesheet" href="/Socicuos/assets/css/tailwind.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js" 
    integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" 
    crossorigin="anonymous"></script>
    <script src="../assets/js/fontawesome.js"></script>
    <script src="../assets/js/solid.js"></script>
    <script src="../assets/js/alert.js"></script>
    <script src="../assets/js/usernameValidator.js"></script>
    <script src="../assets/js/passwordValidator.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new UsernameValidator('username');
            new PasswordValidator('password');
        });
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    
    <?php require($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/Pages/Layout/Navbar.php'); ?>  
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex my-15 flex-col lg:flex-row items-center justify-center gap-8">
            <!-- Profile Picture Section -->
            <div class="w-full lg:w-auto flex flex-col items-center">    
                <div class="w-48 h-48 lg:w-80 lg:h-80 rounded-full bg-blue-600 flex items-center justify-center">
                    <span class="text-white text-6xl lg:text-9xl/4 pt-8 noto-serif-dives-akuru-regular flex space-between">
                        <?php echo strtoupper(substr($_SESSION['userName'], 0, 1)); ?>
                    </span>
                </div>
                <span method="post" action="/upload"  accept="image/*" enctype="multipart/form-data" class="space-y-6" name="editProfileForm">
                    <button  type="file" class="cursor-pointer h-16 w-16 lg:h-20 lg:w-20 text-white left-20 lg:left-20 text-3xl bottom-20 l;g:bottom-20 lg:text-4xl relative bg-zinc-700 active:bg-zinc-600 hover:bg-zinc-800 rounded-full p-2">
                        <label  for="profilePictureInput" class="cursor-pointer">
                            <i class="fa-solid fa-camera"></i>
                        </label>
                    </button>
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
                        <button type="submit" name="editProfileBtn" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/Pages/Layout/Footer.php'); ?>
</body>
</html>