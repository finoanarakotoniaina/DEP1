<?php 
/**
 * SUPERCAR - Dashboard Administration (Version MySQLi Optimisée)
 */
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config_admin.php'; 
$db = $conn; 

// Stats avec alias pour la clarté
$countCars = $db->query("SELECT COUNT(*) FROM voiture")->fetch_row()[0] ?? 0;
$countClients = $db->query("SELECT COUNT(*) FROM client")->fetch_row()[0] ?? 0;
$countMessages = $db->query("SELECT COUNT(*) FROM contact")->fetch_row()[0] ?? 0;

// On récupère aussi la valeur totale du stock (optionnel mais intéressant)
// $totalValue = $db->query("SELECT SUM(prix) FROM voiture")->fetch_row()[0] ?? 0;

$lastClientsRes = $db->query("SELECT nom, prenom, email, id_client FROM client ORDER BY id_client DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="fr" class="bg-[#050505]">
<head>
    <meta charset="UTF-8">
    <title>SuperCar | Command Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #050505; color: white; overflow: hidden; }
        .font-title { font-family: 'Syncopate', sans-serif; }
        
        /* Effet Glassmorphism Avancé */
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

        /* Animation de scanline (effet écran cockpit) */
        body::before {
            content: " ";
            position: fixed;
            top: 0; left: 0; bottom: 0; right: 0;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.1) 50%), 
                        linear-gradient(90deg, rgba(255, 0, 0, 0.03), rgba(0, 255, 0, 0.01), rgba(0, 0, 255, 0.03));
            z-index: 100;
            background-size: 100% 4px, 3px 100%;
            pointer-events: none;
            opacity: 0.3;
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
                    <a href="dashboard.php" class="flex items-center gap-6 p-4 text-[#e31e24] bg-red-500/10 rounded-xl border border-red-500/20">
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
                <li>
                    <a href="admin_clients.php" class="flex items-center gap-6 p-4 hover:bg-white/5 text-gray-400 hover:text-white rounded-xl transition-all">
                        <i class="fa-solid fa-user-gear text-lg w-6"></i>
                        <span class="group-hover:opacity-100 opacity-0 transition-opacity">Drivers</span>
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

    <main class="flex-1 p-12 overflow-y-auto relative">
        
        <header class="flex justify-between items-center mb-12">
            <div class="relative">
                <div class="absolute -left-4 top-0 w-1 h-full bg-[#e31e24] shadow-[0_0_15px_#e31e24]"></div>
                <h1 class="font-title text-5xl uppercase tracking-tighter italic">System <span class="text-[#e31e24] not-italic">Overview</span></h1>
                <p class="text-gray-500 font-medium tracking-widest text-[10px] uppercase mt-2">Operator: <?= htmlspecialchars($_SESSION['prenom']) ?> // Auth: Level_01</p>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="text-right">
                    <div class="text-[10px] font-title text-green-500 animate-pulse">● System Online</div>
                    <div class="text-xs opacity-50 font-mono">LAT: 48.8566 | LON: 2.3522</div>
                </div>
                <div class="w-14 h-14 rounded-2xl glass-card flex items-center justify-center border-[#e31e24]/30 shadow-[0_0_20px_rgba(227,30,36,0.2)]">
                    <i class="fa-solid fa-shield-halved text-[#e31e24] text-xl"></i>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            
            <div class="glass-card p-8 rounded-3xl relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 text-white/5 text-9xl group-hover:text-[#e31e24]/10 transition-colors duration-700">
                    <i class="fa-solid fa-car"></i>
                </div>
                <span class="text-[10px] font-title text-gray-500 block mb-2 uppercase tracking-[0.2em]">Active Inventory</span>
                <div class="flex items-baseline gap-4">
                    <div class="text-6xl font-title font-bold text-white"><?= $countCars ?></div>
                    <div class="text-[#e31e24] text-xs font-title tracking-tighter">Units</div>
                </div>
                <div class="mt-4 w-full bg-white/5 h-1 rounded-full overflow-hidden">
                    <div class="bg-[#e31e24] h-full shadow-[0_0_10px_#e31e24]" style="width: <?= min(($countCars/50)*100, 100) ?>%"></div>
                </div>
            </div>

            <div class="glass-card p-8 rounded-3xl border-b-4 border-[#e31e24]">
                <span class="text-[10px] font-title text-gray-500 block mb-2 uppercase tracking-[0.2em]">Registered Drivers</span>
                <div class="text-6xl font-title font-bold leading-none mb-4"><?= $countClients ?></div>
                <div class="flex gap-2">
                    <span class="px-2 py-1 rounded bg-green-500/10 text-green-500 text-[8px] font-title tracking-widest">+12% Monthly</span>
                </div>
            </div>

            <div class="glass-card p-8 rounded-3xl group">
                <span class="text-[10px] font-title text-gray-500 block mb-2 uppercase tracking-[0.2em]">Comm. Link</span>
                <div class="flex justify-between items-center">
                    <div class="text-6xl font-title font-bold"><?= $countMessages ?></div>
                    <div class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center group-hover:rotate-12 transition-transform">
                        <i class="fa-solid fa-envelope-open-text text-gray-500"></i>
                    </div>
                </div>
                <p class="text-[9px] font-title text-gray-600 mt-4 uppercase">Awaiting processing...</p>
            </div>
        </div>

        <div class="glass-card rounded-3xl overflow-hidden border-white/10">
            <div class="p-8 border-b border-white/5 flex justify-between items-center bg-white/[0.02]">
                <h2 class="font-title text-xs text-[#e31e24] uppercase tracking-[0.3em]">Latest Mission Logs</h2>
                <div class="flex gap-2">
                    <div class="w-2 h-2 rounded-full bg-[#e31e24] animate-ping"></div>
                    <span class="text-[9px] font-title opacity-50">Real-time Feed</span>
                </div>
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
                        <?php while($row = $lastClientsRes->fetch_assoc()): ?>
                        <tr class="border-t border-white/5 hover:bg-[#e31e24]/5 transition-colors group">
                            <td class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-gray-800 to-black border border-white/10 flex items-center justify-center font-title text-[#e31e24]">
                                        <?= strtoupper(substr($row['nom'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="text-white font-bold tracking-tight uppercase"><?= htmlspecialchars($row['nom'] . ' ' . $row['prenom']) ?></div>
                                        <div class="text-[9px] font-title text-gray-600">ID: #00<?= $row['id_client'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-6">
                                <span class="font-mono text-xs opacity-50 group-hover:opacity-100 transition-opacity">
                                    <i class="fa-solid fa-at mr-2 text-[#e31e24]"></i><?= htmlspecialchars($row['email']) ?>
                                </span>
                            </td>
                            <td class="p-6 text-right">
                                <button class="px-4 py-2 border border-white/10 rounded-lg font-title text-[9px] hover:bg-white hover:text-black transition-all uppercase tracking-widest">
                                    Profile
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        // Effet de parallaxe léger sur les cartes
        document.querySelectorAll('.glass-card').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                card.style.setProperty('--mouse-x', `${x}px`);
                card.style.setProperty('--mouse-y', `${y}px`);
            });
        });
    </script>
</body>
</html>