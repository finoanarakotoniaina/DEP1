<?php include 'config.php'; ?>
<?php
// --- CONFIGURATION ET CONNEXION BDD ---
$host = 'localhost';
$dbname = 'supercar';
$user = 'root'; 
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// --- RÉCUPÉRATION DES DONNÉES ---
$query = "SELECT v.*, m.nom_marque 
          FROM voiture v 
          LEFT JOIN marque m ON v.id_marque = m.id_marque 
          ORDER BY m.nom_marque, v.modele";
$stmt = $pdo->query($query);
$toutes_voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);

$voituresParMarque = [];
foreach ($toutes_voitures as $v) {
    $nomM = !empty($v['nom_marque']) ? $v['nom_marque'] : 'Autres';
    $voituresParMarque[$nomM][] = $v;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaire | SuperCar Luxury</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">

    <style>
        .row-wrapper { position: relative; padding: 0 40px; margin-bottom: 60px; overflow: visible !important; }
        .story-row { 
            display: flex; overflow-x: auto; scroll-behavior: smooth; gap: 25px; 
            padding: 20px 10px; scrollbar-width: none; overflow-y: visible !important; 
            scroll-snap-type: x mandatory; 
        }
        .story-row::-webkit-scrollbar { display: none; }
        .car-card { 
            min-width: calc(33.333% - 20px); flex-shrink: 0; position: relative; 
            transition: transform 0.4s ease; z-index: 10; scroll-snap-align: start; 
        }
        .hover-specs {
            position: absolute; top: 0; width: 280px; height: 100%; background: #C1121F; 
            color: white; padding: 30px; border-radius: 24px; opacity: 0; visibility: hidden;
            transition: all 0.4s ease; display: flex; flex-direction: column; 
            justify-content: center; pointer-events: none;
        }
        .car-card .hover-specs { left: 105%; }
        .car-card:nth-child(3n) .hover-specs { left: auto !important; right: 105% !important; }
        .car-card:hover { transform: scale(1.05); z-index: 1000 !important; }
        .car-card:hover .hover-specs { opacity: 1; visibility: visible; }
        .nav-btn {
            position: absolute; top: 50%; transform: translateY(-50%); width: 35px; height: 35px;
            border-radius: 50%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.15);
            color: white; display: flex; align-items: center; justify-content: center; cursor: pointer;
            z-index: 100; backdrop-filter: blur(5px); transition: 0.3s; font-size: 12px;
        }
        .nav-btn:hover { background: white; color: black; border-color: white; }
        .btn-prev { left: 0; } .btn-next { right: 0; }
        .spec-item { display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.2); padding: 12px 0; font-size: 11px; text-transform: uppercase; }
        .btn-details { padding: 8px 18px; background: #C1121F; border: 1px solid #C1121F; color: white; font-size: 10px; text-transform: uppercase; font-weight: 800; letter-spacing: 1px; transition: all 0.3s ease; border-radius: 6px; }
        .btn-details:hover { background: white; color: #C1121F; border-color: white; transform: translateY(-2px); }
    </style>
</head>
<body class="split-bg text-white">

    <nav class="fixed top-0 w-full z-50 px-6 lg:px-10 py-6 flex justify-between items-center backdrop-blur-md border-b border-white/5">
        <div class="font-title text-xl tracking-tighter">SUPER<span class="text-[var(--crimson)]">CAR</span></div>
        <ul class="hidden md:flex gap-8 font-title text-[10px] tracking-[0.2em] uppercase items-center">
            <li><a href="accueil.php" class="nav-link">Accueil</a></li>
            <li><a href="voiture.php" class="nav-link active">Voitures</a></li>
            <li><a href="demande.php" class="nav-link">Essai</a></li>
            <li><a href="services.php" class="nav-link">Services</a></li>
            <li><a href="contact.php" class="nav-link">Contact</a></li> |
            <?php if (isset($_SESSION['id_client'])): ?>
                <li><a href="deconnexion.php" class="bg-[var(--crimson)] px-4 py-2 rounded text-white hover:bg-white hover:text-black transition-all duration-300 flex items-center gap-2"><i class="fa-solid fa-door-open text-[18px]"></i> Déconnexion</a></li>
            <?php else: ?>
                <li><strong><a href="connexion.php" class="nav-link flex items-center gap-2"><i class="fa-solid fa-user text-[var(--crimson)] text-[18px]"></i> Connexion</a></strong></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main class="pt-32"> 
        <div class="px-6 lg:px-24 mb-12"> 
            <a href="accueil.php" id="back-to-home" style="display: <?php echo (isset($_GET['from']) && $_GET['from'] == 'accueil') ? 'flex' : 'none'; ?>;" class="inline-flex items-center text-[10px] font-title uppercase tracking-[0.2em] text-gray-400 hover:text-[#C1121F] mb-8 transition-all group">
                <span class="mr-3 transition-transform group-hover:-translate-x-1">❮</span> Retour vers l'accueil
            </a>
            <h1 class="font-title text-3xl md:text-5xl font-bold leading-none uppercase tracking-tight">NOS <span class="text-[#C1121F]">MODÈLES</span></h1>
            <div class="w-20 h-1 bg-[#C1121F] mt-4"></div>
        </div>

        <?php foreach ($voituresParMarque as $nomMarque => $voitures): ?>
        <section class="mb-10"> 
            <h2 class="font-title text-sm px-6 lg:px-24 mb-4 text-gray-500 uppercase tracking-[0.3em]"><?php echo htmlspecialchars($nomMarque); ?></h2>
            <div class="row-wrapper">
                <?php $rowId = "row-" . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nomMarque)); ?>
                <button class="nav-btn btn-prev" onclick="scrollByThree('<?php echo $rowId; ?>', -1)">❮</button>
                <div class="story-row" id="<?php echo $rowId; ?>">
                    <?php foreach ($voitures as $car): ?>
                    <a href="demande.php?modele=<?php echo urlencode($car['modele']); ?>#booking" class="car-card block no-underline">
                        <div class="hover-specs">
                            <p class="font-title text-[10px] mb-4 border-b border-white/20 pb-2 text-center uppercase">Fiche Technique</p>
                            <div class="spec-item"><span>Année</span> <span><?php echo $car['annee']; ?></span></div>
                            <div class="spec-item"><span>Prix</span> <span><?php echo number_format($car['prix'], 0, '.', ' '); ?> Rs</span></div>
                            <div class="spec-item"><span>Etat</span> <span><?php echo htmlspecialchars($car['etat']); ?></span></div>
                            <div class="spec-item"><span>Kilométrage</span> <span><?php echo number_format($car['kilometrage'], 0, '.', ' '); ?> km</span></div>
                            <div class="spec-item"><span>Type</span> <span><?php echo htmlspecialchars($car['type']); ?></span></div>
                            <p class="text-[9px] mt-4 text-center opacity-70 italic">Cliquez pour réserver un essai</p>
                        </div>
                        
                        <img src="image_url/<?php echo htmlspecialchars($car['image_url']); ?>" class="w-full h-64 object-cover rounded-t-[24px]" alt="<?php echo htmlspecialchars($car['modele']); ?>">
                        
                        <div class="p-8 bg-white/5 rounded-b-[24px]">
                            <div class="flex justify-between items-center mb-2">
                                <?php if ($car['disponibilite']): ?>
                                    <span class="text-[#C1121F] font-bold text-[10px] uppercase">Disponible</span>
                                <?php else: ?>
                                    <span class="text-gray-600 font-bold text-[10px] uppercase line-through">Indisponible</span>
                                <?php endif; ?>
                                <span class="text-gray-400 text-[10px] font-bold uppercase"><?php echo htmlspecialchars($car['type']); ?></span>
                            </div>
                            <h3 class="font-title text-[13px] mb-4 text-white uppercase h-10 leading-tight">
                                <?php echo htmlspecialchars($car['modele']); ?>
                            </h3>
                            <div class="flex justify-between items-center mt-4">
                                <p class="font-bold text-xl text-white"><?php echo number_format($car['prix'], 0, '.', ' '); ?> Rs</p>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <button class="nav-btn btn-next" onclick="scrollByThree('<?php echo $rowId; ?>', 1)">❯</button>
            </div>
        </section>
        <?php endforeach; ?>
    </main>

    <script>
        function scrollByThree(id, direction) {
            const container = document.getElementById(id);
            const card = container.querySelector('.car-card');
            if(card) {
                const cardWidth = card.offsetWidth + 25; 
                container.scrollBy({ left: direction * (cardWidth * 3), behavior: 'smooth' });
            }
        }
    </script>
    
    <footer class="py-16 text-center border-t border-white/5 text-gray-500 text-[9px] tracking-[0.5em] font-title uppercase">
        &copy; 2026 SUPERCAR MAURITIUS - TOUS DROITS RÉSERVÉS
    </footer>
</body>
</html>