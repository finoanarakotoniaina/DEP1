<?php
/**
 * SUPERCAR - Cockpit Precision HUD v6.0
 * Fusion: Session Sécurisée + Télémétrie BMW M5
 * Structure UI inspirée par Rentable
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
    // 2. Statistiques réelles pour les cadrans
    $countCars = $database->query("SELECT COUNT(*) FROM voiture")->fetchColumn() ?: 0;
    $countClients = $database->query("SELECT COUNT(*) FROM client")->fetchColumn() ?: 0;
    $countMessages = $database->query("SELECT COUNT(*) FROM contact")->fetchColumn() ?: 0;

    // 3. Récupération des 5 derniers clients (Mission Logs)
    // Correction fetch_row() -> fetchAll(PDO::FETCH_ASSOC)
    $lastClientsStmt = $database->query("SELECT nom, prenom, email, id_client FROM client ORDER BY id_client DESC LIMIT 5");
    $lastClients = $lastClientsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // En cas d'erreur de requête
    $db_error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SuperCar | M5 Telemetry HUD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --m-red: #ff1e26; /* Rouge M-Sport exact */
            --bg-deep: #050505;
            --bg-card: #0d0d10;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-deep);
            color: #fff;
            overflow: hidden;
            background-image: radial-gradient(circle at top right, #1a1a22 0%, var(--bg-deep) 100%);
        }

        .font-m { font-family: 'Orbitron', sans-serif; }

        /* --- STYLISATION DES CADRANS BMW M5 (RÉALISTE) --- */
        .gauge-container {
            position: relative;
            width: 210px; height: 180px; /* Réduit pour l'ouverture du bas */
            display: flex; align-items: center; justify-content: center;
        }

        /* L'arc gradué ouvert en bas (from -135deg to +135deg) */
        .gauge-ring {
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: conic-gradient(
                from -135deg, 
                var(--c, var(--m-red)) 0%, 
                var(--c, var(--m-red)) calc(var(--v) * 2.7deg), 
                rgba(255,255,255,0.03) calc(var(--v) * 2.7deg),
                rgba(255,255,255,0.03) 270deg,
                transparent 270deg
            );
            /* Masque pour créer l'anneau ouvert en bas */
            -webkit-mask: radial-gradient(transparent 87%, black 88%);
            mask: radial-gradient(transparent 87%, black 88%);
        }

        /* L'aiguille fine avec pointe blanche */
        .gauge-needle {
            position: absolute;
            width: 2px; height: 90px;
            background: linear-gradient(to top, transparent 15%, var(--c, var(--m-red)) 80%, #fff 100%);
            bottom: 50%; left: 50%;
            transform-origin: bottom center;
            /* Rotation calculée de -135° (min) à +135° (max) */
            transform: translateX(-50%) rotate(calc(-135deg + (var(--v) * 2.7deg)));
            transition: transform 1.5s cubic-bezier(0.1, 0.5, 0.2, 1);
            z-index: 10;
        }

        .gauge-center {
            position: absolute;
            width: 14px; height: 14px;
            background: #000;
            border: 2px solid var(--c, var(--m-red));
            border-radius: 50%;
            z-index: 20;
            box-shadow: 0 0 10px #000;
        }

        /* --- UI RENTABLE STYLE --- */
        .nav-link {
            display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1.25rem;
            border-radius: 14px; color: #555; transition: 0.3s; text-decoration: none;
            font-size: 0.85rem; font-weight: 600;
        }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,0.05); }
        .nav-link.active {
            background: linear-gradient(90deg, rgba(255,30,38,0.1) 0%, transparent 100%);
            border-left: 3px solid var(--m-red); color: #fff;
        }

        .glass-card {
            background: var(--bg-card);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 28px;
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="flex h-screen">

    <aside class="w-72 bg-black border-r border-white/5 flex flex-col p-8">
        <div class="mb-12">
            <h1 class="text-xl font-m font-black italic uppercase tracking-tighter">SUPER<span class="text-red-600">CAR</span></h1>
        </div>
        
        <nav class="flex-1 space-y-2">
            <p class="text-[9px] font-bold text-gray-600 uppercase tracking-widest mb-4">Navigation</p>
            
            <a href="DashBoard.php" class="nav-link active">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>
            <a href="admin_voitures.php" class="nav-link">
                <i class="fa-solid fa-car-side"></i> Inventaire
            </a>
            <a href="admin_demande.php" class="nav-link">
                <i class="fa-solid fa-calendar-check"></i> Demandes
            </a>
            <a href="admin_contact.php" class="nav-link">
                <i class="fa-solid fa-envelope"></i> Messages
            </a>
            <a href="admin_services.php" class="nav-link">
                <i class="fa-solid fa-screwdriver-wrench"></i> Services
            </a>
        </nav>

        <div class="mt-auto pt-6 border-t border-white/5">
            <a href="logout.php" class="flex items-center justify-between p-4 bg-red-600/5 hover:bg-red-600/15 border border-red-600/10 rounded-2xl transition group no-underline">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center">
                        <i class="fa-solid fa-power-off text-[10px] text-white"></i>
                    </div>
                    <span class="text-[11px] font-bold text-red-500 uppercase tracking-wider">Déconnexion</span>
                </div>
                <i class="fa-solid fa-chevron-right text-[10px] text-red-900 group-hover:translate-x-1 transition"></i>
            </a>
        </div>
    </aside>

    <main class="flex-1 p-12 overflow-y-auto bg-[#080a0c]">
        
        <header class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-4xl font-m italic uppercase tracking-tighter leading-none">Command <span class="text-gray-600">Center</span></h2>
                <div class="flex items-center gap-3 mt-2">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-[9px] font-bold text-gray-500 uppercase tracking-[2px]">Admin HUD / ID: <?= $_SESSION['admin_id'] ?></span>
                </div>
            </div>
            
            <button class="bg-red-600 text-white px-8 py-3 rounded-xl font-m text-[10px] uppercase shadow-[0_10px_30px_rgba(255,30,38,0.25)]">System Secure</button>
        </header>

        <?php if(isset($db_error)): ?>
            <div class="mb-6 p-4 bg-red-600/20 border border-red-600 rounded-xl text-xs font-mono text-red-500">
                ⚠️ ALERTE TÉLÉMÉTRIE : <?= $db_error ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-12">
            
            <div class="glass-card p-10 flex flex-col items-center">
                <div class="gauge-container" style="--v: <?= min($countCars, 100) ?>; --c: var(--m-red);">
                    <div class="gauge-ring"></div>
                    <div class="gauge-needle"></div>
                    <div class="gauge-center"></div>
                </div>
                <div class="text-center -mt-6 relative z-30">
                    <span class="text-5xl font-m font-black italic block leading-none"><?= $countCars ?></span>
                    <span class="text-[10px] text-gray-500 uppercase tracking-widest mt-2 block">Stock Actif</span>
                </div>
            </div>

            <div class="glass-card p-10 flex flex-col items-center">
                <div class="gauge-container" style="--v: <?= min($countClients, 100) ?>; --c: #fff;">
                    <div class="gauge-ring"></div>
                    <div class="gauge-needle"></div>
                    <div class="gauge-center"></div>
                </div>
                <div class="text-center -mt-6 relative z-30">
                    <span class="text-5xl font-m font-black italic block leading-none"><?= $countClients ?></span>
                    <span class="text-[10px] text-gray-500 uppercase tracking-widest mt-2 block">Pilotes</span>
                </div>
            </div>

            <div class="glass-card p-10 flex flex-col items-center">
                <div class="gauge-container" style="--v: <?= min($countMessages * 10, 100) ?>; --c: var(--m-red);">
                    <div class="gauge-ring"></div>
                    <div class="gauge-needle"></div>
                    <div class="gauge-center"></div>
                </div>
                <div class="text-center -mt-6 relative z-30">
                    <span class="text-5xl font-m font-black italic block leading-none"><?= $countMessages ?></span>
                    <span class="text-[10px] text-gray-500 uppercase tracking-widest mt-2 block">Flux Messages</span>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 glass-card overflow-hidden">
                <div class="p-8 border-b border-white/5 flex justify-between items-center bg-white/[0.01]">
                    <h3 class="text-xs font-m uppercase tracking-widest italic">Mission Logs: Derniers Conducteurs</h3>
                    <a href="admin_contact.php" class="text-[9px] font-bold text-red-600 uppercase hover:text-white transition">Voir Tout</a>
                </div>
                <table class="w-full text-left">
                    <thead class="text-[9px] text-gray-600 uppercase font-black bg-black/40">
                        <tr>
                            <th class="p-6">Conducteur Identify</th>
                            <th class="p-6">Canal Link</th>
                            <th class="p-6 text-right">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach($lastClients as $client): ?>
                        <tr class="hover:bg-red-600/[0.03] transition group">
                            <td class="p-6 flex items-center gap-5">
                                <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center font-m text-red-600 group-hover:border-red-600/50 transition">
                                    <?= strtoupper(substr($client['nom'], 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="text-xs font-bold uppercase italic leading-none"><?= htmlspecialchars($client['nom'] . ' ' . $client['prenom']) ?></p>
                                    <p class="text-[9px] text-gray-500 font-bold uppercase mt-1">Grade: Platinum</p>
                                </div>
                            </td>
                            <td class="p-6 font-mono text-xs text-gray-500"><?= htmlspecialchars($client['email']) ?></td>
                            <td class="p-6 text-right">
                                <span class="px-3 py-1 bg-green-500/10 text-green-500 text-[8px] font-m uppercase rounded-full">Actif</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="glass-card p-8 border-t-2 border-red-600 h-64 flex flex-col justify-center">
                <h4 class="text-[10px] font-m uppercase text-gray-500 mb-6">Efficiency HUD</h4>
                <div class="flex items-baseline gap-2">
                    <span class="text-7xl font-m font-black italic tracking-tighter">98</span>
                    <span class="text-xl font-bold text-red-600">%</span>
                </div>
                <div class="w-full bg-white/5 h-2 rounded-full mt-6 overflow-hidden">
                    <div class="h-full bg-red-600" style="width: 98%"></div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>