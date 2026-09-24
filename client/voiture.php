<?php 
require_once '../config.php'; 

// Détecte si on vient de la page demande.php
$from = $_GET['from'] ?? '';

try {
    $query = "SELECT v.*, m.nom_marque 
              FROM voiture v 
              LEFT JOIN marque m ON v.id_marque = m.id_marque 
              ORDER BY m.nom_marque, v.modele";
    $stmt = $db->query($query); 
    $toutes_voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $voituresParMarque = [];
    foreach ($toutes_voitures as $v) {
        $nomM = !empty($v['nom_marque']) ? $v['nom_marque'] : 'Autres';
        $voituresParMarque[$nomM][] = $v;
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaire | SuperCar Luxury</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body class="bg page-voiture">

    <?php include 'composants_client/navbar.php'; ?>

    <main style="padding-top: 6rem;">
        <div class="container page-header">
            <div>
                <h2>NOS <span class="title-accent">MODÈLES</span></h2>
                <div class="title-bar"></div>
            </div>

            <div class="search-wrapper">
                <input type="text" id="searchCar" class="search-bar" 
                    placeholder="RECHERCHER UN MODÈLE...">
            </div>
        </div>

        <?php foreach ($voituresParMarque as $nomMarque => $voitures): ?>
        <section class="brand-section">
            <h2 class="brand-title"><?= htmlspecialchars($nomMarque) ?></h2>

            <div class="row-wrapper">
                <?php $rowId = "row-" . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nomMarque)); ?>

                <button class="nav-arrow prev" onclick="scrollByThree('<?= $rowId ?>', -1)">❮</button>

                <div class="story-row" id="<?= $rowId ?>">
                    <?php foreach ($voitures as $car): 
                        // Si on vient de demande.php, on retourne sur demande.php avec l'id + modèle
                        // Sinon, on va sur demande.php normalement
                        if ($from === 'demande') {
                            $lien = "demande.php?id_v=" . $car['id_voiture'] . "&modele=" . urlencode($car['modele']) . "#booking";
                        } else {
                            $lien = "demande.php?id_v=" . $car['id_voiture'] . "&modele=" . urlencode($car['modele']) . "#booking";
                        }
                    ?>
                    <a href="<?= $lien ?>" class="car-card" <?= $from === 'demande' ? 'data-from-demande="1"' : '' ?>>
                        <div class="hover-specs">
                            <p class="spec-title">Fiche Technique</p>
                            <div class="spec-item"><span>Année</span>       <span><?= $car['annee'] ?></span></div>
                            <div class="spec-item"><span>État</span>        <span><?= htmlspecialchars($car['etat']) ?></span></div>
                            <div class="spec-item"><span>Type</span>        <span><?= htmlspecialchars($car['type']) ?></span></div>
                            <div class="spec-item"><span>Kilométrage</span> <span><?= number_format($car['kilometrage'], 0, '.', ' ') ?> km</span></div>
                            <?php if (!empty($car['description'])): ?>
                            <p class="spec-desc"><?= htmlspecialchars($car['description']) ?></p>
                            <?php endif; ?>
                            <p style="font-size:0.65rem; margin-top:1rem; text-align:center; opacity:0.7; font-style:italic;">
                                <?= $from === 'demande' ? 'Cliquez pour sélectionner ce modèle' : 'Cliquez pour réserver un essai' ?>
                            </p>
                        </div>

                        <img src="<?= UPLOAD_URL ?>/voitures/<?= htmlspecialchars($car['image_url']) ?>" alt="<?= htmlspecialchars($car['modele']) ?>">

                        <div class="car-info">
                            <h3><?= htmlspecialchars($car['modele']) ?></h3>
                            <p class="car-price"><?= number_format($car['prix'], 0, '.', ' ') ?> Rs</p>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>

                <button class="nav-arrow next" onclick="scrollByThree('<?= $rowId ?>', 1)">❯</button>
            </div>
        </section>
        <?php endforeach; ?>
    </main>

    <script>
        // 1. Carrousel : scroll de 3 cartes
        function scrollByThree(id, direction) {
            const container = document.getElementById(id);
            const card = container.querySelector('.car-card');
            if (card) {
                const w = card.offsetWidth + 24;
                container.scrollBy({ left: direction * (w * 3), behavior: 'smooth' });
            }
        }

        // 2. Détection dynamique : fiche à droite ou à gauche selon la place
        document.querySelectorAll('.car-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                const rect = this.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                const specsWidth = 280;

                if (rect.right + specsWidth > viewportWidth) {
                    this.classList.add('specs-left');
                } else {
                    this.classList.remove('specs-left');
                }
            });
        });

        // 3. Recherche dynamique
        document.getElementById('searchCar').addEventListener('input', function(e) {
            const q = e.target.value.toLowerCase().trim();

            document.querySelectorAll('.car-card').forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(q) ? '' : 'none';
            });

            document.querySelectorAll('.brand-section').forEach(sec => {
                const visible = sec.querySelectorAll('.car-card:not([style*="display: none"])').length;
                sec.style.display = visible > 0 ? '' : 'none';
            });
        });
    </script>

    <?php include 'composants_client/footer.php'; ?>
</body>
</html>