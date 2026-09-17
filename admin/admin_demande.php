<?php
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn;
$message_status = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id_demande'])) {
    $new_statut = $_POST['action'] === 'accepte' ? 'accepte' : 'refuse';
    $db->prepare("UPDATE demande_essai SET statut = ? WHERE id_demande = ?")
       ->execute([$new_statut, $_POST['id_demande']]);
    header('Location: admin_demande.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_ui'])) {
    try {
        $stmt_text = $db->prepare("UPDATE essai_content SET content = ? WHERE element_id = ?");
        $stmt_text->execute([$_POST['txt_1'] ?? '', 1]);
        $stmt_text->execute([$_POST['txt_2'] ?? '', 2]);
        $stmt_text->execute([$_POST['txt_3'] ?? '', 3]);
        $stmt_text->execute([$_POST['txt_4'] ?? '', 4]);
        $stmt_text->execute([$_POST['txt_5'] ?? '', 5]);

        if (isset($_FILES['image_hero']) && $_FILES['image_hero']['error'] === 0) {
            $dir = '../image_url/essai/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $extension = pathinfo($_FILES['image_hero']['name'], PATHINFO_EXTENSION);
            $new_file_name = 'hero_essai_' . time() . '.' . $extension;
            $destination = $dir . $new_file_name;
            if (move_uploaded_file($_FILES['image_hero']['tmp_name'], $destination)) {
                $db->prepare("UPDATE essai_content SET content = ? WHERE element_id = 6")
                   ->execute(['image_url/essai/' . $new_file_name]);
            }
        }
        $message_status = "✅ INTERFACE MISE À JOUR";
    } catch (Exception $e) {
        $message_status = "❌ ERREUR : " . $e->getMessage();
    }
}

try {
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";

    $ui = $db->query("SELECT element_id, content FROM essai_content")->fetchAll(PDO::FETCH_KEY_PAIR);
    $img_path = $ui[6] ?? 'photo_essai.jpeg';

    $totalDemandes = $db->query("SELECT COUNT(*) FROM demande_essai")->fetchColumn();
    $enCours = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut = 'en_attente' OR statut IS NULL OR statut = 'en attente'")->fetchColumn();
    $acceptees = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut = 'accepte'")->fetchColumn();
    $refusees = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut = 'refuse' OR statut = 'refusé'")->fetchColumn();

    $demandes = $db->query("
        SELECT de.id_demande, de.date_souhaitee, de.statut,
               CONCAT(c.prenom, ' ', c.nom) AS nom_complet,
               c.email, c.telephone, v.modele
        FROM demande_essai de
        LEFT JOIN client c ON de.id_client = c.id_client
        LEFT JOIN voiture v ON de.id_voiture = v.id_voiture
        ORDER BY de.id_demande DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SuperCar | Command Center</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="admin-body">

    <?php include 'composants/sidebar.php'; ?>

    <main class="admin-main">
        <header class="admin-header">
            <div>
                <h2 class="font-title">GESTION DES <span class="title-accent">DEMANDES</span></h2>
            </div>
            <div class="tabs">
                <div onclick="switchTab('radar')" id="btn-radar" class="tab-btn active">Demandes</div>
                <div onclick="switchTab('editor')" id="btn-editor" class="tab-btn">Interface</div>
                <div onclick="switchTab('history')" id="btn-history" class="tab-btn">Historique</div>
            </div>
        </header>

        <?php if (!empty($message_status)): ?>
            <div class="alert-message"><?= $message_status ?></div>
        <?php endif; ?>

        <section id="tab-radar" class="tab-content">
            <div class="admin-stats">
                <div class="stat-card"><p class="stat-label">Flux Total</p><h3 class="stat-value"><?= $totalDemandes ?></h3></div>
                <div class="stat-card"><p class="stat-label">En Attente</p><h3 class="stat-value"><?= $enCours ?></h3></div>
                <div class="stat-card"><p class="stat-label">Approuvées</p><h3 class="stat-value"><?= $acceptees ?></h3></div>
                <div class="stat-card"><p class="stat-label">Refusées</p><h3 class="stat-value"><?= $refusees ?></h3></div>
            </div>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Client</th><th>Modèle Véhicule</th><th>Téléphone</th>
                            <th>Date Souhaitée</th><th class="text-center">Statut</th><th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $hasActive = false;
                        foreach($demandes as $d): 
                            $statut = strtolower(trim($d['statut'] ?? ''));
                            if ($statut === 'termine' || $statut === 'terminé' || $statut === 'refuse' || $statut === 'refusé') continue;
                            $hasActive = true;
                            $dot = ($statut === 'accepte' || $statut === 'accepté') ? 'dot-green' : 'dot-orange';
                        ?>
                        <tr>
                            <td>
                                <p class="driver-name"><?= htmlspecialchars($d['nom_complet'] ?? 'Anonyme') ?></p>
                                <p class="driver-status">#<?= $d['id_demande'] ?></p>
                            </td>
                            <td><span class="car-model"><?= htmlspecialchars($d['modele'] ?? '—') ?></span></td>
                            <td><?= htmlspecialchars($d['telephone'] ?? '—') ?></td>
                            <td><?= $d['date_souhaitee'] ? date('d/m/Y', strtotime($d['date_souhaitee'])) : '—' ?></td>
                            <td class="text-center"><span class="status-dot <?= $dot ?>"></span></td>
                            <td class="text-center">
                                <form method="POST" style="display:inline">
                                    <input type="hidden" name="id_demande" value="<?= $d['id_demande'] ?>">
                                    <button type="submit" name="action" value="accepte" class="btn-accept">✓</button>
                                </form>
                                <form method="POST" style="display:inline">
                                    <input type="hidden" name="id_demande" value="<?= $d['id_demande'] ?>">
                                    <button type="submit" name="action" value="refuse" class="btn-refuse">✕</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(!$hasActive): ?>
                            <tr><td colspan="6" class="table-empty">Aucune nouvelle demande sur le radar.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="tab-editor" class="tab-content hidden">
            <form method="POST" enctype="multipart/form-data">
                <div class="live-wrap">
                    <?php 
                    $src_img = (strpos($img_path, 'image_url/') === 0) 
                        ? BASE_URL . '/' . $img_path 
                        : ASSETS_URL . '/hero/' . $img_path;
                    ?>
                    <img id="preview_img" src="<?= $src_img ?>" class="live-preview-img">
                    <div class="live-fields">
                        <input type="text" name="txt_1" value="<?= htmlspecialchars($ui[1] ?? '') ?>" class="live-field">
                        <textarea name="txt_2" rows="1" class="live-field font-title"><?= htmlspecialchars($ui[2] ?? '') ?></textarea>
                        <textarea name="txt_3" rows="1" class="live-field font-title title-accent"><?= htmlspecialchars($ui[3] ?? '') ?></textarea>
                        <textarea name="txt_4" rows="3" class="live-field"><?= htmlspecialchars($ui[4] ?? '') ?></textarea>
                        <input type="text" name="txt_5" value="<?= htmlspecialchars($ui[5] ?? '') ?>" class="live-field">
                    </div>
                    <label for="image_hero_file" class="upload-btn">Changer l'image
                        <input type="file" id="image_hero_file" name="image_hero" class="hidden" accept="image/*" onchange="previewHero(event)">
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" name="save_ui" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </section>

        <section id="tab-history" class="tab-content hidden">
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Client</th><th>Modèle</th><th>Téléphone / Email</th>
                            <th>Date Session</th><th class="text-center">État Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $hasArchive = false;
                        foreach($demandes as $row): 
                            $st = strtolower(trim($row['statut'] ?? ''));
                            if ($st !== 'refuse' && $st !== 'refusé' && $st !== 'termine' && $st !== 'terminé') continue;
                            $hasArchive = true;
                            if ($st === 'termine' || $st === 'terminé') {
                                $badgeClass = "badge-termine"; $badgeLabel = "Terminé";
                            } else {
                                $badgeClass = "badge-refuse"; $badgeLabel = "Refusé";
                            }
                        ?>
                        <tr>
                            <td>
                                <p class="driver-name"><?= htmlspecialchars($row['nom_complet'] ?? 'Anonyme') ?></p>
                                <p class="driver-status">#<?= $row['id_demande'] ?></p>
                            </td>
                            <td><?= htmlspecialchars($row['modele'] ?? '—') ?></td>
                            <td>
                                <p><?= htmlspecialchars($row['telephone'] ?? '—') ?></p>
                                <p class="driver-email"><?= htmlspecialchars($row['email'] ?? '—') ?></p>
                            </td>
                            <td><?= $row['date_souhaitee'] ? date('d/m/Y', strtotime($row['date_souhaitee'])) : '—' ?></td>
                            <td class="text-center"><span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(!$hasArchive): ?>
                            <tr><td colspan="5" class="table-empty">Aucun historique enregistré.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            document.getElementById('btn-' + tabId).classList.add('active');
        }
        function previewHero(event) {
            const reader = new FileReader();
            reader.onload = function(){
                document.getElementById('preview_img').src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>