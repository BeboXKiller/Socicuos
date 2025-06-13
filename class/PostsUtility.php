<?php

namespace App;
/**
 * Posts class handles post creation, retrieval, liking, and searching functionalities
 * 
 * This class provides methods to create new posts, get news feed posts, like/unlike posts,
 * and search for posts based on content.
 */

class PostsUtility extends Posts {

    public function getPosts() {
        $query = "SELECT * FROM posts";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserPosts($userId) {
        $query = "SELECT * FROM posts WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }
    
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function isLikedByUser($postId, $userId) {
        $query = "SELECT COUNT(*) as count FROM likes WHERE post_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param("ii", $postId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $isLiked = (bool)$row['count'] > 0;
        return $isLiked;
         
    }

    /**
     * Get the number of likes for a post
     * @param int $postId The ID of the post
     * @return int The number of likes
     */
    public function getLikeCount($postId) {
        $query = "SELECT COUNT(*) as count FROM likes WHERE post_id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $likeCount = (int)$row['count'];
        return $likeCount;
    }
    private static function human_time_diff($timestamp) {
    $current_time = time();
    $diff = $current_time - $timestamp;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . 'm ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . 'h ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . 'd ago';
    } else {
        return date('M j', $timestamp);
        }
    }

    /**
     * Generate HTML for a user post
     * @param string $postId The ID of the post
     * @param string $username The username of the user
     * @param string $title The title of the post
     * @param string $content The content of the post
     * @param string|null $profilePic URL of the user's profile picture
     * @param string $createdAt The creation date of the post
     * @param bool $isLiked Whether the post is liked by the current user
     * @param int $likeCount The number of likes on the post
     * @param string|null $image URL of an image in the post, if any
     * @return string HTML representation of the post
     */    
    public static function UserPost($postId, $username, $title, $content, $profilePic = null, $createdAt = null, $isLiked = false, $likeCount = 0, $image = null)
    {
        $timeAgo = '';
        if ($createdAt) {
            $postTime = strtotime($createdAt);
            $timeAgo = self::human_time_diff($postTime);
        }
        
        $html = '<div class="bg-white rounded-lg shadow" data-post-id="' . htmlspecialchars($postId) . '">
            <div class="p-4 space-y-4">
                <!-- Post Header -->
                <div class="flex items-center space-x-4">
                    <a href="'.  htmlspecialchars($username) .'" class="profile-link flex-shrink-0 cursor-pointer">
                        
                    ' . \App\UserInfoUtility::userIcon(htmlspecialchars($username), 'w-12 h-12 text-lg') . '
                    </a>
                    <div class="flex-1">
                        <a href="'.  htmlspecialchars($username) .'" 
                        class="profile-link font-semibold text-gray-900  cursor-pointer">' . htmlspecialchars($username) . '</a>
                        <p class="text-sm text-gray-500">' . $timeAgo . '</p>
                    </div>
                </div>
                
                <!-- Post Content -->
                <div class="space-y-3">
                    <h2 class="text-xl font-semibold text-gray-900">' . htmlspecialchars($title) . '</h2>
                    <p class="text-gray-700">' . nl2br(htmlspecialchars($content)) . '</p>';
        
        // Add image if present
        if ($image) {
            $html .= '<div class="mt-3">
                <img src="' . htmlspecialchars($image) . '" alt="Post image" class="rounded-lg max-h-96 w-auto">
            </div>';
        }
        
        $html .= '</div>
                
                <!-- Post Actions -->
                <div class="flex items-center space-x-4 pt-3 border-t">
                    <button class="js-like-button flex items-center space-x-2 text-gray-500 hover:text-blue-600 transition-colors' . ($isLiked ? ' text-blue-600' : '') . '">
                        <i class="' . ($isLiked ? 'fa' : 'fas') . ' fa-heart"></i>
                        <span class="js-like-count">' . $likeCount . '</span>
                    </button>
                    <button class="flex items-center space-x-2 text-gray-500 hover:text-blue-600 transition-colors">
                        <i class="fa fa-comment"></i>
                        <span>Comment</span>
                    </button>
                </div>
            </div>
        </div>';
        
        return $html;
    }


    /**
     * Get the news feed for a user
     * @param int $userId The ID of the user
     * @param int $limit Maximum number of posts to return
     * @param int $offset Offset for pagination
     * @return array Array of posts
     */
    public function getNewsFeed($userId, $limit = 20, $offset = 0) {
        $query = "SELECT p.*, u.username, u.profile_pic 
                FROM posts p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.user_id = ? 
                    OR p.user_id IN (SELECT user2_id FROM friends WHERE user1_id = ? AND status = 'accepted')
                    OR p.user_id IN (SELECT user1_id FROM friends WHERE user2_id = ? AND status = 'accepted')
                ORDER BY p.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param("iiiii", $userId, $userId, $userId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $postsHtml = '';
        $hasMore = false;
        
        while ($row = $result->fetch_assoc()) {
            $isLiked = $this->isLikedByUser($row['id'], $userId);
            $likeCount = $this->getLikeCount($row['id']);
            
            $postsHtml .= self::UserPost(
                $row['id'],
                $row['username'],
                $row['title'],
                $row['content'],
                $row['profile_pic'] ?? null,
                $row['created_at'] ?? null,
                $isLiked,
                $likeCount,
                $row['image'] ?? null
            );
            
            $hasMore = true; // At least one post was fetched
        }
        
        return [
            'html' => $postsHtml,
            'hasMore' => $hasMore,
            'nextOffset' => $offset + $limit
        ];
    }

// Function to display all posts in one HTML string
    public function displayNewsFeed($userId, $limit = 20, $offset = 0) {
        $posts = $this->getNewsFeed($userId, $limit, $offset);
        
        $html = '<div class="news-feed-container space-y-4">';
        foreach ($posts as $postHtml) {
            $html .= $postHtml;
        }
        $html .= '</div>';
        
        return $html;
    }
    /**
     * Toggle like status for a post
     * @param int $postId The ID of the post
     * @param int $userId The ID of the user
     * @return string 'liked' if post was liked, 'unliked' if post was unliked
     */

    /**
     * Check if a post is liked by a user
     * @param int $postId The ID of the post
     * @param int $userId The ID of the user
     * @return bool Whether the post is liked by the user
     */
}
