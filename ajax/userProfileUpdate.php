<?php
require_once('../vendor/autoload.php');

use App\Alert;
use App\User;
$userObj = new User();

// Image type check 
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$mime = mime_content_type(filename: $_FILES['profilePicture']['tmp_name']);

if(!in_array($mime, $allowed_types)) {
    die("Invalid file type");
}
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
                    $_SESSION['profilePic'] = $updatedData['profile_pic'];
                }
                Alert::PrintMessage("Profile updated successfully", 'Success');
            }
        }
