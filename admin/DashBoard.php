<?php 
/**
 * SUPERCAR - Dashboard Administration (Version MySQLi)
 */
session_start();

// 1. Sécurité : Si Madison n'est pas connectée, retour au login
if (!isset($_SESSION['admin_id'])) {
    header('Location: LOGIN.php');
    exit();
}

$prenomAdmin = $_SESSION['prenom'] ?? 'Admin';
$nomAdmin = $_SESSION['nom'] ?? '';

// 2. Connexion à la base (config_admin.php doit contenir $conn)
require_once 'config_admin.php'; 

// On s'assure que la variable de connexion est la bonne
$db = $conn; 

// 3. Statistiques (Version MySQLi)
$countCars = $db->query("SELECT COUNT(*) FROM voiture")->fetch_row()[0] ?? 0;
$countClients = $db->query("SELECT COUNT(*) FROM client")->fetch_row()[0] ?? 0;
$countMessages = $db->query("SELECT COUNT(*) FROM contact")->fetch_row()[0] ?? 0;

// 4. Récupération des 5 derniers clients pour le tableau
$lastClientsRes = $db->query("SELECT nom, prenom, email FROM client ORDER BY id_client DESC LIMIT 5");
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
        <nav class="flex-1 p-4 mt-4">
             <ul class="space-y-4 font-title text-[10px] tracking-widest uppercase">
                <li><a href="dashboard.php" class="flex items-center gap-4 p-4 text-[#e31e24] bg-white/5 rounded"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                <li><a href="admin_voitures.php" class="flex items-center gap-4 p-4 hover:bg-white/5 rounded transition-all"><i class="fa-solid fa-car"></i> Stock</a></li>
                <li><a href="admin_clients.php" class="flex items-center gap-4 p-4 hover:bg-white/5 rounded transition-all"><i class="fa-solid fa-users"></i> Clients</a></li>
            </ul>
        </nav>
        <div class="p-8 border-t border-white/5">
            <a href="logout.php" class="text-[10px] font-title text-gray-500 hover:text-[#e31e24] transition-all uppercase tracking-widest">
                <i class="fa-solid fa-power-off mr-2"></i> Déconnexion
            </a>
        </div>
    </aside>

    <main class="flex-1 p-16 overflow-y-auto">
        <header class="flex justify-between items-start mb-16">
            <div>
                <h1 class="font-title text-4xl uppercase mb-2">Tableau de <span class="text-[#e31e24]">Bord</span></h1>
                <p class="text-gray-400">Bienvenue, <?= htmlspecialchars($prenomAdmin . " " . $nomAdmin) ?>.</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-[#e31e24] to-[#ff4d4d] flex items-center justify-center border-2 border-white/10 shadow-lg shadow-red-500/20">
                <i class="fa-solid fa-user text-white"></i>
            </div>
        </header>

        <div class="grid grid-cols-3 gap-8 mb-16">
            <div class="glass-card p-8 rounded-2xl">
                <span class="text-xs font-title text-gray-500 block mb-2 uppercase">Total Stock</span>
                <div class="text-5xl font-title font-bold"><?= $countCars ?></div>
            </div>
            <div class="glass-card p-8 rounded-2xl border-b-2 border-[#e31e24]">
                <span class="text-xs font-title text-gray-500 block mb-2 uppercase">Clients Inscrits</span>
                <div class="text-5xl font-title font-bold"><?= $countClients ?></div>
            </div>
            <div class="glass-card p-8 rounded-2xl">
                <span class="text-xs font-title text-gray-500 block mb-2 uppercase">Messages</span>
                <div class="text-5xl font-title font-bold"><?= $countMessages ?></div>
            </div>
        </div>

        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-white/5">
                <h2 class="font-title text-[10px] text-[#e31e24] uppercase tracking-widest">Dernières Interactions</h2>
            </div>
            <table class="w-full text-left">
                <thead class="text-[10px] font-title text-gray-500 uppercase">
                    <tr>
                        <th class="p-6">Nom / Prénom</th>
                        <th class="p-6">Email</th>
                        <th class="p-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-300">
                    <?php while($row = $lastClientsRes->fetch_assoc()): ?>
                    <tr class="border-t border-white/5 hover:bg-white/[0.02] transition-all">
                        <td class="p-6 text-white font-semibold"><?= htmlspecialchars($row['nom'] . ' ' . $row['prenom']) ?></td>
                        <td class="p-6 opacity-70"><?= htmlspecialchars($row['email']) ?></td>
                        <td class="p-6 text-right">
                            <span class="text-[9px] font-title px-3 py-1 border border-white/10 rounded-full text-gray-500">CLIENT</span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>