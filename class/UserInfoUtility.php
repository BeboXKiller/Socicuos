<?php

namespace App;

class UserInfoUtility extends User {
    public function getUserInfo($username)
     { 
        try {
            
            if ($username !== null && !is_string($username)) {
                throw new \Exception('Username must be a string');
            }
            if ($username === null) {
                $username = $_SESSION['userName'] ?? null;
            }
            
            if ($username !== null) {
                $query = "SELECT id, username, email, profile_pic, bio FROM users WHERE username = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param('s', $username);
            }
            if (!$stmt) {
                throw new \Exception("Failed to prepare statement: " . $this->conn->error);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
            
        } catch (\Exception $e) {
            error_log("Error in getUserInfo: " . $e->getMessage());
            return null; // or handle the error as needed
        }

    }
    
    public function getUserInfoById($userId) {
        try {
            if (!$userId || !is_numeric($userId)) {
                throw new \Exception('User ID must be a valid number');
            }
            
            $query = "SELECT id, username, email, profile_pic, bio FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new \Exception("Failed to prepare statement: " . $this->conn->error);
            }
            
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
            
        } catch (\Exception $e) {
            error_log("Error in getUserInfoById: " . $e->getMessage());
            return null;
        }
    }
    
    public function isClosed($userId) {
        try {
            if (!$userId || !is_numeric($userId)) {
                throw new \Exception('User ID must be a valid number');
            }
            
            $query = "SELECT is_closed FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new \Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param('i', $userId);
            if (!$stmt->execute()) {
                throw new \Exception("Execute failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return $row && $row['is_closed'] == 1;
            
        } catch (\Exception $e) {
            error_log("Error in isClosed: " . $e->getMessage());
            return false; // Default to not closed if there's an error
        }
    }
    public static function userIcon($username, $sizeClass = 'w-8 h-8 sm:w-10 sm:h-10', $textClass = 'text-base sm:text-xl mt-1 sm:mt-2')
    {   if(!isset($_SESSION['profilePic']) || empty($image))
        {
        $firstLetter = strtoupper(substr($username, 0, 1));
        return sprintf('
            <div class="%s rounded-full bg-blue-600 flex items-center justify-center transition-colors group-hover:bg-blue-700">
                <span class="text-white '. $textClass .' items-center absolute justify-center noto-serif-dives-akuru-regular  mx-auto">
                    %s
                </span>
            </div>
        ', $sizeClass, $firstLetter);
        // #online status code: <span class="relative -bottom-3 -right-4 w-2 h-2 sm:w-3 sm:h-3 bg-green-500 border-2 border-white rounded-full"></span>
        } else {
            echo '
            <div class="' . $sizeClass . ' rounded-full bg-blue-600 flex items-center justify-center transition-colors group-hover:bg-blue-700">
                <img src="' . htmlspecialchars($image) . '" alt="Profile Picture" class="rounded-full w-full h-full object-cover">
            </div>';
            
        };
    }
}