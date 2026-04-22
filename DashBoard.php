<?php
/**
 * SUPERCAR - Cockpit Precision Edition v14.3
 * Concept : Industrial Data Modules & Automotive UI
 */
session_start();
require_once 'config.php';

// Vérification de l'authentification
if (!array_key_exists('admin_id', $_SESSION)) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn; 

try {
    // Récupération dynamique de l'administrateur
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    
    if (!$resAdmin) {
        session_destroy();
        header('Location: login.php');
        exit();
    }

    $adminName = $resAdmin['prenom'] . " " . $resAdmin['nom'];

    // Statistiques en temps réel
    $countCars = $db->query("SELECT COUNT(*) FROM voiture")->fetchColumn() ?: 0;
    $countClients = $db->query("SELECT COUNT(*) FROM client")->fetchColumn() ?: 0;
    $countMessages = $db->query("SELECT COUNT(*) FROM contact")->fetchColumn() ?: 0;

    // Récupération des pilotes récents
    $stmtRecent = $db->query("SELECT nom, prenom, email FROM client ORDER BY id_client DESC LIMIT 3");
    $recentDrivers = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("ERREUR SYSTÈME : Connexion au serveur SuperCar interrompue.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Command Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root { --bg-deep: #050505; --accent-red: #ff1e26; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-deep); color: #fff; margin:0; overflow: hidden; }
        .font-m { font-family: 'Orbitron', sans-serif; }

        /* Arrière-plan Grille Technologique */
        .grid-bg {
            background-image: linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px), 
                              linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* Menu de Navigation */
        .nav-link {
            display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1.25rem;
            border-radius: 14px; color: #555; transition: 0.3s; font-size: 0.85rem; font-weight: 600;
        }
        .nav-link:hover, .nav-link.active { color: #fff; background: rgba(255,255,255,0.05); }
        .nav-link.active { border-left: 3px solid var(--accent-red); background: linear-gradient(90deg, rgba(255,30,38,0.1), transparent); }

        /* DATA MODULES */
        .data-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 32px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            backdrop-filter: blur(10px);
        }
        .data-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 30, 38, 0.3);
            background: rgba(255, 255, 255, 0.04);
        }
        .data-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%;
            background: var(--c); opacity: 0.6;
        }

        .unit-badge {
            font-size: 9px; font-weight: 900; padding: 4px 10px;
            border-radius: 6px; text-transform: uppercase; letter-spacing: 1px;
            background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
        }

        .logout-btn {
            display: flex; align-items: center; justify-content: center; gap: 0.75rem;
            width: 100%; padding: 0.75rem; margin-bottom: 0.8rem;
            background: rgba(255, 30, 38, 0.05); border: 1px solid rgba(255, 30, 38, 0.15);
            border-radius: 14px; color: #ff3b3b; font-size: 11px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 1px; transition: 0.3s;
        }
        .logout-btn:hover { background: rgba(255, 30, 38, 0.15); color: #fff; border-color: var(--accent-red); }
    </style>
</head>
<body class="flex h-screen">

    <aside class="w-72 bg-black border-r border-white/5 p-6 flex flex-col z-10">
        <div class="mb-12 px-2 text-2xl font-m font-black italic tracking-tighter uppercase">
            SUPER<span class="text-red-600">CAR</span>
        </div>
        
        <nav class="flex-1 space-y-1">
            <p class="text-[9px] text-gray-700 font-bold uppercase tracking-[2px] mb-4 px-2">Navigation</p>
            <a href="DashBoard.php" class="nav-link active"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
            <a href="admin_voitures.php" class="nav-link"><i class="fa-solid fa-car-side"></i> Inventaire</a>
            <a href="admin_demande.php" class="nav-link"><i class="fa-solid fa-calendar-check"></i> Demandes</a>
            <a href="admin_contact.php" class="nav-link"><i class="fa-solid fa-envelope"></i> Messages</a>
            <a href="admin_services.php" class="nav-link"><i class="fa-solid fa-screwdriver-wrench"></i> Services</a>
        </nav>

        <div class="mt-auto pt-6 border-t border-white/5">
            <a href="logout.php" class="logout-btn">
                <i class="fa-solid fa-power-off text-xs"></i> Se déconnecter
            </a>
            
            <div class="bg-white/5 p-4 rounded-2xl flex items-center justify-between border border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-xs font-bold border border-white/10 text-gray-400">
                        <?= strtoupper(substr($adminName, 0, 1)) ?>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white"><?= htmlspecialchars($adminName) ?></p>
                        <p class="text-[10px] text-gray-500 font-medium italic">Authentifié</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 p-10 overflow-y-auto bg-[#080808] grid-bg">
        <header class="flex justify-between items-center mb-16">
            <div>
                <h2 class="text-4xl font-m font-black italic uppercase tracking-tighter">Command <span class="text-gray-800">Center</span></h2>
                <p class="text-[10px] text-green-500 font-bold uppercase tracking-[3px] mt-2">SYSTEM STATUS: NOMINAL</p>
            </div>
            <div class="bg-red-600 px-8 py-3 rounded-full font-m text-[10px] uppercase shadow-[0_0_20px_rgba(255,30,38,0.3)]">Session Sécurisée</div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-16">
            
            <div class="data-card" style="--c: var(--accent-red)">
                <div class="flex justify-between items-start mb-6">
                    <div class="unit-badge text-red-500">Inventaire</div>
                    <i class="fa-solid fa-warehouse text-gray-700"></i>
                </div>
                <div class="text-6xl font-m font-black italic tracking-tighter mb-1"><?= $countCars ?></div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Véhicules en Stock</p>
            </div>

            <div class="data-card" style="--c: #fff">
                <div class="flex justify-between items-start mb-6">
                    <div class="unit-badge text-white">Registre</div>
                    <i class="fa-solid fa-users text-gray-700"></i>
                </div>
                <div class="text-6xl font-m font-black italic tracking-tighter mb-1"><?= $countClients ?></div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Pilotes Enregistrés</p>
            </div>

            <div class="data-card" style="--c: #00ffaa">
                <div class="flex justify-between items-start mb-6">
                    <div class="unit-badge text-green-500">Flux Live</div>
                    <i class="fa-solid fa-comment-dots text-gray-700"></i>
                </div>
                <div class="text-6xl font-m font-black italic tracking-tighter mb-1"><?= $countMessages ?></div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Messages Reçus</p>
            </div>

            <div class="data-card" style="--c: var(--accent-red)">
                <div class="flex justify-between items-start mb-6">
                    <div class="unit-badge text-red-500">Performance</div>
                    <i class="fa-solid fa-bolt text-gray-700"></i>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-6xl font-m font-black italic tracking-tighter">98</span>
                    <span class="text-red-600 font-bold text-xl">%</span>
                </div>
                <div class="mt-4 h-1 bg-white/5 rounded-full">
                    <div class="h-full bg-red-600 shadow-[0_0_10px_#ff1e26]" style="width: 98%"></div>
                </div>
            </div>
        </div>

        <div class="bg-white/[0.02] border border-white/5 rounded-[32px] overflow-hidden backdrop-blur-md">
            <div class="p-8 border-b border-white/5 bg-white/[0.01] flex justify-between items-center">
                <h3 class="text-[11px] font-m uppercase tracking-[3px] text-gray-500 italic">Télémétrie Pilotes Récents</h3>
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
            </div>
            <table class="w-full text-left">
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($recentDrivers)): ?>
                        <tr><td class="p-10 text-center text-gray-600 italic">Aucune donnée pilote détectée.</td></tr>
                    <?php else: ?>
                        <?php foreach($recentDrivers as $driver): ?>
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="p-6 flex items-center gap-5">
                                <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center font-bold text-lg text-red-600 border border-white/5 group-hover:border-red-600/30 transition-all">
                                    <?= strtoupper(substr($driver['nom'], 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="text-sm font-bold uppercase italic tracking-tight text-gray-200"><?= htmlspecialchars($driver['nom'] . ' ' . $driver['prenom']) ?></p>
                                    <p class="text-[9px] text-gray-600 font-bold uppercase tracking-widest">Profil Vérifié</p>
                                </div>
                            </td>
                            <td class="p-6 font-mono text-xs text-gray-500"><?= htmlspecialchars($driver['email']) ?></td>
                            <td class="p-6 text-right">
                                <span class="px-4 py-2 bg-white/5 text-gray-400 text-[8px] font-black uppercase rounded-lg border border-white/5 hover:text-white transition-colors cursor-pointer">Détails</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>