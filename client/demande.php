<?php 
require_once '../config.php'; 

try {
    $query_ui = $db->query("SELECT element_id, content FROM essai_content");
    $ui = $query_ui->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {
    $ui = []; 
}

// Textes HERO
$txt_accroche = $ui[1] ?? "EXPÉRIENCE ULTIME";
$txt_ligne1   = $ui[2] ?? "DEMANDE";
$txt_ligne2   = $ui[3] ?? "D'ESSAI.";
$txt_desc     = $ui[4] ?? "Prenez les commandes de l'exceptionnel. Choisissez votre modèle, définissez votre créneau et laissez-vous transporter par une ingénierie hors du commun.";
$txt_btn      = $ui[5] ?? "RÉSERVER MON CRÉNEAU";
$img_hero     = $ui[6] ?? "photo_essai.jpeg";

// Textes CONFIGURATEUR
$txt_config_title   = $ui[7]  ?? "Configurateur<br>d'essai";
$txt_mes_demandes   = $ui[8]  ?? "Mes demandes";
$txt_empty_demandes = $ui[9]  ?? "Aucune demande en cours pour le moment.";
$txt_btn_valider    = $ui[10] ?? "Confirmer la demande";
$txt_btn_limite     = $ui[11] ?? "Limite de 3 demandes atteinte";
$txt_placeholder    = $ui[12] ?? "AUCUN MODÈLE SÉLECTIONNÉ";

$id_voiture_hidden  = $_GET['id_v'] ?? $_POST['id_voiture_cache'] ?? '';
$modele_selectionne = $_GET['modele'] ?? $_POST['modele_affiche'] ?? '';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_essai'])) {
    $id_client = $_SESSION['id_client'] ?? null;
    
    if ($id_client) {
        $date_essai     = $_POST['date_essai'];
        $heure_essai    = $_POST['heure_essai'];
        $id_v_a_inserer = $_POST['id_voiture_cache']; 

        // --- Vérifier la limite de 3 demandes actives ---
        try {
            $stmtLimit = $db->prepare("
                SELECT COUNT(*) 
                FROM demande_essai 
                WHERE id_client = ? 
                  AND (
                    statut IS NULL 
                    OR statut = 'en attente' 
                    OR statut = 'en_attente' 
                    OR statut = 'accepte' 
                    OR statut = 'accepté'
                  )
            ");
            $stmtLimit->execute([$id_client]);
            $nbDemandesActives = (int) $stmtLimit->fetchColumn();
        } catch (PDOException $e) {
            $nbDemandesActives = 0;
        }

        if ($nbDemandesActives >= 3) {
            $message = "LIMITE ATTEINTE : Vous avez déjà 3 demandes d'essai en cours. Veuillez attendre qu'elles soient traitées avant d'en créer une nouvelle.";
        } elseif ($heure_essai < "08:00" || $heure_essai > "17:00") {
            $message = "ERREUR : CRÉNEAU DISPONIBLE UNIQUEMENT ENTRE 08H ET 17H.";
        } else {
            try {
                $sql = "INSERT INTO demande_essai (id_client, date_souhaitee, heure_souhaitee, statut, id_voiture) VALUES (?, ?, ?, 'en attente', ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$id_client, $date_essai, $heure_essai, $id_v_a_inserer]);
                
                // Redirect pour recharger la liste avec la nouvelle demande
                header("Location: demande.php?success=1#booking");
                exit();
            } catch (Exception $e) {
                $message = "ERREUR : " . $e->getMessage();
            }
        }
    }
}

$isLoggedIn = isset($_SESSION['id_client']); 

// Calcul du nombre de demandes actives (pour la limite)
$nbDemandesActives = 0;
if ($isLoggedIn) {
    try {
        $stmtLimit = $db->prepare("
            SELECT COUNT(*) 
            FROM demande_essai 
            WHERE id_client = ? 
              AND (
                statut IS NULL 
                OR statut = 'en attente' 
                OR statut = 'en_attente' 
                OR statut = 'accepte' 
                OR statut = 'accepté'
              )
        ");
        $stmtLimit->execute([$_SESSION['id_client']]);
        $nbDemandesActives = (int) $stmtLimit->fetchColumn();
    } catch (PDOException $e) {
        $nbDemandesActives = 0;
    }
}

// Récupérer TOUTES les demandes du client connecté (pour les 3 onglets)
$mesDemandes = [];
if ($isLoggedIn) {
    try {
        $stmtMes = $db->prepare("
            SELECT de.id_demande, de.date_souhaitee, de.heure_souhaitee, de.statut,
                   v.modele, m.nom_marque
            FROM demande_essai de
            LEFT JOIN voiture v ON de.id_voiture = v.id_voiture
            LEFT JOIN marque m ON v.id_marque = m.id_marque
            WHERE de.id_client = ?
            ORDER BY de.id_demande DESC
        ");
        $stmtMes->execute([$_SESSION['id_client']]);
        $mesDemandes = $stmtMes->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $mesDemandes = [];
    }
}

// Séparer les demandes par catégorie
$demandesEnCours = [];
$demandesRefusees = [];
$demandesTerminees = [];

foreach ($mesDemandes as $index => $d) {
    $st = strtolower(trim($d['statut'] ?? ''));
    $estAcceptee = ($st === 'accepte' || $st === 'accepté');
    $estRefusee  = ($st === 'refuse' || $st === 'refusé');
    $estTerminee = ($st === 'termine' || $st === 'terminé');

    // Conserver l'index original pour détecter la nouvelle demande
    $d['_is_new'] = (isset($_GET['success']) && $index === 0);

    if ($estRefusee) {
        $demandesRefusees[] = $d;
    } elseif ($estTerminee) {
        $demandesTerminees[] = $d;
    } else {
        // En attente ou approuvé
        $demandesEnCours[] = $d;
    }
}

$src_img = (strpos($img_hero, 'image_url/') === 0) 
    ? BASE_URL . '/' . $img_hero 
    : ASSETS_URL . '/hero/' . $img_hero;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | L'Inévitable</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="bg">

    <!-- MODAL AUTH -->
    <div id="authModal" class="modal-overlay <?= $isLoggedIn ? 'hidden' : '' ?>">
        <div class="modal-box">
            <span class="modal-close" onclick="closeModal()">✕</span>
            <h2>Accès Réservé</h2>
            <p class="modal-desc">Veuillez vous identifier pour accéder au configurateur d'essai.</p>
            <div class="modal-actions">
                <a href="connexion.php?redirect=demande.php" class="btn btn-primary btn-block">Connexion</a>
                <a href="inscription.php" class="btn btn-outline btn-block">Inscription</a>
            </div>
        </div>
    </div>

    <?php include 'composants_client/navbar.php'; ?>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-content">
            <p class="hero-subtitle"><?= htmlspecialchars($txt_accroche) ?></p>
            <h1>
                <?= htmlspecialchars($txt_ligne1) ?><br>
                <span class="title-accent"><?= htmlspecialchars($txt_ligne2) ?></span>
            </h1>
            <p class="hero-desc"><?= htmlspecialchars($txt_desc) ?></p>
            <a href="#booking" class="btn btn-outline"><?= htmlspecialchars($txt_btn) ?></a>
        </div>

        <div class="hero-bg">
            <img src="<?= $src_img ?>" alt="Essai">
            <div class="hero-overlay"></div>
        </div>
    </section>

    <!-- BOOKING -->
    <section id="booking" class="section" style="border-top: 1px solid var(--border);">
        <div class="container">
            <div class="booking-grid">

                <!-- GAUCHE : titre + demandes du client avec onglets -->
                <div>
                    <h2 class="mb-2"><?= $txt_config_title ?></h2>

                    <?php if ($message): ?>
                        <div class="form-msg"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>

                    <!-- Demandes du client -->
                    <?php if ($isLoggedIn): ?>
                        <h3 class="mes-demandes-title">
                            <?= htmlspecialchars($txt_mes_demandes) ?>
                        </h3>

                        <!-- Onglets -->
                        <div class="demandes-tabs">
                            <button type="button" class="demande-tab active" onclick="switchDemandeTab('encours', this)">En cours</button>
                            <button type="button" class="demande-tab" onclick="switchDemandeTab('refuses', this)">Refusés</button>
                            <button type="button" class="demande-tab" onclick="switchDemandeTab('termines', this)">Terminés</button>
                        </div>

                        <!-- Onglet : En cours -->
                        <div class="demandes-panel active" id="panel-encours">
                            <?php if (!empty($demandesEnCours)): ?>
                                <div class="mes-demandes-list">
                                    <?php foreach ($demandesEnCours as $d): 
                                        $st = strtolower(trim($d['statut'] ?? ''));
                                        $estAcceptee = ($st === 'accepte' || $st === 'accepté');
                                        $badgeClass = $estAcceptee ? 'badge-accepte' : 'badge-attente';
                                        $badgeLabel = $estAcceptee ? 'Approuvé' : 'En attente';
                                        $isNew = !empty($d['_is_new']);
                                    ?>
                                    <div class="demande-item <?= $isNew ? 'demande-new' : '' ?>">
                                        <div>
                                            <p class="demande-model"><?= htmlspecialchars(($d['nom_marque'] ?? '') . ' ' . ($d['modele'] ?? 'Véhicule')) ?></p>
                                            <p class="demande-date">
                                                <?= $d['date_souhaitee'] ? date('d/m/Y', strtotime($d['date_souhaitee'])) : '—' ?>
                                                <?= !empty($d['heure_souhaitee']) ? ' à ' . htmlspecialchars(substr($d['heure_souhaitee'], 0, 5)) : '' ?>
                                            </p>
                                        </div>
                                        <span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="mes-demandes-empty"><?= htmlspecialchars($txt_empty_demandes) ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Onglet : Refusés -->
                        <div class="demandes-panel" id="panel-refuses">
                            <?php if (!empty($demandesRefusees)): ?>
                                <div class="mes-demandes-list">
                                    <?php foreach ($demandesRefusees as $d): ?>
                                    <div class="demande-item">
                                        <div>
                                            <p class="demande-model"><?= htmlspecialchars(($d['nom_marque'] ?? '') . ' ' . ($d['modele'] ?? 'Véhicule')) ?></p>
                                            <p class="demande-date">
                                                <?= $d['date_souhaitee'] ? date('d/m/Y', strtotime($d['date_souhaitee'])) : '—' ?>
                                                <?= !empty($d['heure_souhaitee']) ? ' à ' . htmlspecialchars(substr($d['heure_souhaitee'], 0, 5)) : '' ?>
                                            </p>
                                        </div>
                                        <span class="badge badge-refuse">Refusé</span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="mes-demandes-empty">Aucune demande refusée.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Onglet : Terminés -->
                        <div class="demandes-panel" id="panel-termines">
                            <?php if (!empty($demandesTerminees)): ?>
                                <div class="mes-demandes-list">
                                    <?php foreach ($demandesTerminees as $d): ?>
                                    <div class="demande-item">
                                        <div>
                                            <p class="demande-model"><?= htmlspecialchars(($d['nom_marque'] ?? '') . ' ' . ($d['modele'] ?? 'Véhicule')) ?></p>
                                            <p class="demande-date">
                                                <?= $d['date_souhaitee'] ? date('d/m/Y', strtotime($d['date_souhaitee'])) : '—' ?>
                                                <?= !empty($d['heure_souhaitee']) ? ' à ' . htmlspecialchars(substr($d['heure_souhaitee'], 0, 5)) : '' ?>
                                            </p>
                                        </div>
                                        <span class="badge badge-termine">Terminé</span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="mes-demandes-empty">Aucun essai terminé.</p>
                            <?php endif; ?>
                        </div>

                    <?php endif; ?>
                </div>

                <!-- DROITE : formulaire -->
                <form action="demande.php#booking" method="POST" class="form-card">
                    <input type="hidden" name="id_voiture_cache" value="<?= htmlspecialchars($id_voiture_hidden) ?>">

                    <div class="grid grid-2" style="gap:1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Nom</label>
                            <input type="text" value="<?= strtoupper($_SESSION['nom'] ?? '') ?>" readonly class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Prénom</label>
                            <input type="text" value="<?= ucfirst(strtolower($_SESSION['prenom'] ?? '')) ?>" readonly class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" value="<?= $_SESSION['email'] ?? '' ?>" readonly class="form-input">
                    </div>

                    <div class="grid grid-2" style="gap:1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Modèle</label>
                            <div class="modele-input-wrapper">
                                <input type="text" name="modele_affiche" 
                                       value="<?= htmlspecialchars($modele_selectionne) ?>" 
                                       placeholder="<?= htmlspecialchars($txt_placeholder) ?>" readonly required 
                                       class="form-input input-crimson">
                                <a href="voiture.php?from=demande" class="btn-choose-modele" title="Choisir un modèle">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="mobile" value="<?= $_SESSION['telephone'] ?? '' ?>" required class="form-input">
                        </div>
                    </div>

                    <div class="grid grid-2" style="gap:1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Date préférée</label>
                            <input type="date" name="date_essai" id="d" required class="form-input" style="border:1px solid var(--border); border-radius:8px; padding:0.75rem;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Créneau (08h-17h)</label>
                            <input type="time" name="heure_essai" id="h" min="08:00" max="17:00" required class="form-input" style="border:1px solid var(--border); border-radius:8px; padding:0.75rem;">
                        </div>
                    </div>

                    <?php if ($nbDemandesActives >= 3): ?>
                        <button type="button" class="btn btn-primary btn-block" disabled style="opacity: 0.5; cursor: not-allowed;">
                            <?= htmlspecialchars($txt_btn_limite) ?>
                        </button>
                    <?php else: ?>
                        <button type="submit" name="valider_essai" class="btn btn-primary btn-block">
                            <?= htmlspecialchars($txt_btn_valider) ?>
                        </button>
                    <?php endif; ?>
                </form>

            </div>
        </div>
    </section>

    <script>
        function closeModal() { window.location.href = 'accueil.php'; }

        // Basculer entre les onglets "Mes demandes"
        function switchDemandeTab(tabId, btn) {
            // Cacher tous les panels
            document.querySelectorAll('.demandes-panel').forEach(p => p.classList.remove('active'));
            // Retirer active de tous les boutons
            document.querySelectorAll('.demande-tab').forEach(b => b.classList.remove('active'));

            // Afficher le panel + activer le bouton
            document.getElementById('panel-' + tabId).classList.add('active');
            btn.classList.add('active');
        }

        // Bloquer la date : minimum demain
        const dateIn = document.getElementById('d');
        if (dateIn) {
            const today = new Date();
            today.setDate(today.getDate() + 1);
            dateIn.min = today.toISOString().split('T')[0];
        }

        // Bloquer les heures hors 08h-17h
        const heureIn = document.getElementById('h');
        if (heureIn) {
            heureIn.addEventListener('change', function() {
                const val = this.value;
                if (!val) return;

                if (val < "08:00") {
                    this.value = "08:00";
                } else if (val > "17:00") {
                    this.value = "17:00";
                }
            });

            heureIn.addEventListener('blur', function() {
                const val = this.value;
                if (!val) return;

                if (val < "08:00") this.value = "08:00";
                else if (val > "17:00") this.value = "17:00";
            });
        }

        // Retirer le surlignage de la nouvelle demande après 3 secondes
        setTimeout(() => {
            document.querySelectorAll('.demande-new').forEach(el => {
                el.classList.remove('demande-new');
            });
        }, 3000);
    </script>

    <?php include 'composants_client/footer.php'; ?>
</body>
</html>