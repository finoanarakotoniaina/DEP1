<?php
/**
 * SUPERCAR - Cockpit de Gestion & Éditeur Live
 * Module : Boîte de Réception des Messages Contact
 */
session_start();
require_once 'config.php';

// Vérification de la session
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn;
$message_status = "";

// --- TRAITEMENT DES ACTIONS (Ex: Supprimer un message) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id_contact'])) {
    try {
        if ($_POST['action'] === 'delete') {
            // Requête corrigée raccord avec 'contact_client'
            $stmt = $db->prepare("DELETE FROM contact_client WHERE id_contact = ?");
            $stmt->execute([$_POST['id_contact']]);
            $message_status = "✅ MESSAGE SUPPRIMÉ AVEC SUCCÈS";
        }
    } catch (Exception $e) {
        $message_status = "❌ ERREUR : " . $e->getMessage();
    }
}

try {
    // Récupération des informations de l'administrateur connecté
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";

    // Statistiques rapides pour la boîte de réception (Corrigé avec contact_client)
    $totalMessages = $db->query("SELECT COUNT(*) FROM contact_client")->fetchColumn();

    // Récupération de tous les messages de contact reçus (Corrigé avec contact_client)
    $messages = $db->query("
        SELECT 
            id_contact,
            nom,
            prenom,
            email,
            message,
            date_envoi
        FROM contact_client
        ORDER BY id_contact DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur Système : " . $e->getMessage());
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SuperCar | Inbox Messages</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Syncopate:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    
    <style>
        body { background-color: var(--cosmos); }
        .sidebar { background: rgba(0, 0, 0, 0.2); border-right: 1px solid rgba(255, 255, 255, 0.05); }
        .nav-link-admin { display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1.25rem; border-radius: 14px; color: rgba(255,255,255,0.6); transition: 0.3s; font-size: 0.85rem; font-weight: 600; }
        .nav-link-admin:hover { color: white; background: rgba(255,255,255,0.05); }
        .nav-link-admin.active { color: white; background: linear-gradient(90deg, rgba(193, 18, 31, 0.2), transparent); border-left: 3px solid var(--crimson); }
        .logout-btn-admin { display: flex; align-items: center; justify-content: center; gap: 0.75rem; width: 100%; padding: 0.75rem; margin-bottom: 0.8rem; background: rgba(193, 18, 31, 0.1); border: 1px solid rgba(193, 18, 31, 0.2); border-radius: 14px; color: #ff4d4d; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
        .logout-btn-admin:hover { background: var(--crimson); color: white; }
        .stat-icon { position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.05; transform: rotate(-15deg); transition: all 0.5s ease; pointer-events: none; }
        .group:hover .stat-icon { opacity: 0.15; transform: rotate(0deg) scale(1.1); color: white; }
        .table-container { background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="flex h-screen overflow-hidden text-white">

    <aside class="w-72 sidebar p-6 flex flex-col">
        <div class="font-title text-xl tracking-tighter uppercase mb-10">
            SUPER<span class="text-[var(--crimson)]">CAR</span>
        </div>
        
        <nav class="flex-1 space-y-1">
<<<<<<< HEAD
            <p class="text-[9px] text-gray-500 font-bold uppercase tracking-[2px] mb-4 px-2">Menu Principal</p>
            <a href="tableaudebord.php" class="nav-link-admin <?= ($currentPage == 'tableaudebord.php') ? 'active' : '' ?>"><i class="fa-solid fa-gauge-high w-5"></i> Tableau de bord</a>
            <a href="admin_accueil.php" class="nav-link-admin <?= ($currentPage == 'admin_accueil.php') ? 'active' : '' ?>"><i class="fa-solid fa-house w-5"></i> Page Accueil</a>
            <a href="admin_affichage_contact.php" class="nav-link-admin <?= ($currentPage == 'admin_affichage_contact.php') ? 'active' : '' ?>"><i class="fa-solid fa-address-book w-5"></i> Page Contact</a>
            <a href="admin_voitures.php" class="nav-link-admin <?= ($currentPage == 'admin_voitures.php') ? 'active' : '' ?>"><i class="fa-solid fa-car-side w-5"></i> Inventaire</a>
            <a href="admin_demande.php" class="nav-link-admin <?= ($currentPage == 'admin_demande.php') ? 'active' : '' ?>"><i class="fa-solid fa-calendar-check w-5"></i> Demandes</a>
            <a href="admin_contact.php" class="nav-link-admin <?= ($currentPage == 'admin_contact.php') ? 'active' : '' ?>"><i class="fa-solid fa-envelope w-5"></i> Messages</a>
            <a href="admin_services.php" class="nav-link-admin <?= ($currentPage == 'admin_services.php') ? 'active' : '' ?>"><i class="fa-solid fa-screwdriver-wrench w-5"></i> Services</a>
            <a href="admin_archive.php" class="nav-link-admin <?= ($currentPage == 'admin_archive.php') ? 'active' : '' ?>"><i class="fa-solid fa-box-archive w-5"></i> Archives</a>

            
=======
            <a href="tableaudebord.php" class="nav-link-admin"><i class="fa-solid fa-gauge-high w-5"></i> Tableau de bord</a>
            <a href="admin_accueil.php" class="nav-link-admin"><i class="fa-solid fa-house w-5"></i> Page Accueil</a>
            <a href="admin_voitures.php" class="nav-link-admin"><i class="fa-solid fa-car-side w-5"></i> Inventaire</a>
            <a href="admin_demande.php" class="nav-link-admin active"><i class="fa-solid fa-calendar-check w-5"></i> Demandes</a>
            <a href="admin_contact.php" class="nav-link-admin"><i class="fa-solid fa-envelope w-5"></i> Messages</a>
            <a href="admin_services.php" class="nav-link-admin"><i class="fa-solid fa-screwdriver-wrench w-5"></i> Services</a>
        </nav>
<<<<<<< HEAD
=======            <a href="admin_archive.php" class="nav-link-admin"><i class="fa-solid fa-box-archive w-5"></i> Archives</a>


>>>>>>> 30fc3c6a15f145f2a8d6a9d825c840acea814e7c
        </nav>
=======
>>>>>>> 01662d737553bb0fd24042a9bdcbf34123cb42a2


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
        <header class="mb-8">
            <h2 class="text-4xl font-title font-black italic uppercase tracking-tighter">
                INBOX <span style="color: var(--crimson)">MESSAGES</span>
            </h2>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[3px] mt-2">Gestion des retours et messages du formulaire de contact client</p>
        </header>

        <?php if (!empty($message_status)): ?>
            <div class="mb-6 p-4 bg-white/5 border-l-4 border-[var(--crimson)] rounded-r-xl text-sm flex items-center gap-3 font-semibold tracking-wide">
                <?= $message_status ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-white/5 border border-white/10 p-6 rounded-3xl backdrop-blur-md relative overflow-hidden group">
                <i class="fa-solid fa-envelope-open-text stat-icon"></i>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Messages Reçus</p>
                <h3 class="text-3xl font-title font-black italic text-white"><?= $totalMessages ?></h3>
            </div>
        </div>

        <div class="table-container rounded-[32px] overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-[10px] uppercase font-title tracking-widest text-gray-400">
                    <tr>
                        <th class="p-5 w-1/5">Expéditeur</th>
                        <th class="p-5 w-1/5">Email</th>
                        <th class="p-5 w-1/5">Date Envoi</th>
                        <th class="p-5 w-1/3">Message</th>
                        <th class="p-5 text-center w-1/12">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (count($messages) > 0): ?>
                        <?php foreach($messages as $m): ?>
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="p-5 align-top">
                                    <p class="font-bold text-sm uppercase tracking-tight text-white">
                                        <?= htmlspecialchars(($m['prenom'] ?? '') . ' ' . ($m['nom'] ?? '')) ?>
                                    </p>
                                    <p class="text-[9px] text-gray-500 uppercase tracking-widest mt-0.5">
                                        ID #<?= $m['id_contact'] ?>
                                    </p>
                                </td>

                                <td class="p-5 align-top">
                                    <span class="text-[11px] font-semibold text-gray-300">
                                        <?= htmlspecialchars($m['email'] ?? '—') ?>
                                    </span>
                                </td>

                                <td class="p-5 align-top">
                                    <span class="text-[11px] text-gray-400 font-mono">
                                        <?= !empty($m['date_envoi']) ? date('d/m/Y H:i', strtotime($m['date_envoi'])) : '—' ?>
                                    </span>
                                </td>

                                <td class="p-5 align-top">
                                    <div class="text-xs text-gray-300 leading-relaxed bg-white/[0.01] border border-white/5 p-4 rounded-2xl whitespace-pre-line">
                                        <?= htmlspecialchars($m['message'] ?? '') ?>
                                    </div>
                                </td>

                                <td class="p-5 align-top text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <form method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce message ?');">
                                            <input type="hidden" name="id_contact" value="<?= $m['id_contact'] ?>">
                                            <button type="submit" name="action" value="delete"
                                                    class="w-8 h-8 flex items-center justify-center bg-red-500/10 border border-red-500/30 text-red-400 rounded-full hover:bg-red-500 hover:text-white transition-all"
                                                    title="Supprimer définitivement">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="p-10 text-center text-sm text-gray-500 italic uppercase tracking-wider">
                                <i class="fa-solid fa-folder-open block text-2xl mb-2 text-white/10"></i> Boîte de réception vide
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>