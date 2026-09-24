<?php 
require_once '../config.php'; 

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Mentions Légales</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body class="bg">

    <?php include 'composants_client/navbar.php'; ?>

    <main class="section" style="padding-top: 10rem;">
        <div class="container">

            <!-- Mot décoratif en arrière-plan -->
            <div class="outline-text" style="position:absolute; left:2rem; top:8rem; font-size:10vw; font-family:Georgia,serif; opacity:0.05; pointer-events:none; user-select:none;">
                LEGAL
            </div>

            <!-- Titre -->
            <div class="relative mb-3" style="max-width: 900px; margin-left: auto; margin-right: auto;">
                <h2>MENTIONS <span class="title-accent">LÉGALES</span></h2>
                <div class="title-bar" style="width: 8rem;"></div>
            </div>

            <!-- Cartes -->
            <div class="legal-grid">

                <div class="legal-card reveal-up" data-delay="0">
                    <div class="legal-head">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="legal-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"/>
                        </svg>
                        <h3>1. ÉDITION DU SITE</h3>
                    </div>
                    <p class="legal-text">
                        Le site internet <strong>SuperCar-Web</strong> est développé dans un cadre académique et professionnel par l'équipe de développement SLAM :
                    </p>
                    <ul class="legal-list">
                        <li>RANDRIA-MANALINA Koloina Magalie</li>
                        <li>RANDRIANTAHIRY Miantsasoa</li>
                        <li>RAKOTONIAINA Finoana Safidisoa</li>
                    </ul>
                    <p class="legal-footer">
                        Étudiantes en BTS SIO (Solutions Logicielles et Applications Métiers) à la MCCI Business School (Ébène, Île Maurice).
                    </p>
                </div>

                <div class="legal-card reveal-up" data-delay="150">
                    <div class="legal-head">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="legal-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3V6.75a3 3 0 013-3h13.5a3 3 0 013 3v4.5a3 3 0 01-3 3m-13.5 0v3.75a3 3 0 003 3h7.5a3 3 0 003-3v-3.75M6.75 18v2.25M17.25 18v2.25"/>
                        </svg>
                        <h3>2. HÉBERGEMENT</h3>
                    </div>
                    <p class="legal-text">
                        Dans le cadre de cette réalisation applicative, l'application est configurée pour s'exécuter sur une infrastructure locale contrôlée :
                    </p>
                    <p class="legal-text">
                        <strong>Environnement :</strong> Serveur Web local (Architecture Apache / MySQL / PHP).
                    </p>
                    <p class="legal-footer">
                        Simulation de déploiement réseau pour les évaluations du BTS SIO Session 2027.
                    </p>
                </div>

                <div class="legal-card legal-card-wide reveal-up" data-delay="300">
                    <div class="legal-head">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="legal-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
                        </svg>
                        <h3>3. PROTECTION DES DONNÉES</h3>
                    </div>
                    <p class="legal-text">
                        Conformément aux réglementations relatives à la protection des données personnelles (notamment la <strong>Data Protection Act</strong>), les informations saisies par les utilisateurs dans les formulaires de <strong>Demande d'Essai</strong> et de <strong>Contact</strong> sont traitées de manière strictement confidentielle.
                    </p>
                    <p class="legal-text">
                        Ces données collectées dynamiquement en base de données possèdent pour unique finalité la gestion administrative et la simulation de la relation client de la concession de luxe SuperCar. Elles ne font l'objet d'aucun transfert ni d'aucune cession commerciale à des tiers.
                    </p>
                </div>

            </div>
        </div>
    </main>

    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const delay = entry.target.getAttribute('data-delay') || 0;
                    setTimeout(() => {
                        entry.target.classList.add('reveal-active');
                    }, delay);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.reveal-up').forEach(el => observer.observe(el));
    </script>

    <?php include 'composants_client/footer.php'; ?>