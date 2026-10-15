<?php
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn;
$message_status = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id_contact'])) {
    try {
        if ($_POST['action'] === 'delete') {
            $stmt = $db->prepare("DELETE FROM contact_client WHERE id_contact = ?");
            $stmt->execute([$_POST['id_contact']]);
            $message_status = "✅ MESSAGE SUPPRIMÉ AVEC SUCCÈS";
        }
    } catch (Exception $e) {
        error_log("[SuperCar] admin_contact delete : " . $e->getMessage());
        $message_status = "❌ " . MSG_ERREUR;
    }
}

try {
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";

    $totalMessages = $db->query("SELECT COUNT(*) FROM contact_client")->fetchColumn();

    $messages = $db->query("
        SELECT id_contact, nom, prenom, email, message, date_envoi
        FROM contact_client
        ORDER BY id_contact DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("[SuperCar] admin_contact lecture : " . $e->getMessage());
    die(MSG_ERREUR);
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SuperCar | Inbox Messages</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="admin-body">

    <?php include 'composants/sidebar.php'; ?>

    <main class="admin-main">
        <header class="admin-header">
            <div>
                <h2 ><span class="title-accent">MESSAGES</span></h2>
                <p class="admin-subtitle">Gestion des retours et messages du formulaire de contact client</p>
            </div>
        </header>

        <?php if (!empty($message_status)): ?>
            <div class="alert-message"><?= $message_status ?></div>
        <?php endif; ?>

        <div class="admin-stats">
            <div class="stat-card">
                <p class="stat-label">Messages Reçus</p>
                <h3 class="stat-value"><?= $totalMessages ?></h3>
            </div>
        </div>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Expéditeur</th>
                        <th>Email</th>
                        <th>Date Envoi</th>
                        <th>Message</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($messages) > 0): ?>
                        <?php foreach($messages as $m): ?>
                            <tr>
                                <td>
                                    <p class="driver-name"><?= htmlspecialchars(($m['prenom'] ?? '') . ' ' . ($m['nom'] ?? '')) ?></p>
                                    <p class="driver-status">ID #<?= $m['id_contact'] ?></p>
                                </td>
                                <td><?= htmlspecialchars($m['email'] ?? '—') ?></td>
                                <td class="driver-email"><?= !empty($m['date_envoi']) ? date('d/m/Y H:i', strtotime($m['date_envoi'])) : '—' ?></td>
                                <td>
                                    <div class="message-box"><?= htmlspecialchars($m['message'] ?? '') ?></div>
                                </td>
                                <td class="text-center">
                                    <form method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce message ?');" style="display:inline">
                                        <input type="hidden" name="id_contact" value="<?= $m['id_contact'] ?>">
                                        <button type="submit" name="action" value="delete" class="btn-delete">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="table-empty">Boîte de réception vide</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>