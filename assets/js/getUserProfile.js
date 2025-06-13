$(document).ready(function() {
    // Handle profile link clicks
    $(document).on('click', '.profile-link', function(e) {
        e.preventDefault();
        const username = $(this).data('username');
        
        $.ajax({
            url: '/Socicuos/ajax/getUserProfile.php',
            method: 'GET',
            data: { username },
            success: function(data) {
                if (data.error) {
                    showAlert(data.error, 'error');
                    return;
                }
                
                // Update profile section
                $('#profileUsername').text(data.username);
                $('#profileEmail').text(data.email);
                $('#profilePicture').attr('src', data.profilePictureUrl);
                $('#profileBio').text(data.bio);
                
                
                // Show profile modal/section
                $('#profileModal').removeClass('hidden');
            },
            error: function(xhr) {
                showAlert('Error loading profile', 'error');
            }
        });
    });
    
});