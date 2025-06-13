<?php

namespace App;
/**
 * Comment class handles comments on posts
 * 
 * This class provides methods to add comments to posts and retrieve comments for a post.
 */

class Comment {
    private $conn;
    private $table = 'comments';

    public function __construct($db) {
        $this->conn = $db;
    }    // Add comment to post
    public function addComment($postId, $userId, $content) {
        $query = "INSERT INTO " . $this->table . " (post_id, user_id, content) 
                 VALUES (?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param("iis", $postId, $userId, $content);
        
        return $stmt->execute();
    }    // Get comments for a post    
    public function getComments($postId) {
        $query = "SELECT c.*, u.username, u.profile_pic 
                 FROM " . $this->table . " c 
                 INNER JOIN users u ON c.user_id = u.id 
                 WHERE c.post_id = ? 
                 ORDER BY c.created_at ASC";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
