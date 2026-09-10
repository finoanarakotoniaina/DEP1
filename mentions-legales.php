<?php 
include 'config.php'; 

// Petite fonction de sécurité pour l'affichage
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}





// Récupération dynamique du nom du fichier actuel pour le menu actif
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Mentions Légales</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    
    <style>
        .reveal-up { transform: translateY(50px); opacity: 0; transition: all 0.8s ease-out; }
        .reveal-active { transform: translateY(0); opacity: 1; }
        .desc-font { font-family: 'Times New Roman', serif; }
        .font-title { font-family: 'Syncopate', sans-serif; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Style pour l'état actif des liens de navigation côté client */
        .nav-link.active { color: var(--crimson); font-weight: 800; border-bottom: 2px solid var(--crimson); pb-1; }
    </style>
</head>

<body class="home-bg text-white min-h-screen flex flex-col justify-between">

    <nav class="fixed top-0 w-full z-50 px-6 lg:px-10 py-6 flex justify-between items-center backdrop-blur-md border-b border-white/5">
        <div class="font-title text-xl tracking-tighter">SUPER<span class="text-[var(--crimson)]">CAR</span></div>
        
        <ul class="hidden md:flex gap-8 font-title text-[10px] tracking-[0.2em] uppercase items-center">
            <li><a href="accueil.php" class="nav-link <?= ($currentPage == 'accueil.php') ? 'active' : '' ?>">Accueil</a></li>
            <li><a href="voiture.php" class="nav-link <?= ($currentPage == 'voiture.php') ? 'active' : '' ?>">Voitures</a></li>
            <li><a href="demande.php" class="nav-link <?= ($currentPage == 'demande.php') ? 'active' : '' ?>">Essai</a></li>
            <li><a href="services.php" class="nav-link <?= ($currentPage == 'services.php') ? 'active' : '' ?>">Services</a></li>
            <li><a href="contact.php" class="nav-link <?= ($currentPage == 'contact.php') ? 'active' : '' ?>">Contact</a></li>
            <li><a href="mentions_legales.php" class="nav-link <?= ($currentPage == 'mentions_legales.php') ? 'active' : '' ?>">Mentions Légales</a></li>

            <?php if (isset($_SESSION['id_client'])): ?>
                <li>
                    <a href="deconnexion.php" class="bg-[var(--crimson)] px-4 py-2 rounded text-white hover:bg-white hover:text-black transition-all duration-300 flex items-center gap-2">
                        <i class="fa-solid fa-door-open text-[18px]"></i> Déconnexion
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <strong>
                        <a href="connexion.php" class="nav-link flex items-center gap-2 <?= ($currentPage == 'connexion.php') ? 'active' : '' ?>">
                            <i class="fa-solid fa-user text-[var(--crimson)] text-[18px]"></i> Connexion
                        </a>
                    </strong>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <main class="relative pt-40 pb-24 px-6 lg:px-24 flex-grow">
        
        <div class="absolute left-10 top-24 outline-text font-title text-[10vw] opacity-5 select-none leading-none tracking-widest pointer-events-none">
            LEGAL
        </div>

        <div class="relative z-10 mb-16 max-w-5xl mx-auto">
            <h1 class="font-title text-4xl md:text-5xl mb-4 text-[var(--varden)]">
                MENTIONS <span class="text-[var(--crimson)]">LÉGALES</span>
            </h1>
            <div class="w-32 h-1 bg-[var(--crimson)]"></div>
        </div>

        <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
            
            <div class="p-8 bg-white/5 backdrop-blur-sm border border-white/5 reveal-up" data-delay="0">
                <div class="flex items-center gap-3 mb-4">
                    <i class="fa-solid fa-file-code text-[var(--crimson)] text-xl"></i>
                    <h3 class="font-title text-[var(--marble)] text-lg tracking-wider">1. ÉDITION DU SITE</h3>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed font-light mb-4">
                    Le site internet <strong class="text-white">SuperCar-Web</strong> est développé dans un cadre académique et professionnel par l'équipe de développement SLAM :
                </p>
                <ul class="space-y-2 text-sm font-medium tracking-wide text-gray-200 mb-4 pl-1">
                    <li><i class="fa-solid fa-chevron-right text-[var(--crimson)] text-[10px] mr-2"></i>RANDRIA-MANALINA Koloina Magalie</li>
                    <li><i class="fa-solid fa-chevron-right text-[var(--crimson)] text-[10px] mr-2"></i>RANDRIATAHIRY Miantsasoa</li>
                    <li><i class="fa-solid fa-chevron-right text-[var(--crimson)] text-[10px] mr-2"></i>RAKOTONIAINA Finoana Safidisoa</li>
                </ul>
                <p class="text-gray-400 text-xs leading-relaxed desc-font italic border-t border-white/5 pt-3">
                    Étudiantes en première année de BTS SIO (Solutions Logicielles et Applications Métiers) à la MCCI Business School (Ébène, Île Maurice).
                </p>
            </div>

            <div class="p-8 bg-white/5 backdrop-blur-sm border border-white/5 reveal-up" data-delay="150">
                <div class="flex items-center gap-3 mb-4">
                    <i class="fa-solid fa-server text-[var(--crimson)] text-xl"></i>
                    <h3 class="font-title text-[var(--marble)] text-lg tracking-wider">2. HÉBERGEMENT</h3>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed font-light mb-4">
                    Dans le cadre de cette réalisation applicative, l'application est configurée pour s'exécuter sur une infrastructure locale contrôlée :
                </p>
                <p class="text-sm text-gray-200 font-light mb-2">
                    <strong class="text-white">Environnement :</strong> Serveur Web local (Architecture Apache / MySQL / PHP).
                </p>
                <p class="text-gray-400 text-xs leading-relaxed desc-font italic border-t border-white/5 pt-3">
                    Simulation de déploiement réseau pour les évaluations du BTS SIO Session 2027.
                </p>
            </div>

            <div class="md:col-span-2 p-8 bg-white/5 backdrop-blur-sm border border-white/5 reveal-up" data-delay="300">
                <div class="flex items-center gap-3 mb-4">
                    <i class="fa-solid fa-shield-halved text-[var(--crimson)] text-xl"></i>
                    <h3 class="font-title text-[var(--marble)] text-lg tracking-wider">3. PROTECTION DES DONNÉES</h3>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed font-light mb-4">
                    Conformément aux réglementations relatives à la protection des données personnelles (notamment la <span class="text-white">Data Protection Act</span>), les informations saisies par les utilisateurs dans les formulaires de <strong class="text-white">Demande d'Essai</strong> et de <strong class="text-white">Contact</strong> sont traitées de manière strictement confidentielle.
                </p>
                <p class="text-gray-400 text-sm leading-relaxed font-light">
                    Ces données collectées dynamiquement en base de données possèdent pour unique finalité la gestion administrative et la simulation de la relation client de la concession de luxe SuperCar. Elles ne font l’objet d'aucun transfert ni d'aucune cession commerciale à des tiers.
                </p>
            </div>

        </div>
    </main>

    <footer class="py-10 text-center border-t border-white/5 text-gray-500 text-xs tracking-widest font-title">
        &copy; 2026 SUPERCAR MAURITIUS - TOUS DROITS RÉSERVÉS
    </footer>

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
</body>
</html>