<?php

namespace App;
/**
 * Posts class handles post creation, retrieval, liking, and searching functionalities
 * 
 * This class provides methods to create new posts, get news feed posts, like/unlike posts,
 * and search for posts based on content.
 */

class Posts extends User
{
        private $table = 'posts';
        protected $conn;

    public function __construct() 
    {
        $myDatabaseObj = new \App\Database();
        $this->conn = $myDatabaseObj->conn;
    } 
        // Create new post      
    public function createPost($userId, $title, $content, $image = null)
    {
        try 
        {
            // Validate parameters
            if (!$userId) {
                throw new \Exception('User ID is required');
            }

            $title = trim($title);
            $content = trim($content);

            if (empty($title)) {
                throw new \Exception('Post title cannot be empty');
            }

            if (strlen($title) > 150) {
                throw new \Exception('Post title cannot exceed 150 characters');
            }

            if (empty($content)) {
                throw new \Exception('Post content cannot be empty');
            }

            // Prepare query
            if ($image === null) {
                $query = "INSERT INTO " . $this->table . " (user_id, title, content, created_at) 
                        VALUES (?, ?, ?, NOW())";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("iss", $userId, $title, $content);
            } else {
                $query = "INSERT INTO " . $this->table . " (user_id, title, content, image, created_at) 
                        VALUES (?, ?, ?, ?, NOW())";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("isss", $userId, $title, $content, $image);
            }

            if (!$stmt->execute()) {
                throw new \Exception("Failed to create post: " . $stmt->error);
            }

            $postId = $stmt->insert_id;
            $stmt->close();

            return ['success' => true, 'postId' => $postId, 
                    'message' => 'Post created successfully',
                    'postHtml' => \App\PostsUtility::UserPost(
                        $postId,
                        $_SESSION['userName'],
                        $title,
                        $content,
                        $_SESSION['profile_pic'] ?? null,
                        date('Y-m-d H:i:s'),
                        false, // isLiked
                        0, // likeCount
                        $image
                    )]; 

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

        // Get news feed posts    
    public function getNewsFeed($userId, $limit = 20, $offset = 0) 
    {
        
            $query = "SELECT p.*, u.username, u.profile_pic,
                            (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id AND l.user_id = ?) as user_liked
                    FROM posts p 
                    INNER JOIN users u ON p.user_id = u.id 
                    LEFT JOIN friends f ON (
                        (f.user1_id = ? AND f.user2_id = p.user_id) OR 
                        (f.user2_id = ? AND f.user1_id = p.user_id)
                    )
                    WHERE p.user_id = ? OR 
                        (f.status = 'accepted')
                    ORDER BY p.created_at DESC 
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new \Exception("Prepare failed: " . $this->conn->error);
            }
        
            $stmt->bind_param("iiiiii", $userId, $userId, $userId, $userId, $limit, $offset);
            
            if (!$stmt->execute()) {
                throw new \Exception("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $posts = $result->fetch_all(MYSQLI_ASSOC);
            
            $stmt->close();
            return $posts;
        
        
    }    
    // Like/Unlike post    
    public function toggleLike($postId, $userId)
    {   
        try {
                // Validate parameters
            if (!$postId || !$userId) {
                throw new \Exception('Post ID and User ID are required');
            }
            
            // Check if already liked
            $checkQuery = "SELECT id FROM likes WHERE post_id = ? AND user_id = ? AND comment_id IS NULL";
            
            $checkStmt = $this->conn->prepare($checkQuery);
            if (!$checkStmt) {
                throw new \Exception("Prepare failed: " . $this->conn->error);
            }
        
            $checkStmt->bind_param("ii", $postId, $userId);
            
            if (!$checkStmt->execute()) {
                throw new \Exception("Execute failed: " . $checkStmt->error);
            }

            $result = $checkStmt->get_result();
            $isLiked = $result->num_rows > 0;
            $checkStmt->close();
            
            // Prepare the appropriate query based on current like status
            if($isLiked) {
                // Unlike
                $query = "DELETE FROM likes WHERE post_id = ? AND user_id = ? AND comment_id IS NULL";
                $action = 'unliked';
            } else {
                // Like
                $query = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
                $action = 'liked';
            }
            
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new \Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("ii", $postId, $userId);
            
            if (!$stmt->execute()) {
                throw new \Exception("Execute failed: " . $stmt->error);
            }
            
            $stmt->close();
            return $action;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }    
    // Search posts
    public function searchPosts($searchTerm, $userId, $limit = 20) {
        $query = "SELECT p.*, u.username, u.profile_pic 
                 FROM posts p 
                 INNER JOIN users u ON p.user_id = u.id 
                 LEFT JOIN friends f ON (
                     (f.user1_id = ? AND f.user2_id = p.user_id) OR 
                     (f.user2_id = ? AND f.user1_id = p.user_id)
                 )
                 WHERE p.content LIKE ? 
                       AND (p.user_id = ? OR f.status = 'accepted')
                 ORDER BY p.created_at DESC 
                 LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }

        $searchPattern = "%{$searchTerm}%";
        $stmt->bind_param("iisii", $userId, $userId, $searchPattern, $userId, $limit);
        
        if (!$stmt->execute()) {
            throw new \Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $posts = $result->fetch_all(MYSQLI_ASSOC);
        
        $stmt->close();
        return $posts;
    }
}
