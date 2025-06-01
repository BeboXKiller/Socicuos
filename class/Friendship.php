<?php

namespace App;
use PDO;
/**
 * Friendship class handles friend requests and friendships
 * 
 * This class provides methods to send friend requests, accept them,
 * retrieve friends list, and check friendship status.
 */
class Friendship {
    private $conn;
    private $table = 'friends';

    public function __construct($db) {
        $this->conn = $db;
    }    // Send friend request
    public function sendRequest($senderId, $receiverId) {
        // Check if friendship already exists
        $existing = $this->checkFriendship($senderId, $receiverId);
        if($existing) return false;

        $query = "INSERT INTO " . $this->table . " 
                 (user1_id, user2_id, status, action_user_id) 
                 VALUES (:user1, :user2, 'pending', :action_user)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user1', min($senderId, $receiverId));
        $stmt->bindParam(':user2', max($senderId, $receiverId));
        $stmt->bindParam(':action_user', $senderId);
        
        return $stmt->execute();
    }

    // Accept friend request  
    public function acceptRequest($userId, $friendId) {
        $query = "UPDATE " . $this->table . " 
                 SET status = 'accepted', action_user_id = :action_user 
                 WHERE (user1_id = :user1 AND user2_id = :user2) OR 
                       (user1_id = :user2 AND user2_id = :user1)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user1', min($userId, $friendId));
        $stmt->bindParam(':user2', max($userId, $friendId));
        $stmt->bindParam(':action_user', $userId);
        
        return $stmt->execute();
    }

    // Get friends list
    public function getFriends($userId) {
        $query = "SELECT u.id, u.username, u.profile_pic
                 FROM users u 
                 INNER JOIN " . $this->table . " f ON 
                    (f.user1_id = :user_id AND f.user2_id = u.id) OR 
                    (f.user2_id = :user_id AND f.user1_id = u.id)
                 WHERE f.status = 'accepted'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Check friendship status
    public function checkFriendship($userId1, $userId2) {
        $query = "SELECT status FROM " . $this->table . " 
                 WHERE (user1_id = :user1 AND user2_id = :user2) OR 
                       (user1_id = :user2 AND user2_id = :user1)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user1', min($userId1, $userId2));
        $stmt->bindParam(':user2', max($userId1, $userId2));
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
