<?php 
require_once '../config.php'; 

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

<body class="bg">

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
                    <?php foreach ($voitures as $car): ?>
                    <a href="demande.php?id_v=<?= $car['id_voiture'] ?>&modele=<?= urlencode($car['modele']) ?>#booking" class="car-card">
                        <div class="hover-specs">
                            <p class="spec-title">Fiche Technique</p>
                            <div class="spec-item"><span>Année</span> <span><?= $car['annee'] ?></span></div>
                            <div class="spec-item"><span>Etat</span>  <span><?= htmlspecialchars($car['etat']) ?></span></div>
                            <div class="spec-item"><span>Type</span>  <span><?= htmlspecialchars($car['type']) ?></span></div>
                            <p style="font-size:0.65rem; margin-top:1rem; text-align:center; opacity:0.7; font-style:italic;">
                                Cliquez pour réserver un essai
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

        // 2. Recherche dynamique
        document.getElementById('searchCar').addEventListener('input', function(e) {
            const q = e.target.value.toLowerCase().trim();

            document.querySelectorAll('.car-card').forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(q) ? '' : 'none';
            });

            // Masque les marques sans voiture visible
            document.querySelectorAll('.brand-section').forEach(sec => {
                const visible = sec.querySelectorAll('.car-card:not([style*="display: none"])').length;
                sec.style.display = visible > 0 ? '' : 'none';
            });
        });
    </script>

    <?php include 'composants_client/footer.php'; ?>