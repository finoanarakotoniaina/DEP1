<?php
/**
 * SUPERCAR - Cockpit Precision Edition v14.3
 * Central Archive Vault (Version Corrective - Sans crash Heure)
 */
session_start();
require_once 'config.php';

// Vérification de l'authentification de l'administrateur
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn; 

try {
    // Configuration des erreurs PDO
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération du profil de l'administrateur connecté
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";

    // --- 1. ARCHIVES VÉHICULES ---
    $countArchivedCars = $db->query("SELECT COUNT(*) FROM voiture WHERE disponibilite = 0")->fetchColumn() ?: 0;
    
    $stmtCars = $db->query("
        SELECT v.id_voiture, v.modele, v.annee, v.prix, m.nom_marque 
        FROM voiture v
        LEFT JOIN marque m ON v.id_marque = m.id_marque
        WHERE v.disponibilite = 0 
        ORDER BY v.id_voiture DESC
    ");
    $archivedCars = $stmtCars->fetchAll(PDO::FETCH_ASSOC);

    // --- 2. ARCHIVES DEMANDES D'ESSAIS ---
    $countArchivedEssais = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut = 'termine' OR statut = 'refuse'")->fetchColumn() ?: 0;

    // FIX : Suppression de d.heure_souhaitee pour contourner le bug de colonne manquante
    $stmtEssais = $db->query("
        SELECT d.id_demande, d.date_souhaitee, d.statut, 
               c.nom AS client_nom, c.prenom AS client_prenom, 
               v.modele AS voiture_modele, m.nom_marque
        FROM demande_essai d
        JOIN client c ON d.id_client = c.id_client
        JOIN voiture v ON d.id_voiture = v.id_voiture
        LEFT JOIN marque m ON v.id_marque = m.id_marque
        WHERE d.statut = 'termine' OR d.statut = 'refuse'
        ORDER BY d.date_souhaitee DESC
    ");
    $archivedEssais = $stmtEssais->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("ERREUR SYSTÈME : Connexion interrompue ou structure BDD invalide. Détail : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Archive Vault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Syncopate:wght@700&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    
    <style>
        body { background-color: var(--cosmos); color: #fff; }
        
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

        .stat-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            transition: 0.4s;
        }

        .table-container {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .grid-bg-overlay {
            background-image: linear-gradient(rgba(255, 255, 255, 0.01) 1px, transparent 1px), 
                              linear-gradient(90deg, rgba(255, 255, 255, 0.01) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .tab-btn {
            padding: 0.75rem 1.5rem; font-size: 10px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 2px; border-radius: 12px;
            transition: 0.3s; border: 1px solid transparent; color: rgba(255,255,255,0.5);
        }
        .tab-btn:hover { color: #fff; background: rgba(255,255,255,0.02); }
        .tab-btn.active {
            background: rgba(193, 18, 31, 0.12); border-color: rgba(193, 18, 31, 0.3); color: #fff;
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <aside class="w-72 sidebar p-6 flex flex-col z-20">
        <div class="font-title text-xl tracking-tighter uppercase mb-10">
            SUPER<span class="text-[var(--crimson)]">CAR</span>
        </div>
        
        <nav class="flex-1 space-y-1">
            <p class="text-[9px] text-gray-500 font-bold uppercase tracking-[2px] mb-4 px-2">Menu Principal</p>
            <a href="tableaudebord.php" class="nav-link-admin"><i class="fa-solid fa-gauge-high w-5"></i> Tableau de bord</a>
            <a href="admin_accueil.php" class="nav-link-admin"><i class="fa-solid fa-house w-5"></i> Page Accueil</a>
            <a href="admin_voitures.php" class="nav-link-admin"><i class="fa-solid fa-car-side w-5"></i> Inventaire</a>
            <a href="admin_demande.php" class="nav-link-admin"><i class="fa-solid fa-calendar-check w-5"></i> Demandes</a>
            <a href="admin_contact.php" class="nav-link-admin"><i class="fa-solid fa-envelope w-5"></i> Messages</a>
            <a href="admin_services.php" class="nav-link-admin"><i class="fa-solid fa-screwdriver-wrench w-5"></i> Services</a>
            <a href="admin_archive.php" class="nav-link-admin active"><i class="fa-solid fa-box-archive w-5"></i> Archives</a>
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
                    ARCHIVE <span style="color: var(--crimson)">VAULT</span>
                </h2>
                <p class="text-[10px] text-green-500 font-bold uppercase tracking-[3px] mt-2">
                    <i class="fa-solid fa-circle text-[6px] mr-2 animate-pulse"></i> System Status: Operational
                </p>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="stat-card p-6 rounded-3xl border-l-4 border-l-[var(--crimson)]">
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Unités Hors-Flotte</p>
                <h3 class="text-4xl font-black italic text-white"><?= $countArchivedCars ?></h3>
            </div>
            <div class="stat-card p-6 rounded-3xl border-l-4 border-l-amber-500">
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Sessions Clôturées</p>
                <h3 class="text-4xl font-black italic text-white"><?= $countArchivedEssais ?></h3>
            </div>
        </div>

        <div class="flex gap-3 mb-6 bg-white/5 p-1.5 rounded-2xl w-fit border border-white/5 backdrop-blur-md">
            <button onclick="switchTab('cars')" id="btn-tab-cars" class="tab-btn active">
                <i class="fa-solid fa-car-side mr-2"></i> Flotte Retirée (<?= $countArchivedCars ?>)
            </button>
            <button onclick="switchTab('essais')" id="btn-tab-essais" class="tab-btn">
                <i class="fa-solid fa-clock-rotate-left mr-2"></i> Historique Essais (<?= $countArchivedEssais ?>)
            </button>
        </div>

        <div id="container-tab-cars" class="table-container rounded-[32px] overflow-hidden backdrop-blur-md">
            <div class="p-6 border-b border-white/5 bg-white/[0.01]">
                <h3 class="text-[11px] font-title uppercase tracking-[3px] text-gray-400 italic">Registre Matériel</h3>
            </div>
            <table class="w-full text-left">
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($archivedCars)): ?>
                        <tr><td class="p-10 text-center text-gray-600 italic text-[10px] uppercase tracking-widest">Aucun véhicule archivé</td></tr>
                    <?php else: ?>
                        <?php foreach($archivedCars as $car): ?>
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="p-6 flex items-center gap-5">
                                <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center font-bold text-md text-[var(--crimson)] border border-white/5">
                                    <?= strtoupper(substr($car['nom_marque'] ?? 'S', 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="font-bold text-sm uppercase italic text-white"><?= htmlspecialchars(($car['nom_marque'] ?? 'Marque') . ' ' . $car['modele']) ?></p>
                                    <p class="text-[9px] text-gray-500 font-bold uppercase">Année : <?= htmlspecialchars($car['annee']) ?></p>
                                </div>
                            </td>
                            <td class="p-6 font-mono text-xs text-gray-400"><?= number_format($car['prix'], 0, ',', ' ') ?> €</td>
                            <td class="p-6 text-right">
                                <a href="admin_voitures.php?action=edit&id=<?= $car['id_voiture'] ?>" class="px-4 py-2 bg-white/5 text-[8px] font-black uppercase rounded-lg border border-white/5 hover:bg-[var(--crimson)] transition-all">Dossier</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="container-tab-essais" class="table-container rounded-[32px] overflow-hidden backdrop-blur-md hidden">
            <div class="p-6 border-b border-white/5 bg-white/[0.01]">
                <h3 class="text-[11px] font-title uppercase tracking-[3px] text-gray-400 italic">Sessions Roulages Triées</h3>
            </div>
            <table class="w-full text-left">
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($archivedEssais)): ?>
                        <tr><td class="p-10 text-center text-gray-600 italic text-[10px] uppercase tracking-widest">Aucun historique d'essai</td></tr>
                    <?php else: ?>
                        <?php foreach($archivedEssais as $essai): ?>
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="p-6">
                                <p class="font-bold text-sm uppercase italic text-white"><?= htmlspecialchars($essai['client_prenom'] . ' ' . $essai['client_nom']) ?></p>
                                <p class="text-[9px] text-gray-500 font-bold uppercase">Pilote Client</p>
                            </td>
                            <td class="p-6">
                                <p class="font-bold text-sm text-gray-300 italic"><?= htmlspecialchars(($essai['nom_marque'] ?? 'Marque') . ' ' . $essai['voiture_modele']) ?></p>
                                <p class="text-[9px] text-gray-500 font-bold uppercase">Bolide</p>
                            </td>
                            <td class="p-6 font-mono text-xs text-gray-400">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-calendar-days text-[11px] text-gray-600"></i>
                                    <span><?= date('d/m/Y', strtotime($essai['date_souhaitee'])) ?></span>
                                </div>
                            </td>
                            <td class="p-6 text-right">
                                <?php if ($essai['statut'] === 'termine'): ?>
                                    <span class="inline-flex items-center gap-2 px-4 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <i class="fa-solid fa-flag-checkered text-xs"></i> Terminé
                                    </span>
                                <?php elseif ($essai['statut'] === 'refuse'): ?>
                                    <span class="inline-flex items-center gap-2 px-4 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        <i class="fa-solid fa-ban text-xs"></i> Refusé
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function switchTab(tabName) {
            document.getElementById('container-tab-cars').classList.add('hidden');
            document.getElementById('container-tab-essais').classList.add('hidden');
            document.getElementById('btn-tab-cars').classList.remove('active');
            document.getElementById('btn-tab-essais').classList.remove('active');

            if (tabName === 'cars') {
                document.getElementById('container-tab-cars').classList.remove('hidden');
                document.getElementById('btn-tab-cars').classList.add('active');
            } else if (tabName === 'essais') {
                document.getElementById('container-tab-essais').classList.remove('hidden');
                document.getElementById('btn-tab-essais').classList.add('active');
            }
        }
    </script>
</body>
</html>