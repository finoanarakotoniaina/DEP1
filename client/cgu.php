<?php
require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | CGU</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="bg">

    <?php include 'composants_client/navbar.php'; ?>

    <main class="section" style="padding-top: 10rem;">
        <div class="container">

            <!-- En-tête -->
            <header class="legal-header">
                <h2>CONDITIONS GÉNÉRALES <span class="title-accent">D'UTILISATION</span></h2>
                <div class="title-bar" style="width: 8rem;"></div>
                <p class="legal-update">Dernière mise à jour : Septembre 2026 — Édition intégrale</p>
            </header>

            <!-- Articles -->
            <div class="legal-articles">

                <article class="legal-article">
                    <h3><span class="legal-num">01.</span> Objet</h3>
                    <p>Les présentes Conditions Générales d'Utilisation (ci-après « CGU ») ont pour objet de définir les règles d'accès, de consultation, de navigation et d'utilisation de la plateforme web <strong>SuperCar</strong> (ci-après le « Site »), développée dans le cadre d'un projet technique et pédagogique BTS SIO (Services Informatiques aux Organisations) option SLAM au sein de la <em>MCCI Business School</em>.</p>
                </article>

                <article class="legal-article">
                    <h3><span class="legal-num">02.</span> Définitions</h3>
                    <ul class="legal-list">
                        <li><strong>Site</strong> : Désigne la plateforme web SuperCar (front-office client et back-office administratif).</li>
                        <li><strong>Utilisateur</strong> : Toute personne physique qui visite, consulte ou interagit avec le Site.</li>
                        <li><strong>Client</strong> : Utilisateur effectuant une demande de renseignements, de contact ou une réservation d'essai.</li>
                        <li><strong>Administrateur</strong> : Utilisateur habilité disposant d'identifiants d'accès au cockpit de gestion (<code>/admin</code>).</li>
                        <li><strong>Services</strong> : Ensemble des prestations de consultation de catalogue, formulaires interactifs et modules de réservation.</li>
                    </ul>
                </article>

                <article class="legal-article">
                    <h3><span class="legal-num">03.</span> Acceptation et Opposabilité</h3>
                    <p>L'utilisation de la plateforme SuperCar vaut acceptation pleine, entière et sans réserve des présentes CGU. L'opposition ou la non-adhésion à ces conditions implique l'interdiction immédiate d'utiliser les services interactifs (formulaires de contact, authentification, réservation d'essai).</p>
                </article>

                <article class="legal-article">
                    <h3><span class="legal-num">04.</span> Accès et Disponibilité des Services</h3>
                    <p>Le Site est accessible gratuitement en tout lieu à tout Utilisateur disposant d'un accès réseau. Les coûts afférents à la connexion (matériel, forfait internet) demeurent exclusivement à la charge de l'Utilisateur.</p>
                    <p>L'éditeur s'efforce d'assurer une accessibilité continue (24/7), mais se réserve le droit de suspendre l'accès (en raison de maintenances sur XAMPP, déploiements Git/CI-CD, ou incidents techniques) sans engagement de préavis ni indemnité.</p>
                </article>

                <article class="legal-article">
                    <h3><span class="legal-num">05.</span> Espace Membre, Authentification et Sécurité</h3>
                    <p>L'accès aux espaces restreints (connexion client, espace d'administration <code>/admin</code>) requiert la saisie d'identifiants valides et sécurisés (chiffrement des sessions/mots de passe).</p>
                    <p>L'Utilisateur/Administrateur est responsable de la confidentialité de ses codes d'accès. Toute action initiée depuis un compte authentifié est réputée effectuée par son titulaire légitime. En cas d'intrusion avérée ou de compromission, l'accès pourra être révoqué unilatéralement.</p>
                </article>

                <article class="legal-article">
                    <h3><span class="legal-num">06.</span> Conditions de Réservation d'Essai sur Route</h3>
                    <p>Le module de réservation (<code>demande.php</code> / table <code>demande_essai</code>) permet de planifier une session d'essai dynamique.</p>
                    <ul class="legal-list">
                        <li>La soumission engendre un statut initial <strong>« en attente »</strong> et ne constitue en aucun cas une confirmation définitive d'attribution de créneau.</li>
                        <li>L'essai effectif est conditionné par la validation explicite d'un administrateur, la présentation physique d'un permis de conduire valide, ainsi que la signature d'une décharge d'assurance/responsabilité civile sur site.</li>
                        <li>SuperCar se réserve le droit d'annuler ou reporter un créneau pour impératif de sécurité routière, indisponibilité mécanique ou intempéries.</li>
                    </ul>
                </article>

                <article class="legal-article">
                    <h3><span class="legal-num">07.</span> Propriété Intellectuelle</h3>
                    <p>L'intégralité des éléments composant le Site SuperCar — charte graphique <em>cosmos/crimson</em>, typographies, architecture de la base de données relationnelle (MySQL), scripts PHP/PDO, et assets visuels — est protégée par le droit d'auteur.</p>
                    <p>Toute reproduction, extraction ou rediffusion non autorisée du code source ou de l'identité visuelle est formellement prohibée, hors cadre d'évaluation pédagogique officiel BTS SIO.</p>
                </article>

                <article class="legal-article">
                    <h3><span class="legal-num">08.</span> Protection des Données Personnelles (RGPD)</h3>
                    <p>Les données collectées (formulaires de contact, gestion des leads, planification d'essais) font l'objet d'un traitement interne et strictement confidentiel.</p>
                    <ul class="legal-list">
                        <li><strong>Finalité</strong> : Traitement des demandes entrantes, gestion opérationnelle du parc de véhicules et suivi des prospects.</li>
                        <li><strong>Confidentialité</strong> : Aucune revente, location ou cession commerciale des données à des tiers n'est opérée.</li>
                        <li><strong>Droits des personnes</strong> : Droit d'accès, de rectification, de portabilité et d'effacement exerçable via le canal de contact de l'éditeur.</li>
                    </ul>
                </article>

                <article class="legal-article">
                    <h3><span class="legal-num">09.</span> Limitation de Responsabilité</h3>
                    <p>Le Site est fourni « en l'état ». L'éditeur exclut toute garantie implicite relative à l'adéquation du contenu à un usage particulier. La responsabilité de SuperCar ne saurait être engagée en cas de perte de données, d'interruption de réseau local, ou de mauvaise interprétation des spécifications techniques des véhicules présentés au catalogue.</p>
                </article>

                <article class="legal-article">
                    <h3><span class="legal-num">10.</span> Évolution des CGU</h3>
                    <p>L'éditeur se réserve la faculté de modifier à tout moment les termes des présentes CGU pour les aligner sur de nouvelles fonctionnalités back-office/front-office ou sur des contraintes légales. Les modifications prennent effet dès leur mise en ligne.</p>
                </article>

                <article class="legal-article">
                    <h3><span class="legal-num">11.</span> Loi Applicable et Litiges</h3>
                    <p>Les présentes CGU sont régies par le droit en vigueur dans le pays d'exploitation et par le cadre académique de référence de la <strong>MCCI Business School</strong>. En cas de différend, une résolution amiable sera privilégiée avant toute action.</p>
                </article>

            </div>
        </div>
    </main>

    <?php include 'composants_client/footer.php'; ?>