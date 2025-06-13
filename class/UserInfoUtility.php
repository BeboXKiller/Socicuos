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
                $query = "SELECT * FROM users WHERE username = ?";
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
    public static function userIcon($username, $sizeClass = 'w-8 h-8 sm:w-10 sm:h-10', $textClass = 'text-base sm:text-xl mt-1 sm:mt-2')
    {   
        $firstLetter = strtoupper(substr($username, 0, 1));
        return sprintf('
            <div class="%s rounded-full bg-blue-600 flex items-center justify-center transition-colors group-hover:bg-blue-700">
                <span class="text-white '. $textClass .' items-center justify-center noto-serif-dives-akuru-regular  mx-auto">
                    %s
                </span>
                <span class="absolute -bottom-1 -right-1 w-2 h-2 sm:w-3 sm:h-3 bg-green-500 border-2 border-white rounded-full"></span>
            </div>
        ', $sizeClass, $firstLetter);
    }

}