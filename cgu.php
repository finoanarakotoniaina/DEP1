<?php
require_once __DIR__ . '/config.php';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SuperCar | CGU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Syncopate:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
    <style>
        body { background-color: var(--cosmos); color: white; font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-title { font-family: 'Syncopate', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between">

    <!-- Header minimaliste unifié -->
    <header class="p-6 border-b border-white/5 flex justify-between items-center max-w-7xl mx-auto w-full">
        <a href="<?= BASE_URL ?>/client/accueil.php" class="text-2xl font-title font-black italic uppercase tracking-tighter">
            SUPER<span style="color: var(--crimson)">CAR</span>
        </a>
        <button onclick="history.back()" class="text-[10px] font-bold uppercase tracking-[2px] text-gray-400 hover:text-white transition-all cursor-pointer">
            <i class="fa-solid fa-arrow-left mr-2"></i>Retour
        </button>
    </header>

    <!-- Contenu principal -->
    <main class="flex-1 max-w-4xl mx-auto px-6 py-16 w-full">
        <header class="mb-12 border-b border-white/5 pb-8">
            <h1 class="text-4xl font-title font-black italic uppercase tracking-tighter">
                CONDITIONS GÉNÉRALES <span style="color: var(--crimson)">D'UTILISATION</span>
            </h1>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[3px] mt-3">Date de dernière mise à jour : Septembre 2026</p>
        </header>

        <div class="space-y-6 text-sm text-gray-300 leading-relaxed">
            <section class="bg-white/[0.02] border border-white/5 p-8 rounded-3xl space-y-3">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-3">
                    <span class="text-[var(--crimson)] text-xs font-mono">01.</span> Préambule
                </h2>
                <p>Les présentes Conditions Générales d’Utilisation régissent l'accès, la consultation et l'utilisation du site web <strong>SuperCar</strong> (Côté Client et Espace Administration).</p>
            </section>

            <section class="bg-white/[0.02] border border-white/5 p-8 rounded-3xl space-y-3">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-3">
                    <span class="text-[var(--crimson)] text-xs font-mono">02.</span> Définition des Services
                </h2>
                <ul class="list-disc list-inside space-y-2 text-gray-400 text-xs pl-2">
                    <li><strong class="text-white">Consultation & Réservation</strong> : Catalogue véhicules et essais (<code class="text-gray-200">client/</code>).</li>
                    <li><strong class="text-white">Administration</strong> : Cockpit de gestion des leads, contacts et catalogue (<code class="text-gray-200">admin/</code>). Accès strictement restreint aux comptes habilités.</li>
                </ul>
            </section>

            <section class="bg-white/[0.02] border border-white/5 p-8 rounded-3xl space-y-3">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-3">
                    <span class="text-[var(--crimson)] text-xs font-mono">03.</span> Sécurité et Accès Admin
                </h2>
                <p>Tout usage abusif ou tentative d'intrusion non autorisée sur l'espace administration engage la responsabilité de l'auteur dans le cadre académique et légal.</p>
            </section>

            <section class="bg-white/[0.02] border border-white/5 p-8 rounded-3xl space-y-3">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-3">
                    <span class="text-[var(--crimson)] text-xs font-mono">04.</span> Données Personnelles (RGPD)
                </h2>
                <p>Les données collectées (formulaires contact, essais, gestion admin) sont traitées de manière strictement confidentielle.</p>
            </section>

            <section class="bg-white/[0.02] border border-white/5 p-8 rounded-3xl space-y-3">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-3">
                    <span class="text-[var(--crimson)] text-xs font-mono">05.</span> Propriété Intellectuelle
                </h2>
                <p>L'ensemble de la charte graphique (*cosmos/crimson*), des typographies et du code source de SuperCar est protégé.</p>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="p-6 border-t border-white/5 text-center text-[10px] text-gray-500 uppercase tracking-[2px]">
        &copy; 2026 SuperCar — Projet BTS SIO (MCCI Business School)
    </footer>

</body>
</html>