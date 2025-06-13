$(document).ready(function() {
  
    const initAlert = ($alertElement) => {
        if (!$alertElement.length) return;
        
        // Set a timeout to fade out the alert
        setTimeout(() => {
            // Fade out the alert
            $alertElement.fadeOut(500, function() {
                // Remove the element after fade out completes
                $(this).remove();
            });
        }, 2000); // Show alert for 2 seconds
    };

    // Find all alerts and initialize them
    $('.alert-message').each(function() {
        initAlert($(this));
    });

    // Create a MutationObserver to watch for new alerts
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1 && $(node).hasClass('alert-message')) {
                    initAlert($(node));
                }
            });
        });
    });

    // Start observing the document body for new alerts
    observer.observe(document.body, { childList: true, subtree: true });
  
});