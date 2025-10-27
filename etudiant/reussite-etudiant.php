
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Réussie - Class Connect</title>
    <link rel="stylesheet" href="../bootstrap-5.3.7/bootstrap-5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome\css\all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4cc9f0;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary) !important;
        }
        
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 100px 20px 60px;
        }
        
        .success-container {
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        
        .success-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            padding: 50px 40px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: fadeInUp 0.8s ease-out;
            position: relative;
            overflow: hidden;
        }
        
        .success-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--success), var(--accent));
        }
        
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 25px;
            animation: bounce 1.5s infinite alternate;
            position: relative;
        }
        
        .success-icon::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            background: rgba(40, 167, 69, 0.1);
            border-radius: 50%;
            z-index: -1;
        }
        
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background: var(--primary);
            opacity: 0.7;
            border-radius: 50%;
            animation: confettiFall 5s linear infinite;
        }
        
        .confetti:nth-child(2n) {
            background: var(--accent);
        }
        
        .confetti:nth-child(3n) {
            background: var(--success);
        }
        
        .welcome-title {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 2.2rem;
        }
        
        .welcome-text {
            color: #666;
            line-height: 1.6;
            margin-bottom: 25px;
            font-size: 1.1rem;
        }
        
        .highlight {
            color: var(--primary);
            font-weight: 600;
        }
        
        .user-info-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
            text-align: left;
            border-left: 4px solid var(--primary);
        }
        
        .info-title {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 500;
            color: #6c757d;
        }
        
        .info-value {
            font-weight: 600;
            color: var(--dark);
        }
        
        .next-steps {
            background: rgba(67, 97, 238, 0.05);
            border-radius: 15px;
            padding: 20px;
            margin: 25px 0;
            text-align: left;
        }
        
        .steps-title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .step-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        
        .step-number {
            background: var(--primary);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            margin-right: 12px;
            flex-shrink: 0;
        }
        
        .step-text {
            color: #666;
            line-height: 1.5;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        
        .btn-primary-custom {
            background: var(--primary);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-primary-custom:hover {
            background: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(67, 97, 238, 0.3);
            color: white;
        }
        
        .btn-outline-primary-custom {
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: white;
        }
        
        .btn-outline-primary-custom:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(67, 97, 238, 0.3);
        }
        
        .countdown {
            font-size: 14px;
            color: #888;
            margin-top: 20px;
            font-weight: 500;
        }
        
        .auto-redirect {
            background: rgba(67, 97, 238, 0.1);
            border-radius: 10px;
            padding: 12px 20px;
            margin-top: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: var(--primary);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes bounce {
            from {
                transform: translateY(0px);
            }
            to {
                transform: translateY(-10px);
            }
        }
        
        @keyframes confettiFall {
            0% {
                transform: translateY(-100px) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(500px) rotate(360deg);
                opacity: 0;
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .success-card {
                padding: 40px 25px;
            }
            
            .welcome-title {
                font-size: 1.8rem;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-primary-custom, 
            .btn-outline-primary-custom {
                width: 100%;
                max-width: 250px;
            }
        }
        
        @media (max-width: 576px) {
            .success-card {
                padding: 30px 20px;
            }
            
            .success-icon {
                font-size: 60px;
            }
            
            .user-info-card,
            .next-steps {
                padding: 20px 15px;
            }
            
            .info-item {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="../index.php">
            <i class="fas fa-graduation-cap me-2"></i>Class <span class="text-warning" style="font-family: cubic;">Connect</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="inscription-etudiant.php">
                        <i class="fas fa-user-plus me-1"></i> Inscription
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="connexion-etudiant.php">
                        <i class="fas fa-sign-in-alt me-1"></i> Connexion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Contenu principal -->
<div class="main-content">
    <div class="success-container">
        <div class="success-card">
            <!-- Confetti animation -->
            <div id="confetti-container"></div>
            
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1 class="welcome-title">Félicitations !</h1>
            <p class="welcome-text">
                Votre inscription sur <span class="highlight">Class Connect</span> a été effectuée avec succès. 
                Bienvenue dans notre communauté éducative !
            </p>
            
            
            
            <!-- Étapes suivantes -->
            <div class="next-steps">
                <div class="steps-title">
                    <i class="fas fa-list-ol me-2"></i>Prochaines étapes
                </div>
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div class="step-text">Connectez-vous à votre compte</div>
                </div>
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-text">Complétez votre profil étudiant</div>
                </div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-text">Consultez vos cours et emploi du temps</div>
                </div>
                <div class="step-item">
                    <div class="step-number">4</div>
                    <div class="step-text">Téléchargez l'application mobile (disponible bientôt)</div>
                </div>
            </div>
            
            <p class="welcome-text">
                Vous pouvez maintenant accéder à votre espace personnel et découvrir 
                toutes les fonctionnalités de <span class="highlight">Class Connect</span>.
            </p>
            
            <!-- Boutons d'action -->
            <div class="action-buttons">
                <a href="connexion-etudiant.php" class="btn-primary-custom">
                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter maintenant
                </a>
                <a href="../index.php" class="btn-outline-primary-custom">
                    <i class="fas fa-home me-2"></i>Retour à l'accueil
                </a>
            </div>
            
            <!-- Redirection automatique -->
            <div class="auto-redirect">
                <i class="fas fa-clock me-1"></i>
                <span>Redirection automatique dans </span>
                <span id="countdown">10</span>
                <span> secondes</span>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Créer des confettis
        createConfetti();
        
        // Compte à rebours pour la redirection automatique
        let countdown = 10;
        const countdownElement = document.getElementById('countdown');
        const countdownInterval = setInterval(function() {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                window.location.href = 'connexion-etudiant.php';
            }
        }, 1000);
        
        // Fonction pour créer des confettis
        function createConfetti() {
            const container = document.getElementById('confetti-container');
            const confettiCount = 50;
            
            for (let i = 0; i < confettiCount; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                
                // Position aléatoire
                const left = Math.random() * 100;
                const animationDelay = Math.random() * 5;
                const size = Math.random() * 10 + 5;
                
                confetti.style.left = `${left}%`;
                confetti.style.animationDelay = `${animationDelay}s`;
                confetti.style.width = `${size}px`;
                confetti.style.height = `${size}px`;
                
                container.appendChild(confetti);
            }
        }
        
       
    });
</script>
<script src="../bootstrap-5.3.7\bootstrap-5.3.7\dist\js\bootstrap.bundle.min.js"></script>

</body>
</html>