<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politique de confidentialité - SuperCar</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

   <!-- Barre du haut -->
<div class="privacy-topbar">
    <a href="index.php" class="logo-mini">SUPER<span>CAR</span></a>
    <a href="accueil.php" class="back-link-inline">&larr; Retour au site</a>
</div>

    <!-- Contenu principal -->
    <main class="privacy-main">
        <article class="privacy-content container">

            <div class="privacy-intro">
                <span class="privacy-tag">Conformité &amp; Transparence</span>
                <h1>Politique de confidentialité</h1>
                <p class="privacy-subtitle">Dernière mise à jour : Mars 2026 | Cadre : Projet pédagogique BTS SIO</p>
            </div>

            <div class="privacy-warning">
                <strong>Avertissement important :</strong> Le site <strong>SuperCar</strong> est un projet
                étudiant fictif réalisé dans le cadre de l'épreuve E4 / SLAM du BTS SIO.
                Aucune exploitation commerciale réelle n'est effectuée.
            </div>

            <section class="privacy-section">
                <h2>1. Responsable du traitement</h2>
                <p>Le traitement des données est opéré dans un cadre strictement académique par l'équipe
                   de développement du projet SuperCar (MCCI Business School). Contact référent projet :
                   <span class="muted">contact@supercar-fictif.bts</span>.</p>
            </section>

            <section class="privacy-section">
                <h2>2. Données collectées</h2>
                <p>Dans le cadre de l'utilisation des formulaires de test (connexion, inscription, ou
                   simulation de réservation de véhicule), les données suivantes peuvent être stockées
                   en base de données (MySQL) :</p>
                <ul class="privacy-list">
                    <li>Nom / Prénom ou pseudonyme de test</li>
                    <li>Adresse e-mail (fictive ou de démonstration)</li>
                    <li>Mot de passe (stocké sous forme hachée avec <code>password_hash()</code>)</li>
                    <li>Données de simulation de réservation (modèle de supercar sélectionné, dates fictives)</li>
                </ul>
            </section>

            <section class="privacy-section">
                <h2>3. Finalité du traitement</h2>
                <p>Ces données servent exclusivement à :</p>
                <ul class="privacy-list">
                    <li>Démontrer l'authentification des utilisateurs (Session PHP)</li>
                    <li>Valider les opérations CRUD (Création, Lecture, Mise à jour, Suppression) sur la base de données relationnelle</li>
                    <li>Illustrer la gestion des rôles ou des favoris liés aux véhicules</li>
                </ul>
            </section>

            <section class="privacy-section">
                <h2>4. Destinataires des données</h2>
                <p>Les données ne font l'objet d'aucune transmission à des tiers, d'aucune revente, ni
                   d'aucun traitement marketing. Elles restent strictement sur l'environnement de
                   développement ou d'hébergement de test du projet.</p>
            </section>

            <section class="privacy-section">
                <h2>5. Cookies et sessions</h2>
                <p>Le site utilise un cookie de session PHP indispensable au maintien de la connexion
                   utilisateur. Aucun traceur publicitaire ou cookie tiers de mesure d'audience
                   (Google Analytics, Meta Pixel, etc.) n'est implanté.</p>
            </section>

            <section class="privacy-section">
                <h2>6. Vos droits (Informatique et Libertés / RGPD)</h2>
                <p>S'agissant d'un environnement de démonstration, vous pouvez à tout moment réinitialiser
                   ou demander la suppression de votre compte test directement via l'interface du profil
                   ou en purgeant la table SQL associée.</p>
            </section>

        </article>
    </main>

    <footer class="site-footer">
        <p>&copy; 2026 SuperCar - Projet BTS SIO SLAM. Tous droits réservés.</p>
    </footer>

</body>
</html>