<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | L'Élégance d'Occasion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
    <style>
        /* Animations pour les entrées fluides */
        .reveal-right { transform: translateX(100px); opacity: 0; transition: all 1s ease-out; }
        .reveal-left { transform: translateX(-100px); opacity: 0; transition: all 1s ease-out; }
        .reveal-active { transform: translateX(0); opacity: 1; }
        
        /* Police pour les descriptions */
        .desc-font { font-family: 'Times New Roman', serif; }
    </style>
</head>

<body class="home-bg">

    <nav class="fixed top-0 w-full z-50 px-6 lg:px-10 py-6 flex justify-between items-center backdrop-blur-md border-b border-white/5">
        <div class="font-title text-xl tracking-tighter">SUPER<span class="text-[var(--crimson)]">CAR</span></div>
        
        <ul class="hidden md:flex gap-8 font-title text-[10px] tracking-[0.2em] uppercase items-center">
            <li><a href="accueil.php" class="nav-link active">Accueil</a></li>
            <li><a href="voiture.php" class="nav-link">Voitures</a></li>
            <li><a href="demande.php" class="nav-link">Essai</a></li>
            <li><a href="services.php" class="nav-link">Services</a></li>
            <li><a href="contact.php" class="nav-link">Contact</a></li>

            <?php if (isset($_SESSION['id_client'])): ?>
                <li>
                    <a href="deconnexion.php" class="bg-[var(--crimson)] px-4 py-2 rounded text-white hover:bg-white hover:text-black transition-all duration-300">
                        Déconnexion
                    </a>
                </li>
            <?php else: ?>
                <li><a href="connexion.php" class="nav-link">Connexion</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <section class="relative h-screen w-full flex items-center px-6 lg:px-24 overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full z-0">
            <div class="absolute inset-0 video-overlay z-10"></div>
            <video autoplay muted loop playsinline class="w-full h-full object-cover">
                <source src="mercedes.mp4" type="video/mp4">
            </video>
        </div>

        <div class="relative z-20 w-full mt-20">
            <div class="max-w-3xl">
                <h1 class="font-title text-4xl md:text-7xl font-bold leading-none mb-8">
                    L'ÉLÉGANCE <br> <span class="text-[var(--crimson)]">CERTIFIEE.</span>
                </h1>
                <p class="text-lg text-gray-200 max-w-xl font-light leading-relaxed mb-10">
                    Votre partenaire de confiance à l'Île Maurice pour l'acquisition de véhicules d'exception importés d'Allemagne, du Japon et des USA.
                </p>
                <div class="flex flex-wrap gap-6">
                    <a href="voiture.php?from=accueil" class="bg-[var(--crimson)] px-8 py-4 font-title text-xs tracking-widest hover:bg-white hover:text-black transition-all">DÉCOUVRIR LE STOCK</a>
                    <a href="contact.php?from=accueil" class="border border-white/30 px-8 py-4 font-title text-xs tracking-widest hover:border-[var(--crimson)] transition-all">NOUS CONTACTER</a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-32 px-6 lg:px-24 relative overflow-hidden">
        <div class="absolute right-[-5%] top-10 outline-text font-title text-[15vw] opacity-10 select-none leading-none">
            GERMAN
        </div>
        
        <div class="relative z-10 mb-20">
            <h2 class="font-title text-4xl md:text-5xl mb-4 text-[var(--varden)]">NOS <span class="text-[var(--crimson)]">SÉLECTIONS</span></h2>
            <div class="w-32 h-1 bg-[var(--crimson)]"></div>
        </div>

        <div class="relative z-10 flex flex-col gap-12 max-w-3xl mx-auto">
            
            <div class="brand-card p-8 bg-white/5 backdrop-blur-sm reveal-right" data-delay="0">
                <h3 class="font-title text-[var(--marble)] text-2xl mb-4">BMW</h3>
                <p class="text-gray-300 text-lg leading-relaxed desc-font opacity-0 transition-opacity duration-300 typewriter-text" data-text="Bien que la marque se soit diversifiée, son cœur de métier reste la « driver’s car ». bmw se distingue par une ergonomie intérieure orientée vers le conducteur et une mise au point de châssis qui favorise la précision de conduite et l'agilité. contrairement à beaucoup de constructeurs, ils privilégient souvent une répartition des masses équilibrée et une direction communicative, cherchant à donner au conducteur l'impression de faire corps avec la machine, même sur des modèles familiaux."></p>
            </div>
            
            <div class="brand-card p-8 bg-white/5 backdrop-blur-sm reveal-left" data-delay="300">
                <h3 class="font-title text-[var(--marble)] text-2xl mb-4">MERCEDES</h3>
                <p class="text-gray-300 text-lg leading-relaxed desc-font opacity-0 transition-opacity duration-300 typewriter-text" data-text="Mercedes se positionne comme l'inventeur de l'automobile, et cela se ressent dans leur approche : tout est une question d'innovation au service du confort et de la sécurité. si bmw cherche la sportivité, mercedes cherche la sérénité. leur force réside dans une insonorisation de premier ordre, des suspensions souvent typées 'tapis volant' et une interface technologique (mbux) qui vise à réduire la fatigue au volant. c'est le choix de la rigueur et de la vie à bord apaisée."></p>
            </div>
            
            <div class="brand-card p-8 bg-white/5 backdrop-blur-sm reveal-right" data-delay="600">
                <h3 class="font-title text-[var(--marble)] text-2xl mb-4">PORSCHE</h3>
                <p class="text-gray-300 text-lg leading-relaxed desc-font opacity-0 transition-opacity duration-300 typewriter-text" data-text="Dire que porsche est juste « sportif » est un euphémisme. la marque est surtout une référence en ingénierie de précision. le tour de force de porsche est de proposer des voitures aux performances démoniaques (avec la 911 en icône absolue) qui restent paradoxalement utilisables au quotidien. leur fiabilité mécanique est souvent supérieure à la moyenne des voitures de sport, et ils excellent dans l'art de rendre une conduite complexe accessible et gratifiante, peu importe le niveau du pilote."></p>
            </div>
        </div>
    </section>

    <footer class="py-10 text-center border-t border-white/5 text-gray-500 text-xs tracking-widest font-title">
        &copy; 2024 SUPERCAR MAURITIUS - TOUS DROITS RÉSERVÉS
    </footer>

    <script>
        // 1. Animation d'entrée au scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const delay = entry.target.getAttribute('data-delay');
                    setTimeout(() => {
                        entry.target.classList.add('reveal-active');
                    }, delay);
                }
            });
        }, { threshold: 0.2 });

        document.querySelectorAll('.reveal-right, .reveal-left').forEach(el => observer.observe(el));

        // 2. Animation machine à écrire au survol
        document.querySelectorAll('.brand-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                const textElement = this.querySelector('.typewriter-text');
                // On ne lance l'animation que si elle n'a jamais été lancée
                if (!textElement.dataset.hasTyped) {
                    textElement.dataset.hasTyped = "true";
                    textElement.classList.remove('opacity-0');
                    textElement.classList.add('opacity-100');
                    
                    const text = textElement.getAttribute('data-text');
                    let i = 0;
                    function typing() {
                        if (i < text.length) {
                            textElement.textContent += text.charAt(i);
                            i++;
                            setTimeout(typing, 15); // Vitesse
                        }
                    }
                    typing();
                }
            });
        });
    </script>
</body>
</html>