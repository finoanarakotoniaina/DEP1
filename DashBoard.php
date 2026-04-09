<?php 
/**
 * SUPERCAR - Dashboard Administration (Version PDO Sécurisée)
 */
session_start();

// 1. Protection de la page : si pas de session, retour au login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config.php'; 

// On utilise la variable $db définie dans ton config.php
$database = $db; 

try {
    // 2. Statistiques (Comptage des entrées dans les tables)
    $countCars = $database->query("SELECT COUNT(*) FROM voiture")->fetchColumn() ?: 0;
    $countClients = $database->query("SELECT COUNT(*) FROM client")->fetchColumn() ?: 0;
    $countMessages = $database->query("SELECT COUNT(*) FROM contact")->fetchColumn() ?: 0;

    // 3. Récupération des 5 derniers clients (Mission Logs)
    $lastClientsStmt = $database->query("SELECT nom, prenom, email, id_client FROM client ORDER BY id_client DESC LIMIT 5");
    $lastClients = $lastClientsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // En cas d'erreur de requête (ex: table manquante)
    $db_error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr" class="bg-[#050505]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Command Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #050505; color: white; margin: 0; }
        .font-title { font-family: 'Syncopate', sans-serif; }
        
        /* Effet Glassmorphism */
        .glass-card { 
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.01) 100%);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.8);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .glass-card:hover {
            border-color: rgba(227, 30, 36, 0.5);
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.07);
        }

        /* Scrollbar custom */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #e31e24; border-radius: 10px; }
    </style>
</head>
<body class="flex min-h-screen">

    <aside class="w-24 hover:w-72 transition-all duration-500 border-r border-white/5 flex flex-col bg-[#080808] z-50 group">
        <div class="p-8 font-title text-xl border-b border-white/5 flex items-center gap-4">
            <span class="text-[#e31e24] text-2xl"><i class="fa-solid fa-bolt"></i></span>
            <span class="group-hover:opacity-100 opacity-0 transition-opacity duration-300">SUPER<span class="text-[#e31e24]">CAR</span></span>
        </div>
        
        <nav class="flex-1 px-4 mt-8">
            <ul class="space-y-6 font-title text-[10px] tracking-widest uppercase">
                <li>
                    <a href="DashBoard.php" class="flex items-center gap-6 p-4 text-[#e31e24] bg-red-500/10 rounded-xl border border-red-500/20">
                        <i class="fa-solid fa-gauge-high text-lg w-6"></i>
                        <span class="group-hover:opacity-100 opacity-0 transition-opacity">Telemetry</span>
                    </a>
                </li>
                <li>
                    <a href="admin_voitures.php" class="flex items-center gap-6 p-4 hover:bg-white/5 text-gray-500 hover:text-white rounded-xl transition-all">
                        <i class="fa-solid fa-car-rear text-lg w-6"></i>
                        <span class="group-hover:opacity-100 opacity-0 transition-opacity">Hangar</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="p-8 border-t border-white/5">
            <a href="logout.php" class="flex items-center gap-6 text-gray-600 hover:text-[#e31e24] transition-all">
                <i class="fa-solid fa-power-off text-lg w-6"></i>
                <span class="group-hover:opacity-100 opacity-0 transition-opacity font-title text-[10px]">Shutdown</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 p-12 overflow-y-auto">
        
        <header class="flex justify-between items-center mb-12">
            <div class="relative">
                <div class="absolute -left-4 top-0 w-1 h-full bg-[#e31e24] shadow-[0_0_15px_#e31e24]"></div>
                <h1 class="font-title text-5xl uppercase tracking-tighter italic">System <span class="text-[#e31e24] not-italic">Overview</span></h1>
                <p class="text-gray-500 font-medium tracking-widest text-[10px] uppercase mt-2">
                    Operator: <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Inconnu') ?> // Auth: Level_01
                </p>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="text-right">
                    <div class="text-[10px] font-title text-green-500 animate-pulse">● System Online</div>
                    <div class="text-xs opacity-50 font-mono">127.0.0.1</div>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="glass-card p-8 rounded-3xl">
                <span class="text-[10px] font-title text-gray-500 block mb-2 uppercase tracking-[0.2em]">Active Inventory</span>
                <div class="flex items-baseline gap-4">
                    <div class="text-6xl font-title font-bold text-white"><?= $countCars ?></div>
                    <div class="text-[#e31e24] text-xs font-title tracking-tighter">Units</div>
                </div>
            </div>

            <div class="glass-card p-8 rounded-3xl border-b-4 border-[#e31e24]">
                <span class="text-[10px] font-title text-gray-500 block mb-2 uppercase tracking-[0.2em]">Registered Drivers</span>
                <div class="text-6xl font-title font-bold leading-none"><?= $countClients ?></div>
            </div>

            <div class="glass-card p-8 rounded-3xl">
                <span class="text-[10px] font-title text-gray-500 block mb-2 uppercase tracking-[0.2em]">Comm. Link</span>
                <div class="text-6xl font-title font-bold"><?= $countMessages ?></div>
            </div>
        </div>

        <div class="glass-card rounded-3xl overflow-hidden border-white/10">
            <div class="p-8 border-b border-white/5 flex justify-between items-center bg-white/[0.02]">
                <h2 class="font-title text-xs text-[#e31e24] uppercase tracking-[0.3em]">Latest Mission Logs</h2>
                <span class="text-[9px] font-title opacity-50">Base de données : Supercar</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-[10px] font-title text-gray-500 uppercase bg-black/20">
                        <tr>
                            <th class="p-6 tracking-widest">Operator Identity</th>
                            <th class="p-6 tracking-widest">Network Access</th>
                            <th class="p-6 text-right tracking-widest">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php if(isset($lastClients)): foreach($lastClients as $row): ?>
                        <tr class="border-t border-white/5 hover:bg-[#e31e24]/5 transition-colors group">
                            <td class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-black border border-white/10 flex items-center justify-center font-title text-[#e31e24]">
                                        <?= strtoupper(substr($row['nom'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="text-white font-bold uppercase"><?= htmlspecialchars($row['nom'] . ' ' . $row['prenom']) ?></div>
                                        <div class="text-[9px] font-title text-gray-600">ID: #00<?= $row['id_client'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-6 font-mono text-xs opacity-50">
                                <?= htmlspecialchars($row['email']) ?>
                            </td>
                            <td class="p-6 text-right">
                                <button class="px-4 py-2 border border-white/10 rounded-lg font-title text-[9px] hover:bg-white hover:text-black transition-all uppercase tracking-widest">
                                    Profile
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>