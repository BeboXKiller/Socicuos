<?php

namespace App;
use PDO;

class Posts {
    private $conn;
    private $table = 'posts';

    public function __construct($db) {
        $this->conn = $db;
    }    // Create new post
    public function create($userId, $content, $image = null) {
        $query = "INSERT INTO " . $this->table . " (user_id, content, image) 
                 VALUES (:user_id, :content, :image)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':image', $image);
        
        return $stmt->execute();
    }    // Get news feed posts
    public function getNewsFeed($userId, $limit = 20, $offset = 0) {
        $query = "SELECT p.*, u.username, u.profile_pic,
                         (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id AND l.user_id = :user_id) as user_liked
                 FROM posts p 
                 INNER JOIN users u ON p.user_id = u.id 
                 LEFT JOIN friends f ON (
                     (f.user1_id = :user_id AND f.user2_id = p.user_id) OR 
                     (f.user2_id = :user_id AND f.user1_id = p.user_id)
                 )
                 WHERE p.user_id = :user_id OR 
                       (f.status = 'accepted')
                 ORDER BY p.created_at DESC 
                 LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    // Like/Unlike post
    public function toggleLike($postId, $userId) {
        // Check if already liked
        $checkQuery = "SELECT id FROM likes WHERE post_id = :post_id AND user_id = :user_id AND comment_id IS NULL";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':post_id', $postId);
        $checkStmt->bindParam(':user_id', $userId);
        $checkStmt->execute();
        
        if($checkStmt->rowCount() > 0) {
            // Unlike
            $query = "DELETE FROM likes WHERE post_id = :post_id AND user_id = :user_id AND comment_id IS NULL";
            $action = 'unliked';
        } else {
            // Like
            $query = "INSERT INTO likes (post_id, user_id) VALUES (:post_id, :user_id)";
            $action = 'liked';
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $postId);
        $stmt->bindParam(':user_id', $userId);
        
        return $stmt->execute() ? $action : false;
    }    // Search posts
    public function searchPosts($searchTerm, $userId, $limit = 20) {
        $query = "SELECT p.*, u.username, u.profile_pic 
                 FROM posts p 
                 INNER JOIN users u ON p.user_id = u.id 
                 LEFT JOIN friends f ON (
                     (f.user1_id = :user_id AND f.user2_id = p.user_id) OR 
                     (f.user2_id = :user_id AND f.user1_id = p.user_id)
                 )
                 WHERE p.content LIKE :search 
                       AND (p.user_id = :user_id OR f.status = 'accepted')
                 ORDER BY p.created_at DESC 
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$searchTerm}%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
