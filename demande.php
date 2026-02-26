<?php
session_start();
// On vérifie si l'utilisateur est connecté pour afficher ou non la fenêtre
$isLoggedIn = isset($_SESSION['user_id']); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | L'Inévitable</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --crimson: #C1121F;
        }
        .modal-blur {
             --cosmos: #003049;
            backdrop-filter: blur(10px);
        }
        /* Style pour l'icône calendrier/heure blanche */
        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="time"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }
    </style>
</head>
<body class="split-bg text-white bg-[#0a0a0a]">

    <div id="authModal" class="<?php echo $isLoggedIn ? 'hidden' : ''; ?> fixed inset-0 z-[100] flex items-center justify-center modal-blur px-6">
        <div class="bg-[#0a0a0a] border border-white/10 p-10 rounded-[20px] max-w-md w-full text-center relative shadow-2xl">
            <button onclick="closeModal()" class="absolute top-5 right-5 text-gray-500 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h2 class="font-title text-2xl mb-4 uppercase tracking-tighter">Accès Réservé</h2>
            <p class="text-gray-400 text-sm mb-8 leading-relaxed">
                Pour réserver un essai exclusif, veuillez vous identifier ou rejoindre le cercle SuperCar.
            </p>
            <div class="flex flex-col gap-4">
                <a href="connexion.php" class="bg-[var(--crimson)] py-4 font-title text-[10px] tracking-[0.2em] uppercase hover:bg-white hover:text-black transition-all text-center">Connexion</a>
                <a href="inscription.php" class="border border-white/20 py-4 font-title text-[10px] tracking-[0.2em] uppercase hover:border-[var(--crimson)] hover:text-[var(--crimson)] transition-all text-center">Inscription</a>
            </div>
        </div>
    </div>

    <nav class="fixed top-0 w-full z-50 px-6 lg:px-10 py-6 flex justify-between items-center backdrop-blur-md border-b border-white/5">
        <div class="font-title text-xl tracking-tighter">SUPER<span class="text-[var(--crimson)]">CAR</span></div>
        <ul class="hidden md:flex gap-8 font-title text-[10px] tracking-[0.2em] uppercase items-center">
            <li><a href="accueil.php" class="nav-link">Accueil</a></li>
            <li><a href="voiture.php" class="nav-link">Voitures</a></li>
            <li><a href="demande.php" class="nav-link">Essai</a></li>
            <li><a href="services.php" class="nav-link">Services</a></li>
            <li><a href="contact.php" class="nav-link">Contact</a></li>
            <li><a href="connexion.php" class="nav-link active">Connexion</a></li>
        </ul>
    </nav>

    <section class="relative h-screen flex items-center px-6 lg:px-24 overflow-hidden">
        <div class="z-10 max-w-4xl relative">
            <span class="text-gray-500 font-bold tracking-[0.5em] uppercase mb-4 block text-[10px]">Édition Limitée</span>
            <h1 class="font-title text-5xl md:text-7xl font-bold leading-none mb-8 uppercase">
                DEMANDE <br> <span class="text-[var(--crimson)]">D'ESSAI.</span>
            </h1>
            <p class="text-xl text-gray-400 font-light max-w-2xl leading-relaxed mb-10">
                Une ingénierie qui défie les lois de la physique. Ne vous contentez pas de conduire, fusionnez avec la route.
            </p>
            <a href="#booking" class="inline-flex items-center gap-8 group">
                <div class="w-16 h-16 rounded-full border border-white/20 flex items-center justify-center group-hover:border-[var(--crimson)] transition-all duration-500">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="group-hover:text-[var(--crimson)] transform group-hover:translate-y-1 transition-all">
                        <path d="M7 13l5 5 5-5M7 6l5 5 5-5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <span class="font-title text-[10px] tracking-[0.2em] group-hover:text-[var(--crimson)] transition-colors uppercase">Défiler pour réserver</span>
            </a>
        </div>
        <div class="absolute inset-y-0 right-0 w-full md:w-1/2 z-0">
            <img src="photo_essai.jpeg" alt="Essai" class="w-full h-full object-cover grayscale opacity-30 [mask-image:linear-gradient(to_left,black_40%,transparent_100%)]">
        </div>
    </section>

    <section id="booking" class="min-h-screen pt-10 pb-24 px-6 lg:px-24 flex flex-col justify-center relative border-t border-white/5">
        <div class="absolute left-0 top-0 h-full w-24 border-r border-white/5 flex items-center justify-center">
            <span class="vertical-text tracking-[1em] text-xs text-gray-600 font-bold uppercase">Reservation Form</span>
        </div>
        <div class="max-w-5xl mx-auto w-full grid lg:grid-cols-2 gap-20 items-start">
            <div class="space-y-12">
                <h2 class="font-title text-4xl uppercase">Configurateur <br> d'essai</h2>
                <div class="grid gap-8">
                    <div class="flex items-start gap-4">
                        <span class="text-[var(--crimson)] font-title text-2xl">01</span>
                        <div><h3 class="font-bold text-lg uppercase">Identité</h3><p class="text-gray-500 text-sm">Vos informations.</p></div>
                    </div>
                </div>
            </div>

            <form id="mainForm" action="traitement_demande.php" method="POST" class="space-y-8 bg-white/5 p-10 rounded-[40px] border border-white/10 backdrop-blur-sm">
                <div class="grid grid-cols-2 gap-6">
                    <input type="text" name="nom" placeholder="NOM" required class="bg-transparent border-b border-white/20 p-4 text-xs focus:border-[var(--crimson)] outline-none uppercase">
                    <input type="text" name="prenom" placeholder="PRÉNOM" required class="bg-transparent border-b border-white/20 p-4 text-xs focus:border-[var(--crimson)] outline-none uppercase">
                </div>

                <input type="email" name="email" placeholder="VOTRE@EMAIL.COM" required class="w-full bg-transparent border-b border-white/20 p-4 text-xs focus:border-[var(--crimson)] outline-none">

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 font-bold uppercase">Modèle</label>
                        <select name="modele" class="bg-transparent border-b border-white/20 w-full p-4 text-xs outline-none focus:border-[var(--crimson)] cursor-pointer">
                            <option value="GT-CRIMSON" class="bg-black">GT-CRIMSON</option>
                            <option value="SF90-STRADALE" class="bg-black">SF90 STRADALE</option>
                            <option value="LAFERRARI" class="bg-black">LAFERRARI</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 font-bold uppercase">Mobile</label>
                        <input type="tel" name="mobile" placeholder="N° MOBILE" required class="bg-transparent border-b border-white/20 w-full p-4 text-xs focus:border-[var(--crimson)] outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 font-bold uppercase">Date préférée</label>
                        <input type="date" name="date_essai" id="d" required class="bg-transparent border border-white/10 rounded-lg w-full p-3 text-xs">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 font-bold uppercase">Créneau (08h-17h)</label>
                        <input type="time" name="heure_essai" min="08:00" max="17:00" required class="bg-transparent border border-white/10 rounded-lg w-full p-3 text-xs">
                    </div>
                </div>

                <button type="submit" class="w-full bg-[var(--crimson)] py-6 font-title text-xs tracking-[0.4em] uppercase hover:bg-white hover:text-black transition-all">Valider l'accès</button>
            </form>
        </div>
    </section>

    <script>
        const modal = document.getElementById('authModal');
        
        function closeModal() {
            modal.classList.add('hidden');
        }

        // Bloquer les dates passées
        const dateIn = document.getElementById('d');
        if(dateIn) {
            const today = new Date();
            today.setDate(today.getDate() + 1); // Interdire aujourd'hui, résa dès demain
            dateIn.min = today.toISOString().split('T')[0];
        }
    </script>
</body>
</html>