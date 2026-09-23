<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politique de confidentialité - SuperCar</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen flex flex-col">

    <!-- Header / Navigation simple -->
    <header class="bg-gray-900 border-b border-gray-800">
        <div class="max-w-4xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="text-xl font-extrabold tracking-wider text-white">SUPER<span class="text-red-500">CAR</span></a>
            <a href="index.php" class="text-sm text-gray-400 hover:text-white transition">&larr; Retour au site</a>
        </div>
    </header>

    <!-- Contenu Principal -->
    <main class="flex-grow max-w-4xl mx-auto px-4 py-12">
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8 md:p-12 shadow-2xl space-y-8">
            
            <div>
                <span class="text-xs uppercase tracking-widest text-red-500 font-semibold">Conformité & Transparence</span>
                <h1 class="text-3xl font-bold mt-2 text-white">Politique de confidentialité</h1>
                <p class="text-sm text-gray-400 mt-2">Dernière mise à jour : Mars 2026 | Cadre : Projet pédagogique BTS SIO</p>
            </div>

            <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg text-sm text-red-200">
                <strong>Avertissement important :</strong> Le site <strong>SuperCar</strong> est un projet étudiant fictif réalisé dans le cadre de l'épreuve E4 / SLAM du BTS SIO. Aucune exploitation commerciale réelle n'est effectuée.
            </div>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-white">1. Responsable du traitement</h2>
                <p class="text-gray-300 text-sm leading-relaxed">
                    Le traitement des données est opéré dans un cadre strictement académique par l'équipe de développement du projet SuperCar (MCCI Business School). Contact référent projet : <span class="text-gray-400">contact@supercar-fictif.bts</span>.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-white">2. Données collectées</h2>
                <p class="text-gray-300 text-sm leading-relaxed">
                    Dans le cadre de l'utilisation des formulaires de test (connexion, inscription, ou simulation de réservation de véhicule), les données suivantes peuvent être stockées en base de données (MySQL) :
                </p>
                <ul class="list-disc list-inside text-gray-300 text-sm space-y-1 pl-2">
                    <li>Nom / Prénom ou pseudonyme de test</li>
                    <li>Adresse e-mail (fictive ou de démonstration)</li>
                    <li>Mot de passe (stocké sous forme hachée avec <code>password_hash()</code>)</li>
                    <li>Données de simulation de réservation (modèle de supercar sélectionné, dates fictives)</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-white">3.Finalité du traitement</h2>
                <p class="text-gray-300 text-sm leading-relaxed">
                    Ces données servent exclusivement à :
                </p>
                <ul class="list-disc list-inside text-gray-300 text-sm space-y-1 pl-2">
                    <li>Démontrer l'authentification des utilisateurs (Session PHP)</li>
                    <li>Valider les opérations CRUD (Création, Lecture, Mise à jour, Suppression) sur la base de données relationnelle</li>
                    <li>Illustrer la gestion des rôles ou des favoris liés aux véhicules</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-white">4. Destinataires des données</h2>
                <p class="text-gray-300 text-sm leading-relaxed">
                    Les données ne font l'objet d'aucune transmission à des tiers, d'aucune revente, ni d'aucun traitement marketing. Elles restent strictement sur l'environnement de développement ou d'hébergement de test du projet.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-white">5. Cookies et sessions</h2>
                <p class="text-gray-300 text-sm leading-relaxed">
                    Le site utilise un cookie de session PHP indispensable au maintien de la connexion utilisateur. Aucun traceur publicitaire ou cookie tiers de mesure d'audience (Google Analytics, Meta Pixel, etc.) n'est implanté.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-white">6. Vos droits (Informatique et Libertés / RGPD)</h2>
                <p class="text-gray-300 text-sm leading-relaxed">
                    S'agissant d'un environnement de démonstration, vous pouvez à tout moment réinitialiser ou demander la suppression de votre compte test directement via l'interface du profil ou en purgeant la table SQL associée.
                </p>
            </section>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 border-t border-gray-800 py-6 text-center text-xs text-gray-500">
        <p>&copy; 2026 SuperCar - Projet BTS SIO SLAM. Tous droits réservés.</p>
    </footer>

</body>
</html>