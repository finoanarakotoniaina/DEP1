<?php
require_once '../config.php';
requireAdmin();

/** @var PDO $db */

$message_status = "";

// =========================================================
// 1. AUTO-STATUTS
// =========================================================
try {
    // Auto-REFUS : En attente + date passée
    $db->query("
        UPDATE demande_essai 
        SET statut = 'refuse' 
        WHERE (statut = 'en attente' OR statut = 'en_attente' OR statut IS NULL OR statut = '')
          AND CONCAT(date_souhaitee, ' ', COALESCE(heure_souhaitee, '23:59:59')) < NOW()
    ");

    // Auto-TERMINÉ : Approuvé + date passée
    $db->query("
        UPDATE demande_essai 
        SET statut = 'termine' 
        WHERE statut = 'accepte' 
          AND CONCAT(date_souhaitee, ' ', COALESCE(heure_souhaitee, '23:59:59')) < NOW()
    ");
} catch (PDOException $e) {
    // Silencieux
}

// =========================================================
// 2. ACTIONS POST (accepter / refuser / terminer / modifier date)
// =========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id_demande'])) {
    $id = (int) $_POST['id_demande'];
    $action = $_POST['action'];

    try {
        if ($action === 'accepte') {
            $db->prepare("UPDATE demande_essai SET statut = 'accepte' WHERE id_demande = ?")
               ->execute([$id]);
        } elseif ($action === 'refuse') {
            $db->prepare("UPDATE demande_essai SET statut = 'refuse' WHERE id_demande = ?")
               ->execute([$id]);
        } elseif ($action === 'termine') {
            $db->prepare("UPDATE demande_essai SET statut = 'termine' WHERE id_demande = ?")
               ->execute([$id]);
        } elseif ($action === 'modifier_date' && !empty($_POST['nouvelle_date'])) {
            $nouvelle_date  = $_POST['nouvelle_date'];
            $nouvelle_heure = $_POST['nouvelle_heure'] ?? null;
            $db->prepare("UPDATE demande_essai SET date_souhaitee = ?, heure_souhaitee = ? WHERE id_demande = ?")
               ->execute([$nouvelle_date, $nouvelle_heure, $id]);
        }

        header("Location: admin_demande.php");
        exit();
    } catch (PDOException $e) {
        $message_status = "Erreur : " . $e->getMessage();
    }
}

// =========================================================
// 3. SAVE UI (onglet Interface) — avec les champs 7 à 12
// =========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_ui'])) {
    try {
        $stmt_text = $db->prepare("UPDATE essai_content SET content = ? WHERE element_id = ?");

        // IDs existants (1 à 5)
        $stmt_text->execute([$_POST['txt_1']  ?? '', 1]);
        $stmt_text->execute([$_POST['txt_2']  ?? '', 2]);
        $stmt_text->execute([$_POST['txt_3']  ?? '', 3]);
        $stmt_text->execute([$_POST['txt_4']  ?? '', 4]);
        $stmt_text->execute([$_POST['txt_5']  ?? '', 5]);

        // Nouveaux IDs : configurateur (7 à 12)
        $stmt_text->execute([$_POST['txt_7']  ?? '', 7]);
        $stmt_text->execute([$_POST['txt_8']  ?? '', 8]);
        $stmt_text->execute([$_POST['txt_9']  ?? '', 9]);
        $stmt_text->execute([$_POST['txt_10'] ?? '', 10]);
        $stmt_text->execute([$_POST['txt_11'] ?? '', 11]);
        $stmt_text->execute([$_POST['txt_12'] ?? '', 12]);

        // Image hero (ID 6)
        if (isset($_FILES['image_hero']) && $_FILES['image_hero']['error'] === 0) {
            $dir = '../image_url/essai/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $extension = pathinfo($_FILES['image_hero']['name'], PATHINFO_EXTENSION);
            $new_file_name = 'hero_essai_' . time() . '.' . $extension;
            $destination = $dir . $new_file_name;
            if (move_uploaded_file($_FILES['image_hero']['tmp_name'], $destination)) {
                $stmt_text->execute(['image_url/essai/' . $new_file_name, 6]);
            }
        }

        header("Location: admin_demande.php");
        exit();
    } catch (Exception $e) {
        $message_status = "❌ ERREUR : " . $e->getMessage();
    }
}

// =========================================================
// 4. LECTURE DES DONNÉES
// =========================================================
try {
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";

    $ui = $db->query("SELECT element_id, content FROM essai_content")->fetchAll(PDO::FETCH_KEY_PAIR);
    $img_path = $ui[6] ?? 'photo_essai.jpeg';

    // Compteurs
    $totalDemandes = $db->query("SELECT COUNT(*) FROM demande_essai")->fetchColumn();
    $enCours       = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut IS NULL OR statut = '' OR statut = 'en attente' OR statut = 'en_attente'")->fetchColumn();
    $acceptees     = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut IN ('accepte','accepté')")->fetchColumn();
    $refusees      = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut IN ('refuse','refusé')")->fetchColumn();
    $terminees     = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut IN ('termine','terminé')")->fetchColumn();

    // Requête principale
    $demandes = $db->query("
        SELECT de.id_demande, de.date_souhaitee, de.heure_souhaitee, de.statut,
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

$filtre = $_GET['filtre'] ?? '';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des demandes | SuperCar Admin</title>
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
            <div class="alert-message"><?= htmlspecialchars($message_status) ?></div>
        <?php endif; ?>

        <!-- CARTES STATISTIQUES CLIQUABLES -->
        <div class="admin-stats">
            <a href="?filtre=all" class="stat-card stat-card-link <?= ($filtre === 'all' || $filtre === '') ? 'stat-active' : '' ?>">
                <p class="stat-label">Flux Total</p>
                <h3 class="stat-value"><?= $totalDemandes ?></h3>
            </a>
            <a href="?filtre=attente" class="stat-card stat-card-link <?= $filtre === 'attente' ? 'stat-active' : '' ?>">
                <p class="stat-label">En Attente</p>
                <h3 class="stat-value"><?= $enCours ?></h3>
            </a>
            <a href="?filtre=accepte" class="stat-card stat-card-link <?= $filtre === 'accepte' ? 'stat-active' : '' ?>">
                <p class="stat-label">Approuvées</p>
                <h3 class="stat-value"><?= $acceptees ?></h3>
            </a>
            <a href="?filtre=refuse" class="stat-card stat-card-link <?= $filtre === 'refuse' ? 'stat-active' : '' ?>">
                <p class="stat-label">Refusées</p>
                <h3 class="stat-value"><?= $refusees ?></h3>
            </a>
            <a href="?filtre=termine" class="stat-card stat-card-link <?= $filtre === 'termine' ? 'stat-active' : '' ?>">
                <p class="stat-label">Terminées</p>
                <h3 class="stat-value"><?= $terminees ?></h3>
            </a>
        </div>

        <!-- ============ ONGLET DEMANDES ============ -->
        <section id="tab-radar" class="tab-content">
            <?php if ($filtre === 'accepte'): ?>
                <h3 class="section-title">Demandes Approuvées (<?= $acceptees ?>)</h3>
            <?php elseif ($filtre === 'refuse'): ?>
                <h3 class="section-title">Demandes Refusées (<?= $refusees ?>)</h3>
            <?php elseif ($filtre === 'attente'): ?>
                <h3 class="section-title">Demandes En Attente (<?= $enCours ?>)</h3>
            <?php elseif ($filtre === 'termine'): ?>
                <h3 class="section-title">Essais Terminés (<?= $terminees ?>)</h3>
            <?php else: ?>
                <h3 class="section-title">Toutes les demandes (<?= $totalDemandes ?>)</h3>
            <?php endif; ?>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Modèle</th>
                            <th>Téléphone</th>
                            <th>Date & Heure souhaitées</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $hasActive = false;
                        foreach($demandes as $d): 
                            $statut = strtolower(trim($d['statut'] ?? ''));
                            
                            $estEnAttente = ($statut === '' || $statut === 'en_attente' || $statut === 'en attente' || $d['statut'] === null);
                            $estAcceptee  = ($statut === 'accepte' || $statut === 'accepté');
                            $estRefusee   = ($statut === 'refuse' || $statut === 'refusé');
                            $estTerminee  = ($statut === 'termine' || $statut === 'terminé');
                            
                            // Filtres
                            if ($filtre === 'accepte' && !$estAcceptee) continue;
                            if ($filtre === 'refuse'  && !$estRefusee)  continue;
                            if ($filtre === 'attente' && !$estEnAttente) continue;
                            if ($filtre === 'termine' && !$estTerminee) continue;
                            
                            $hasActive = true;

                            // Badge
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
                            <td>
                                <p class="driver-name"><?= htmlspecialchars($d['nom_complet'] ?? 'Anonyme') ?></p>
                                <p class="driver-status">#<?= $d['id_demande'] ?></p>
                            </td>
                            <td><span class="car-model"><?= htmlspecialchars($d['modele'] ?? '—') ?></span></td>
                            <td><?= htmlspecialchars($d['telephone'] ?? '—') ?></td>
                            <td>
                                <p><?= $d['date_souhaitee'] ? date('d/m/Y', strtotime($d['date_souhaitee'])) : '—' ?></p>
                                <?php if (!empty($d['heure_souhaitee'])): ?>
                                    <p class="driver-status"><?= htmlspecialchars(substr($d['heure_souhaitee'], 0, 5)) ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($estEnAttente): ?>
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="id_demande" value="<?= $d['id_demande'] ?>">
                                        <button type="submit" name="action" value="accepte" class="btn-accept" title="Approuver">✓</button>
                                    </form>
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="id_demande" value="<?= $d['id_demande'] ?>">
                                        <button type="submit" name="action" value="refuse" class="btn-refuse" title="Refuser">✕</button>
                                    </form>
                                    <button type="button" class="btn-edit" title="Modifier la date"
                                        onclick="ouvrirModifDate(<?= $d['id_demande'] ?>, '<?= $d['date_souhaitee'] ?>', '<?= $d['heure_souhaitee'] ?? '' ?>')">✎</button>

                                <?php elseif ($estAcceptee): ?>
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="id_demande" value="<?= $d['id_demande'] ?>">
                                        <button type="submit" name="action" value="termine" class="btn-accept" title="Marquer terminé">✔</button>
                                    </form>
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="id_demande" value="<?= $d['id_demande'] ?>">
                                        <button type="submit" name="action" value="refuse" class="btn-refuse" title="Refuser">✕</button>
                                    </form>

                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(!$hasActive): ?>
                            <tr>
                                <td colspan="6" class="table-empty">Aucune demande dans cette catégorie.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ============ ONGLET INTERFACE ============ -->
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

                    <!-- Nouveaux champs : Configurateur d'essai -->
                    <div class="live-block" style="margin-top: 2rem;">
                        <p class="spec-title" style="color: var(--navy); border-color: var(--border);">Configurateur d'essai</p>
                        
                        <input type="text" name="txt_7" 
                               value="<?= htmlspecialchars($ui[7] ?? "Configurateur<br>d'essai") ?>" 
                               class="live-field font-title" 
                               placeholder="Titre configurateur (HTML autorisé)">
                        
                        <input type="text" name="txt_8" 
                               value="<?= htmlspecialchars($ui[8] ?? 'Mes demandes en cours') ?>" 
                               class="live-field font-title" 
                               placeholder="Titre mes demandes">
                        
                        <input type="text" name="txt_9" 
                               value="<?= htmlspecialchars($ui[9] ?? 'Aucune demande en cours pour le moment.') ?>" 
                               class="live-field" 
                               placeholder="Message vide">
                        
                        <input type="text" name="txt_10" 
                               value="<?= htmlspecialchars($ui[10] ?? 'Confirmer la demande') ?>" 
                               class="live-field" 
                               placeholder="Texte bouton confirmer">
                        
                        <input type="text" name="txt_11" 
                               value="<?= htmlspecialchars($ui[11] ?? 'Limite de 3 demandes atteinte') ?>" 
                               class="live-field" 
                               placeholder="Texte bouton limite">
                        
                        <input type="text" name="txt_12" 
                               value="<?= htmlspecialchars($ui[12] ?? 'AUCUN MODÈLE SÉLECTIONNÉ') ?>" 
                               class="live-field" 
                               placeholder="Placeholder champ modèle">
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

        <!-- ============ ONGLET HISTORIQUE ============ -->
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
                            $estRefusee  = ($st === 'refuse'  || $st === 'refusé');
                            $estTerminee = ($st === 'termine' || $st === 'terminé');
                            
                            if (!$estRefusee && !$estTerminee) continue;
                            $hasArchive = true;
                            
                            if ($estTerminee) {
                                $badgeClass = "badge-termine"; $badgeLabel = "Terminé";
                            } else {
                                $badgeClass = "badge-refuse";  $badgeLabel = "Refusé";
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

    <!-- MODALE : Modifier la date -->
    <div id="modal-date" class="modal-bg">
        <div class="modal-box" style="max-width: 420px;">
            <div class="modal-head">
                <h3>MODIFIER LA DATE</h3>
                <button class="modal-close" onclick="fermerModifDate()">✕</button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_demande" id="md-id">
                    <input type="hidden" name="action" value="modifier_date">
                    <div class="form-group">
                        <label>Nouvelle date</label>
                        <input type="date" name="nouvelle_date" id="md-date" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>Nouvelle heure (optionnel)</label>
                        <input type="time" name="nouvelle_heure" id="md-heure" class="form-input" min="08:00" max="17:00">
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-outline" onclick="fermerModifDate()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

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

        function ouvrirModifDate(id, date, heure) {
            document.getElementById('md-id').value = id;
            document.getElementById('md-date').value = date;
            document.getElementById('md-heure').value = heure ? heure.substring(0, 5) : '';
            document.getElementById('modal-date').classList.add('open');
        }

        function fermerModifDate() {
            document.getElementById('modal-date').classList.remove('open');
        }
    </script>
</body>
</html>