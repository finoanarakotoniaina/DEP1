<?php 
<<<<<<< HEAD
include 'config2.php'; 
// Sécurité : décommente la ligne suivante quand ton système de connexion admin sera prêt
// if(!isset($_SESSION['admin'])) { header('Location: connexion.php'); exit; }
?>
=======
/**
 * SUPERCAR - Dashboard Administration
 */

// 1. Démarrage de la session
session_start();

// 2. Inclusion de la config
require_once '../config.php'; 

// 3. Correction de la variable de connexion
// Ce bloc vérifie si ta variable s'appelle $conn ou $db et la renomme en $pdo pour le reste du script
if (!isset($pdo)) {
    if (isset($conn)) { $pdo = $conn; }
    elseif (isset($db)) { $pdo = $db; }
}

// 4. Sécurité : Vérification de la connexion
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}

$prenom = $_SESSION['prenom'] ?? 'Admin';
$nom = $_SESSION['nom'] ?? '';

// 5. Récupération des stats (on utilise query car c'est du MySQLi d'après tes erreurs précédentes)
$countCars = $pdo->query("SELECT COUNT(*) FROM voiture")->fetch_row()[0] ?? 0;
$countClients = $pdo->query("SELECT COUNT(*) FROM client")->fetch_row()[0] ?? 0;
$countMessages = $pdo->query("SELECT COUNT(*) FROM contact")->fetch_row()[0] ?? 0;
?>

>>>>>>> 66abf5fc3cd368f6f04d57f34f1ad15de56981e0
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
<<<<<<< HEAD
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN | Édition SuperCar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    
    <style>
        /* Style pour montrer que c'est éditable */
        [contenteditable="true"] { border: 1px dashed rgba(255,255,255,0.1); padding: 2px; transition: 0.3s; }
        [contenteditable="true"]:hover { border: 1px solid var(--crimson); cursor: edit; }
        [contenteditable="true"]:focus { outline: none; background: rgba(193, 18, 31, 0.1); border: 1px solid var(--crimson); }
        
        .admin-status {
            position: fixed; bottom: 20px; left: 20px; z-index: 100;
            background: var(--crimson); color: white; padding: 10px 20px;
            font-family: 'Syncopate'; font-size: 10px; border-radius: 4px;
        }
    </style>
</head>

<body class="home-bg">

    <div class="admin-status text-white"><i class="fas fa-lock-open mr-2"></i> MODE ÉDITION ADMIN</div>

    <section class="relative h-screen w-full flex items-center px-6 lg:px-24 overflow-hidden group">
        <div class="absolute top-0 left-0 w-full h-full z-0">
            <video id="video_preview" autoplay muted loop playsinline class="w-full h-full object-cover">
                <source src="<?php echo getContent('hero_video'); ?>" type="video/mp4">
            </video>
            <div class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity z-30">
                <label class="cursor-pointer bg-white text-black px-4 py-2 font-bold text-xs uppercase tracking-widest">
                    Changer la vidéo (.mp4)
                    <input type="file" class="hidden" accept="video/mp4" onchange="updateVideo(this)">
                </label>
            </div>
        </div>

        <div class="relative z-20 w-full mt-20">
            <h1 id="hero_title" contenteditable="true" onblur="saveText('hero_title', this.innerHTML)" class="font-title text-4xl md:text-7xl font-bold leading-none mb-8">
                <?php echo getContent('hero_title'); ?>
            </h1>
            <p id="hero_subtitle" contenteditable="true" onblur="saveText('hero_subtitle', this.innerText)" class="text-lg text-gray-200 max-w-xl font-light leading-relaxed mb-10">
                <?php echo getContent('hero_subtitle'); ?>
            </p>
        </div>
    </section>

    <section class="py-32 px-6 lg:px-24">
        <div class="flex flex-col gap-12 max-w-3xl mx-auto">
            <?php for($i=1; $i<=4; $i++): ?>
            <div class="brand-card p-8 bg-white/5 backdrop-blur-sm border border-white/5">
                <h3 id="brand_<?php echo $i; ?>_name" contenteditable="true" onblur="saveText('brand_<?php echo $i; ?>_name', this.innerText)" class="font-title text-[var(--marble)] text-2xl mb-4">
                    <?php echo getContent('brand_'.$i.'_name'); ?>
                </h3>
                <p id="brand_<?php echo $i; ?>_desc" contenteditable="true" onblur="saveText('brand_<?php echo $i; ?>_desc', this.innerText)" class="text-gray-300 text-lg leading-relaxed desc-font">
                    <?php echo getContent('brand_'.$i.'_desc'); ?>
                </p>
            </div>
            <?php endfor; ?>
        </div>
    </section>

    <script>
        function saveText(id, content) {
            let formData = new FormData();
            formData.append('id', id);
            formData.append('content', content);

            fetch('update_home.php', {
                method: 'POST',
                body: formData
            }).then(() => console.log(id + " sauvegardé !"));
        }

        function updateVideo(input) {
            if (input.files && input.files[0]) {
                let formData = new FormData();
                formData.append('video', input.files[0]);
                formData.append('id', 'hero_video');

                fetch('upload_video.php', {
                    method: 'POST',
                    body: formData
                }).then(res => res.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('video_preview').src = data.path;
                        alert("Vidéo mise à jour !");
                    }
                });
            }
        }
    </script>
=======
    <title>SuperCar Admin | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0a0a0a; color: white; }
        .font-title { font-family: 'Syncopate', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="flex min-h-screen">

    <aside class="w-72 border-r border-white/5 flex flex-col bg-[#0d0d0d]">
        <div class="p-8 font-title text-xl border-b border-white/5">
            SUPER<span class="text-[#e31e24]">CAR</span>
        </div>
        <nav class="flex-1 p-4">
             <ul class="space-y-4 font-title text-[10px] tracking-widest uppercase">
                <li><a href="#" class="flex items-center gap-4 p-4 text-[#e31e24] bg-white/5 rounded"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                <li><a href="admin_voitures.php" class="flex items-center gap-4 p-4 hover:bg-white/5 rounded transition-all"><i class="fa-solid fa-car"></i> Stock</a></li>
            </ul>
        </nav>
    </aside>

    <main class="flex-1 p-10">
        <header class="flex justify-between items-start mb-16">
            <div>
                <h1 class="font-title text-4xl uppercase mb-2">Tableau de <span class="text-[#e31e24]">Bord</span></h1>
                <p class="text-gray-400">Bienvenue, <?= htmlspecialchars($prenom . " " . $nom) ?>.</p>
            </div>

            <a href="../deconnexion.php" class="flex items-center gap-3 px-4 py-2 bg-white/5 border border-white/10 rounded-full hover:bg-[#e31e24]/10 transition-all group">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-[#e31e24] to-[#ff4d4d] flex items-center justify-center">
                    <i class="fa-solid fa-user text-white"></i>
                </div>
                <i class="fa-solid fa-power-off text-gray-500 group-hover:text-[#e31e24]"></i>
            </a>
        </header>

        <div class="grid grid-cols-3 gap-8">
            <div class="glass-card p-8 rounded-2xl">
                <span class="text-xs font-title text-gray-500 block mb-2 uppercase">Stock</span>
                <div class="text-5xl font-title font-bold"><?= $countCars ?></div>
            </div>
            <div class="glass-card p-8 rounded-2xl border-b-2 border-[#e31e24]">
                <span class="text-xs font-title text-gray-500 block mb-2 uppercase">Clients</span>
                <div class="text-5xl font-title font-bold"><?= $countClients ?></div>
            </div>
            <div class="glass-card p-8 rounded-2xl">
                <span class="text-xs font-title text-gray-500 block mb-2 uppercase">Messages</span>
                <div class="text-5xl font-title font-bold"><?= $countMessages ?></div>
            </div>
        </div>
    </main>
>>>>>>> 66abf5fc3cd368f6f04d57f34f1ad15de56981e0
</body>
</html>