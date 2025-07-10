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
    protected $conn;

    public function __construct() {
        $myDatabaseObj = new \App\Database();
        $this->conn = $myDatabaseObj->conn;
    }

    public function getUserProfile($userId) 
    {
        $query = "SELECT id, username, email, profile_pic, bio, created_at 
                 FROM " . $this->table . " 
                 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
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
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }
        
        $searchTerm = "%{$searchTerm}%";
        $stmt->bind_param("si", $searchTerm, $currentUserId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Update profile
    public function updateProfileInfo($userId, $data) {
        $currentData = $this->getUserProfile($userId);
        if (!$currentData) {
            return false;
        }

        $fieldTypes = [
            'username' => 's',
            'email' => 's',
            'bio' => 's',
            'profile_pic' => 's'
        ];

        $updates = [];
        $values = [];
        $types = "";

        foreach ($data as $field => $newValue) {
            if (!isset($fieldTypes[$field])) {
                continue;
            }

            if ($field === 'bio') {
                if ($currentData[$field] !== $newValue) {
                    $updates[] = "{$field} = ?";
                    $values[] = $newValue;
                    $types .= $fieldTypes[$field];
                }
            } else {
                if (!empty(trim($newValue)) && $currentData[$field] !== $newValue) {
                    $updates[] = "{$field} = ?";
                    $values[] = $newValue;
                    $types .= $fieldTypes[$field];
                }
            }
        }

        if (empty($updates)) {
            return true;
        }

        $query = "UPDATE {$this->table} SET " . implode(', ', $updates) . "WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }

        $values[] = $userId;
        $types .= "i";

        $refs = array();
        $refs[] = $types;
        foreach ($values as $key => $value) {
            $refs[] = &$values[$key];
        }
        call_user_func_array(array($stmt, 'bind_param'), $refs);

        $success = $stmt->execute();
        
        if (!$success) {
            return false;
        }

        return $this->getUserProfile($userId);
    }

    public function deleteOrDeactivateAccount($userId, $method) {
        
        if($method === "delete_user") {
            try {
            $query = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $userId);
            $queryStatus = $stmt->execute();

            if($queryStatus) {

                header('location: SignIn.php');
                \App\Alert::PrintMessage("Account Deleted Successfully", 'Success');
                session_unset();
                session_destroy();
                exit();

            } else {
                throw new \Exception("Something Wrong Happend");
            }
            } catch (\Exception $e) {
            \App\Alert::PrintMessage($e->getMessage(), 'Danger');
        }
        } elseif ($method === 'deactivate_user') {
            try {

                $query = "UPDATE {$this->table} SET is_closed = ? WHERE id = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param('ii', 1 ,$userId);
                $queryStatus = $stmt->execute();

            if($queryStatus) {

                \App\Alert::PrintMessage("Account Deactivated Successfully", 'Success');

            } else {
                throw new \Exception("Something Wrong Happend");
            }
            } catch (\Exception $e) {
            \App\Alert::PrintMessage($e->getMessage(), 'Danger');
            }
        }

    }

}
