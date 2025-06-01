<?php

namespace App;

use PDO;

class Message {
    private $conn;
    private $table = 'messages';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Send message
    public function sendMessage($senderId, $receiverId, $message) {
        $query = "INSERT INTO " . $this->table . " 
                 (sender_id, receiver_id, message) 
                 VALUES (:sender_id, :receiver_id, :message)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sender_id', $senderId);
        $stmt->bindParam(':receiver_id', $receiverId);
        $stmt->bindParam(':message', $message);
        
        return $stmt->execute();
    }

    // Get messages between two users
    public function getConversation($userId, $otherUserId, $limit = 50) {
        $query = "SELECT m.*, u.username, u.profile_pic 
                 FROM " . $this->table . " m 
                 INNER JOIN users u ON m.sender_id = u.id 
                 WHERE (m.sender_id = :user_id AND m.receiver_id = :other_user_id) OR 
                       (m.sender_id = :other_user_id AND m.receiver_id = :user_id) 
                 ORDER BY m.created_at DESC 
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':other_user_id', $otherUserId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // Get user's recent conversations
    public function getRecentConversations($userId) {
        $query = "SELECT 
                    u.id as other_user_id,
                    u.username as other_user_name,
                    u.profile_pic as other_user_picture,
                    m.message as last_message,
                    m.created_at as last_message_time,
                    m.is_read
                 FROM (
                    SELECT 
                        CASE 
                            WHEN sender_id = :user_id THEN receiver_id
                            ELSE sender_id
                        END as other_id,
                        MAX(id) as last_message_id
                    FROM messages
                    WHERE sender_id = :user_id OR receiver_id = :user_id
                    GROUP BY other_id
                 ) as latest
                 JOIN messages m ON m.id = latest.last_message_id
                 JOIN users u ON u.id = latest.other_id
                 ORDER BY m.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mark message as read
    public function markAsRead($messageId, $userId) {
        $query = "UPDATE " . $this->table . " 
                 SET is_read = 1 
                 WHERE id = :message_id AND receiver_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':message_id', $messageId);
        $stmt->bindParam(':user_id', $userId);
        
        return $stmt->execute();
    }
}
