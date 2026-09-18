<?php
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn; 

try {
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";

    $countCars = $db->query("SELECT COUNT(*) FROM voiture")->fetchColumn() ?: 0;
    $countClients = $db->query("SELECT COUNT(*) FROM client")->fetchColumn() ?: 0;
    $countMessages = $db->query("SELECT COUNT(*) FROM contact_client")->fetchColumn() ?: 0;

    $stmtRecent = $db->query("SELECT id_client, nom, prenom, email FROM client ORDER BY id_client DESC LIMIT 3");
    $recentDrivers = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("ERREUR SYSTÈME : Connexion interrompue.");
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Command Center</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="admin-body">

    <?php include 'composants/sidebar.php'; ?>

    <main class="admin-main">
        <header class="admin-header">
            <div>
                <h2 class="font-title">COMMAND <span class="title-accent">CENTER</span></h2>
                <p class="status-live">● System Status: Nominal</p>
            </div>
            <div>
                <span class="session-badge">Session ID: <?= session_id() ?></span>
            </div>
        </header>

        <div class="admin-stats">
            <div class="stat-card">
                <p class="stat-label">Inventaire</p>
                <h3 class="stat-value"><?= $countCars ?></h3>
                <p class="stat-sub">Véhicules en stock</p>
                <div class="stat-bar"></div>
            </div>

            <div class="stat-card">
                <p class="stat-label">Registre</p>
                <h3 class="stat-value"><?= $countClients ?></h3>
                <p class="stat-sub">Pilotes certifiés</p>
                <div class="stat-bar"></div>
            </div>

            <div class="stat-card">
                <p class="stat-label">Messages</p>
                <h3 class="stat-value"><?= $countMessages ?></h3>
                <p class="stat-sub">Flux live reçu</p>
                <div class="stat-bar"></div>
            </div>

            <div class="stat-card stat-card-accent">
                <p class="stat-label">Performance</p>
                <h3 class="stat-value">98<span class="stat-unit">%</span></h3>
                <div class="progress-bar"><div class="progress-fill" style="width:98%"></div></div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h3>Derniers Pilotes Enregistrés</h3>
                <span class="status-live">Live Telemetry</span>
            </div>
            
            <table class="admin-table">
                <tbody>
                    <?php if (empty($recentDrivers)): ?>
                        <tr><td colspan="3" class="table-empty">Aucun signal détecté</td></tr>
                    <?php else: ?>
                        <?php foreach($recentDrivers as $driver): ?>
                        <tr>
                            <td>
                                <div class="driver-cell">
                                    <div class="driver-avatar"><?= strtoupper(substr($driver['nom'], 0, 1)) ?></div>
                                    <div>
                                        <p class="driver-name"><?= htmlspecialchars($driver['nom'] . ' ' . $driver['prenom']) ?></p>
                                        <p class="driver-status">Status: Verified Pilot</p>
                                    </div>
                                </div>
                            </td>
                            <td class="driver-email"><?= htmlspecialchars($driver['email']) ?></td>
                            <td class="text-right">
                                <a href="admin_client_detail.php?id=<?= $driver['id_client'] ?>" class="btn-consulter">Consulter</a>
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