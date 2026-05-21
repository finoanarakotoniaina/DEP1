<?php
/**
 * SUPERCAR - Cockpit Precision Edition v14.3
 * Fiche Pilote - Client Detail
 */
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: tableaudebord.php');
    exit();
}

$clientId = (int) $_GET['id'];
$db = $pdo ?? $db ?? $conn;

try {
    $stmtClient = $db->prepare("SELECT * FROM client WHERE id_client = ?");
    $stmtClient->execute([$clientId]);
    $client = $stmtClient->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        header('Location: tableaudebord.php');
        exit();
    }

    $demandes = [];
    try {
        $stmtDemandes = $db->prepare("
            SELECT d.*, v.marque, v.modele 
            FROM demande_essai d 
            LEFT JOIN voiture v ON d.id_voiture = v.id_voiture 
            WHERE d.id_client = ? 
            ORDER BY d.id_demande DESC
        ");
        $stmtDemandes->execute([$clientId]);
        $demandes = $stmtDemandes->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}

    $messages = [];
    try {
        $stmtMessages = $db->prepare("SELECT * FROM contact WHERE id_contact = ? ORDER BY id_contact DESC");
        $stmtMessages->execute([$client['id_contact']]);
        $messages = $stmtMessages->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}

    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";

} catch (PDOException $e) {
    die("ERREUR SYSTÈME : " . $e->getMessage());
}

$initials = strtoupper(substr($client['nom'], 0, 1) . substr($client['prenom'], 0, 1));
$fullName = htmlspecialchars($client['prenom'] . ' ' . $client['nom']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Fiche Pilote — <?= $fullName ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Syncopate:wght@700&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">

    <style>
        body { background-color: var(--cosmos); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; }

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

        .grid-bg-overlay {
            background-image: linear-gradient(rgba(255,255,255,0.01) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255,255,255,0.01) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .glass-card {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.05);
            backdrop-filter: blur(12px);
        }

        .pilot-avatar {
            background: linear-gradient(135deg, rgba(193,18,31,0.3), rgba(193,18,31,0.05));
            border: 2px solid rgba(193,18,31,0.4);
            box-shadow: 0 0 40px rgba(193,18,31,0.15), inset 0 0 20px rgba(193,18,31,0.05);
        }

        .info-field {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            transition: 0.3s;
        }
        .info-field:hover {
            border-color: rgba(193,18,31,0.3);
            background: rgba(255,255,255,0.04);
        }

        .badge-active {
            background: rgba(34,197,94,0.1);
            border: 1px solid rgba(34,197,94,0.2);
            color: #22c55e;
        }

        .demande-row { border-bottom: 1px solid rgba(255,255,255,0.04); }
        .demande-row:hover { background: rgba(255,255,255,0.02); }

        .scanline::after {
            content: ''; position: absolute; inset: 0;
            background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,0.005) 2px, rgba(255,255,255,0.005) 4px);
            pointer-events: none;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up  { animation: fadeUp 0.5s ease forwards; }
        .delay-1  { animation-delay: 0.1s; opacity: 0; }
        .delay-2  { animation-delay: 0.2s; opacity: 0; }
        .delay-3  { animation-delay: 0.3s; opacity: 0; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
        <aside class="w-72 sidebar p-6 flex flex-col z-20">
        <div class="font-title text-xl tracking-tighter uppercase mb-4">
            SUPER<span class="text-[var(--crimson)]">CAR</span>
        </div>
        
        <nav class="flex-1 space-y-1">
            <a href="tableaudebord.php" class="nav-link-admin"><i class="fa-solid fa-gauge-high w-5"></i> Tableau de bord</a>
            <a href="admin_accueil.php" class="nav-link-admin"><i class="fa-solid fa-house w-5"></i> Page Accueil</a>
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

    <!-- MAIN -->
    <main class="flex-1 p-10 overflow-y-auto grid-bg-overlay">

        <!-- HEADER -->
        <header class="mb-10 flex items-center gap-6 fade-up">
            <a href="tableaudebord.php" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-white/10 transition-all">
                <i class="fa-solid fa-arrow-left text-sm text-gray-400"></i>
            </a>
            <div>
                <p class="text-[9px] text-gray-500 font-bold uppercase tracking-[3px] mb-1">
                    <i class="fa-solid fa-chevron-right text-[6px] mr-1 text-[var(--crimson)]"></i>
                    Tableau de bord / Fiche Pilote
                </p>
                <h2 class="text-4xl font-title font-black italic uppercase tracking-tighter">
                    FICHE <span style="color: var(--crimson)">PILOTE</span>
                </h2>
            </div>
            <div class="ml-auto flex items-center gap-4">
                <span class="badge-active px-4 py-1.5 rounded-full text-[9px] font-bold uppercase tracking-[2px]">
                    <i class="fa-solid fa-circle text-[6px] mr-2 animate-pulse"></i> Verified Pilot
                </span>
                <a href="admin_demande.php?client=<?= $clientId ?>"
                   class="px-5 py-2.5 bg-white/5 border border-white/10 rounded-xl text-[10px] font-bold uppercase tracking-widest text-gray-300 hover:bg-white/10 transition-all">
                    <i class="fa-solid fa-calendar-check mr-2"></i>Demandes
                </a>
            </div>
        </header>

        <!-- PROFIL CARD -->
        <div class="glass-card rounded-3xl p-8 mb-6 scanline relative overflow-hidden fade-up delay-1">
            <div class="absolute right-0 top-0 w-96 h-96 rounded-full opacity-5"
                 style="background: radial-gradient(circle, var(--crimson), transparent); transform: translate(30%, -30%)"></div>

            <div class="flex flex-col md:flex-row items-start md:items-center gap-8 relative z-10">
                <div class="pilot-avatar w-28 h-28 rounded-3xl flex items-center justify-center flex-shrink-0">
                    <span class="font-title text-4xl font-black text-white"><?= $initials ?></span>
                </div>

                <div class="flex-1">
                    <p class="text-[9px] text-[var(--crimson)] font-bold uppercase tracking-[3px] mb-2">
                        ID PILOTE #<?= str_pad($client['id_client'], 4, '0', STR_PAD_LEFT) ?>
                    </p>
                    <h1 class="text-4xl font-title font-black italic uppercase tracking-tighter text-white mb-3">
                        <?= $fullName ?>
                    </h1>
                    <div class="flex flex-wrap gap-3">
                        <span class="px-4 py-1.5 bg-white/5 border border-white/10 rounded-full text-[9px] font-bold uppercase tracking-widest text-gray-400">
                            <i class="fa-solid fa-id-card mr-2"></i>Login #<?= $client['id_login'] ?? 'N/A' ?>
                        </span>
                        <span class="px-4 py-1.5 bg-white/5 border border-white/10 rounded-full text-[9px] font-bold uppercase tracking-widest text-gray-400">
                            <i class="fa-solid fa-link mr-2"></i>Contact #<?= $client['id_contact'] ?? 'N/A' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- INFOS GRID -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 fade-up delay-2">

            <div class="info-field">
                <p class="text-[8px] text-gray-500 font-bold uppercase tracking-[2px] mb-2">
                    <i class="fa-solid fa-at mr-2 text-[var(--crimson)]"></i>Adresse Email
                </p>
                <p class="font-mono text-sm text-white font-semibold truncate">
                    <?= htmlspecialchars($client['email']) ?>
                </p>
                <a href="mailto:<?= htmlspecialchars($client['email']) ?>"
                   class="text-[8px] text-[var(--crimson)] font-bold uppercase tracking-widest mt-2 block hover:underline">
                    Envoyer un email →
                </a>
            </div>

            <div class="info-field">
                <p class="text-[8px] text-gray-500 font-bold uppercase tracking-[2px] mb-2">
                    <i class="fa-solid fa-phone mr-2 text-[var(--crimson)]"></i>Téléphone
                </p>
                <p class="font-mono text-sm text-white font-semibold">
                    <?= htmlspecialchars($client['telephone'] ?? 'Non renseigné') ?>
                </p>
                <?php if (!empty($client['telephone'])): ?>
                <a href="tel:<?= htmlspecialchars($client['telephone']) ?>"
                   class="text-[8px] text-[var(--crimson)] font-bold uppercase tracking-widest mt-2 block hover:underline">
                    Appeler →
                </a>
                <?php endif; ?>
            </div>

            <div class="info-field">
                <p class="text-[8px] text-gray-500 font-bold uppercase tracking-[2px] mb-2">
                    <i class="fa-solid fa-shield-halved mr-2 text-[var(--crimson)]"></i>Statut Compte
                </p>
                <div class="flex items-center gap-3 mt-1">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                    <p class="text-sm text-white font-bold uppercase tracking-wider">Actif</p>
                </div>
                <p class="text-[8px] text-gray-600 font-bold uppercase tracking-widest mt-2">Accès vérifié</p>
            </div>
        </div>

        <!-- DEMANDES -->
        <div class="glass-card rounded-3xl overflow-hidden mb-6 fade-up delay-3">
            <div class="p-6 border-b border-white/5 flex justify-between items-center">
                <div>
                    <h3 class="text-[11px] font-title uppercase tracking-[3px] text-gray-400 italic">Historique des Demandes</h3>
                    <p class="text-[9px] text-gray-600 mt-0.5"><?= count($demandes) ?> demande(s) enregistrée(s)</p>
                </div>
                <div class="text-[9px] font-bold uppercase text-[var(--crimson)] tracking-tighter">
                    <i class="fa-solid fa-clock-rotate-left mr-1"></i> Historique
                </div>
            </div>

            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="p-4 text-[8px] font-bold uppercase tracking-[2px] text-gray-600">ID</th>
                        <th class="p-4 text-[8px] font-bold uppercase tracking-[2px] text-gray-600">Véhicule</th>
                        <th class="p-4 text-[8px] font-bold uppercase tracking-[2px] text-gray-600">Statut</th>
                        <th class="p-4 text-[8px] font-bold uppercase tracking-[2px] text-gray-600">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($demandes)): ?>
                        <tr>
                            <td colspan="4" class="p-10 text-center text-gray-600 italic uppercase text-[10px] tracking-widest">
                                <i class="fa-solid fa-ghost opacity-30 text-2xl mb-3 block"></i>
                                Aucune demande enregistrée
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($demandes as $d): ?>
                        <tr class="demande-row transition-colors">
                            <td class="p-4 font-mono text-xs text-gray-500">#<?= str_pad($d['id_demande'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td class="p-4 text-sm font-bold text-white uppercase italic">
                                <?= htmlspecialchars(($d['marque'] ?? '') . ' ' . ($d['modele'] ?? 'Véhicule inconnu')) ?>
                            </td>
                            <td class="p-4">
                                <?php
                                $statut = $d['statut'] ?? 'En attente';
                                $color = match(strtolower($statut)) {
                                    'accepté', 'accepte', 'confirmé' => 'text-green-400 bg-green-500/10 border-green-500/20',
                                    'refusé', 'refuse', 'annulé'    => 'text-red-400 bg-red-500/10 border-red-500/20',
                                    default                          => 'text-yellow-400 bg-yellow-500/10 border-yellow-500/20',
                                };
                                ?>
                                <span class="px-3 py-1 rounded-full text-[8px] font-bold uppercase tracking-widest border <?= $color ?>">
                                    <?= htmlspecialchars($statut) ?>
                                </span>
                            </td>
                            <td class="p-4 font-mono text-xs text-gray-500">
                                <?= htmlspecialchars($d['date_demande'] ?? '—') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- MESSAGES LIÉS -->
        <?php if (!empty($messages)): ?>
        <div class="glass-card rounded-3xl overflow-hidden fade-up delay-3">
            <div class="p-6 border-b border-white/5 flex justify-between items-center">
                <h3 class="text-[11px] font-title uppercase tracking-[3px] text-gray-400 italic">Messages Associés</h3>
                <div class="text-[9px] font-bold uppercase text-green-500 tracking-tighter">
                    <i class="fa-solid fa-satellite-dish mr-1"></i> Live Flux
                </div>
            </div>
            <div class="divide-y divide-white/5">
                <?php foreach ($messages as $msg): ?>
                <div class="p-6 hover:bg-white/[0.02] transition-colors">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-[8px] text-gray-500 font-bold uppercase tracking-[2px] mb-1">
                                Sujet : <?= htmlspecialchars($msg['sujet'] ?? 'Sans sujet') ?>
                            </p>
                            <p class="text-sm text-gray-300"><?= htmlspecialchars($msg['message'] ?? '') ?></p>
                        </div>
                        <span class="text-[8px] text-gray-600 font-mono flex-shrink-0">
                            <?= htmlspecialchars($msg['date'] ?? '') ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </main>
</body>
</html>