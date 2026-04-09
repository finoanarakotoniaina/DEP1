<?php
// --- 1. LOGIQUE PHP (DOIT ÊTRE TOUT EN HAUT) ---
session_start();
require_once 'config.php';

// Vérification de la session (accepte l'ID 0 de Madison)
if (!isset($_SESSION['admin_id']) && !array_key_exists('admin_id', $_SESSION)) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn;

try {
    // Récupération dynamique de l'admin depuis la table 'admin'
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    
    // On définit le nom ou on redirige si l'admin n'existe plus en base
    if ($resAdmin) {
        $adminFullName = $resAdmin['prenom'] . " " . $resAdmin['nom'];
    } else {
        header('Location: logout.php');
        exit();
    }

    // Récupération des demandes d'essais
    $query = "SELECT * FROM demande_essai ORDER BY id_demande DESC";
    $demandes = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $demandes = [];
    $adminFullName = "Admin";
}

// --- 2. FIN DU PHP ET DÉBUT DU HTML ---
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SuperCar | Demandes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --bg-deep: #050505; --accent-red: #ff1e26; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-deep); color: #fff; margin:0; }
        .font-m { font-family: 'Orbitron', sans-serif; }
        
        .nav-link {
            display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1.25rem;
            border-radius: 14px; color: #555; transition: 0.3s; font-size: 0.85rem;
        }
        .nav-link:hover, .nav-link.active { color: #fff; background: rgba(255,255,255,0.05); }
        .nav-link.active { border-left: 3px solid var(--accent-red); background: linear-gradient(90deg, rgba(255,30,38,0.1), transparent); }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <aside class="w-72 bg-black border-r border-white/5 p-6 flex flex-col">
        <div class="mb-12 px-2 text-2xl font-m font-black italic tracking-tighter uppercase">
            SUPER<span class="text-red-600">CAR</span>
        </div>
        
        <nav class="flex-1 space-y-1">
            <p class="text-[9px] text-gray-700 font-bold uppercase tracking-[2px] mb-4 px-2">Navigation</p>
            <a href="DashBoard.php" class="nav-link"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
            <a href="admin_voitures.php" class="nav-link"><i class="fa-solid fa-car-side"></i> Inventaire</a>
            <a href="admin_demande.php" class="nav-link active"><i class="fa-solid fa-calendar-check"></i> Demandes</a>
            <a href="admin_contact.php" class="nav-link"><i class="fa-solid fa-envelope"></i> Messages</a>
        </nav>

        <div class="mt-auto pt-6 border-t border-white/5">
            <div class="bg-white/5 p-4 rounded-2xl flex items-center gap-3 border border-white/5">
                <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-xs font-bold text-red-600">
                    <?= strtoupper(substr($adminFullName, 0, 1)) ?>
                </div>
                <div>
                    <p class="text-xs font-bold text-white"><?= htmlspecialchars($adminFullName) ?></p>
                    <p class="text-[10px] text-gray-500">Connecté</p>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 p-10 overflow-y-auto bg-[#080808]">
        <header class="mb-12">
            <h2 class="text-4xl font-m font-black italic uppercase tracking-tighter">
                Gestion des <span class="text-red-600">Demandes</span>
            </h2>
        </header>

        <div class="bg-white/[0.02] border border-white/5 rounded-[32px] overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-white/[0.02] text-[10px] uppercase font-m tracking-widest text-gray-500">
                    <tr>
                        <th class="p-6">Client / Pilote</th>
                        <th class="p-6">Statut Mission</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($demandes)): ?>
                        <tr><td colspan="2" class="p-10 text-center text-gray-600 italic font-m uppercase text-[10px]">Aucun log détecté</td></tr>
                    <?php else: ?>
                        <?php foreach($demandes as $d): ?>
                        <tr class="hover:bg-white/[0.01] transition">
                            <td class="p-6">
                                <p class="font-bold text-sm uppercase italic"><?= htmlspecialchars($d['nom_client'] ?? 'Anonyme') ?></p>
                                <p class="text-[10px] text-gray-500 uppercase tracking-widest">ID Log: #<?= $d['id_demande'] ?></p>
                            </td>
                            <td class="p-6">
                                <span class="px-3 py-1 bg-red-600/10 text-red-600 text-[8px] font-black uppercase rounded-full border border-red-600/10 tracking-widest">Reçu</span>
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