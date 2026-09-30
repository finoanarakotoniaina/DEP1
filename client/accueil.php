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
    <title>SuperCar | L'Élégance d'Occasion</title>
    <!-- Correct : cherche style.css au même niveau que votre fichier PHP/HTML -->
    <link rel="stylesheet" href="../style.css">
</head>

<body class="bg">

    <?php include 'composants_client/navbar.php'; ?>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-bg">
            <div class="hero-video-overlay"></div>
            <video autoplay muted loop playsinline>
                <source src="<?= ASSETS_URL ?>/video/mercedes.mp4" type="video/mp4">
            </video>
        </div>

        <div class="hero-content">
            <h1><?= getContent('hero_title') ?></h1>
            <p class="hero-desc"><?= e(getContent('hero_subtitle')) ?></p>

            <div class="flex flex-wrap gap-2">
                <a href="voiture.php?from=accueil" class="btn btn-primary">Découvrir la collection</a>
                <a href="contact.php" class="btn btn-primary">Nous contacter</a>
            </div>
        </div>
    </section>

    <!-- SÉLECTIONS -->
    <section class="section relative">
        <div class="outline-text" style="position:absolute; right:-5%; top:2rem; font-size:15vw; font-family:Georgia,serif; opacity:0.08; pointer-events:none; user-select:none;">
            GERMAN
        </div>

        <div class="container relative">
            <h2 class="mb-1">NOS <span class="title-accent">SÉLECTIONS</span></h2>
            <div style="width:8rem; height:4px; background:var(--crimson);"></div>

            <div class="flex flex-col gap-3 mt-3" style="max-width:800px; margin-left:auto; margin-right:auto;">

                <div class="brand-card reveal-right" data-delay="0">
                    <h3><?= e(getContent('brand_1_name')) ?></h3>
                    <p class="typewriter-text" data-text="<?= e(getContent('brand_1_desc')) ?>"></p>
                </div>

                <div class="brand-card reveal-left" data-delay="300">
                    <h3><?= e(getContent('brand_2_name')) ?></h3>
                    <p class="typewriter-text" data-text="<?= e(getContent('brand_2_desc')) ?>"></p>
                </div>

                <div class="brand-card reveal-right" data-delay="600">
                    <h3><?= e(getContent('brand_3_name')) ?></h3>
                    <p class="typewriter-text" data-text="<?= e(getContent('brand_3_desc')) ?>"></p>
                </div>

                <div class="brand-card reveal-right" data-delay="450">
                    <h3><?= e(getContent('brand_4_name')) ?></h3>
                    <p class="typewriter-text" data-text="<?= e(getContent('brand_4_desc')) ?>"></p>
                </div>

            </div>
        </div>
    </section>

    <!-- JS SPÉCIFIQUE ACCUEIL -->
    <script>
        // Animation reveal au scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const delay = entry.target.getAttribute('data-delay') || 0;
                    setTimeout(() => entry.target.classList.add('reveal-active'), delay);
                }
            });
        }, { threshold: 0.2 });

        document.querySelectorAll('.reveal-right, .reveal-left').forEach(el => observer.observe(el));

        // Effet machine à écrire au survol
        document.querySelectorAll('.brand-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                const p = this.querySelector('.typewriter-text');
                if (!p || p.dataset.typed) return;
                p.dataset.typed = "1";

                const text = p.getAttribute('data-text');
                let i = 0;
                function type() {
                    if (i < text.length) {
                        p.textContent += text.charAt(i);
                        i++;
                        setTimeout(type, 15);
                    }
                }
                type();
            });
        });
    </script>

    <?php include 'composants_client/footer.php'; ?>