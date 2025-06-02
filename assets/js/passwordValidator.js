class PasswordValidator {
    constructor(inputId) {
        this.input = document.getElementById(inputId);
        this.setupValidator();
    }

    setupValidator() {
        // Create wrapper for the input and feedback
        const wrapper = document.createElement('div');
        wrapper.className = 'relative';
        
        // Create feedback element
        const feedbackEl = document.createElement('div');
        feedbackEl.className = 'mt-1 text-sm hidden';
        
        // Insert elements into DOM
        this.input.parentNode.insertBefore(wrapper, this.input);
        wrapper.appendChild(this.input);
        wrapper.appendChild(feedbackEl);

        this.setupEventListeners(feedbackEl);
    }

    setupEventListeners(feedbackEl) {
        let debounceTimer;

        this.input.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            const password = this.input.value;

            if (password.length === 0) {
                feedbackEl.className = 'mt-1 text-sm hidden';
                return;
            }

            debounceTimer = setTimeout(() => {
                this.validatePassword(password, feedbackEl);
            }, 500);
        });
    }

    async validatePassword(password, feedbackEl) {
        try {
            const response = await fetch('/Socicuos/ajax/validatePassword.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `password=${encodeURIComponent(password)}`
            });

            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            
            if (data.isValid) {
                this.updateFeedback(feedbackEl, 'Password is good', 'green');
                return;
            }

            // Show the first error message
            if (data.errors && data.errors.length > 0) {
                this.updateFeedback(feedbackEl, data.errors[0], 'red');
            }
        } catch (error) {
            console.error('Error:', error);
            this.updateFeedback(feedbackEl, 'Error checking password', 'red');
        }
    }

    updateFeedback(element, message, color) {
        element.className = `mt-1 text-sm text-${color}-600`;
        element.textContent = message;
    }
}
