<?php
/**
 * SUPERCAR - Gestion des Contacts
 * Intégration de la Navigation Master & Session Admin
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
    <title>SuperCar | Messages Clients</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --bg-deep: #050505; --accent-red: #ff1e26; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-deep); color: #fff; margin:0; }
        .font-m { font-family: 'Orbitron', sans-serif; }
        
        /* Sidebar Styles */
        .nav-link {
            display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1.25rem;
            border-radius: 14px; color: #555; transition: 0.3s; font-size: 0.85rem; font-weight: 600;
        }
        .nav-link:hover, .nav-link.active { color: #fff; background: rgba(255,255,255,0.05); }
        .nav-link.active { border-left: 3px solid var(--accent-red); background: linear-gradient(90deg, rgba(255,30,38,0.1), transparent); }

        .logout-btn {
            display: flex; align-items: center; justify-content: center; gap: 0.75rem;
            width: 100%; padding: 0.75rem; margin-bottom: 0.8rem;
            background: rgba(255, 30, 38, 0.05); border: 1px solid rgba(255, 30, 38, 0.15);
            border-radius: 14px; color: #ff3b3b; font-size: 11px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 1px; transition: 0.3s;
        }
        .logout-btn:hover { background: rgba(255, 30, 38, 0.15); border-color: var(--accent-red); color: #fff; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <aside class="w-72 bg-black border-r border-white/5 p-6 flex flex-col">
        <div class="mb-12 px-2 text-2xl font-m font-black italic tracking-tighter uppercase text-white">
            SUPER<span class="text-red-600">CAR</span>
        </div>
        
        <nav class="flex-1 space-y-1">
            <p class="text-[9px] text-gray-700 font-bold uppercase tracking-[2px] mb-4 px-2">Navigation</p>
            <a href="DashBoard.php" class="nav-link"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
            <a href="admin_voitures.php" class="nav-link"><i class="fa-solid fa-car-side"></i> Inventaire</a>
            <a href="admin_demande.php" class="nav-link"><i class="fa-solid fa-calendar-check"></i> Demandes</a>
            <a href="admin_contact.php" class="nav-link active"><i class="fa-solid fa-envelope"></i> Messages</a>
            <a href="admin_services.php" class="nav-link"><i class="fa-solid fa-screwdriver-wrench"></i> Services</a>
        </nav>

        <div class="mt-auto pt-6 border-t border-white/5">
            <a href="logout.php" class="logout-btn">
                <i class="fa-solid fa-power-off text-xs"></i> Se déconnecter
            </a>
            
            <div class="bg-white/5 p-4 rounded-2xl flex items-center justify-between border border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-xs font-bold border border-white/10 text-gray-400">
                        <?= strtoupper(substr($adminFullName, 0, 1)) ?>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white"><?= htmlspecialchars($adminFullName) ?></p>
                        <p class="text-[10px] text-gray-500 font-medium italic">Connecté</p>
                    </div>
                </div>
                <i class="fa-solid fa-sort text-[10px] text-gray-600"></i>
            </div>
        </div>
    </aside>

    <main class="flex-1 p-10 overflow-y-auto bg-[#080808]">
        <header class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-4xl font-m font-black italic uppercase tracking-tighter">
                    Messages <span class="text-red-600">Clients</span>
                </h2>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-[3px] mt-2 italic">Backoffice / Flux de communication</p>
            </div>
            <div class="text-right">
                <p class="text-[9px] text-gray-600 font-black uppercase tracking-widest">Total Logs</p>
                <span class="text-4xl font-m font-black text-red-600"><?= count($messages) ?></span>
            </div>
        </header>

        <?php if (isset($_GET['status'])): ?>
            <div class="mb-6 p-4 bg-red-600/10 border border-red-600/20 rounded-2xl text-red-500 text-[10px] font-bold uppercase tracking-widest animate-pulse">
                <i class="fa-solid fa-circle-check mr-2"></i> Message supprimé de la base
            </div>
        <?php endif; ?>

        <div class="bg-white/[0.02] border border-white/5 rounded-[32px] overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-white/[0.02] text-[10px] uppercase font-m tracking-widest text-gray-500">
                    <tr>
                        <th class="p-6">ID / Client</th>
                        <th class="p-6">Coordonnées</th>
                        <th class="p-6 w-1/3">Transmission</th>
                        <th class="p-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($messages)): ?>
                        <tr><td colspan="4" class="p-10 text-center text-gray-600 italic font-m uppercase text-[10px]">Aucune communication détectée</td></tr>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <tr class="hover:bg-red-600/[0.01] transition-colors group">
                                <td class="p-6">
                                    <p class="font-bold text-sm uppercase italic text-white"><?= htmlspecialchars($msg['prenom'] . ' ' . $msg['nom']) ?></p>
                                    <p class="text-[9px] text-gray-600 font-bold uppercase tracking-widest">Signal #<?= $msg['id_contact'] ?></p>
                                </td>
                                <td class="p-6 text-xs text-red-600/80 font-medium">
                                    <?= htmlspecialchars($msg['email']) ?>
                                </td>
                                <td class="p-6">
                                    <div class="text-[11px] text-gray-400 leading-relaxed italic border-l-2 border-red-600/20 pl-4">
                                        "<?= nl2br(htmlspecialchars($msg['message'])) ?>"
                                    </div>
                                </td>
                                <td class="p-6 text-center">
                                    <a href="admin_contact.php?delete_id=<?= $msg['id_contact'] ?>" 
                                       onclick="return confirm('Confirmer la suppression définitive ?')"
                                       class="w-10 h-10 inline-flex items-center justify-center rounded-full bg-red-600/5 text-red-600 hover:bg-red-600 hover:text-white transition-all">
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