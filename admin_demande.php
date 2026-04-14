<?php
/**
 * SUPERCAR - Gestion des Demandes (Version Cockpit Finale - Icon Only)
 */
session_start();
require_once 'config.php';

// Vérification de la session
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn;

try {
    // Récupération de l'admin
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";

    // --- COMPTAGE DYNAMIQUE ---
    $totalDemandes = $db->query("SELECT COUNT(*) FROM demande_essai")->fetchColumn();
    $enCours = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut = 'en_attente' OR statut IS NULL")->fetchColumn();
    $acceptees = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut = 'accepte'")->fetchColumn();
    $refusees = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut = 'refuse'")->fetchColumn();

    // Récupération de la liste
    $query = "SELECT * FROM demande_essai ORDER BY id_demande DESC";
    $demandes = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $demandes = [];
    $totalDemandes = $enCours = $acceptees = $refusees = 0;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SuperCar | Command Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Syncopate:wght@700&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    
    <style>
        body { background-color: var(--cosmos); }
        
        .sidebar {
            background: rgba(0, 0, 0, 0.2);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .nav-link-admin {
            display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1.25rem;
            border-radius: 14px; color: rgba(255,255,255,0.6); transition: 0.3s; 
            font-size: 0.85rem; font-weight: 600;
        }
        
        .nav-link-admin:hover { color: white; background: rgba(255,255,255,0.05); }
        
        .nav-link-admin.active { 
            color: white; 
            background: linear-gradient(90deg, rgba(193, 18, 31, 0.2), transparent);
            border-left: 3px solid var(--crimson);
        }

        .logout-btn-admin {
            display: flex; align-items: center; justify-content: center; gap: 0.75rem;
            width: 100%; padding: 0.75rem; margin-bottom: 0.8rem;
            background: rgba(193, 18, 31, 0.1); border: 1px solid rgba(193, 18, 31, 0.2);
            border-radius: 14px; color: #ff4d4d; font-size: 11px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 1px; transition: 0.3s;
        }
        .logout-btn-admin:hover { background: var(--crimson); color: white; }

        .stat-icon {
            position: absolute; right: -10px; bottom: -10px;
            font-size: 5rem; opacity: 0.05; transform: rotate(-15deg);
            transition: all 0.5s ease; pointer-events: none;
        }
        .group:hover .stat-icon {
            opacity: 0.15; transform: rotate(0deg) scale(1.1);
            color: white;
        }

        .table-container {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }

        .status-badge {
            background: rgba(193, 18, 31, 0.15);
            color: var(--crimson);
            border: 1px solid rgba(193, 18, 31, 0.3);
        }

        /* Style pour l'icône d'archive seule */
        .archive-icon-only {
            color: rgba(255,255,255,0.3);
            font-size: 1.25rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .archive-icon-only:hover {
            color: var(--crimson);
            transform: scale(1.2) rotate(-5deg);
            text-shadow: 0 0 15px rgba(193, 18, 31, 0.5);
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <aside class="w-72 sidebar p-6 flex flex-col">
        <div class="font-title text-xl tracking-tighter uppercase mb-10">
            SUPER<span class="text-[var(--crimson)]">CAR</span>
        </div>
        
        <nav class="flex-1 space-y-1">
            <p class="text-[9px] text-gray-500 font-bold uppercase tracking-[2px] mb-4 px-2">Menu Principal</p>
            <a href="DashBoard.php" class="nav-link-admin"><i class="fa-solid fa-gauge-high w-5"></i> Dashboard</a>
            <a href="admin_voitures.php" class="nav-link-admin"><i class="fa-solid fa-car-side w-5"></i> Inventaire</a>
            <a href="admin_demande.php" class="nav-link-admin active"><i class="fa-solid fa-calendar-check w-5"></i> Demandes</a>
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

    <main class="flex-1 p-10 overflow-y-auto split-bg">
        <header class="mb-12">
            <h2 class="text-4xl font-title font-black italic uppercase tracking-tighter">
                GESTION <span style="color: var(--crimson)">DEMANDES</span>
            </h2>
            
            <div class="flex items-center justify-between mt-2 border-b border-white/5 pb-4">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[3px] italic">
                    Analyse des flux d'essais en temps réel
                </p>
                
                <a href="admin_archives.php" title="Archives des demandes" class="archive-icon-only">
                    <i class="fa-solid fa-box-archive"></i>
                </a>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-white/5 border border-white/10 p-6 rounded-3xl backdrop-blur-md relative overflow-hidden group">
                <i class="fa-solid fa-chart-line stat-icon"></i>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Flux Total</p>
                <h3 class="text-3xl font-title font-black italic text-white"><?= $totalDemandes ?></h3>
                <div class="w-10 h-1 mt-3 bg-marble rounded-full"></div>
            </div>

            <div class="bg-white/5 border border-white/10 p-6 rounded-3xl backdrop-blur-md relative overflow-hidden group">
                <i class="fa-solid fa-bolt-lightning stat-icon"></i>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">En Attente</p>
                <h3 class="text-3xl font-title font-black italic text-white"><?= $enCours ?></h3>
                <div class="w-10 h-1 mt-3 bg-yellow-500 rounded-full animate-pulse"></div>
            </div>

            <div class="bg-white/5 border border-white/10 p-6 rounded-3xl backdrop-blur-md relative overflow-hidden group border-l-4 border-l-[#00ff88]">
                <i class="fa-solid fa-check-double stat-icon"></i>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Approuvées</p>
                <h3 class="text-3xl font-title font-black italic text-white"><?= $acceptees ?></h3>
                <div class="w-10 h-1 mt-3 bg-green-500 rounded-full"></div>
            </div>

            <div class="bg-white/5 border border-white/10 p-6 rounded-3xl backdrop-blur-md relative overflow-hidden group border-l-4 border-l-[var(--crimson)]">
                <i class="fa-solid fa-ban stat-icon"></i>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Refusées</p>
                <h3 class="text-3xl font-title font-black italic text-white"><?= $refusees ?></h3>
                <div class="w-10 h-1 mt-3 bg-[var(--crimson)] rounded-full"></div>
            </div>
        </div>

        <div class="table-container rounded-[32px] overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-[10px] uppercase font-title tracking-widest text-gray-400">
                    <tr>
                        <th class="p-6">Client / Pilote</th>
                        <th class="p-6">Statut Mission</th>
                        <th class="p-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($demandes)): ?>
                        <tr><td colspan="3" class="p-20 text-center text-gray-500 italic uppercase text-[10px] tracking-widest">Aucune donnée captée par le radar</td></tr>
                    <?php else: ?>
                        <?php foreach($demandes as $d): ?>
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="p-6">
                                <p class="font-bold text-sm uppercase italic tracking-tight text-white"><?= htmlspecialchars($d['nom_client'] ?? 'Anonyme') ?></p>
                                <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">ID Log: #<?= $d['id_demande'] ?></p>
                            </td>
                            <td class="p-6">
                                <span class="px-4 py-1.5 status-badge text-[8px] font-black uppercase rounded-full tracking-widest italic">
                                    <i class="fa-solid fa-satellite-dish mr-1"></i> Signal Reçu
                                </span>
                            </td>
                            <td class="p-6 text-right">
                                <button class="text-[10px] font-bold uppercase text-gray-400 hover:text-white transition-colors">
                                    Détails <i class="fa-solid fa-arrow-right ml-2 text-[var(--crimson)]"></i>
                                </button>
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