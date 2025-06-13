$(document).ready(function () {
    const $feedContainer = $('#news-feed-container');
    const $loadingIndicator = $('#scroll-loading');
    const $endOfPostsIndicator = $('#end-of-posts');

    let isLoading = false;
    let hasMorePosts = true;
    let currentOffset = 10;
    const postsPerLoad = 5;
    const scrollThreshold = 200;

    function showLoading() {
        $loadingIndicator.html(`
            <div class="flex justify-center items-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-3 text-gray-600">Loading more posts...</span>
            </div>
        `).show();
    }

    function hideLoading() {
        $loadingIndicator.hide();
    }

    function showEndMessage() {
        $endOfPostsIndicator.html(`
            <div class="text-center py-8">
                <div class="text-gray-500">
                    <i class="fas fa-check-circle text-2xl mb-2"></i>
                    <p>You've reached the end of your feed</p>
                    <p class="text-sm mt-1">Check back later for new posts!</p>
                </div>
            </div>
        `).show();
    }

    function hideEndMessage() {
        $endOfPostsIndicator.hide();
    }

    function appendPosts(html) {
        const $temp = $('<div>').html(html);
        $temp.children().each(function (i, post) {
            $(post).css({
                opacity: 0,
                transform: 'translateY(20px)',
                transition: 'opacity 0.5s ease, transform 0.5s ease'
            });
            $feedContainer.append(post);
            setTimeout(() => {
                $(post).css({ opacity: 1, transform: 'translateY(0)' });
            }, i * 100);
        });

        if (typeof initializeLikeButtons === 'function') initializeLikeButtons();
        if (typeof initializePostActions === 'function') initializePostActions();

        $(document).trigger('newPostsLoaded');
    }

    function loadMorePosts() {
        if (isLoading || !hasMorePosts) return;

        isLoading = true;
        showLoading();

        $.ajax({
            url: `/Socicuos/Ajax/loadMorePosts.php`,
            method: 'GET',
            data: {
                offset: currentOffset,
                limit: postsPerLoad
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache'
            },
            success: function (data) {
                if (data.error) {
                    showError(data.error);
                    return;
                }

                if (data.html && $.trim(data.html) !== '') {
                    appendPosts(data.html);
                    currentOffset = data.nextOffset || (currentOffset + postsPerLoad);
                    hasMorePosts = data.hasMore !== false;
                } else {
                    hasMorePosts = false;
                }

                if (!hasMorePosts) {
                    showEndMessage();
                }
            },
            error: function () {
                showError('Failed to load more posts. Please try again.');
            },
            complete: function () {
                hideLoading();
                if (hasMorePosts) isLoading = false;
            }
        });
    }

    function showError(msg) {
        const $errorDiv = $(`
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mx-4 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>${msg}</span>
                    <button class="ml-auto" onclick="$(this).closest('div').remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `);

        $feedContainer.before($errorDiv);
        setTimeout(() => $errorDiv.fadeOut(() => $errorDiv.remove()), 5000);
    }

    function checkScroll() {
        const scrollTop = $(window).scrollTop();
        const windowHeight = $(window).height();
        const docHeight = $(document).height();
        const distanceFromBottom = docHeight - (scrollTop + windowHeight);

        if (distanceFromBottom <= scrollThreshold) {
            loadMorePosts();
        }
    }

    // Throttled scroll
    let scrollTimeout;
    $(window).on('scroll', function () {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(checkScroll, 100);
    });

    // Optional IntersectionObserver support (with fallback)
    const $sentinel = $('<div>', {
        id: 'scroll-sentinel',
        css: {
            height: '1px',
            position: 'absolute',
            bottom: '200px',
            width: '100%'
        }
    });
    $loadingIndicator.before($sentinel);

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting && !isLoading && hasMorePosts) {
                    loadMorePosts();
                }
            });
        }, { rootMargin: '200px', threshold: 0.1 });

        observer.observe($sentinel[0]);
    }

    // Initial state
    hideLoading();
    hideEndMessage();

    // Debug & public control
    window.debugInfiniteScroll = () => console.log({ isLoading, hasMorePosts, currentOffset, postsPerLoad });
    window.forceLoadMore = () => loadMorePosts();
    window.resetInfiniteScroll = () => {
        isLoading = false;
        hasMorePosts = true;
        currentOffset = 10;
        hideLoading();
        hideEndMessage();
        console.log('Infinite scroll reset');
    };
});
