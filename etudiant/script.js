
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const loginButton = document.getElementById('loginButton');
        const loginText = document.getElementById('loginText');
        const loadingSpinner = document.getElementById('loadingSpinner');
        
        // Fonctionnalité d'affichage/masquage du mot de passe
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
        
        
        
        // Vérification des champs en temps réel
        const emailInput = document.querySelector('input[name="email"]');
        const passwordField = document.getElementById('password');
        
        function validateForm() {
            const emailValid = emailInput.value.length > 0;
            const passwordValid = passwordField.value.length > 0;
            
            if (emailValid && passwordValid) {
                loginButton.disabled = false;
            } else {
                loginButton.disabled = true;
            }
        }
        
        emailInput.addEventListener('input', validateForm);
        passwordField.addEventListener('input', validateForm);
        
        // Validation initiale
        validateForm();
        
        // Effet de focus amélioré
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.classList.remove('focused');
                }
            });
            
            // Vérifier l'état initial
            if (input.value !== '') {
                input.parentElement.classList.add('focused');
            }
        });
        
        // Récupération des identifiants depuis le stockage local si "Se souvenir de moi" était coché
        const rememberMe = document.getElementById('rememberMe');
        const savedEmail = localStorage.getItem('savedEmail');
        
        if (savedEmail) {
            emailInput.value = savedEmail;
            rememberMe.checked = true;
            validateForm();
        }
        
        // Sauvegarde de l'email si "Se souvenir de moi" est coché
        rememberMe.addEventListener('change', function() {
            if (this.checked && emailInput.value) {
                localStorage.setItem('savedEmail', emailInput.value);
            } else {
                localStorage.removeItem('savedEmail');
            }
        });
    });
