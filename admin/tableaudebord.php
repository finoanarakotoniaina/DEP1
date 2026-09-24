<?php
require_once '../config.php';
requireAdmin();

/** @var PDO $db */

try {
    // Infos admin connecté
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";

    // 1. Véhicules disponibles (non archivés)
    $countCars = $db->query("SELECT COUNT(*) FROM voiture WHERE archive = 0 OR archive IS NULL")->fetchColumn() ?: 0;

    // 2. Clients inscrits
    $countClients = $db->query("SELECT COUNT(*) FROM client")->fetchColumn() ?: 0;

    // 3. Messages reçus
    $countMessages = $db->query("SELECT COUNT(*) FROM contact_client")->fetchColumn() ?: 0;

    // 4. Demandes d'essai en attente
    $countDemandesAttente = $db->query("
        SELECT COUNT(*) FROM demande_essai 
        WHERE statut IS NULL 
           OR statut = 'en attente' 
           OR statut = 'en_attente'
    ")->fetchColumn() ?: 0;

    // 5 dernières demandes d'essai
    $stmtDemandes = $db->query("
        SELECT 
            de.id_demande,
            de.date_souhaitee,
            de.statut,
            CONCAT(c.prenom, ' ', c.nom) AS client_nom,
            c.email AS client_email,
            v.modele AS voiture_modele,
            m.nom_marque AS voiture_marque
        FROM demande_essai de
        LEFT JOIN client c ON de.id_client = c.id_client
        LEFT JOIN voiture v ON de.id_voiture = v.id_voiture
        LEFT JOIN marque m ON v.id_marque = m.id_marque
        ORDER BY de.id_demande DESC
        LIMIT 5
    ");
    $demandesRecentes = $stmtDemandes->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur système : connexion interrompue.");
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord | SuperCar Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="admin-body">

    <?php include 'composants/sidebar.php'; ?>

    <main class="admin-main">
        <header class="admin-header">
            <div>
                <h2 class="font-title">TABLEAU DE <span class="title-accent">BORD</span></h2>
                <p class="status-live">● Système opérationnel</p>
            </div>
        </header>

        <!-- 4 cartes stats -->
        <div class="admin-stats">

            <!-- 1. Véhicules -->
            <div class="stat-card">
                <p class="stat-label">Véhicules disponibles</p>
                <h3 class="stat-value"><?= $countCars ?></h3>
                <p class="stat-sub">En stock actuellement</p>
                <div class="stat-bar"></div>
            </div>

            <!-- 2. Clients -->
            <div class="stat-card">
                <p class="stat-label">Clients inscrits</p>
                <h3 class="stat-value"><?= $countClients ?></h3>
                <p class="stat-sub">Comptes enregistrés</p>
                <div class="stat-bar"></div>
            </div>

            <!-- 3. Messages -->
            <div class="stat-card">
                <p class="stat-label">Messages reçus</p>
                <h3 class="stat-value"><?= $countMessages ?></h3>
                <p class="stat-sub">Depuis le formulaire de contact</p>
                <div class="stat-bar"></div>
            </div>

            <!-- 4. Demandes en attente (cliquable) -->
            <a href="admin_demande.php" class="stat-card stat-card-accent stat-card-link">
                <p class="stat-label">Demandes en attente</p>
                <h3 class="stat-value"><?= $countDemandesAttente ?></h3>
                <p class="stat-sub">À confirmer</p>
                <div class="stat-bar"></div>
            </a>

        </div>

        <!-- Tableau dernières demandes d'essai -->
        <div class="table-container">
            <div class="table-header">
                <h3>Dernières demandes d'essai</h3>
                <a href="admin_demande.php" class="btn-consulter">Voir toutes les demandes</a>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Véhicule</th>
                        <th>Date souhaitée</th>
                        <th class="text-center">Statut</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($demandesRecentes)): ?>
                        <tr><td colspan="5" class="table-empty">Aucune demande enregistrée</td></tr>
                    <?php else: ?>
                        <?php foreach($demandesRecentes as $d): 
                            $statut = strtolower(trim($d['statut'] ?? ''));
                            
                            // Déterminer la classe badge et le label
                            if ($statut === 'accepte' || $statut === 'accepté' || $statut === 'confirmé') {
                                $badgeClass = 'badge-termine';
                                $badgeLabel = 'Acceptée';
                            } elseif ($statut === 'refuse' || $statut === 'refusé' || $statut === 'annulé') {
                                $badgeClass = 'badge-refuse';
                                $badgeLabel = 'Refusée';
                            } else {
                                $badgeClass = 'badge-attente';
                                $badgeLabel = 'En attente';
                            }
                        ?>
                        <tr>
                            <td>
                                <div class="driver-cell">
                                    <div class="driver-avatar"><?= strtoupper(substr($d['client_nom'] ?? 'A', 0, 1)) ?></div>
                                    <div>
                                        <p class="driver-name"><?= htmlspecialchars($d['client_nom'] ?? 'Anonyme') ?></p>
                                        <p class="driver-status"><?= htmlspecialchars($d['client_email'] ?? '—') ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="car-model">
                                    <?= htmlspecialchars(($d['voiture_marque'] ?? '') . ' ' . ($d['voiture_modele'] ?? '—')) ?>
                                </p>
                            </td>
                            <td class="font-mono" style="font-family: 'Courier New', monospace; font-size: 11px; color: var(--gray);">
                                <?= !empty($d['date_souhaitee']) ? date('d/m/Y', strtotime($d['date_souhaitee'])) : '—' ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
                            </td>
                            <td class="text-right">
                                <a href="admin_demande.php" class="btn-consulter">Traiter</a>
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