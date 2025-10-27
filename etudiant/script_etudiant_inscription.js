 document.addEventListener('DOMContentLoaded', function() {
        // Variables pour la navigation par étapes
        const steps = document.querySelectorAll('.form-step');
        const stepIndicators = document.querySelectorAll('.step');
        let currentStep = 0;
        
        // Éléments de navigation
        const next1 = document.getElementById('next1');
        const next2 = document.getElementById('next2');
        const prev2 = document.getElementById('prev2');
        const prev3 = document.getElementById('prev3');
        
        // Fonction pour afficher une étape
        function showStep(stepIndex) {
            steps.forEach((step, index) => {
                step.classList.toggle('active', index === stepIndex);
            });
            
            stepIndicators.forEach((indicator, index) => {
                indicator.classList.toggle('active', index <= stepIndex);
            });
            
            currentStep = stepIndex;
        }
        
        // Navigation entre les étapes
        next1.addEventListener('click', function() {
            // Validation de l'étape 1
            const nom = document.querySelector('input[name="nom"]').value;
            const prenom = document.querySelector('input[name="prenom"]').value;
            const email = document.querySelector('input[name="email"]').value;
            const dateNaissance = document.querySelector('input[name="date_naissance"]').value;
            const sexe = document.querySelector('select[name="sexe"]').value;
            
            if(nom && prenom && email && dateNaissance && sexe) {
                showStep(1);
            } else {
                alert('Veuillez remplir tous les champs obligatoires de cette étape.');
            }
        });
        
        next2.addEventListener('click', function() {
            // Validation de l'étape 2
            const contact = document.querySelector('input[name="contact"]').value;
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('#confirmPassword').value;
            
            if(contact && password && confirmPassword) {
                if(password === confirmPassword) {
                    // Mettre à jour le récapitulatif
                    document.getElementById('summaryNom').textContent = document.querySelector('input[name="nom"]').value;
                    document.getElementById('summaryPrenom').textContent = document.querySelector('input[name="prenom"]').value;
                    document.getElementById('summaryEmail').textContent = document.querySelector('input[name="email"]').value;
                    document.getElementById('summaryDateNaissance').textContent = document.querySelector('input[name="date_naissance"]').value;
                    document.getElementById('summarySexe').textContent = document.querySelector('select[name="sexe"]').value === 'M' ? 'Homme' : 'Femme';
                    document.getElementById('summaryContact').textContent = contact;
                    
                    showStep(2);
                } else {
                    document.getElementById('passwordError').style.display = 'block';
                    document.getElementById('confirmPassword').classList.add('is-invalid');
                }
            } else {
                alert('Veuillez remplir tous les champs obligatoires de cette étape.');
            }
        });
        
        prev2.addEventListener('click', function() {
            showStep(0);
        });
        
        prev3.addEventListener('click', function() {
            showStep(1);
        });
        
        // Fonctionnalité d'affichage/masquage du mot de passe
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
        
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const confirmEyeIcon = document.getElementById('confirmEyeIcon');
        
        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            confirmEyeIcon.classList.toggle('fa-eye');
            confirmEyeIcon.classList.toggle('fa-eye-slash');
        });
        
        // Validation en temps réel de la correspondance des mots de passe
        confirmPasswordInput.addEventListener('input', function() {
            if(passwordInput.value !== confirmPasswordInput.value) {
                document.getElementById('passwordError').style.display = 'block';
                confirmPasswordInput.classList.add('is-invalid');
            } else {
                document.getElementById('passwordError').style.display = 'none';
                confirmPasswordInput.classList.remove('is-invalid');
            }
        });
        
        // Validation du formulaire final
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const termsCheck = document.getElementById('termsCheck');
            if(!termsCheck.checked) {
                e.preventDefault();
                alert('Veuillez accepter les conditions d\'utilisation pour finaliser votre inscription.');
            }
        });
    });