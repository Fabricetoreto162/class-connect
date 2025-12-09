<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Étudiant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            animation: backgroundPulse 8s ease-in-out infinite;
        }

        @keyframes backgroundPulse {
            0%, 100% { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
            50% { background: linear-gradient(135deg, #764ba2 0%, #667eea 100%); }
        }

        /* Particules flottantes */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 8s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% { 
                transform: translateY(0) rotate(0deg) scale(1);
                opacity: 0.7;
            }
            50% { 
                transform: translateY(-30px) rotate(180deg) scale(1.2);
                opacity: 1;
            }
        }

        .message-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 
                0 25px 50px rgba(0,0,0,0.2),
                0 0 0 1px rgba(255,255,255,0.2),
                0 0 60px rgba(220, 53, 69, 0.3);
            text-align: center;
            max-width: 550px;
            position: relative;
            overflow: hidden;
            animation: 
                containerEntrance 1s cubic-bezier(0.34, 1.56, 0.64, 1),
                containerGlow 4s ease-in-out infinite;
            border: 1px solid rgba(220, 53, 69, 0.1);
        }

        /* Animation d'entrée spectaculaire */
        @keyframes containerEntrance {
            0% { 
                opacity: 0;
                transform: 
                    scale(0.8) 
                    rotateX(45deg)
                    translateY(100px);
                filter: blur(10px);
            }
            70% { 
                opacity: 0.9;
                transform: 
                    scale(1.05) 
                    rotateX(0deg)
                    translateY(-10px);
            }
            100% { 
                opacity: 1;
                transform: 
                    scale(1) 
                    rotateX(0deg)
                    translateY(0);
                filter: blur(0);
            }
        }

        /* Pulsation subtile de la carte */
        @keyframes containerGlow {
            0%, 100% { 
                box-shadow: 
                    0 25px 50px rgba(0,0,0,0.2),
                    0 0 0 1px rgba(255,255,255,0.2),
                    0 0 60px rgba(220, 53, 69, 0.3);
            }
            50% { 
                box-shadow: 
                    0 30px 60px rgba(0,0,0,0.25),
                    0 0 0 1px rgba(255,255,255,0.3),
                    0 0 80px rgba(220, 53, 69, 0.5);
            }
        }

        .icon-wrapper {
            position: relative;
            margin-bottom: 2rem;
            animation: iconFloat 4s ease-in-out infinite;
        }

        @keyframes iconFloat {
            0%, 100% { 
                transform: translateY(0) scale(1) rotate(0deg);
            }
            33% { 
                transform: translateY(-8px) scale(1.05) rotate(2deg);
            }
            66% { 
                transform: translateY(-4px) scale(1.03) rotate(-1deg);
            }
        }

        .icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1rem;
            animation: 
                iconPulse 3s infinite ease-in-out;
            text-shadow: 
                0 0 30px rgba(220,53,69,0.6),
                0 0 60px rgba(220,53,69,0.3);
            position: relative;
            z-index: 2;
            display: inline-block;
        }

        /* Pulsation de l'icône */
        @keyframes iconPulse {
            0%, 100% { 
                transform: scale(1);
                text-shadow: 
                    0 0 30px rgba(220,53,69,0.6),
                    0 0 60px rgba(220,53,69,0.3);
            }
            50% { 
                transform: scale(1.15);
                text-shadow: 
                    0 0 40px rgba(220,53,69,0.8),
                    0 0 80px rgba(220,53,69,0.5),
                    0 0 120px rgba(220,53,69,0.2);
            }
        }

        h1 {
            color: #dc3545;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
            animation: 
                titleEntrance 1.2s ease-out 0.3s both,
                titleGlow 3s ease-in-out infinite;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        @keyframes titleEntrance {
            0% { 
                opacity: 0;
                transform: translateY(40px) scale(0.9);
                letter-spacing: 10px;
            }
            100% { 
                opacity: 1;
                transform: translateY(0) scale(1);
                letter-spacing: normal;
            }
        }

        @keyframes titleGlow {
            0%, 100% { 
                text-shadow: 0 2px 10px rgba(220,53,69,0.3);
            }
            50% { 
                text-shadow: 0 2px 20px rgba(220,53,69,0.6),
                            0 4px 30px rgba(220,53,69,0.3);
            }
        }

        p {
            color: #6c757d;
            line-height: 1.7;
            margin-bottom: 2rem;
            font-size: 1.2rem;
            animation: textReveal 1.5s ease-out 0.6s both;
            position: relative;
        }

        @keyframes textReveal {
            0% { 
                opacity: 0;
                transform: translateY(30px);
                filter: blur(5px);
            }
            100% { 
                opacity: 1;
                transform: translateY(0);
                filter: blur(0);
            }
        }

        /* Lignes d'énergie autour de la carte */
        .energy-line {
            position: absolute;
            background: linear-gradient(90deg, transparent, #dc3545, transparent);
            animation: energyFlow 3s linear infinite;
        }

        .energy-line.top {
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
        }

        .energy-line.bottom {
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
        }

        .energy-line.left {
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
        }

        .energy-line.right {
            right: 0;
            top: 0;
            bottom: 0;
            width: 2px;
        }

        @keyframes energyFlow {
            0% { 
                opacity: 0;
                transform: scaleX(0);
            }
            50% { 
                opacity: 1;
                transform: scaleX(1);
            }
            100% { 
                opacity: 0;
                transform: scaleX(0);
            }
        }

        /* Effet de particules autour de l'icône */
        .icon-particles {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 180px;
            height: 180px;
        }

        .icon-particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #dc3545;
            border-radius: 50%;
            animation: orbit 4s linear infinite;
        }

        @keyframes orbit {
            0% { 
                transform: 
                    rotate(0deg) 
                    translateX(70px) 
                    rotate(0deg);
                opacity: 1;
            }
            50% { 
                opacity: 0.5;
            }
            100% { 
                transform: 
                    rotate(360deg) 
                    translateX(70px) 
                    rotate(-360deg);
                opacity: 0;
            }
        }

        /* Animation d'attention pour le texte */
        @keyframes attentionPulse {
            0%, 100% { 
                transform: scale(1);
                color: #dc3545;
            }
            50% { 
                transform: scale(1.05);
                color: #ff6b7a;
            }
        }

        .attention {
            animation: attentionPulse 2s ease-in-out 3s 3;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .message-container {
                margin: 20px;
                padding: 2rem;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .icon {
                font-size: 4rem;
            }
            
            p {
                font-size: 1.1rem;
            }
        }

        /* Effet de brillance sur le container */
        .message-container::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent,
                rgba(255, 255, 255, 0.1),
                transparent
            );
            transform: rotate(45deg);
            animation: shine 6s infinite;
            pointer-events: none;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            80%, 100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }
    </style>
</head>
<body>
    <!-- Particules de fond -->
    <div class="particles" id="particles"></div>

    <div class="message-container">
        <!-- Lignes d'énergie animées -->
        <div class="energy-line top" style="animation-delay: 0s"></div>
        <div class="energy-line right" style="animation-delay: 0.5s"></div>
        <div class="energy-line bottom" style="animation-delay: 1s"></div>
        <div class="energy-line left" style="animation-delay: 1.5s"></div>

        <div class="icon-wrapper">
            <div class="icon">⚠️</div>
            <div class="icon-particles" id="iconParticles"></div>
        </div>
        
        <h1 class="attention">Inscription Incomplète</h1>

        <p>
            Vous n'êtes pas encore éligible à utiliser la plateforme.<br>
            Veuillez passer à l'administration pour payer les frais d'inscription.
        </p>
    </div>

    <script>
        // Génération des particules de fond
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 12;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Propriétés aléatoires
                const size = Math.random() * 15 + 5;
                const posX = Math.random() * 100;
                const posY = Math.random() * 100;
                const delay = Math.random() * 8;
                const duration = Math.random() * 8 + 6;
                
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${posX}%`;
                particle.style.top = `${posY}%`;
                particle.style.animationDelay = `${delay}s`;
                particle.style.animationDuration = `${duration}s`;
                
                particlesContainer.appendChild(particle);
            }
        }

        // Génération des particules autour de l'icône
        function createIconParticles() {
            const container = document.getElementById('iconParticles');
            const particleCount = 6;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'icon-particle';
                
                const delay = (i / particleCount) * 4;
                const duration = 3 + Math.random() * 2;
                particle.style.animationDelay = `${delay}s`;
                particle.style.animationDuration = `${duration}s`;
                
                container.appendChild(particle);
            }
        }

        // Effet d'attention répété
        function triggerAttention() {
            const title = document.querySelector('h1');
            title.classList.remove('attention');
            void title.offsetWidth; // Trigger reflow
            title.classList.add('attention');
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
            createIconParticles();
            
            // Répéter l'effet d'attention toutes les 8 secondes
            setInterval(triggerAttention, 8000);
        });
    </script>
</body>
</html>