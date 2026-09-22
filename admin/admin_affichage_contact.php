<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn;
$message_status = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_ui'])) {
    try {
        $stmt_text = $db->prepare("UPDATE contact_content SET content = ? WHERE element_id = ?");
        $stmt_text->execute([trim($_POST['txt_1'] ?? ''), 'titre_principal']);
        $stmt_text->execute([trim($_POST['txt_2'] ?? ''), 'responsable_contact']);
        $stmt_text->execute([trim($_POST['txt_3'] ?? ''), 'siege_contact']);
        $stmt_text->execute([trim($_POST['txt_4'] ?? ''), 'tel_contact']);
        $stmt_text->execute([trim($_POST['txt_5'] ?? ''), 'email_contact']);
        $message_status = "✅ INTERFACE MISE À JOUR AVEC SUCCÈS";
    } catch (Exception $e) {
        $message_status = "❌ ERREUR : " . $e->getMessage();
    }
}

try {
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";

    $settings = $db->query("SELECT element_id, content FROM contact_content")->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    die("Erreur Système : " . $e->getMessage());
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Contact Config</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="admin-body">

    <?php include 'composants/sidebar.php'; ?>

    <main class="admin-main">
        <header class="admin-header">
            <div>
                <h2 class="font-title">CONTACT <span class="title-accent">LIVE EDITOR</span></h2>
                <p class="admin-subtitle">Cliquez directement dans les zones pointillées pour modifier les textes</p>
            </div>
        </header>

        <?php if (!empty($message_status)): ?>
            <div class="alert-message"><?= $message_status ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="live-wrap">
                <div class="live-block">
                    <input type="text" name="txt_1"
                           value="<?= htmlspecialchars($settings['titre_principal'] ?? '') ?>"
                           class="live-field font-title">
                    <textarea name="txt_2" rows="2"
                              class="live-field"><?= htmlspecialchars($settings['responsable_contact'] ?? '') ?></textarea>
                </div>

                <div class="contact-cards">
                    <div class="contact-card">
                        <span class="contact-info-label">Adresse</span>
                        <textarea name="txt_3" rows="2" class="live-field"><?= htmlspecialchars($settings['siege_contact'] ?? '') ?></textarea>
                    </div>

                    <div class="contact-card">
                        <span class="contact-info-label">Téléphone</span>
                        <input type="text" name="txt_4" value="<?= htmlspecialchars($settings['tel_contact'] ?? '') ?>" class="live-field">
                    </div>

                    <div class="contact-card">
                        <span class="contact-info-label">E-mail</span>
                        <input type="text" name="txt_5" value="<?= htmlspecialchars($settings['email_contact'] ?? '') ?>" class="live-field">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="save_ui" class="btn btn-primary">Mettre à jour l'affichage</button>
            </div>
        </form>
    </main>

</body>
</html>