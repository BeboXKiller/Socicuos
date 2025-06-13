$(document).ready(function() {
    // Like button handler
    $(document).on('click', '.js-like-button', function(e) {
        e.preventDefault();
        const $button = $(this);
        const postId = $button.closest('[data-post-id]').data('postId');
        
        $.ajax({
            url: '/Socicuos/ajax/toggleLike.php',
            method: 'POST',
            data: { postId: postId },
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    if (data.status === 'success') {
                        const $likeCount = $button.find('.js-like-count');
                        let currentCount = parseInt($likeCount.text()) || 0;
                        
                        if (data.action === 'liked') {
                            $button.addClass('text-blue-600').removeClass('text-gray-500');
                            $button.find('i').addClass('fas').removeClass('far');
                            currentCount++;
                        } else {
                            $button.removeClass('text-blue-600').addClass('text-gray-500');
                            $button.find('i').removeClass('fas').addClass('far');
                            currentCount--;
                        }
                        
                        $likeCount.text(currentCount);
                    } else {
                        if (typeof showAlert === 'function') {
                            showAlert(data.message || 'Error updating like status', 'error');
                        } else {
                            alert(data.message || 'Error updating like status');
                        }
                    }
                } catch (e) {
                    console.error('JSON parse error:', e, response);
                    alert('Invalid server response');
                }
            },
            error: function(xhr) {
                console.error("Raw error response:", xhr.responseText);
                if (typeof showAlert === 'function') {
                    showAlert('Error updating like status', 'error');
                } else {
                    alert('Error updating like status');
                }
            }
        });
    });
});