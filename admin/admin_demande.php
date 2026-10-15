<?php
require_once '../config.php';
requireAdmin();

/** @var PDO $db */

$message_status = "";

// =========================================================
// 1. AUTO-STATUTS
// =========================================================
try {
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
} catch (PDOException $e) {
    error_log("[SuperCar] auto-statuts : " . $e->getMessage());
}

// =========================================================
// 2. ACTIONS POST (accepter / refuser / terminer / modifier date)
// =========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id_demande'])) {
    $id      = (int) $_POST['id_demande'];
    $action  = $_POST['action'];
    $idAdmin = (int) $_SESSION['admin_id'];

    try {
        $stmtOld = $db->prepare("SELECT date_souhaitee, heure_souhaitee, statut FROM demande_essai WHERE id_demande = ?");
        $stmtOld->execute([$id]);
        $old = $stmtOld->fetch(PDO::FETCH_ASSOC);

        $ancienneValeur = null;
        $nouvelleValeur = null;

        if ($action === 'accepte') {
            $db->prepare("UPDATE demande_essai SET statut = 'accepte' WHERE id_demande = ?")->execute([$id]);
            $ancienneValeur = $old['statut'] ?? null;
            $nouvelleValeur = 'accepte';

        } elseif ($action === 'refuse') {
            $db->prepare("UPDATE demande_essai SET statut = 'refuse' WHERE id_demande = ?")->execute([$id]);
            $ancienneValeur = $old['statut'] ?? null;
            $nouvelleValeur = 'refuse';

        } elseif ($action === 'termine') {
            $db->prepare("UPDATE demande_essai SET statut = 'termine' WHERE id_demande = ?")->execute([$id]);
            $ancienneValeur = $old['statut'] ?? null;
            $nouvelleValeur = 'termine';

        } elseif ($action === 'modifier_date' && !empty($_POST['nouvelle_date'])) {
            $nouvelle_date  = $_POST['nouvelle_date'];
            $nouvelle_heure = $_POST['nouvelle_heure'] ?? null;

            $oldDate  = $old['date_souhaitee'] ?? null;
            $oldHeure = !empty($old['heure_souhaitee']) ? substr($old['heure_souhaitee'], 0, 5) : '';

            if ($oldDate) {
                $ancienneValeur = date('d/m/Y', strtotime($oldDate)) . ($oldHeure ? ' ' . $oldHeure : '');
            } else {
                $ancienneValeur = '—';
            }

            $newHeure = !empty($nouvelle_heure) ? substr($nouvelle_heure, 0, 5) : '';
            $nouvelleValeur = date('d/m/Y', strtotime($nouvelle_date)) . ($newHeure ? ' ' . $newHeure : '');

            $db->prepare("UPDATE demande_essai SET date_souhaitee = ?, heure_souhaitee = ? WHERE id_demande = ?")
               ->execute([$nouvelle_date, $nouvelle_heure, $id]);
        }

        if ($action !== '') {
            $stmtLog = $db->prepare("
                INSERT INTO demande_essai_logs 
                    (id_demande, id_admin, action, ancienne_valeur, nouvelle_valeur, date_action)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmtLog->execute([$id, $idAdmin, $action, $ancienneValeur, $nouvelleValeur]);
        }

        header("Location: admin_demande.php");
        exit();
    } catch (PDOException $e) {
        error_log("[SuperCar] admin_demande action : " . $e->getMessage());
        $message_status = MSG_ERREUR;
    }
}

// =========================================================
// 2.5. ACTIONS AJAX (live editor)
// =========================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_live') {
    try {
        $key = (int) $_POST['key'];
        $content = $_POST['content'] ?? '';

        $stmt = $db->prepare("UPDATE essai_content SET content = ? WHERE element_id = ?");
        $stmt->execute([$content, $key]);

        echo "OK";
    } catch (Exception $e) {
        error_log("[SuperCar] save_live : " . $e->getMessage());
        echo "ERR";
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_hero') {
    try {
        if (isset($_FILES['image_hero']) && $_FILES['image_hero']['error'] === 0) {
            $dir = '../image_url/essai/';

            $oldStmt = $db->prepare("SELECT content FROM essai_content WHERE element_id = 6");
            $oldStmt->execute();
            $oldPath = $oldStmt->fetchColumn();
            $oldFile = $oldPath ? basename($oldPath) : null;

            $newFile = uploadAsWebp($_FILES['image_hero'], $dir, 'hero_essai');
            if ($newFile) {
                $stmt = $db->prepare("UPDATE essai_content SET content = ? WHERE element_id = 6");
                $stmt->execute(['image_url/essai/' . $newFile]);

                if ($oldFile && file_exists($dir . $oldFile)) @unlink($dir . $oldFile);

                echo json_encode([
                    'success' => true,
                    'path' => BASE_URL . '/image_url/essai/' . $newFile
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Conversion WebP échouée']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'no file']);
        }
    } catch (Exception $e) {
        error_log("[SuperCar] upload_hero : " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => MSG_ERREUR]);
    }
    exit;
}

// =========================================================
// 3. SAVE UI (ancien fallback)
// =========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_ui'])) {
    try {
        $stmt_text = $db->prepare("UPDATE essai_content SET content = ? WHERE element_id = ?");

        $stmt_text->execute([$_POST['txt_1']  ?? '', 1]);
        $stmt_text->execute([$_POST['txt_2']  ?? '', 2]);
        $stmt_text->execute([$_POST['txt_3']  ?? '', 3]);
        $stmt_text->execute([$_POST['txt_4']  ?? '', 4]);
        $stmt_text->execute([$_POST['txt_5']  ?? '', 5]);
        $stmt_text->execute([$_POST['txt_7']  ?? '', 7]);
        $stmt_text->execute([$_POST['txt_8']  ?? '', 8]);
        $stmt_text->execute([$_POST['txt_9']  ?? '', 9]);
        $stmt_text->execute([$_POST['txt_10'] ?? '', 10]);
        $stmt_text->execute([$_POST['txt_11'] ?? '', 11]);
        $stmt_text->execute([$_POST['txt_12'] ?? '', 12]);

        header("Location: admin_demande.php");
        exit();
    } catch (Exception $e) {
        error_log("[SuperCar] save_ui : " . $e->getMessage());
        $message_status = MSG_ERREUR;
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
    $img_path = $ui[6] ?? 'photo_essai.webp';

    $totalDemandes = $db->query("SELECT COUNT(*) FROM demande_essai")->fetchColumn();
    $enCours       = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut IS NULL OR statut = '' OR statut = 'en attente' OR statut = 'en_attente'")->fetchColumn();
    $acceptees     = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut IN ('accepte','accepté')")->fetchColumn();
    $refusees      = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut IN ('refuse','refusé')")->fetchColumn();
    $terminees     = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut IN ('termine','terminé')")->fetchColumn();

    $demandes = $db->query("
        SELECT de.id_demande, de.date_souhaitee, de.heure_souhaitee, de.statut,
               CONCAT(c.prenom, ' ', c.nom) AS nom_complet,
               c.email, c.telephone, v.modele
        FROM demande_essai de
        LEFT JOIN client c ON de.id_client = c.id_client
        LEFT JOIN voiture v ON de.id_voiture = v.id_voiture
        ORDER BY de.id_demande DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $logs = $db->query("
        SELECT 
            l.id_log,
            l.id_demande,
            l.action,
            l.ancienne_valeur,
            l.nouvelle_valeur,
            l.date_action,
            CONCAT(c.prenom, ' ', c.nom) AS client_nom,
            v.modele AS vehicule,
            CONCAT(a.prenom, ' ', a.nom) AS admin_nom,
            l.id_admin
        FROM demande_essai_logs l
        LEFT JOIN demande_essai d ON l.id_demande = d.id_demande
        LEFT JOIN client c        ON d.id_client  = c.id_client
        LEFT JOIN voiture v       ON d.id_voiture = v.id_voiture
        LEFT JOIN admin a         ON l.id_admin   = a.id_admin
        ORDER BY l.date_action DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("[SuperCar] admin_demande lecture : " . $e->getMessage());
    die(MSG_ERREUR);
}

$src_img = (strpos($img_path, 'image_url/') === 0) 
    ? BASE_URL . '/' . $img_path 
    : ASSETS_URL . '/hero/' . $img_path;

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
                <div onclick="switchTab('editor')"  id="btn-editor"  class="tab-btn">Interface</div>
                <div onclick="switchTab('radar')"   id="btn-radar"   class="tab-btn active">Demandes</div>
                <div onclick="switchTab('history')" id="btn-history" class="tab-btn">Historique</div>

                <div class="search-wrapper tabs-search">
                    <input type="text" id="globalSearch" class="search-bar"
                        placeholder="Rechercher">
                </div>
            </div>
        </header>

        <?php if (!empty($message_status)): ?>
            <div class="alert-message"><?= htmlspecialchars($message_status) ?></div>
        <?php endif; ?>

        <div class="admin-stats stat-only-radar">
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
                <table class="admin-table" id="table-demandes">
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
                            
                            if ($filtre === 'accepte' && !$estAcceptee) continue;
                            if ($filtre === 'refuse'  && !$estRefusee)  continue;
                            if ($filtre === 'attente' && !$estEnAttente) continue;
                            if ($filtre === 'termine' && !$estTerminee) continue;
                            
                            $hasActive = true;

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

        <section id="tab-editor" class="tab-content hidden">
            
            <div class="admin-live-hero">
                <div class="admin-live-hero-bg">
                    <img id="live_hero_img" src="<?= $src_img ?>" alt="Hero essai">
                    <div class="admin-live-hero-overlay"></div>
                    
                    <label for="image_hero_file" class="admin-live-img-overlay" title="Changer l'image">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="13" r="4"></circle>
                        </svg>
                        <span>Changer l'image</span>
                    </label>
                    <input type="file" id="image_hero_file" class="hidden" accept="image/*" onchange="uploadHeroImage(event)">
                </div>

                <div class="admin-live-hero-content">
                    <p class="admin-live-subtitle edit-zone" 
                       contenteditable="true" 
                       data-key="1" 
                       onblur="saveLiveField(this)"><?= htmlspecialchars($ui[1] ?? 'EXPÉRIENCE ULTIME') ?></p>

                    <h1 class="admin-live-title">
                        <span class="edit-zone" 
                              contenteditable="true" 
                              data-key="2" 
                              onblur="saveLiveField(this)"><?= htmlspecialchars($ui[2] ?? 'DEMANDE') ?></span><br>
                        <span class="edit-zone title-accent" 
                              contenteditable="true" 
                              data-key="3" 
                              onblur="saveLiveField(this)"><?= htmlspecialchars($ui[3] ?? "D'ESSAI.") ?></span>
                    </h1>

                    <p class="admin-live-desc edit-zone" 
                       contenteditable="true" 
                       data-key="4" 
                       onblur="saveLiveField(this)"><?= htmlspecialchars($ui[4] ?? '') ?></p>

                    <span class="admin-live-btn edit-zone" 
                          contenteditable="true" 
                          data-key="5" 
                          onblur="saveLiveField(this)"><?= htmlspecialchars($ui[5] ?? 'RÉSERVER MON CRÉNEAU') ?></span>
                </div>
            </div>

            <div class="admin-live-booking">

                <div class="admin-live-booking-left">
                    <h2 class="admin-live-config-title">
                        <span class="edit-zone" 
                              contenteditable="true" 
                              data-key="7" 
                              onblur="saveLiveField(this)"><?= htmlspecialchars(strip_tags($ui[7] ?? "Configurateur d'essai")) ?></span>
                    </h2>

                    <h3 class="mes-demandes-title">
                        <span class="edit-zone" 
                              contenteditable="true" 
                              data-key="8" 
                              onblur="saveLiveField(this)"><?= htmlspecialchars($ui[8] ?? 'Mes demandes') ?></span>
                    </h3>

                    <div class="demandes-tabs" style="pointer-events: none; opacity: 0.6;">
                        <button type="button" class="demande-tab active">En cours</button>
                        <button type="button" class="demande-tab">Refusés</button>
                        <button type="button" class="demande-tab">Terminés</button>
                    </div>

                    <p class="mes-demandes-empty">
                        <span class="edit-zone" 
                              contenteditable="true" 
                              data-key="9" 
                              onblur="saveLiveField(this)"><?= htmlspecialchars($ui[9] ?? 'Aucune demande en cours pour le moment.') ?></span>
                    </p>
                </div>

                <div class="admin-live-booking-right">
                    <div class="admin-live-form-preview">
                        <div class="grid grid-2" style="gap:1.5rem;">
                            <div class="form-group">
                                <label class="form-label">Nom</label>
                                <input type="text" value="EXEMPLE" readonly class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Prénom</label>
                                <input type="text" value="Client" readonly class="form-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" value="client@exemple.com" readonly class="form-input">
                        </div>

                        <div class="grid grid-2" style="gap:1.5rem;">
                            <div class="form-group">
                                <label class="form-label">Modèle</label>
                                <div class="modele-input-wrapper">
                                    <input type="text" value="" placeholder="<?= htmlspecialchars($ui[12] ?? 'AUCUN MODÈLE SÉLECTIONNÉ') ?>" readonly class="form-input input-crimson">
                                    <span class="btn-choose-modele">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                    </span>
                                </div>
                                <span class="admin-live-hint">Placeholder : 
                                    <span class="edit-zone" 
                                          contenteditable="true" 
                                          data-key="12" 
                                          onblur="saveLiveField(this)"><?= htmlspecialchars($ui[12] ?? 'AUCUN MODÈLE SÉLECTIONNÉ') ?></span>
                                </span>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Téléphone</label>
                                <input type="tel" value="0000000000" readonly class="form-input">
                            </div>
                        </div>

                        <div class="grid grid-2" style="gap:1.5rem;">
                            <div class="form-group">
                                <label class="form-label">Date préférée</label>
                                <input type="date" readonly class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Créneau (08h-17h)</label>
                                <input type="time" readonly class="form-input">
                            </div>
                        </div>

                        <span class="btn btn-primary btn-block edit-zone" 
                              contenteditable="true" 
                              data-key="10" 
                              onblur="saveLiveField(this)"
                              style="margin-top:1rem;"><?= htmlspecialchars($ui[10] ?? 'Confirmer la demande') ?></span>

                        <span class="admin-live-hint" style="margin-top:0.75rem;">Bouton limite : 
                            <span class="edit-zone" 
                                  contenteditable="true" 
                                  data-key="11" 
                                  onblur="saveLiveField(this)"><?= htmlspecialchars($ui[11] ?? 'Limite de 3 demandes atteinte') ?></span>
                        </span>
                    </div>
                </div>
            </div>

        </section>

        <section id="tab-history" class="tab-content hidden">
            <h3 class="section-title">Journal des actions admin (<?= count($logs) ?>)</h3>

            <div class="table-container">
                <table class="admin-table" id="table-historique">
                    <thead>
                        <tr>
                            <th>Admin</th>
                            <th>Client</th>
                            <th>Véhicule</th>
                            <th class="text-center">Action</th>
                            <th>Détail</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr><td colspan="6" class="table-empty">Aucune action enregistrée.</td></tr>
                        <?php else: ?>
                            <?php foreach($logs as $log): 
                                $act = strtolower($log['action']);

                                if ($act === 'accepte') {
                                    $badgeClass = 'badge-accepte'; $badgeLabel = '✓ Approuvé';
                                } elseif ($act === 'refuse') {
                                    $badgeClass = 'badge-refuse';  $badgeLabel = '✕ Refusé';
                                } elseif ($act === 'termine') {
                                    $badgeClass = 'badge-termine'; $badgeLabel = '✔ Terminé';
                                } elseif ($act === 'modifier_date') {
                                    $badgeClass = 'badge-attente'; $badgeLabel = '✎ Date modifiée';
                                } else {
                                    $badgeClass = 'badge-attente'; $badgeLabel = htmlspecialchars($log['action']);
                                }
                            ?>
                            <tr>
                                <td>
                                    <p class="driver-name"><?= htmlspecialchars($log['admin_nom'] ?? 'Admin supprimé') ?></p>
                                    <p class="driver-status">#<?= $log['id_admin'] ?? '—' ?></p>
                                </td>
                                <td>
                                    <p class="driver-name"><?= htmlspecialchars($log['client_nom'] ?? 'Client supprimé') ?></p>
                                    <p class="driver-status">Demande #<?= $log['id_demande'] ?></p>
                                </td>
                                <td><span class="car-model"><?= htmlspecialchars($log['vehicule'] ?? '—') ?></span></td>
                                <td class="text-center">
                                    <span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
                                </td>
                                <td>
                                    <?php if ($act === 'modifier_date'): ?>
                                        <p class="driver-status">
                                            <?= htmlspecialchars($log['ancienne_valeur'] ?? '—') ?>
                                            →
                                            <?= htmlspecialchars($log['nouvelle_valeur'] ?? '—') ?>
                                        </p>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <p><?= $log['date_action'] ? date('d/m/Y', strtotime($log['date_action'])) : '—' ?></p>
                                    <p class="driver-status"><?= $log['date_action'] ? date('H:i', strtotime($log['date_action'])) : '' ?></p>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <div id="modal-date" class="modal-bg">
        <div class="modal-box-admin" style="max-width: 420px;">
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

            const statsBox = document.querySelector('.stat-only-radar');
            if (statsBox) {
                if (tabId === 'radar') statsBox.classList.remove('hidden');
                else statsBox.classList.add('hidden');
            }
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

        function saveLiveField(el) {
            const key = el.dataset.key;
            const content = el.innerText.trim();

            if (!content) return;

            const fd = new FormData();
            fd.append('action', 'save_live');
            fd.append('key', key);
            fd.append('content', content);

            fetch('admin_demande.php', { method: 'POST', body: fd })
            .then(r => r.text())
            .then(data => {
                if (data.trim() === 'OK') {
                    el.classList.add('live-saved');
                    setTimeout(() => el.classList.remove('live-saved'), 800);
                }
            })
            .catch(() => {});
        }

        function uploadHeroImage(event) {
            if (!event.target.files.length) return;

            const fd = new FormData();
            fd.append('action', 'upload_hero');
            fd.append('image_hero', event.target.files[0]);

            fetch('admin_demande.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('live_hero_img').src = data.path + '?t=' + Date.now();
                } else {
                    alert('Erreur : ' + (data.message || 'upload'));
                }
            })
            .catch(() => alert('Erreur de connexion'));
        }

        document.getElementById('globalSearch').addEventListener('input', function(e) {
            const q = e.target.value.toLowerCase().trim();

            const rowsDemandes = document.querySelectorAll('#table-demandes tbody tr');
            rowsDemandes.forEach(row => {
                if (row.querySelector('.table-empty')) return;
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });

            const rowsHisto = document.querySelectorAll('#table-historique tbody tr');
            rowsHisto.forEach(row => {
                if (row.querySelector('.table-empty')) return;
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    </script>
</body>
</html>