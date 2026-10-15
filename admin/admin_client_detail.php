<?php
require_once '../config.php';
requireAdmin();

/** @var PDO $db */

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: tableaudebord.php');
    exit();
}

$clientId = (int) $_GET['id'];

try {
    // Application des auto-statuts sur les demandes du client
    $db->query("
        UPDATE demande_essai 
        SET statut = 'refuse' 
        WHERE (statut = 'en attente' OR statut = 'en_attente' OR statut IS NULL OR statut = '')
          AND CONCAT(date_souhaitee, ' ', COALESCE(heure_souhaitee, '23:59:59')) < NOW()
    ");
    $db->query("
        UPDATE demande_essai 
        SET statut = 'termine' 
        WHERE statut = 'accepte' 
          AND CONCAT(date_souhaitee, ' ', COALESCE(heure_souhaitee, '23:59:59')) < NOW()
    ");

    $stmtClient = $db->prepare("SELECT * FROM client WHERE id_client = ?");
    $stmtClient->execute([$clientId]);
    $client = $stmtClient->fetch(PDO::FETCH_ASSOC);

    if (!$client) { header('Location: tableaudebord.php'); exit(); }

    // Demandes d'essai du client
    $demandes = [];
    try {
        $stmtDemandes = $db->prepare("
            SELECT d.*, m.nom_marque AS marque, v.modele 
            FROM demande_essai d 
            LEFT JOIN voiture v ON d.id_voiture = v.id_voiture 
            LEFT JOIN marque m ON v.id_marque = m.id_marque 
            WHERE d.id_client = ? 
            ORDER BY d.id_demande DESC
        ");
        $stmtDemandes->execute([$clientId]);
        $demandes = $stmtDemandes->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("[SuperCar] client_detail demandes : " . $e->getMessage());
    }

    // Messages de contact liés par email
    $messages = [];
    try {
        $stmtMessages = $db->prepare("SELECT * FROM contact_client WHERE email = ? ORDER BY id_contact DESC");
        $stmtMessages->execute([$client['email']]);
        $messages = $stmtMessages->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("[SuperCar] client_detail messages : " . $e->getMessage());
    }

    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";
} catch (PDOException $e) {
    error_log("[SuperCar] admin_client_detail lecture : " . $e->getMessage());
    die(MSG_ERREUR);
}

$initials = strtoupper(substr($client['nom'], 0, 1) . substr($client['prenom'], 0, 1));
$fullName = htmlspecialchars($client['prenom'] . ' ' . $client['nom']);
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Fiche client — <?= $fullName ?></title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="admin-body">

    <?php include 'composants/sidebar.php'; ?>

    <main class="admin-main">
        <header class="admin-header">
            <a href="tableaudebord.php" class="back-link">←</a>
            <div>
                <p class="admin-subtitle">Tableau de bord / Fiche client</p>
                <h2 class="font-title">FICHE <span class="title-accent">CLIENT</span></h2>
            </div>
            <div>
                <a href="admin_demande.php?client=<?= $clientId ?>" class="btn btn-outline">Voir les demandes</a>
            </div>
        </header>

        <div class="glass-card">
            <div class="pilot-header">
                <div class="pilot-avatar"><?= $initials ?></div>
                <div>
                    <p class="driver-status">ID CLIENT #<?= str_pad($client['id_client'], 4, '0', STR_PAD_LEFT) ?></p>
                    <h1 class="font-title"><?= $fullName ?></h1>
                    <div class="pilot-badges">
                        <span>Login #<?= $client['id_login'] ?? 'N/A' ?></span>
                        <span>Client #<?= $client['id_client'] ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-stats">
            <div class="info-field">
                <p class="stat-label">Adresse Email</p>
                <p class="font-mono"><?= htmlspecialchars($client['email']) ?></p>
            </div>
            <div class="info-field">
                <p class="stat-label">Téléphone</p>
                <p class="font-mono"><?= htmlspecialchars($client['telephone'] ?? 'Non renseigné') ?></p>
            </div>
            <div class="info-field">
                <p class="stat-label">Statut Compte</p>
                <p>● Actif</p>
                <p class="driver-status">Accès vérifié</p>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h3>Historique des Demandes</h3>
                <span class="driver-status"><?= count($demandes) ?> demande(s)</span>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Véhicule</th>
                        <th>Statut</th>
                        <th>Date souhaitée</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($demandes)): ?>
                        <tr><td colspan="4" class="table-empty">Aucune demande enregistrée</td></tr>
                    <?php else: ?>
                        <?php foreach ($demandes as $d): 
                            $st = strtolower(trim($d['statut'] ?? ''));
                            $estAcceptee  = ($st === 'accepte'  || $st === 'accepté');
                            $estRefusee   = ($st === 'refuse'   || $st === 'refusé');
                            $estTerminee  = ($st === 'termine'  || $st === 'terminé');
                            
                            if ($estTerminee) {
                                $badgeClass = 'badge-termine'; $badgeLabel = 'Terminé';
                            } elseif ($estAcceptee) {
                                $badgeClass = 'badge-accepte'; $badgeLabel = 'Approuvé';
                            } elseif ($estRefusee) {
                                $badgeClass = 'badge-refuse';  $badgeLabel = 'Refusé';
                            } else {
                                $badgeClass = 'badge-attente'; $badgeLabel = 'En attente';
                            }
                        ?>
                        <tr>
                            <td class="font-mono">#<?= str_pad($d['id_demande'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars(($d['marque'] ?? '') . ' ' . ($d['modele'] ?? 'Véhicule inconnu')) ?></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span></td>
                            <td class="font-mono"><?= htmlspecialchars($d['date_souhaitee'] ?? '—') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($messages)): ?>
        <div class="table-container">
            <div class="table-header"><h3>Messages Associés</h3></div>
            <?php foreach ($messages as $msg): ?>
            <div class="message-item">
                <p class="driver-status">Sujet : <?= htmlspecialchars($msg['sujet'] ?? 'Sans sujet') ?></p>
                <p><?= htmlspecialchars($msg['message'] ?? '') ?></p>
                <span class="driver-status"><?= htmlspecialchars($msg['date_envoi'] ?? '') ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>