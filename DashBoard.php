<?php
/**
 * SUPERCAR - Cockpit Precision Edition v14.0
 * Correction : Suppression du nom en dur et gestion de l'ID 0
 */
session_start();
require_once 'config.php';

// 1. SÉCURITÉ : On vérifie si la clé existe (indispensable car l'ID est 0)
if (!array_key_exists('admin_id', $_SESSION)) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn; 

try {
    // 2. RÉCUPÉRATION DYNAMIQUE DE L'ADMIN
    // On ne met PLUS "Madison Carter" par défaut ici.
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    
    // Si l'admin n'existe pas dans la table, on bloque l'accès
    if (!$resAdmin) {
        session_destroy();
        header('Location: login.php');
        exit();
    }

    // Le nom est maintenant 100% issu de ta base de données
    $adminName = $resAdmin['prenom'] . " " . $resAdmin['nom'];

    // 3. TÉLÉMÉTRIE (Compteurs)
    $countCars = $db->query("SELECT COUNT(*) FROM voiture")->fetchColumn() ?: 0;
    $countClients = $db->query("SELECT COUNT(*) FROM client")->fetchColumn() ?: 0;
    $countMessages = $db->query("SELECT COUNT(*) FROM contact")->fetchColumn() ?: 0;

    // 4. DERNIERS CONDUCTEURS
    $stmtRecent = $db->query("SELECT nom, prenom, email FROM client ORDER BY id_client DESC LIMIT 3");
    $recentDrivers = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("ERREUR SYSTÈME : Connexion à la base supercar interrompue.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SuperCar | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root { --bg-deep: #050505; --accent-red: #ff1e26; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-deep); color: #fff; margin:0; overflow: hidden; }
        .font-m { font-family: 'Orbitron', sans-serif; }

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

        .gauge-module { position: relative; width: 180px; height: 180px; display: flex; align-items: center; justify-content: center; }
        .m5-arc {
            position: absolute; width: 100%; height: 100%; border-radius: 50%;
            background: conic-gradient(from -135deg, var(--c) calc(var(--p) * 2.7deg), rgba(255,255,255,0.05) 0deg);
            -webkit-mask: radial-gradient(transparent 84%, black 85%);
        }
        .m5-needle {
            position: absolute; width: 2px; height: 80px; background: linear-gradient(to top, transparent, var(--c), #fff);
            bottom: 50%; left: 50%; transform-origin: bottom center;
            transform: translateX(-50%) rotate(calc(-135deg + (var(--p) * 2.7deg)));
            transition: transform 2s cubic-bezier(0.1, 0.6, 0.2, 1);
        }
    </style>
</head>
<body class="flex h-screen">

    <aside class="w-72 bg-black border-r border-white/5 p-6 flex flex-col">
        <div class="mb-12 px-2 text-2xl font-m font-black italic tracking-tighter uppercase">
            SUPER<span class="text-red-600">CAR</span>
        </div>
        
        <nav class="flex-1 space-y-1">
            <p class="text-[9px] text-gray-700 font-bold uppercase tracking-[2px] mb-4 px-2">Navigation</p>
            <a href="DashBoard.php" class="nav-link active"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
            <a href="admin_voitures.php" class="nav-link"><i class="fa-solid fa-car-side"></i> Inventaire</a>
            <a href="admin_demande.php" class="nav-link"><i class="fa-solid fa-calendar-check"></i> Demandes</a>
            <a href="admin_contact.php" class="nav-link"><i class="fa-solid fa-envelope"></i> Messages</a>
            <a href="admin_services.php" class="nav-link"><i class="fa-solid fa-screwdriver-wrench"></i> Services</a>
        </nav>

        <div class="mt-auto pt-6 border-t border-white/5">
            <a href="logout.php" class="logout-btn">
                <i class="fa-solid fa-power-off text-xs"></i> Se déconnecter
            </a>
            
            <div class="bg-white/5 p-4 rounded-2xl flex items-center justify-between border border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-xs font-bold border border-white/10 text-gray-400">
                        <?= strtoupper(substr($adminName, 0, 1)) ?>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white"><?= htmlspecialchars($adminName) ?></p>
                        <p class="text-[10px] text-gray-500 font-medium italic">Authentifié</p>
                    </div>
                </div>
                <i class="fa-solid fa-sort text-[10px] text-gray-600"></i>
            </div>
        </div>
    </aside>

    <main class="flex-1 p-10 overflow-y-auto bg-[#080808]">
        <header class="flex justify-between items-center mb-16">
            <div>
                <h2 class="text-4xl font-m font-black italic uppercase tracking-tighter">Command <span class="text-gray-800">Center</span></h2>
                <p class="text-[10px] text-green-500 font-bold uppercase tracking-[3px] mt-2">Active Admin: <?= htmlspecialchars($adminName) ?></p>
            </div>
            <div class="bg-red-600 px-8 py-3 rounded-full font-m text-[10px] uppercase shadow-[0_0_20px_rgba(255,30,38,0.3)]">System Secure</div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-16">
            <div class="flex flex-col items-center">
                <div class="gauge-module" style="--p: <?= min($countCars, 100) ?>; --c: var(--accent-red);">
                    <div class="m5-arc"></div><div class="m5-needle"></div>
                    <div class="absolute mt-10 text-center">
                        <span class="text-4xl font-m italic"><?= $countCars ?></span>
                        <p class="text-[9px] text-gray-500 uppercase tracking-widest">Stock Actif</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col items-center">
                <div class="gauge-module" style="--p: <?= min($countClients, 100) ?>; --c: #fff;">
                    <div class="m5-arc"></div><div class="m5-needle"></div>
                    <div class="absolute mt-10 text-center">
                        <span class="text-4xl font-m italic"><?= $countClients ?></span>
                        <p class="text-[9px] text-gray-500 uppercase tracking-widest">Pilotes</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col items-center">
                <div class="gauge-module" style="--p: <?= min($countMessages * 10, 100) ?>; --c: var(--accent-red);">
                    <div class="m5-arc"></div><div class="m5-needle"></div>
                    <div class="absolute mt-10 text-center">
                        <span class="text-4xl font-m italic"><?= $countMessages ?></span>
                        <p class="text-[9px] text-gray-500 uppercase tracking-widest">Flux</p>
                    </div>
                </div>
            </div>
            <div class="bg-white/5 rounded-[32px] p-8 border border-white/5 flex flex-col justify-center">
                <p class="text-[10px] font-m text-gray-500 uppercase tracking-widest mb-2">Efficiency</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-m font-black italic">98</span><span class="text-red-600 text-xl font-bold">%</span>
                </div>
                <div class="mt-4 h-1.5 bg-white/5 rounded-full overflow-hidden">
                    <div class="h-full bg-red-600" style="width: 98%"></div>
                </div>
            </div>
        </div>

        <div class="bg-white/[0.02] border border-white/5 rounded-[32px] overflow-hidden">
            <div class="p-6 border-b border-white/5 bg-white/[0.01]">
                <h3 class="text-[11px] font-m uppercase tracking-[3px] text-gray-500 italic">Télémétrie Pilotes Récents</h3>
            </div>
            <table class="w-full text-left">
                <tbody class="divide-y divide-white/5">
                    <?php foreach($recentDrivers as $driver): ?>
                    <tr class="hover:bg-red-600/[0.02] transition-colors">
                        <td class="p-6 flex items-center gap-5">
                            <div class="w-11 h-11 rounded-xl bg-white/5 flex items-center justify-center font-bold text-lg text-red-600 border border-white/5">
                                <?= strtoupper(substr($driver['nom'], 0, 1)) ?>
                            </div>
                            <div>
                                <p class="text-sm font-bold uppercase italic tracking-tight"><?= htmlspecialchars($driver['nom'] . ' ' . $driver['prenom']) ?></p>
                                <p class="text-[9px] text-gray-600 font-bold uppercase tracking-widest">Platinum Class</p>
                            </div>
                        </td>
                        <td class="p-6 font-mono text-xs text-gray-500"><?= htmlspecialchars($driver['email']) ?></td>
                        <td class="p-6 text-right"><span class="px-4 py-1.5 bg-green-500/10 text-green-500 text-[8px] font-black uppercase rounded-full border border-green-500/10">Linked</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>