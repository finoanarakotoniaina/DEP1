<?php 
include 'config.php';

// Fonction de récupération depuis services_content
function getContent($id) {
    global $pdo, $db, $conn;
    $database = $pdo ?? $db ?? $conn;
    $stmt = $database->prepare("SELECT content FROM services_content WHERE element_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ? $row['content'] : '';
}

// Données des 4 services depuis la BDD
$services = [];
for ($i = 1; $i <= 4; $i++) {
    $services[$i] = [
        'img'   => getContent("service_{$i}_img"),
        'title' => getContent("service_{$i}_title"),
        'text'  => getContent("service_{$i}_text"),
    ];
}

// Fallbacks si la BDD est vide
$fallbacks = [
    1 => ['img' => 'vidange.webp',        'title' => 'Vidange',    'text' => 'Lubrification haute performance avec huiles synthétiques certifiées. Nous protégeons les composants internes pour une fluidité mécanique absolue.'],
    2 => ['img' => 'pneux.jpg',           'title' => 'Pneus',      'text' => 'Contrôle de l\'usure et équilibrage de précision. Maximisez votre grip latéral et votre sécurité sur tous les types de revêtements.'],
    3 => ['img' => 'testdiagnostic.webp', 'title' => 'Diagnostic', 'text' => 'Scan complet des calculateurs et mise à jour des systèmes embarqués. Nous identifions la moindre anomalie avant qu\'elle ne devienne critique.'],
    4 => ['img' => 'moteur.jpg',          'title' => 'Moteur',     'text' => 'Calage et réglages fins de la distribution. Redonnez à votre supercar sa sonorité iconique et son couple dévastateur d\'origine.'],
];

foreach ($services as $i => $s) {
    if (empty($s['img']))    $services[$i]['img']   = $fallbacks[$i]['img'];
    if (empty($s['title']))  $services[$i]['title'] = $fallbacks[$i]['title'];
    if (empty($s['text']))   $services[$i]['text']  = $fallbacks[$i]['text'];
}

// Titre et description hero depuis BDD
$heroTitle = getContent('serv_hero_title') ?: 'Domptez <br> <span class="text-[var(--crimson)]">l\'instinct.</span>';
$heroDesc  = getContent('serv_hero_desc')  ?: 'Nos services de maintenance garantissent que chaque kilomètre reste une expérience pure et sans compromis. L\'excellence au service de votre performance.';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Expertise Technique</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="split-bg text-white font-['Plus_Jakarta_Sans']">
    
    <!-- NAVIGATION -->
    <nav class="fixed top-0 w-full z-50 px-6 lg:px-10 py-6 flex justify-between items-center backdrop-blur-md border-b border-white/5">
        <div class="font-title text-xl tracking-tighter">SUPER<span class="text-[var(--crimson)]">CAR</span></div>
        
        <ul class="hidden md:flex gap-8 font-title text-[10px] tracking-[0.2em] uppercase items-center">
            <li><a href="accueil.php" class="nav-link">Accueil</a></li>
            <li><a href="voiture.php" class="nav-link">Voitures</a></li>
            <li><a href="demande.php" class="nav-link">Essai</a></li>
            <li><a href="services.php" class="nav-link active">Services</a></li>
            <li><a href="contact.php" class="nav-link">Contact</a></li> |

            <?php if (isset($_SESSION['id_client'])): ?>
                <li>
                    <a href="deconnexion.php" class="bg-[var(--crimson)] px-4 py-2 rounded text-white hover:bg-white hover:text-black transition-all duration-300 flex items-center gap-2">
                        <i class="fa-solid fa-door-open text-[18px]"></i> Déconnexion
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <strong>
                        <a href="connexion.php" class="nav-link flex items-center gap-2">
                            <i class="fa-solid fa-user text-[var(--crimson)] text-[18px]"></i> Connexion
                        </a>
                    </strong>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- SECTION HAUTE -->
    <section class="relative h-[80vh] flex items-center px-6 lg:px-24 overflow-hidden">
        <div class="z-10 max-w-4xl relative">
            <span class="text-gray-500 font-bold tracking-[0.5em] uppercase mb-4 block text-[10px]">Ingénierie de pointe</span>
            <h1 class="font-title text-5xl md:text-7xl font-bold leading-none mb-8 uppercase">
                <?= $heroTitle ?>
            </h1>
            <p class="text-xl text-gray-400 font-light max-w-2xl leading-relaxed mb-10">
                <?= $heroDesc ?>
            </p>
            <a href="#services-list" class="inline-flex items-center gap-8 group">
                <div class="w-16 h-16 rounded-full border border-white/20 flex items-center justify-center group-hover:border-[var(--crimson)] transition-all duration-500">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="group-hover:text-[var(--crimson)] transform group-hover:translate-y-1 transition-all">
                        <path d="M7 13l5 5 5-5M7 6l5 5 5-5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <span class="font-title text-[10px] tracking-[0.2em] group-hover:text-[var(--crimson)] transition-colors uppercase">Découvrir l'expertise</span>
            </a>
        </div>
        <div class="absolute inset-y-0 right-0 w-full md:w-1/2 z-0">
            <img src="garage.jfif" alt="Garage" class="w-full h-full object-cover grayscale opacity-30 [mask-image:linear-gradient(to_left,black_40%,transparent_100%)]">
        </div>
    </section>

    <!-- SECTION SERVICES -->
    <section id="services-list" class="py-32 px-6 lg:px-24 relative">
        <div class="mb-24">
            <h2 class="font-title text-3xl md:text-5xl mb-4 uppercase">Notre <span class="text-[var(--crimson)]">Expertise</span></h2>
            <div class="w-24 h-1 bg-[var(--crimson)]"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="service-card p-8 bg-white/5 border border-white/10 rounded-2xl group transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:scale-125 hover:z-50 hover:bg-[#051622] hover:border-[var(--crimson)]/50 hover:shadow-[0_20px_60px_rgba(0,0,0,0.8)]">
                <div class="mb-6 overflow-hidden rounded-xl h-44 bg-gray-900">
                    <img src="<?= htmlspecialchars($services[$i]['img']) ?>" 
                         alt="<?= htmlspecialchars($services[$i]['title']) ?>" 
                         class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
                </div>
                <span class="text-[var(--crimson)] font-title text-sm mb-2 block uppercase">0<?= $i ?></span>
                <h3 class="font-title text-xl leading-tight mb-4 uppercase">
                    <?= htmlspecialchars($services[$i]['title']) ?>
                </h3>
                <p class="text-gray-400 group-hover:text-white text-[14px] leading-relaxed font-light transition-colors">
                    <?= htmlspecialchars($services[$i]['text']) ?>
                </p>
            </div>
            <?php endfor; ?>
        </div>
    </section>

    <footer class="py-16 border-t border-white/5 text-center">
        <p class="text-gray-500 opacity-30 text-[9px] tracking-[0.5em] uppercase">© 2026 SuperCar Expertise Automobile</p>
    </footer>

</body>
</html>