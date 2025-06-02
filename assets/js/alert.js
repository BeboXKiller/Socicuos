document.addEventListener('DOMContentLoaded', function() {
    const initAlert = (alertElement) => {
        if (!alertElement) return;
        
        // Set a timeout to fade out the alert
        setTimeout(() => {
            // Add opacity-0 to trigger fade out
            alertElement.style.opacity = '0';
            
            // Remove the element after animation completes
            setTimeout(() => {
                if (alertElement.parentElement) {
                    alertElement.parentElement.removeChild(alertElement);
                }
            }, 500); // Match this with the CSS transition duration
        }, 2000); // Show alert for 3 seconds
    };

    // Find all alerts and initialize them
    const alerts = document.querySelectorAll('.alert-message');
    alerts.forEach(initAlert);

    // Create a MutationObserver to watch for new alerts
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1 && node.classList.contains('alert-message')) {
                    initAlert(node);
                }
            });
        });
    });

    // Start observing the document body for new alerts
    observer.observe(document.body, { childList: true, subtree: true });
});