<?php 
require_once '../config.php';

$services = [];
for ($i = 1; $i <= 4; $i++) {
    $services[$i] = [
        'img'   => getContent("service_{$i}_img"),
        'title' => getContent("service_{$i}_title"),
        'text'  => getContent("service_{$i}_text"),
    ];
}

$fallbacks = [
    1 => ['img' => 'vidange.webp',        'title' => 'Vidange',    'text' => 'Lubrification haute performance avec huiles synthétiques certifiées.'],
    2 => ['img' => 'pneux.jpg',           'title' => 'Pneus',      'text' => 'Contrôle de l\'usure et équilibrage de précision.'],
    3 => ['img' => 'testdiagnostic.webp', 'title' => 'Diagnostic', 'text' => 'Scan complet des calculateurs et mise à jour des systèmes embarqués.'],
    4 => ['img' => 'moteur.jpg',          'title' => 'Moteur',     'text' => 'Calage et réglages fins de la distribution.'],
];

foreach ($services as $i => $s) {
    if (empty($s['img']))   $services[$i]['img']   = $fallbacks[$i]['img'];
    if (empty($s['title'])) $services[$i]['title'] = $fallbacks[$i]['title'];
    if (empty($s['text']))  $services[$i]['text']  = $fallbacks[$i]['text'];
}

$heroTitle = getContent('serv_hero_title') ?: 'Domptez <span class="title-accent">l\'instinct.</span>';
$heroDesc  = getContent('serv_hero_desc')  ?: 'Nos services de maintenance garantissent que chaque kilomètre reste une expérience pure et sans compromis.';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Expertise Technique</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body class="bg">

    <?php include 'composants_client/navbar.php'; ?>

    <!-- HERO -->
    <section class="hero" style="min-height: 80vh;">
        <div class="hero-content">
            <p class="hero-subtitle">Ingénierie de pointe</p>
            <h1><?= $heroTitle ?></h1>
            <p class="hero-desc" style="max-width:650px;"><?= $heroDesc ?></p>
            <a href="#services-list" class="btn btn-outline">Découvrir l'expertise</a>
        </div>

        <div class="hero-bg">
            <img src="<?= ASSETS_URL ?>/hero/connexionimg.jpg" alt="Garage">
            <div class="hero-overlay"></div>
        </div>
    </section>

    <!-- SERVICES -->
    <section id="services-list" class="section">
        <div class="container">
            <div class="mb-3">
                <h2>Notre <span class="title-accent">Expertise</span></h2>
                <div style="width:6rem; height:4px; background:var(--crimson); margin-top:1rem;"></div>
            </div>

            <div class="grid grid-4">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                <div class="service-card">
                    <img src="<?= ASSETS_URL ?>/services/<?= htmlspecialchars($services[$i]['img']) ?>" 
                         alt="<?= htmlspecialchars($services[$i]['title']) ?>" 
                         class="service-img">
                    <span class="service-num">0<?= $i ?></span>
                    <h3><?= htmlspecialchars($services[$i]['title']) ?></h3>
                    <p><?= htmlspecialchars($services[$i]['text']) ?></p>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <?php include 'composants_client/footer.php'; ?>