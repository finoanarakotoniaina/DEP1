<?php
/**
 * SUPERCAR - Gestion des Demandes d'Essai (Version avec Recherche)
 */
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// --- 1. LOGIQUE DE RECHERCHE ---
$search = isset($_GET['search']) ? $_GET['search'] : '';

// --- 2. ACTIONS SUR LES DEMANDES ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    if ($action === 'accepter') {
        $db->prepare("UPDATE demande_essai SET statut = 'Accepté' WHERE id_demande = ?")->execute([$id]);
    } elseif ($action === 'refuser') {
        $db->prepare("UPDATE demande_essai SET statut = 'Refusé' WHERE id_demande = ?")->execute([$id]);
    }
    // Redirection pour éviter de répéter l'action au rafraîchissement
    header("Location: admin_demandes.php?search=$search");
    exit();
}

// --- 3. RÉCUPÉRATION DES COMPTEURS ---
$stats = [
    'total' => $db->query("SELECT COUNT(*) FROM demande_essai")->fetchColumn(),
    'attente' => $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut = 'En attente'")->fetchColumn(),
    'acceptes' => $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut = 'Accepté'")->fetchColumn(),
    'refus' => $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut = 'Refusé'")->fetchColumn()
];

// --- 4. RÉCUPÉRATION DU DATAGRID FILTRÉ ---
$sql = "SELECT d.id_demande, d.date_souhaitee, d.statut, c.nom as client_nom, v.modele as voiture_nom 
        FROM demande_essai d
        JOIN client c ON d.id_client = c.id_client
        JOIN voiture v ON d.id_voiture = v.id_voiture";

if (!empty($search)) {
    $sql .= " WHERE c.nom LIKE :search OR v.modele LIKE :search OR d.statut LIKE :search";
    $stmt = $db->prepare($sql);
    $stmt->execute(['search' => "%$search%"]);
} else {
    $sql .= " ORDER BY d.id_demande DESC";
    $stmt = $db->query($sql);
}
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr" class="bg-[#121212]">
<head>
    <meta charset="UTF-8">
    <title>SuperCar | Gestion Demandes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: white; }
        .stat-card { background: white; color: black; padding: 20px; text-align: center; border: 1px solid #ddd; }
    </style>
</head>
<body class="flex min-h-screen">

    <aside class="w-64 bg-[#3b71ca] p-6 flex flex-col gap-4">
        <h2 class="font-bold text-lg">Barre de navigation</h2>
        <div class="mt-4">
            <p class="opacity-80">Admin Identity:</p>
            <p class="font-bold mb-4"><?= htmlspecialchars($_SESSION['admin_name']) ?></p>
            <a href="DashBoard.php" class="block py-2 px-4 bg-white/10 rounded hover:bg-white/20 transition">Dashboard</a>
            <a href="logout.php" class="block py-2 px-4 mt-2 text-red-200 hover:text-white transition">Déconnexion</a>
        </div>
    </aside>

    <main class="flex-1 p-10">
        
        <section class="mb-10">
            <h3 class="uppercase text-xs font-bold text-gray-500 mb-4 tracking-widest">Affichage</h3>
            <div class="bg-white p-6 rounded-sm shadow-xl">
                <form action="save_config.php" method="POST">
                    <textarea name="client_instruction" class="w-full h-24 p-4 text-black border border-gray-300 outline-none focus:border-[#3b71ca]" 
                    placeholder="Ecriture modifiable au saisit lié à la page demande.php avec bouton enregistrer"></textarea>
                    <div class="text-right mt-4">
                        <button type="submit" class="bg-[#3b71ca] px-8 py-2 text-white font-bold rounded uppercase text-xs">Enregistrer</button>
                    </div>
                </form>
            </div>
        </section>

        <section>
            <h3 class="uppercase text-xs font-bold text-gray-500 mb-6 tracking-widest">Gestion des demandes d'essai</h3>
            
            <div class="bg-[#3b71ca] w-1/2 min-w-[300px] h-12 flex items-center px-4 rounded-t-lg shadow-lg">
                <form action="" method="GET" class="flex w-full items-center gap-3">
                    <i class="fa-solid fa-magnifying-glass text-white/80"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Rechercher un client, une voiture ou un statut..." 
                           class="bg-transparent border-none outline-none text-white placeholder-white/60 w-full text-sm font-medium">
                    <?php if(!empty($search)): ?>
                        <a href="admin_demandes.php" class="text-white/80 hover:text-white"><i class="fa-solid fa-xmark"></i></a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="grid grid-cols-5 gap-1 mb-6 shadow-sm">
                <div class="stat-card">Total demandes<br><span class="text-2xl font-bold"><?= $stats['total'] ?></span></div>
                <div class="stat-card">En attente<br><span class="text-2xl font-bold"><?= $stats['attente'] ?></span></div>
                <div class="stat-card">Acceptés<br><span class="text-2xl font-bold"><?= $stats['acceptes'] ?></span></div>
                <div class="stat-card"><span class="text-blue-600 underline">refus</span><br><span class="text-2xl font-bold"><?= $stats['refus'] ?></span></div>
                <div class="stat-card bg-gray-50">Planning<br><span class="text-xs font-bold text-gray-400 uppercase">View All</span></div>
            </div>

            <div class="bg-white text-black p-6 rounded-b-sm shadow-2xl min-h-[400px]">
                <p class="text-gray-400 text-[10px] uppercase font-bold mb-4 tracking-tighter italic">Datagrid des listes des demandes (<?= !empty($search) ? 'Filtré' : 'Mélangé' ?>)</p>
                
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b-2 border-gray-100 text-[11px] uppercase text-gray-400 font-black">
                            <th class="pb-4 px-2">ID_DEM</th>
                            <th class="pb-4">Identité Pilote</th>
                            <th class="pb-4">Modèle Véhicule</th>
                            <th class="pb-4">Date Prévue</th>
                            <th class="pb-4">Status</th>
                            <th class="pb-4 text-right">Contrôle</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php foreach($demandes as $d): ?>
                        <tr class="border-b border-gray-50 hover:bg-blue-50/50 transition-all group">
                            <td class="py-4 px-2 font-mono text-gray-400">#<?= $d['id_demande'] ?></td>
                            <td class="py-4 font-bold"><?= htmlspecialchars($d['client_nom']) ?></td>
                            <td class="py-4 text-[#3b71ca] font-semibold italic"><?= htmlspecialchars($d['voiture_nom']) ?></td>
                            <td class="py-4 text-gray-500"><?= htmlspecialchars($d['date_souhaitee']) ?></td>
                            <td class="py-4">
                                <span class="px-3 py-1 rounded text-[10px] font-black uppercase shadow-sm
                                <?= $d['statut'] == 'Accepté' ? 'bg-green-100 text-green-700' : ($d['statut'] == 'Refusé' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') ?>">
                                    <?= $d['statut'] ?>
                                </span>
                            </td>
                            <td class="py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="?action=accepter&id=<?= $d['id_demande'] ?>&search=<?= $search ?>" class="text-green-500 hover:scale-125 transition-transform"><i class="fa-solid fa-circle-check text-xl"></i></a>
                                    <a href="?action=refuser&id=<?= $d['id_demande'] ?>&search=<?= $search ?>" class="text-red-400 hover:scale-125 transition-transform"><i class="fa-solid fa-circle-xmark text-xl"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($demandes)): ?>
                            <tr><td colspan="6" class="p-20 text-center text-gray-300 italic">Aucun résultat trouvé pour "<?= htmlspecialchars($search) ?>"</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>