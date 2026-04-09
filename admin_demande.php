<?php
/**
 * SUPERCAR - Gestion des Demandes d'Essai (Table : demande_essai)
 */
session_start();
require_once 'config.php'; // Assure-toi que ce fichier définit la variable $db

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$message = "";

// --- 1. ACTIONS : ACCEPTER / REFUSER / SUPPRIMER ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    try {
        if ($action === 'accepter') {
            $stmt = $db->prepare("UPDATE demande_essai SET statut = 'Accepté' WHERE id_demande = ?");
            $stmt->execute([$id]);
            $message = "Demande validée.";
        } elseif ($action === 'refuser') {
            $stmt = $db->prepare("UPDATE demande_essai SET statut = 'Refusé' WHERE id_demande = ?");
            $stmt->execute([$id]);
            $message = "Demande refusée.";
        } elseif ($action === 'supprimer') {
            $stmt = $db->prepare("DELETE FROM demande_essai WHERE id_demande = ?");
            $stmt->execute([$id]);
            $message = "Demande supprimée.";
        }
    } catch (PDOException $e) {
        $message = "Erreur : " . $e->getMessage();
    }
}

// --- 2. RÉCUPÉRATION DES DEMANDES (DATAGRID) ---
// On joint les tables client et voiture pour afficher les noms au lieu des IDs
$query = "SELECT d.id_demande, d.date_souhaitee, d.statut, 
                 c.nom as client_nom, v.modele as voiture_nom 
          FROM demande_essai d
          JOIN client c ON d.id_client = c.id_client
          JOIN voiture v ON d.id_voiture = v.id_voiture
          ORDER BY d.date_souhaitee DESC";
$demandes = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SuperCar | Gestion Demandes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #050505; color: white; }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="p-8">

    <div class="max-w-6xl mx-auto">
        <header class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold uppercase italic">Gestion <span class="text-red-600">Essais</span></h1>
            <a href="DashBoard.php" class="text-xs border border-white/20 px-4 py-2 rounded hover:bg-white hover:text-black transition">RETOUR COCKPIT</a>
        </header>

        <?php if($message): ?>
            <div class="bg-red-600/20 border border-red-600 p-4 rounded mb-6 text-sm text-center"><?= $message ?></div>
        <?php endif; ?>

        <div class="glass-card p-6 rounded-2xl mb-8">
            <h2 class="text-red-600 text-xs font-bold uppercase mb-4 tracking-widest">Configuration Contenu Client</h2>
            <form class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" placeholder="Titre page client" class="bg-white/5 border border-white/10 p-2 rounded text-sm outline-none focus:border-red-600">
                <textarea placeholder="Texte d'introduction" class="bg-white/5 border border-white/10 p-2 rounded text-sm outline-none focus:border-red-600"></textarea>
                <div class="md:col-span-2 text-right">
                    <button type="button" class="bg-red-600 px-6 py-2 rounded text-[10px] font-bold">METTRE À JOUR</button>
                </div>
            </form>
        </div>

        <div class="glass-card rounded-2xl overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-white/5 text-gray-400 text-[10px] uppercase">
                    <tr>
                        <th class="p-4">Date Souhaitée</th>
                        <th class="p-4">Client</th>
                        <th class="p-4">Modèle</th>
                        <th class="p-4">Statut</th>
                        <th class="p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($demandes as $d): ?>
                    <tr class="border-t border-white/5 hover:bg-white/5">
                        <td class="p-4 font-mono"><?= htmlspecialchars($d['date_souhaitee']) ?></td>
                        <td class="p-4 font-bold"><?= htmlspecialchars($d['client_nom']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($d['voiture_nom']) ?></td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-[9px] font-bold uppercase <?= $d['statut'] == 'Accepté' ? 'bg-green-500/20 text-green-500' : ($d['statut'] == 'Refusé' ? 'bg-red-500/20 text-red-500' : 'bg-yellow-500/20 text-yellow-500') ?>">
                                <?= $d['statut'] ?>
                            </span>
                        </td>
                        <td class="p-4 flex justify-center gap-2">
                            <a href="?action=accepter&id=<?= $d['id_demande'] ?>" class="p-2 bg-green-500/20 text-green-500 rounded hover:bg-green-500 hover:text-white"><i class="fa-solid fa-check"></i></a>
                            <a href="?action=refuser&id=<?= $d['id_demande'] ?>" class="p-2 bg-orange-500/20 text-orange-500 rounded hover:bg-orange-500 hover:text-white"><i class="fa-solid fa-xmark"></i></a>
                            <a href="?action=supprimer&id=<?= $d['id_demande'] ?>" onclick="return confirm('Supprimer ?')" class="p-2 bg-red-500/20 text-red-500 rounded hover:bg-red-500 hover:text-white"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>