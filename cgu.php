<?php
require_once __DIR__ . '/config.php';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SuperCar | CGU (Version Étendue)</title>
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
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[3px] mt-3">Date de dernière mise à jour : Septembre 2026 — Édition Intégrale</p>
        </header>

        <div class="space-y-6 text-sm text-gray-300 leading-relaxed">
            <!-- Article 1 -->
            <section class="bg-white/[0.02] border border-white/5 p-8 rounded-3xl space-y-3">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-3">
                    <span class="text-[var(--crimson)] text-xs font-mono">01.</span> Objet
                </h2>
                <p>Les présentes Conditions Générales d’Utilisation (ci-après « CGU ») ont pour objet de définir les règles d'accès, de consultation, de navigation et d'utilisation de la plateforme web <strong>SuperCar</strong> (ci-après le « Site »), développée dans le cadre d'un projet technique et pédagogique BTS SIO (Services Informatiques aux Organisations) option SLAM au sein de la <em>MCCI Business School</em>.</p>
            </section>

            <!-- Article 2 -->
            <section class="bg-white/[0.02] border border-white/5 p-8 rounded-3xl space-y-3">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-3">
                    <span class="text-[var(--crimson)] text-xs font-mono">02.</span> Définitions
                </h2>
                <ul class="list-disc list-inside space-y-2 text-gray-400 text-xs pl-2">
                    <li><strong class="text-white">Site</strong> : Désigne la plateforme web SuperCar (front-office client et back-office administratif).</li>
                    <li><strong class="text-white">Utilisateur</strong> : Toute personne physique qui visite, consulte ou interagit avec le Site.</li>
                    <li><strong class="text-white">Client</strong> : Utilisateur effectuant une demande de renseignements, de contact ou une réservation d'essai.</li>
                    <li><strong class="text-white">Administrateur</strong> : Utilisateur habilité disposant d'identifiants d'accès au cockpit de gestion (<code class="text-gray-200">/admin</code>).</li>
                    <li><strong class="text-white">Services</strong> : Ensemble des prestations de consultation de catalogue, formulaires interactifs et modules de réservation.</li>
                </ul>
            </section>

            <!-- Article 3 -->
            <section class="bg-white/[0.02] border border-white/5 p-8 rounded-3xl space-y-3">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-3">
                    <span class="text-[var(--crimson)] text-xs font-mono">03.</span> Acceptation et Opposabilité
                </h2>
                <p>L'utilisation de la plateforme SuperCar vaut acceptation pleine, entière et sans réserve des présentes CGU. L'opposition ou la non-adhésion à ces conditions implique l'interdiction immédiate d'utiliser les services interactifs (formulaires de contact, authentification, réservation d'essai).</p>
            </section>

            <!-- Article 4 -->
            <section class="bg-white/[0.02] border border-white/5 p-8 rounded-3xl space-y-3">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-3">
                    <span class="text-[var(--crimson)] text-xs font-mono">04.</span> Accès et Disponibilité des Services
                </h2>
                <p>Le Site est accessible gratuitement en tout lieu à tout Utilisateur disposant d'un accès réseau. Les coûts afférents à la connexion (matériel, forfait internet) demeurent exclusivement à la charge de l'Utilisateur.</p>
                <p class="text-xs text-gray-400 mt-2">L'éditeur s'efforce d'assurer une accessibilité continue (24/7), mais se réserve le droit de suspendre l'accès (en raison de maintenances sur XAMPP, déploiements Git/CI-CD, ou incidents techniques) sans engagement de préavis ni indemnité.</p>
            </section>

            <!-- Article 5 -->
            <section class="bg-white/[0.02] border border-white/5 p-8 rounded-3xl space-y-3">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-3">
                    <span class="text-[var(--crimson)] text-xs font-mono">05.</span> Espace Membre, Authentification et Sécurité
                </h2>
                <p>L'accès aux espaces restreints (connexion client, espace d'administration <code class="text-gray-200">/admin</code>) requiert la saisie d'identifiants valides et sécurisés (chiffrement des sessions/mots de passe).</p>
                <p class="text-xs text-gray-400 mt-2">L'Utilisateur/Administrateur est responsable de la confidentialité de ses codes d'accès. Toute action initiée depuis un compte authentifié est réputée effectuée par son titulaire légitime. En cas d'intrusion avérée ou de compromission, l'accès pourra être révoqué unilatéralement.</p>
            </section>

            <!-- Article 6 -->
            <section class="bg-white/[0.02] border border-white/5 p-8 rounded-3xl space-y-3">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-3">
                    <span class="text-[var(--crimson)] text-xs font-mono">06.</span> Conditions de Réservation d'Essai sur Route
                </h2>
                <p>Le module de réservation (<code class="text-gray-200">demande.php</code> / table <code class="text-gray-200">demande_essai</code>) permet de planifier une session d'essai dynamique.</p>
                <ul class="list-disc list-inside space-y-1.5 text-gray-400 text-xs pl-2 mt-2">
                    <li>La soumission engendre un statut initial <strong>« en attente »</strong> et ne constitue en aucun cas une confirmation définitive d'attribution de créneau.</li>
                    <li>L'essai effectif est conditionné par la validation explicite d'un administrateur, la présentation physique d'un permis de conduire valide, ainsi que la signature d'une décharge d'assurance/responsabilité civile sur site.</li>
                    <li>SuperCar se réserve le droit d'annuler ou reporter un créneau pour impératif de sécurité routière, indisponibilité mécanique ou intempéries.</li>
                </ul>
            </section>

            <!-- Article 7 -->
            <section class="bg-white/[0.02] border border-white/5 p-8 rounded-3xl space-y-3">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-3">
                    <span class="text-[var(--crimson)] text-xs font-mono">07.</span> Propriété Intellectuelle
                </h2>
                <p>L'intégralité des éléments composant le Site SuperCar — charte graphique <em>cosmos/crimson</em>, typographies <em>Syncopate</em> / <em>Plus Jakarta Sans</em>, architecture de la base de données relationnelle (MySQL), scripts PHP/PDO, et assets visuels — est protégée par le droit d'auteur.</p>
                <p class="text-xs text-gray-400 mt-2">Toute reproduction, extraction ou rediffusion non autorisée du code source ou de l'identité visuelle est formellement prohibée, hors cadre d'évaluation pédagogique officiel BTS SIO.</p>
            </section>

            <!-- Article 8 -->
            <section class="bg-white/[0.02] border border-white/5 p-8 rounded-3xl space-y-3">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-3">
                    <span class="text-[var(--crimson)] text-xs font-mono">08.</span> Protection des Données Personnelles (RGPD)
                </h2>
                <p>Les données collectées (formulaires de contact, gestion des leads, planification d'essais) font l'objet d'un traitement interne et strictement confidentiel.</p>
                <ul class="list-disc list-inside space-y-1.5 text-gray-400 text-xs pl-2 mt-2">
                    <li><strong>Finalité</strong> : Traitement des demandes entrantes, gestion opérationnelle du parc de véhicules et suivi des prospects.</li>
                    <li><strong>Confidentialité</strong> : Aucune revente, location ou cession commerciale des données à des tiers n'est opérée.</li>
                    <li><strong>Droits des personnes</strong> : Droit d'accès, de rectification, de portabilité et d'effacement exerçable via le canal de contact de l'éditeur.</li>
                </ul>
            </section>

            <!-- Article 9 -->
            <section class="bg-white/[0.02] border border-white/5 p-8 rounded-3xl space-y-3">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-3">
                    <span class="text-[var(--crimson)] text-xs font-mono">09.</span> Limitation de Responsabilité
                </h2>
                <p>Le Site est fourni « en l'état ». L'éditeur exclut toute garantie implicite relative à l'adéquation du contenu à un usage particulier. La responsabilité de SuperCar ne saurait être engagée en cas de perte de données, d'interruption de réseau local, ou de mauvaise interprétation des spécifications techniques des véhicules présentés au catalogue.</p>
            </section>

            <!-- Article 10 -->
            <section class="bg-white/[0.02] border border-white/5 p-8 rounded-3xl space-y-3">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-3">
                    <span class="text-[var(--crimson)] text-xs font-mono">10.</span> Évolution des CGU
                </h2>
                <p>L'éditeur se réserve la faculté de modifier à tout moment les termes des présentes CGU pour les aligner sur de nouvelles fonctionnalités back-office/front-office ou sur des contraintes légales. Les modifications prennent effet dès leur mise en ligne.</p>
            </section>

            <!-- Article 11 -->
            <section class="bg-white/[0.02] border border-white/5 p-8 rounded-3xl space-y-3">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-3">
                    <span class="text-[var(--crimson)] text-xs font-mono">11.</span> Loi Applicable et Litiges
                </h2>
                <p>Les présentes CGU sont régies par le droit en vigueur dans le pays d'exploitation et par le cadre académique de référence de la <strong>MCCI Business School</strong>. En cas de différend, une résolution amiable sera privilégiée avant toute action.</p>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="p-6 border-t border-white/5 text-center text-[10px] text-gray-500 uppercase tracking-[2px]">
        &copy; 2026 SuperCar — Projet BTS SIO (MCCI Business School)
    </footer>

</body>
</html>