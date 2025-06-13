<?php

namespace App;
/**
 * Friendship class handles friend requests and friendships
 * 
 * This class provides methods to send friend requests, accept them,
 * retrieve friends list, and check friendship status.
 */
class Friendship extends User {
    
    private $table = 'friends';
  // Send friend request
    public function sendRequest($senderId, $receiverId) {
        $existing = $this->checkFriendship($senderId, $receiverId);
        if($existing) return false;

        $query = "INSERT INTO " . $this->table . " 
                 (user1_id, user2_id, status, action_user_id) 
                 VALUES (?, ?, 'pending', ?)";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }

        $user1 = min($senderId, $receiverId);
        $user2 = max($senderId, $receiverId);
        $stmt->bind_param("iii", $user1, $user2, $senderId);
        
        return $stmt->execute();
    }

    // Accept friend request  
    public function acceptRequest($userId, $friendId) {
        $query = "UPDATE " . $this->table . " 
                 SET status = 'accepted', action_user_id = ? 
                 WHERE (user1_id = ? AND user2_id = ?) OR 
                       (user1_id = ? AND user2_id = ?)";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }

        $user1 = min($userId, $friendId);
        $user2 = max($userId, $friendId);
        $stmt->bind_param("iiiii", $userId, $user1, $user2, $user2, $user1);
        
        return $stmt->execute();
    }

    // Get friends list
    public function getFriends($userId) {
        $query = "SELECT u.id, u.username, u.profile_pic
                 FROM users u 
                 INNER JOIN " . $this->table . " f ON 
                    (f.user1_id = ? AND f.user2_id = u.id) OR 
                    (f.user2_id = ? AND f.user1_id = u.id)
                 WHERE f.status = 'accepted'";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("ii", $userId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Check friendship status
    public function checkFriendship($userId1, $userId2) {
        $query = "SELECT status FROM " . $this->table . " 
                 WHERE (user1_id = ? AND user2_id = ?) OR 
                       (user1_id = ? AND user2_id = ?)";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }

        $user1 = min($userId1, $userId2);
        $user2 = max($userId1, $userId2);
        $stmt->bind_param("iiii", $user1, $user2, $user2, $user1);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
}
