<?php
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn; 

try {
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";

    $countCars = $db->query("SELECT COUNT(*) FROM voiture")->fetchColumn() ?: 0;
    $countClients = $db->query("SELECT COUNT(*) FROM client")->fetchColumn() ?: 0;
    $countMessages = $db->query("SELECT COUNT(*) FROM contact_client")->fetchColumn() ?: 0;

    $stmtRecent = $db->query("SELECT id_client, nom, prenom, email FROM client ORDER BY id_client DESC LIMIT 3");
    $recentDrivers = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("ERREUR SYSTÈME : Connexion interrompue.");
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Command Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Syncopate:wght@700&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
     <link rel="stylesheet" href="../style.css">
    <style>
        body { background-color: var(--cosmos); color: #fff; }
        .sidebar { background: rgba(0, 0, 0, 0.2); border-right: 1px solid rgba(255, 255, 255, 0.05); }
        .nav-link-admin { display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1.25rem; border-radius: 14px; color: rgba(255,255,255,0.6); transition: 0.3s; font-size: 0.85rem; font-weight: 600; }
        .nav-link-admin:hover { color: white; background: rgba(255,255,255,0.05); }
        .nav-link-admin.active { color: white; background: linear-gradient(90deg, rgba(193, 18, 31, 0.2), transparent); border-left: 3px solid var(--crimson); }
        .logout-btn-admin { display: flex; align-items: center; justify-content: center; gap: 0.75rem; width: 100%; padding: 0.75rem; margin-bottom: 0.8rem; background: rgba(193, 18, 31, 0.1); border: 1px solid rgba(193, 18, 31, 0.2); border-radius: 14px; color: #ff4d4d; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
        .logout-btn-admin:hover { background: var(--crimson); color: white; }
        .stat-card { background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .stat-card:hover { transform: translateY(-5px); border-color: rgba(193, 18, 31, 0.3); background: rgba(255, 255, 255, 0.04); }
        .stat-icon { position: absolute; right: -10px; bottom: -10px; font-size: 4rem; opacity: 0.03; transform: rotate(-15deg); transition: all 0.5s ease; pointer-events: none; }
        .group:hover .stat-icon { opacity: 0.1; transform: rotate(0deg) scale(1.1); }
        .table-container { background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05); }
        .grid-bg-overlay { background-image: linear-gradient(rgba(255, 255, 255, 0.01) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.01) 1px, transparent 1px); background-size: 40px 40px; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <aside class="w-72 sidebar p-6 flex flex-col z-20">
        <div class="font-title text-xl tracking-tighter uppercase mb-4">
            SUPER<span class="text-[var(--crimson)]">CAR</span>
        </div>
        
        <nav class="flex-1 space-y-1">
            <a href="tableaudebord.php" class="nav-link-admin active"><i class="fa-solid fa-gauge-high w-5"></i> Tableau de bord</a>
            <a href="admin_accueil.php" class="nav-link-admin"><i class="fa-solid fa-house w-5"></i> Page Accueil</a>
            <a href="admin_affichage_contact.php" class="nav-link-admin"><i class="fa-solid fa-address-book w-5"></i> Page Contact</a>
            <a href="admin_voitures.php" class="nav-link-admin"><i class="fa-solid fa-car-side w-5"></i> Inventaire</a>
            <a href="admin_demande.php" class="nav-link-admin"><i class="fa-solid fa-calendar-check w-5"></i> Demandes</a>
            <a href="admin_contact.php" class="nav-link-admin"><i class="fa-solid fa-envelope w-5"></i> Messages</a>
            <a href="admin_services.php" class="nav-link-admin"><i class="fa-solid fa-screwdriver-wrench w-5"></i> Services</a>
        </nav>

        <div class="mt-auto pt-6 border-t border-white/5">
            <a href="logout.php" class="logout-btn-admin">
                <i class="fa-solid fa-power-off text-xs"></i> Se déconnecter
            </a>
            
            <div class="bg-white/5 p-4 rounded-2xl flex items-center justify-between border border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-xs font-bold border border-white/10 text-white">
                        <?= strtoupper(substr($adminFullName, 0, 1)) ?>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white"><?= htmlspecialchars($adminFullName) ?></p>
                        <p class="text-[10px] text-gray-400 font-medium italic">Administrateur</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 p-10 overflow-y-auto grid-bg-overlay">
        <header class="mb-12 flex justify-between items-end">
            <div>
                <h2 class="text-4xl font-title font-black italic uppercase tracking-tighter">
                    COMMAND <span style="color: var(--crimson)">CENTER</span>
                </h2>
                <p class="text-[10px] text-green-500 font-bold uppercase tracking-[3px] mt-2">
                    <i class="fa-solid fa-circle text-[6px] mr-2 animate-pulse"></i> System Status: Nominal
                </p>
            </div>
            <div class="hidden md:block">
                <span class="px-6 py-2 bg-white/5 border border-white/10 rounded-full text-[9px] font-bold uppercase tracking-widest text-gray-400">
                    Session ID: <?= session_id() ?>
                </span>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            <div class="stat-card p-8 rounded-3xl relative overflow-hidden group">
                <i class="fa-solid fa-car-side stat-icon"></i>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Inventaire</p>
                <h3 class="text-5xl font-m font-black italic text-white"><?= $countCars ?></h3>
                <p class="text-[9px] text-gray-600 mt-2 font-bold uppercase">Véhicules en stock</p>
                <div class="w-12 h-1 mt-4 bg-[var(--crimson)] rounded-full"></div>
            </div>

            <div class="stat-card p-8 rounded-3xl relative overflow-hidden group">
                <i class="fa-solid fa-user-astronaut stat-icon"></i>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Registre</p>
                <h3 class="text-5xl font-m font-black italic text-white"><?= $countClients ?></h3>
                <p class="text-[9px] text-gray-600 mt-2 font-bold uppercase">Pilotes certifiés</p>
                <div class="w-12 h-1 mt-4 bg-white/20 rounded-full"></div>
            </div>

            <div class="stat-card p-8 rounded-3xl relative overflow-hidden group">
                <i class="fa-solid fa-satellite-dish stat-icon"></i>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Messages</p>
                <h3 class="text-5xl font-m font-black italic text-white"><?= $countMessages ?></h3>
                <p class="text-[9px] text-gray-600 mt-2 font-bold uppercase">Flux live reçu</p>
                <div class="w-12 h-1 mt-4 bg-green-500/50 rounded-full"></div>
            </div>

            <div class="stat-card p-8 rounded-3xl relative overflow-hidden group border-l-4 border-l-[var(--crimson)]">
                <i class="fa-solid fa-gauge-simple-high stat-icon"></i>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Performance</p>
                <div class="flex items-baseline gap-1">
                    <h3 class="text-5xl font-m font-black italic text-white">98</h3>
                    <span class="text-[var(--crimson)] font-bold text-xl">%</span>
                </div>
                <div class="mt-4 h-1.5 bg-white/5 rounded-full overflow-hidden">
                    <div class="h-full bg-[var(--crimson)] shadow-[0_0_15px_#C1121F]" style="width: 98%"></div>
                </div>
            </div>
        </div>

        <div class="table-container rounded-[32px] overflow-hidden backdrop-blur-md">
            <div class="p-8 border-b border-white/5 bg-white/[0.01] flex justify-between items-center">
                <h3 class="text-[11px] font-title uppercase tracking-[3px] text-gray-400 italic">Derniers Pilotes Enregistrés</h3>
                <div class="text-[9px] font-bold uppercase text-green-500 tracking-tighter">Live Telemetry</div>
            </div>
            
            <table class="w-full text-left">
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($recentDrivers)): ?>
                        <tr><td class="p-10 text-center text-gray-600 italic uppercase text-[10px] tracking-widest">Aucun signal détecté</td></tr>
                    <?php else: ?>
                        <?php foreach($recentDrivers as $driver): ?>
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="p-6 flex items-center gap-5">
                                <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center font-bold text-lg text-[var(--crimson)] border border-white/5 group-hover:border-[var(--crimson)]/30 transition-all">
                                    <?= strtoupper(substr($driver['nom'], 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="font-bold text-sm uppercase italic tracking-tight text-white">
                                        <?= htmlspecialchars($driver['nom'] . ' ' . $driver['prenom']) ?>
                                    </p>
                                    <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">Status: Verified Pilot</p>
                                </div>
                            </td>
                            <td class="p-6 font-mono text-xs text-gray-500">
                                <i class="fa-solid fa-at mr-2 opacity-30"></i><?= htmlspecialchars($driver['email']) ?>
                            </td>
                            <td class="p-6 text-right">
                                <a href="admin_client_detail.php?id=<?= $driver['id_client'] ?>"
                                   class="px-5 py-2 bg-white/5 text-[8px] font-black uppercase rounded-lg border border-white/5 hover:bg-[var(--crimson)] hover:text-white transition-all italic tracking-widest">
                                    Consulter
                                </a>
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