$(document).ready(function() {
    let currentOffset = 0;
    let isLoading = false;
    let hasMore = true;
    const limit = 10;
    
    // Get user ID and username from the page
    const userId = $('#userWallData').data('user-id');
    const userTo = $('#userWallData').data('user-to');
    
    // Load initial posts
    loadUserWallPosts();
    
    // Load more posts on scroll
    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            if (!isLoading && hasMore) {
                loadUserWallPosts();
            }
        }
    });
    
    function loadUserWallPosts() {
        if (isLoading) return;
        
        isLoading = true;
        $('#scroll-loading').show();
        
        $.ajax({
            url: '/Socicuos/ajax/getUserWall.php',
            method: 'POST',
            data: {
                userId: userId,
                userTo: userTo,
                limit: limit,
                offset: currentOffset
            },
            success: function(data) {
                if (data.status === 'success') {
                    if (currentOffset === 0) {
                        // First load - replace content
                        $('#posts-container').html(data.html);
                    } else {
                        // Append new posts
                        $('#posts-container').append(data.html);
                    }
                    
                    currentOffset = data.nextOffset;
                    hasMore = data.hasMore;
                    
                    if (!hasMore) {
                        $('#end-of-posts').show();
                    }
                } else {
                    console.error('Error loading posts:', data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
            },
            complete: function() {
                isLoading = false;
                $('#scroll-loading').hide();
            }
        });
    }
    
    // Handle like button clicks
    $(document).on('click', '.js-like-button', function() {
        const $button = $(this);
        const postId = $button.closest('[data-post-id]').data('post-id');
        
        $.ajax({
            url: '/Socicuos/ajax/toggleLike.php',
            method: 'POST',
            data: { postId: postId },
            success: function(data) {
                if (data.status === 'success') {
                    const $likeCount = $button.find('.js-like-count');
                    const currentCount = parseInt($likeCount.text());
                    
                    if (data.action === 'liked') {
                        $button.addClass('text-blue-600').removeClass('text-gray-500');
                        $button.find('i').removeClass('far').addClass('fas');
                        $likeCount.text(currentCount + 1);
                    } else {
                        $button.removeClass('text-blue-600').addClass('text-gray-500');
                        $button.find('i').removeClass('fas').addClass('far');
                        $likeCount.text(Math.max(0, currentCount - 1));
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error toggling like:', error);
            }
        });
    });
}); 