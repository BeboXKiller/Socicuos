<?php

namespace App;
use PDO;
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
                 VALUES (:post_id, :user_id, :content)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $postId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':content', $content);
        
        return $stmt->execute();
    }    // Get comments for a post
    public function getComments($postId) {
        $query = "SELECT c.*, u.username, u.profile_pic 
                 FROM " . $this->table . " c 
                 INNER JOIN users u ON c.user_id = u.id 
                 WHERE c.post_id = :post_id 
                 ORDER BY c.created_at ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $postId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
