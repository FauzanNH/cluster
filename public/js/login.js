document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    
    if (togglePassword && passwordField) {
        togglePassword.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            // Toggle icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
    
    // Form validation
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            console.log('Form submit triggered');
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            let isValid = true;
            
            // Clear previous errors
            document.querySelectorAll('.error-message').forEach(el => el.remove());
            
            // Validate email
            if (!email) {
                showError('email', 'Email tidak boleh kosong');
                isValid = false;
            } else if (!isValidEmail(email)) {
                showError('email', 'Format email tidak valid');
                isValid = false;
            }
            
            // Validate password
            if (!password) {
                showError('password', 'Password tidak boleh kosong');
                isValid = false;
            }

            // Validasi reCAPTCHA
            const recaptchaError = document.getElementById('recaptcha-error');
            if (recaptchaError) {
                if (typeof grecaptcha === 'undefined' || !grecaptcha.getResponse) {
                    recaptchaError.textContent = 'reCAPTCHA belum siap. Silakan tunggu beberapa detik.';
                    recaptchaError.style.display = 'block';
                    isValid = false;
                } else if (grecaptcha.getResponse().length === 0) {
                    recaptchaError.textContent = 'Silakan centang reCAPTCHA terlebih dahulu.';
                    recaptchaError.style.display = 'block';
                    isValid = false;
                } else {
                    recaptchaError.textContent = '';
                    recaptchaError.style.display = 'none';
                }
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Check for server errors and show toast
    const serverErrors = document.querySelectorAll('.server-error');
    if (serverErrors.length > 0) {
        serverErrors.forEach(error => {
            showToast('error', 'Kesalahan Login', error.textContent);
        });
    }
    
    // Check for success messages
    const successMessages = document.querySelectorAll('.success-message');
    if (successMessages.length > 0) {
        successMessages.forEach(message => {
            showToast('success', 'Berhasil', message.textContent);
        });
    }
    
    // Helper functions
    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = 'red';
        errorDiv.style.fontSize = '12px';
        errorDiv.style.marginTop = '5px';
        errorDiv.textContent = message;
        
        field.parentNode.appendChild(errorDiv);
        field.classList.add('is-invalid');
    }
    
    function isValidEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
    
    function showToast(type, title, message) {
        // Create toast container if it doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container';
            document.body.appendChild(toastContainer);
        }
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        // Create toast content
        const icon = type === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check';
        toast.innerHTML = `
            <i class="fas ${icon} toast-icon"></i>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close">&times;</button>
        `;
        
        // Add toast to container
        toastContainer.appendChild(toast);
        
        // Add event listener for close button
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', function() {
            toast.style.animation = 'fadeOut 0.5s ease forwards';
            setTimeout(() => {
                toast.remove();
            }, 500);
        });
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    }
});
