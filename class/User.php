<?php


namespace App;
use PDO;
/**
 * User class handles user profile and search functionalities
 * 
 * This class provides methods to get user profile details and search for users.
 */

class User
{
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }    public function getProfile($userId) {
        $query = "SELECT id, username, email, profile_pic, bio, created_at 
                 FROM " . $this->table . " 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Search users
    public function searchUsers($searchTerm, $currentUserId) {
        $query = "SELECT id, username, profile_pic 
                 FROM " . $this->table . " 
                 WHERE username LIKE :search 
                 AND id != :current_user 
                 LIMIT 20";
        
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$searchTerm}%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->bindParam(':current_user', $currentUserId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update profile
    public function updateProfile($userId, $data) {
        $allowedFields = ['username', 'email', 'bio', 'profile_pic'];
        $updates = [];
        $params = [];

        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $updates[] = "{$field} = :{$field}";
                $params[":{$field}"] = $value;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $query = "UPDATE " . $this->table . " 
                 SET " . implode(', ', $updates) . " 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindParam(':id', $userId);
        
        return $stmt->execute();
    }
}