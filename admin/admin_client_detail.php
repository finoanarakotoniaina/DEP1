<?php
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: tableaudebord.php');
    exit();
}

$clientId = (int) $_GET['id'];
$db = $pdo ?? $db ?? $conn;

try {
    $stmtClient = $db->prepare("SELECT * FROM client WHERE id_client = ?");
    $stmtClient->execute([$clientId]);
    $client = $stmtClient->fetch(PDO::FETCH_ASSOC);

    if (!$client) { header('Location: tableaudebord.php'); exit(); }

    $demandes = [];
    try {
        $stmtDemandes = $db->prepare("
            SELECT d.*, v.marque, v.modele 
            FROM demande_essai d 
            LEFT JOIN voiture v ON d.id_voiture = v.id_voiture 
            WHERE d.id_client = ? 
            ORDER BY d.id_demande DESC
        ");
        $stmtDemandes->execute([$clientId]);
        $demandes = $stmtDemandes->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}

    $messages = [];
    try {
        $stmtMessages = $db->prepare("SELECT * FROM contact WHERE id_contact = ? ORDER BY id_contact DESC");
        $stmtMessages->execute([$client['id_contact']]);
        $messages = $stmtMessages->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}

    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";
} catch (PDOException $e) {
    die("ERREUR SYSTÈME : " . $e->getMessage());
}

$initials = strtoupper(substr($client['nom'], 0, 1) . substr($client['prenom'], 0, 1));
$fullName = htmlspecialchars($client['prenom'] . ' ' . $client['nom']);
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SuperCar | Fiche Pilote — <?= $fullName ?></title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="admin-body">

    <?php include 'composants/sidebar.php'; ?>

    <main class="admin-main">
        <header class="admin-header">
            <a href="tableaudebord.php" class="back-link">← Retour</a>
            <div>
                <p class="admin-subtitle">Tableau de bord / Fiche Pilote</p>
                <h2 class="font-title">FICHE <span class="title-accent">PILOTE</span></h2>
            </div>
            <div>
                <span class="badge badge-termine">● Verified Pilot</span>
                <a href="admin_demande.php?client=<?= $clientId ?>" class="btn btn-outline">Demandes</a>
            </div>
        </header>

        <div class="glass-card">
            <div class="pilot-header">
                <div class="pilot-avatar"><?= $initials ?></div>
                <div>
                    <p class="driver-status">ID PILOTE #<?= str_pad($client['id_client'], 4, '0', STR_PAD_LEFT) ?></p>
                    <h1 class="font-title"><?= $fullName ?></h1>
                    <div class="pilot-badges">
                        <span>Login #<?= $client['id_login'] ?? 'N/A' ?></span>
                        <span>Contact #<?= $client['id_contact'] ?? 'N/A' ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-stats">
            <div class="info-field">
                <p class="stat-label">Adresse Email</p>
                <p class="font-mono"><?= htmlspecialchars($client['email']) ?></p>
                <a href="mailto:<?= htmlspecialchars($client['email']) ?>" class="link-crimson">Envoyer un email →</a>
            </div>
            <div class="info-field">
                <p class="stat-label">Téléphone</p>
                <p class="font-mono"><?= htmlspecialchars($client['telephone'] ?? 'Non renseigné') ?></p>
                <?php if (!empty($client['telephone'])): ?>
                <a href="tel:<?= htmlspecialchars($client['telephone']) ?>" class="link-crimson">Appeler →</a>
                <?php endif; ?>
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
                    <tr><th>ID</th><th>Véhicule</th><th>Statut</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($demandes)): ?>
                        <tr><td colspan="4" class="table-empty">Aucune demande enregistrée</td></tr>
                    <?php else: ?>
                        <?php foreach ($demandes as $d): ?>
                        <tr>
                            <td class="font-mono">#<?= str_pad($d['id_demande'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars(($d['marque'] ?? '') . ' ' . ($d['modele'] ?? 'Véhicule inconnu')) ?></td>
                            <td>
                                <?php
                                $statut = $d['statut'] ?? 'En attente';
                                $cls = match(strtolower($statut)) {
                                    'accepté','accepte','confirmé' => 'badge-termine',
                                    'refusé','refuse','annulé' => 'badge-refuse',
                                    default => 'badge-attente',
                                };
                                ?>
                                <span class="badge <?= $cls ?>"><?= htmlspecialchars($statut) ?></span>
                            </td>
                            <td class="font-mono"><?= htmlspecialchars($d['date_demande'] ?? '—') ?></td>
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
                <span class="driver-status"><?= htmlspecialchars($msg['date'] ?? '') ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>