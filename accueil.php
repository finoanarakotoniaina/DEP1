<?php 
// --- 1. CONNEXION & CONFIG ---
// Assure-toi que ton fichier config.php contient bien la connexion $pdo
include '../config.php'; 

// --- 2. RÉCUPÉRATION DES STATISTIQUES (KPI) ---
$countCars = $pdo->query("SELECT COUNT(*) FROM voiture")->fetchColumn();
$countClients = $pdo->query("SELECT COUNT(*) FROM client")->fetchColumn();
$countMessages = $pdo->query("SELECT COUNT(*) FROM contact")->fetchColumn();

// --- 3. RÉCUPÉRATION DES INFOS CLIENTS ---
$lastClients = $pdo->query("SELECT nom, prenom, email, telephone FROM client ORDER BY id_client DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="../style.css"> <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0a0a0a; color: white; }
        .font-title { font-family: 'Syncopate', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .crimson-gradient { background: linear-gradient(135deg, #e31e24 0%, #8b0000 100%); }
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
                <li><a href="Dashboard.php" class="flex items-center gap-4 p-4 rounded text-[#e31e24] bg-white/5 nav-sidebar-link"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
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
        <div class="absolute top-0 right-0 w-1/2 h-1/2 bg-[#e31e24]/5 blur-[120px] -z-10 rounded-full"></div>

        <header class="mb-16">
            <h1 class="font-title text-4xl font-bold uppercase mb-2">Tableau de <span class="text-[#e31e24]">Bord</span></h1>
            <p class="text-gray-400 font-light tracking-wide">Bienvenue, Magalie. Voici l'état actuel de votre parc automobile.</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <div class="glass-card p-8 rounded-2xl relative overflow-hidden group">
                <div class="relative z-10">
                    <span class="text-xs font-title tracking-widest text-gray-500 block mb-4 uppercase">Total Stock</span>
                    <div class="text-5xl font-title font-bold text-white group-hover:scale-110 transition-transform duration-500"><?= $countCars ?></div>
                </div>
                <i class="fa-solid fa-car absolute -right-4 -bottom-4 text-7xl opacity-5"></i>
            </div>

            <div class="glass-card p-8 rounded-2xl relative overflow-hidden group border-b-2 border-[#e31e24]">
                <div class="relative z-10">
                    <span class="text-xs font-title tracking-widest text-gray-500 block mb-4 uppercase">Clients Inscrits</span>
                    <div class="text-5xl font-title font-bold text-white"><?= $countClients ?></div>
                </div>
                <i class="fa-solid fa-user-group absolute -right-4 -bottom-4 text-7xl opacity-5"></i>
            </div>

            <div class="glass-card p-8 rounded-2xl relative overflow-hidden group">
                <div class="relative z-10">
                    <span class="text-xs font-title tracking-widest text-gray-500 block mb-4 uppercase">Nouveaux Messages</span>
                    <div class="text-5xl font-title font-bold text-white"><?= $countMessages ?></div>
                </div>
                <i class="fa-solid fa-paper-plane absolute -right-4 -bottom-4 text-7xl opacity-5"></i>
            </div>
        </div>

        <section class="glass-card rounded-2xl overflow-hidden">
            <div class="p-8 border-b border-white/5 flex justify-between items-center bg-white/[0.01]">
                <h2 class="font-title text-xs tracking-[0.3em] uppercase text-[#e31e24]">Dernières Interactions Clients</h2>
                <a href="admin_clients.php" class="text-[10px] font-title tracking-widest text-gray-500 hover:text-white transition-all">VOIR TOUT <i class="fa-solid fa-chevron-right ml-2 text-[8px]"></i></a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-[10px] font-title tracking-widest text-gray-500 uppercase">
                        <tr>
                            <th class="p-8">Nom / Prénom</th>
                            <th class="p-8">Email</th>
                            <th class="p-8">Téléphone</th>
                            <th class="p-8 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-light text-gray-300">
                        <?php if (empty($lastClients)): ?>
                            <tr><td colspan="4" class="p-8 text-center text-gray-600">Aucun client enregistré pour le moment.</td></tr>
                        <?php else: ?>
                            <?php foreach ($lastClients as $c): ?>
                            <tr class="hover:bg-white/[0.02] transition-colors border-t border-white/5 group">
                                <td class="p-8 text-white font-medium italic"><?= htmlspecialchars($c['nom'] . ' ' . $c['prenom']) ?></td>
                                <td class="p-8"><?= htmlspecialchars($c['email']) ?></td>
                                <td class="p-8"><?= htmlspecialchars($c['telephone']) ?></td>
                                <td class="p-8 text-right">
                                    <a href="fiche_client.php?email=<?= $c['email'] ?>" class="text-[10px] font-title tracking-widest px-4 py-2 border border-white/10 rounded hover:border-[#e31e24] hover:bg-[#e31e24] hover:text-white transition-all">PROFIL</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <footer class="mt-20 text-center text-[8px] font-title tracking-[0.4em] text-gray-600 uppercase">
            &copy; 2024 SUPERCAR Mauritus - Admin Portal
        </footer>
    </main>
</body>
</html>