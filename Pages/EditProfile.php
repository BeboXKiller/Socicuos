<?php
require_once('../vendor/autoload.php');

    use App\Authenticate;
        $authObj = new Authenticate();
        $authObj->redirectIfNotAuth();
        
    use App\FormUtility;
        $formUtility = new FormUtility();    use App\User;
        $userObj = new User();
        if (isset($_POST['editProfileBtn'])) {
            $updatedData = $userObj->updateProfileInfo($_SESSION['userID'], $_POST);
            if ($updatedData) {
                // Update session data with new values
                $_SESSION['userName'] = $updatedData['username'];
                $_SESSION['bio'] = $updatedData['bio'];
                $_SESSION['email'] = $updatedData['email'];
                if (isset($updatedData['profile_pic'])) {
                    $_SESSION['profile_pic'] = $updatedData['profile_pic'];
                }
                
                \App\Alert::PrintMessage("Profile updated successfully", 'Success');
            } else {
                \App\Alert::PrintMessage("No changes were made to your profile", 'Info');
            }
        }
        
        
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
    
    <div class="flex min-h-screen">
    
        <div class="min-w-screen bg-gray-100 flex items-center justify-center">
            <div class="flex items-center justify-center w-2/6 bg-gray-100 ">    
                
                <div class="w-80 h-80 rounded-full bg-blue-600 flex items-center justify-center">
                    <span class="text-white text-9xl/4 pt-8 noto-serif-dives-akuru-regular flex space-between">
                        <?php echo strtoupper(substr($_SESSION['userName'], 0, 1)); ?>
                    </span>
                </div>
                 <span  method="post" enctype="multipart/form-data" class="space-y-6" name="editProfileForm" class="">
                        <button class="h-20 w-20 text-white top-20 right-20 text-4xl relative bg-zinc-700 active:bg-zinc-600 hover:bg-zinc-800 rounded-full p-2">
                            <!-- <input type="file" name="profile_picture" 
                             id="profilePictureInput" class="hidden" accept="image/*"> -->
                            <label for="profilePictureInput" class="cursor-pointer">
                                
                            <i class="fa-solid fa-camera"></i>
                        </button>
                    </span>
        
            </div>
            
            <div class="w-2/6 flex items-center justify-center">
                    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
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
        </div>    </div>
    <?php var_dump($_SESSION['bio']) ;require($_SERVER['DOCUMENT_ROOT'] . '/Socicuos/Pages/Layout/Footer.php'); ?>
</body>
</html>