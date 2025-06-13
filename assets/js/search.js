$(document).ready(function() {
    let searchTimer;
    const $searchInput = $('#searchInput');
    const $searchResults = $('#searchResults');

    // Handle input in search box
    $searchInput.on('input', function() {
        clearTimeout(searchTimer);
        const searchTerm = $(this).val().trim();
        
        if (searchTerm.length === 0) {
            $searchResults.addClass('hidden');
            return;
        }

        searchTimer = setTimeout(function() {
            $.ajax({
                url: '/Socicuos/ajax/search.php',
                method: 'POST',
                data: { search: searchTerm },
                success: function(results) {
                    if (!results.length) {
                        $searchResults
                            .html('<div class="p-4 text-gray-500">No results found</div>')
                            .removeClass('hidden');
                        return;
                    }

                    const html = results.map(function(user) {
                        const profileImage = user.profile_pic === 'default.jpg' || !user.profile_pic
                            ? `<span class="text-white pt-2 noto-serif-dives-akuru-regular text-lg">${user.username.charAt(0).toUpperCase()}</span>`
                            : `<img src="/Socicuos/assets/img/profile_pics/${user.profile_pic}" 
                                   alt="${user.username}" 
                                   class="w-10 h-10 rounded-full object-cover">`;

                        return `
                            <a href="UserProfile.php?id=${user.id}" 
                               class="flex items-center space-x-4 p-3 hover:bg-gray-50 transition-colors">
                                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center">
                                    ${profileImage}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">${user.username}</div>
                                </div>
                            </a>
                        `;
                    }).join('');

                    $searchResults
                        .html(html)
                        .removeClass('hidden');
                },
                error: function(xhr, status, error) {
                    console.error('Search error:', error);
                }
            });
        }, 300);
    });

    // Close search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#searchInput, #searchResults').length) {
            $searchResults.addClass('hidden');
        }
    });
});
