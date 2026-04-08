<?php 
/**
 * SUPERCAR - Dashboard Administration (Version MySQLi)
 */

// 1. Inclusion de la configuration
require_once '../config.php'; 

// 2. Détection de la variable de connexion
// MySQLi utilise souvent $conn ou $db. On s'adapte automatiquement.
if (!isset($pdo)) {
    if (isset($conn)) { $pdo = $conn; }
    elseif (isset($db)) { $pdo = $db; }
}

// 3. Récupération des statistiques avec MySQLi
// On utilise query()->fetch_row() car fetchColumn() n'existe pas en MySQLi
$countCars = $pdo->query("SELECT COUNT(*) FROM voiture")->fetch_row()[0] ?? 0;
$countClients = $pdo->query("SELECT COUNT(*) FROM client")->fetch_row()[0] ?? 0;
$countMessages = $pdo->query("SELECT COUNT(*) FROM contact")->fetch_row()[0] ?? 0;

// 4. Récupération des derniers clients
$lastClientsRes = $pdo->query("SELECT nom, prenom, email, telephone FROM client ORDER BY id_client DESC LIMIT 5");
$lastClients = $lastClientsRes->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar Admin | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0a0a0a; color: white; }
        .font-title { font-family: 'Syncopate', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .nav-sidebar-link:hover { background: rgba(227, 30, 36, 0.1); border-right: 3px solid #e31e24; }
    </style>
</head>

<body class="flex min-h-screen overflow-hidden">

    <aside class="w-72 border-r border-white/5 flex flex-col z-50 bg-[#0d0d0d]">
        <div class="p-8 font-title text-xl tracking-tighter border-b border-white/5">
            SUPER<span class="text-[#e31e24]">CAR</span> <span class="text-[8px] block tracking-widest opacity-50 uppercase">Administration</span>
        </div>
        
        <nav class="flex-1 px-4 py-8">
            <ul class="space-y-4 font-title text-[10px] tracking-[0.2em] uppercase">
                <li><a href="admin_accueil.php" class="flex items-center gap-4 p-4 rounded text-[#e31e24] bg-white/5 nav-sidebar-link"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                <li><a href="admin_voitures.php" class="flex items-center gap-4 p-4 rounded hover:text-white transition-all nav-sidebar-link"><i class="fa-solid fa-car"></i> Stock</a></li>
                <li><a href="admin_clients.php" class="flex items-center gap-4 p-4 rounded hover:text-white transition-all nav-sidebar-link"><i class="fa-solid fa-users"></i> Clients</a></li>
                <li><a href="admin_messages.php" class="flex items-center gap-4 p-4 rounded hover:text-white transition-all nav-sidebar-link"><i class="fa-solid fa-envelope"></i> Messages</a></li>
            </ul>
        </nav>

        <div class="p-8 border-t border-white/5">
            <a href="../deconnexion.php" class="flex items-center gap-4 font-title text-[10px] tracking-[0.2em] text-gray-500 hover:text-[#e31e24] transition-all">
                <i class="fa-solid fa-power-off"></i> Déconnexion
            </a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto p-10 lg:p-16 relative">
        <header class="mb-16">
            <h1 class="font-title text-4xl font-bold uppercase mb-2">Tableau de <span class="text-[#e31e24]">Bord</span></h1>
            <p class="text-gray-400 font-light tracking-wide">Bienvenue, Magalie. Voici l'état actuel de votre parc automobile.</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <div class="glass-card p-8 rounded-2xl relative overflow-hidden">
                <span class="text-xs font-title tracking-widest text-gray-500 block mb-4 uppercase">Total Stock</span>
                <div class="text-5xl font-title font-bold"><?= (int)$countCars ?></div>
                <i class="fa-solid fa-car absolute -right-4 -bottom-4 text-7xl opacity-5"></i>
            </div>

            <div class="glass-card p-8 rounded-2xl relative overflow-hidden border-b-2 border-[#e31e24]">
                <span class="text-xs font-title tracking-widest text-gray-500 block mb-4 uppercase">Clients Inscrits</span>
                <div class="text-5xl font-title font-bold"><?= (int)$countClients ?></div>
                <i class="fa-solid fa-user-group absolute -right-4 -bottom-4 text-7xl opacity-5"></i>
            </div>

            <div class="glass-card p-8 rounded-2xl relative overflow-hidden">
                <span class="text-xs font-title tracking-widest text-gray-500 block mb-4 uppercase">Messages</span>
                <div class="text-5xl font-title font-bold"><?= (int)$countMessages ?></div>
                <i class="fa-solid fa-paper-plane absolute -right-4 -bottom-4 text-7xl opacity-5"></i>
            </div>
        </div>

        <section class="glass-card rounded-2xl overflow-hidden">
            <div class="p-8 border-b border-white/5 flex justify-between items-center bg-white/[0.01]">
                <h2 class="font-title text-xs tracking-[0.3em] uppercase text-[#e31e24]">Dernières Interactions</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-[10px] font-title tracking-widest text-gray-500 uppercase">
                        <tr>
                            <th class="p-8">Nom / Prénom</th>
                            <th class="p-8">Email</th>
                            <th class="p-8 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-light text-gray-300">
                        <?php if (empty($lastClients)): ?>
                            <tr><td colspan="3" class="p-8 text-center text-gray-600 italic">Aucun client trouvé.</td></tr>
                        <?php else: ?>
                            <?php foreach ($lastClients as $c): ?>
                            <tr class="hover:bg-white/[0.02] transition-colors border-t border-white/5">
                                <td class="p-8 text-white font-medium"><?= htmlspecialchars($c['nom'] . ' ' . $c['prenom']) ?></td>
                                <td class="p-8"><?= htmlspecialchars($c['email']) ?></td>
                                <td class="p-8 text-right">
                                    <span class="text-[10px] font-title tracking-widest px-4 py-2 border border-white/10 rounded">CLIENT</span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <footer class="mt-20 text-center text-[8px] font-title tracking-[0.4em] text-gray-600 uppercase">
            &copy; <?= date('Y') ?> SUPERCAR Mauritius - Admin Portal
        </footer>
    </main>
</body>
</html>