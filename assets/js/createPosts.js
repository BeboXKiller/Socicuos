$(document).ready(function() {
    const $createPostButton = $('#createPostButton');
    const $createPostForm = $('#createPostForm');
    const $imagePreview = $('#imagePreview');

    const $postsContainer = $('#news-feed-container');

    
    
    
    // Add helper function
    function showError(message) {
        if (typeof showAlert === 'function') {
            showAlert(message, 'error');
        } else {
            alert(message);
        }
    }
    
    // Form submission handler
    $createPostForm.on('submit', 
        function(e)
        {
            e.preventDefault();
            
            // Get form values
            const title = $('input[name="title"]').val().trim();
            const content = $('textarea[name="content"]').val().trim();
            const imageFile = $('input[name="image"]').prop('files')[0];
            
            // Validate inputs
            if (!title || !content) 
            {
                if (typeof showAlert === 'function') {
                    showAlert('Title and content are required!', 'error');
                } else {
                    alert('Title and content are required!');
                }
                return;
            }
            
            // Create FormData
            const formData = new FormData();
            formData.append('title', title);
            formData.append('content', content);
            if (imageFile) formData.append('image', imageFile);
            
            // AJAX request
            $.ajax({
                url: '/Socicuos/ajax/createPost.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) { 
                    console.log(data);
                    if (data.status === 'success') {
                        // Create and display alert
                        const alertHtml = `
                            <div class="alert-message fixed top-20 left-1/2 transform -translate-x-1/2 w-full max-w-md p-4 rounded-lg shadow-sm bg-green-50 text-green-800 border border-green-200" style="transition: opacity 0.5s ease-out;">
                                <div class="flex items-center justify-center">
                                    <p class="text-sm font-medium">${data.message}</p>
                                </div>
                            </div>
                        `;
                        $('body').append(alertHtml);

                        $('<div>', {
                            id: 'newPost',
                            html: $(data.postHtml)
                        }).appendTo('#news-feed-container');
                        
                        // Reset form
                        $('#createPostForm')[0].reset();
                        $('#imagePreview').addClass('hidden').find('img').attr('src', '');
                        $('input[name="image"]').val('');
                        // location.reload();
                        
                        // Prepend new post to feed
                        if (data.postHtml) {
                            $('#news-feed-container').prepend(data.postHtml);
                        }
                        
                        // Remove alert after 3 seconds
                        setTimeout(() => {
                            $('.alert-message').fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }, 3000);

                    } else {
                        showAlert(data.message || 'Error creating post', 'error');
                    }
                },
                error: function(xhr) {
                    try {
                        // Handle cases where error response is JSON
                        const errorData = JSON.parse(xhr.responseText);
                        showAlert(errorData.message || 'Server error', 'error');
                    } catch (e) {
                        // Handle non-JSON errors
                        showAlert('Server error: ' + xhr.statusText, 'error');
                    }

                }
            });
    });

    // Image preview handler
    $('input[name="image"]').on('change', function(e) {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').removeClass('hidden')
                    .find('img').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // Remove image handler
    $('.remove-image').on('click', function() {
        $('input[name="image"]').val('');
        $('#imagePreview').addClass('hidden');
    });

});
