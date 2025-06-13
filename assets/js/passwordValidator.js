$(document).ready(function() {
    class PasswordValidator {
        constructor(inputId) {
            this.$input = $(`#${inputId}`);
            this.setupValidator();
        }

        setupValidator() {
            // Create wrapper for the input and feedback
            this.$input.wrap('<div class="relative"></div>');
            this.$wrapper = this.$input.parent();
            this.$feedbackEl = $('<div>')
                .addClass('mt-1 text-sm hidden')
                .insertAfter(this.$input);

            this.setupEventListeners();
        }

        setupEventListeners() {
            let debounceTimer;

            this.$input.on('input', () => {
                clearTimeout(debounceTimer);
                const password = this.$input.val();

                if (password.length === 0) {
                    this.$feedbackEl.addClass('hidden');
                    return;
                }

                debounceTimer = setTimeout(() => {
                    this.validatePassword(password);
                }, 500);
            });
        }

        validatePassword(password) {
            $.ajax({
                url: '/Socicuos/ajax/validatePassword.php',
                method: 'POST',
                data: { password: password },
                success: (data) => {
                    if (data.isValid) {
                        this.updateFeedback('Password is good', 'green');
                        return;
                    }

                    // Show the first error message
                    if (data.errors && data.errors.length > 0) {
                        this.updateFeedback(data.errors[0], 'red');
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Error:', error);
                    this.updateFeedback('Error checking password', 'red');
                }
            });
        }

        updateFeedback(message, color) {
            this.$feedbackEl
                .removeClass('hidden')
                .attr('class', `mt-1 text-sm text-${color}-600`)
                .text(message);
        }
    }

    // Initialize validation for password input
    new PasswordValidator('password');
});
