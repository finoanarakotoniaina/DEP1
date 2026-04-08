<?php 
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

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
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
</body>
</html>