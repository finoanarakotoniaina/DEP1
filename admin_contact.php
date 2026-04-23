<?php
/**
 * SUPERCAR - Gestion des Contacts
 * Style unifié Cockpit Precision Edition v14.3
 */
session_start();
require_once 'config.php';

// 1. VÉRIFICATION SESSION ADMIN
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn;

// 2. RÉCUPÉRATION INFOS ADMIN
try {
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";
} catch (PDOException $e) {
    $adminFullName = "Admin";
}

// 3. LOGIQUE DE SUPPRESSION
if (isset($_GET['delete_id'])) {
    $id_a_supprimer = $_GET['delete_id'];
    try {
        $stmt_del = $db->prepare("DELETE FROM contact WHERE id_contact = :id");
        $stmt_del->execute([':id' => $id_a_supprimer]);
        header("Location: admin_contact.php?status=deleted");
        exit();
    } catch (PDOException $e) {
        $error = "Erreur : " . $e->getMessage();
    }
}

// 4. RÉCUPÉRATION DES MESSAGES
try {
    $stmt = $db->query("SELECT * FROM contact ORDER BY id_contact DESC");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur base de données : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Messages Clients</title>
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

        .table-container {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .grid-bg-overlay {
            background-image: linear-gradient(rgba(255, 255, 255, 0.01) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255, 255, 255, 0.01) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .msg-row:hover { background: rgba(255,255,255,0.02); }

        .stat-badge {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="w-72 sidebar p-6 flex flex-col z-20 flex-shrink-0">
        <div class="font-title text-xl tracking-tighter uppercase mb-10">
            SUPER<span class="text-[var(--crimson)]">CAR</span>
        </div>

        <nav class="flex-1 space-y-1">
            <p class="text-[9px] text-gray-500 font-bold uppercase tracking-[2px] mb-4 px-2">Menu Principal</p>
            <a href="tableaudebord.php" class="nav-link-admin"><i class="fa-solid fa-gauge-high w-5"></i> Tableau de bord</a>
            <a href="admin_voitures.php" class="nav-link-admin"><i class="fa-solid fa-car-side w-5"></i> Inventaire</a>
            <a href="admin_demande.php" class="nav-link-admin"><i class="fa-solid fa-calendar-check w-5"></i> Demandes</a>
            <a href="admin_contact.php" class="nav-link-admin active"><i class="fa-solid fa-envelope w-5"></i> Messages</a>
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
        <header class="mb-12 flex justify-between items-end">
            <div>
                <h2 class="text-4xl font-title font-black italic uppercase tracking-tighter">
                    MESSAGES <span style="color: var(--crimson)">CLIENTS</span>
                </h2>
                <p class="text-[10px] text-green-500 font-bold uppercase tracking-[3px] mt-2">
                    <i class="fa-solid fa-circle text-[6px] mr-2 animate-pulse"></i> Flux de communication actif
                </p>
            </div>
            <div class="stat-badge px-8 py-4 rounded-3xl text-center">
                <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest mb-1">Total Logs</p>
                <span class="text-4xl font-title font-black italic text-white"><?= count($messages) ?></span>
            </div>
        </header>

        <!-- NOTIFICATION SUPPRESSION -->
        <?php if (isset($_GET['status'])): ?>
            <div class="mb-6 p-4 bg-[var(--crimson)]/10 border border-[var(--crimson)]/20 rounded-2xl text-[var(--crimson)] text-[10px] font-bold uppercase tracking-widest">
                <i class="fa-solid fa-circle-check mr-2"></i> Message supprimé de la base
            </div>
        <?php endif; ?>

        <!-- TABLE -->
        <div class="table-container rounded-[32px] overflow-hidden backdrop-blur-md">
            <div class="p-8 border-b border-white/5 bg-white/[0.01] flex justify-between items-center">
                <h3 class="text-[11px] font-title uppercase tracking-[3px] text-gray-400 italic">Transmissions Reçues</h3>
                <div class="text-[9px] font-bold uppercase text-green-500 tracking-tighter">
                    <i class="fa-solid fa-satellite-dish mr-1"></i> Live Flux
                </div>
            </div>

            <table class="w-full text-left">
                <thead class="border-b border-white/5">
                    <tr class="text-[9px] uppercase font-bold tracking-[2px] text-gray-600">
                        <th class="p-6">ID / Client</th>
                        <th class="p-6">Coordonnées</th>
                        <th class="p-6 w-1/3">Message</th>
                        <th class="p-6 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($messages)): ?>
                        <tr>
                            <td colspan="4" class="p-10 text-center text-gray-600 italic uppercase text-[10px] tracking-widest">
                                <i class="fa-solid fa-ghost opacity-30 text-2xl mb-3 block"></i>
                                Aucune communication détectée
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                        <tr class="msg-row transition-colors group">
                            <td class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-white/5 border border-white/5 flex items-center justify-center font-bold text-sm text-[var(--crimson)] group-hover:border-[var(--crimson)]/30 transition-all flex-shrink-0">
                                        <?= strtoupper(substr($msg['prenom'] ?? $msg['nom'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm uppercase italic tracking-tight text-white">
                                            <?= htmlspecialchars($msg['prenom'] . ' ' . $msg['nom']) ?>
                                        </p>
                                        <p class="text-[9px] text-gray-600 font-bold uppercase tracking-widest">
                                            Signal #<?= str_pad($msg['id_contact'], 4, '0', STR_PAD_LEFT) ?>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-6 font-mono text-xs text-gray-500">
                                <i class="fa-solid fa-at mr-2 opacity-30"></i><?= htmlspecialchars($msg['email']) ?>
                            </td>
                            <td class="p-6">
                                <div class="text-[11px] text-gray-400 leading-relaxed italic border-l-2 border-[var(--crimson)]/20 pl-4 line-clamp-3">
                                    "<?= nl2br(htmlspecialchars($msg['message'])) ?>"
                                </div>
                            </td>
                            <td class="p-6 text-center">
                                <a href="admin_contact.php?delete_id=<?= $msg['id_contact'] ?>"
                                   onclick="return confirm('Confirmer la suppression définitive ?')"
                                   class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-white/5 border border-white/5 text-gray-500 hover:bg-[var(--crimson)] hover:text-white hover:border-[var(--crimson)] transition-all">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
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