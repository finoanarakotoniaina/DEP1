<?php
/**
 * SUPERCAR - Gestion des Demandes
 * Correction : Système de session basé sur la table admin réelle
 */
session_start();
require_once 'config.php';

// 1. SÉCURITÉ : Vérification de la session
// Si l'id_admin n'est pas dans la session, on redirige vers le login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn;

try {
    // 2. RÉCUPÉRATION NOM ADMIN (Dynamique selon ta table 'admin')
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    
    // Si trouvé, on affiche le vrai nom, sinon valeur de secours
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";

    // 3. RÉCUPÉRATION DES DEMANDES (Depuis ta table 'demande_essai')
    $query = "SELECT * FROM demande_essai ORDER BY id_demande DESC";
    $demandes = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $demandes = [];
}
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

        .logout-btn {
            display: flex; align-items: center; justify-content: center; gap: 0.75rem;
            width: 100%; padding: 0.75rem; margin-bottom: 0.8rem;
            background: rgba(255, 30, 38, 0.05); border: 1px solid rgba(255, 30, 38, 0.15);
            border-radius: 14px; color: #ff3b3b; font-size: 11px; font-weight: 800;
            text-transform: uppercase; transition: 0.3s;
        }
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
            <a href="admin_services.php" class="nav-link"><i class="fa-solid fa-screwdriver-wrench"></i> Services</a>
        </nav>

        <div class="mt-auto pt-6 border-t border-white/5">
            <a href="logout.php" class="logout-btn"><i class="fa-solid fa-power-off text-xs"></i> Déconnexion</a>
            <div class="bg-white/5 p-4 rounded-2xl flex items-center justify-between border border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-xs font-bold border border-white/10 text-red-600">
                        <?= strtoupper(substr($adminFullName, 0, 1)) ?>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white"><?= htmlspecialchars($adminFullName) ?></p>
                        <p class="text-[10px] text-gray-500 font-medium">Connecté</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 p-10 overflow-y-auto bg-[#080808]">
        <header class="mb-12">
            <h2 class="text-4xl font-m font-black italic uppercase tracking-tighter">Gestion des <span class="text-red-600">Demandes</span></h2>
        </header>

        <div class="bg-white/[0.02] border border-white/5 rounded-[32px] overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-white/[0.02] text-[10px] uppercase font-m tracking-widest text-gray-500">
                    <tr>
                        <th class="p-6">Client</th>
                        <th class="p-6">Détails</th>
                        <th class="p-6 text-right">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($demandes)): ?>
                        <tr><td colspan="3" class="p-10 text-center text-gray-600 italic">Aucune demande enregistrée.</td></tr>
                    <?php else: ?>
                        <?php foreach($demandes as $d): ?>
                        <tr class="hover:bg-white/[0.01] transition">
                            <td class="p-6">
                                <p class="font-bold text-sm uppercase"><?= htmlspecialchars($d['nom_client'] ?? 'Inconnu') ?></p>
                            </td>
                            <td class="p-6 text-xs text-gray-400">Demande d'essai n°<?= $d['id_demande'] ?></td>
                            <td class="p-6 text-right">
                                <span class="px-3 py-1 bg-red-600/10 text-red-600 text-[8px] font-black uppercase rounded-full border border-red-600/10">Reçu</span>
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