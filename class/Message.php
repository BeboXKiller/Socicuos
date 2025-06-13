<?php

namespace App;

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
                 VALUES (?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param("iis", $senderId, $receiverId, $message);
        
        return $stmt->execute();
    }

    // Get messages between two users
    public function getConversation($userId, $otherUserId, $limit = 50) {
        $query = "SELECT m.*, u.username, u.profile_pic 
                 FROM " . $this->table . " m 
                 INNER JOIN users u ON m.sender_id = u.id 
                 WHERE (m.sender_id = ? AND m.receiver_id = ?) OR 
                       (m.sender_id = ? AND m.receiver_id = ?) 
                 ORDER BY m.created_at DESC 
                 LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param("iiiii", $userId, $otherUserId, $otherUserId, $userId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return array_reverse($result->fetch_all(MYSQLI_ASSOC));
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
                            WHEN sender_id = ? THEN receiver_id
                            ELSE sender_id
                        END as other_id,
                        MAX(id) as last_message_id
                    FROM messages
                    WHERE sender_id = ? OR receiver_id = ?
                    GROUP BY other_id
                 ) as latest
                 JOIN messages m ON m.id = latest.last_message_id
                 JOIN users u ON u.id = latest.other_id
                 ORDER BY m.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param("iii", $userId, $userId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Mark message as read
    public function markAsRead($messageId, $userId) {
        $query = "UPDATE " . $this->table . " 
                 SET is_read = 1 
                 WHERE id = ? AND receiver_id = ?";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param("ii", $messageId, $userId);
        
        return $stmt->execute();
    }
}
