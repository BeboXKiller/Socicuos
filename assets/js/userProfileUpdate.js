$(document).ready(function() {

    const $UpdateProfileForm = $('#updateProfileForm');
    const $imagePreview = $('#imagePreview');
    const newUsername = $('input[name="username"]');
    const $newBio = $('textarea[name="bio"]');
    const $newProfilePicture = $('input[name="profilePicture"]');

    // Initialize the image preview if an image is already set
    if ($imagePreview.attr('src')) {
        $imagePreview.show();
    } else {
        $imagePreview.hide();
    }


    // Handle the form submission
    $UpdateProfileForm.on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        const username = $newUsername.val().trim();
        const bio = $newBio.val().trim();
        const newProfilePicture = $newProfilePicture.prop('files')[0];

        const $FromData = new FormData();
        $FromData.append('username', username);
        $FromData.append('bio', bio);
        $FromData.append('ProfilePicture', newProfilePicture);


        // Show loading state
        $updateProfileButton.prop('disabled', true).text('Updating...');

        // Submit the form via AJAX
        $.ajax({
            url: '/Socicuos/ajax/userProfileUpdate.php',
            type: 'POST',
            data: $FromData,
            processData: false,
            success: function(response) {
                
                if( response.status === success ) {
                
                alert('Profile updated successfully!');
                $updateProfileButton.prop('disabled', false).text('Update Profile');
                } else {

                    // 

                }


            },
            error: function(xhr) {
                // Handle error response
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
    }
    );

    // Image preview functionality
    $('#profileImage').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $imagePreview.attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        } else {
            $imagePreview.attr('src', ''); // Reset preview if no file is selected
        }
    });

    
    









});