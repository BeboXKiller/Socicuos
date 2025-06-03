<?php

namespace App;
/**
 * User class handles user profile and search functionalities
 * 
 * This class provides methods to get user profile details and search for users.
 */

class User
{
    
    private $table = 'users';

    public function getUserProfile($userId) 
    {
        $query = "SELECT id, username, email, profile_pic, bio, created_at 
                 FROM " . $this->table . " 
                 WHERE id = ?";
        $myDatabaseObj = new \App\Database();   
        $queryObj = $myDatabaseObj->conn->prepare($query);
        $queryObj->bind_param("i", $userId);
        $queryObj->execute();
        $result = $queryObj->get_result();
        
        return $result->fetch_assoc();
    }

    // Search users
    public function searchUsers($searchTerm, $currentUserId) 
    {
        $query = "SELECT id, username, profile_pic 
                 FROM " . $this->table . " 
                 WHERE username LIKE ? 
                 AND id != ? 
                 LIMIT 20";
        $myDatabaseObj = new \App\Database();
        $queryObj = $myDatabaseObj->conn->prepare($query);
        $searchTerm = "%{$searchTerm}%";
        $queryObj->bind_param("si", $searchTerm, $currentUserId);
        $queryObj->execute();
        $result = $queryObj->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }    // Update profile    public function updateProfileInfo($userId, $data) {
        
    public function updateProfileInfo($userId, $data) {
        // Get current user data to compare changes
        $currentData = $this->getUserProfile($userId);
        if (!$currentData) {
            return false;
        }

        // Define all allowed fields and their types
        $fieldTypes = [
            'username' => 's',
            'email' => 's',
            'bio' => 's',
            'profile_pic' => 's'
        ];

        $updates = [];
        $values = [];
        $types = "";

        // Only process fields that have actually changed
        foreach ($data as $field => $newValue) {
            // Skip non-updatable fields
            if (!isset($fieldTypes[$field])) {
                continue;
            }

            // Handle empty values
            if ($field === 'bio') {
                // Bio can be empty
                if ($currentData[$field] !== $newValue) {
                    $updates[] = "{$field} = ?";
                    $values[] = $newValue;
                    $types .= $fieldTypes[$field];
                }
            } else {
                // Other fields can't be empty
                if (!empty(trim($newValue)) && $currentData[$field] !== $newValue) {
                    $updates[] = "{$field} = ?";
                    $values[] = $newValue;
                    $types .= $fieldTypes[$field];
                }
            }
        }

        // If no fields have changed or all values were invalid
        if (empty($updates)) {
            return true; // No changes needed
        }

        // Prepare and execute update query
        $query = "UPDATE " . $this->table . " SET " . implode(', ', $updates) . " WHERE id = ?";
        $myDatabaseObj = new \App\Database();
        $queryObj = $myDatabaseObj->conn->prepare($query);

        // Add userId to values and types
        $values[] = $userId;
        $types .= "i";

        // Bind parameters
        $refs = array();
        $refs[] = $types;
        foreach ($values as $key => $value) {
            $refs[] = &$values[$key];
        }
        call_user_func_array(array($queryObj, 'bind_param'), $refs);

        // Execute update
        $success = $queryObj->execute();
        
        if (!$success) {
            return false;
        }

        // Return the updated user data
        return $this->getUserProfile($userId);
    }
}
