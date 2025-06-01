// Create new file: assets/js/usernameValidator.js
class UsernameValidator {
    constructor(inputId) {
        this.input = document.getElementById(inputId);
        this.setupValidator();
    }

    setupValidator() {
        const wrapper = document.createElement('div');
        wrapper.className = 'relative';
        
        const statusIcon = document.createElement('div');
        statusIcon.className = 'absolute right-3 top-1/2 transform -translate-y-1/2 hidden';
        
        this.input.parentNode.insertBefore(wrapper, this.input);
        wrapper.appendChild(this.input);
        wrapper.appendChild(statusIcon);

        const statusMessage = document.createElement('div');
        statusMessage.className = 'mt-1 text-sm';
        wrapper.parentNode.appendChild(statusMessage);

        this.setupEventListeners(statusIcon, statusMessage);
    }

    setupEventListeners(statusIcon, statusMessage) {
        let debounceTimer;

        this.input.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            const username = this.input.value;

            if(username.length < 3) {
                this.updateStatus(statusIcon, statusMessage, 'gray', this.xIcon, 
                    'Username must be at least 3 characters');
                return;
            }

            debounceTimer = setTimeout(() => {
                this.checkUsername(username, statusIcon, statusMessage);
            }, 500);
        });
    }

    async checkUsername(username, statusIcon, statusMessage) {
        try {
            const response = await fetch('/Socicuos/ajax/checkUsername.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `username=${encodeURIComponent(username)}`
            });

            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            this.updateStatus(
                statusIcon, 
                statusMessage,
                data.exists ? 'red' : 'green',
                data.exists ? this.xIcon : this.checkIcon,
                data.message
            );
        } catch (error) {
            console.error('Error:', error);
            this.updateStatus(statusIcon, statusMessage, 'red', this.xIcon, 
                'Error checking username availability');
        }
    }

    updateStatus(icon, message, color, iconSvg, text) {
        icon.className = `absolute right-3 top-1/2 transform -translate-y-1/2 text-${color}-600`;
        icon.innerHTML = iconSvg;
        message.className = `mt-1 text-sm text-${color}-600`;
        message.textContent = text;
    }

    get checkIcon() {
        return `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>`;
    }

    get xIcon() {
        return `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>`;
    }
}