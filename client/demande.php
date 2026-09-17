<?php 
require_once '../config.php'; 

try {
    $query_ui = $db->query("SELECT element_id, content FROM essai_content");
    $ui = $query_ui->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {
    $ui = []; 
}

$txt_accroche = $ui[1] ?? "EXPÉRIENCE ULTIME";
$txt_ligne1   = $ui[2] ?? "DEMANDE";
$txt_ligne2   = $ui[3] ?? "D'ESSAI.";
$txt_desc     = $ui[4] ?? "Prenez les commandes de l'exceptionnel. Choisissez votre modèle, définissez votre créneau et laissez-vous transporter par une ingénierie hors du commun.";
$txt_btn      = $ui[5] ?? "RÉSERVER MON CRÉNEAU";
$img_hero     = $ui[6] ?? "photo_essai.jpeg";

$id_voiture_hidden  = $_GET['id_v'] ?? $_POST['id_voiture_cache'] ?? '';
$modele_selectionne = $_GET['modele'] ?? $_POST['modele_affiche'] ?? '';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_essai'])) {
    $id_client = $_SESSION['id_client'] ?? null;
    
    if ($id_client) {
        $date_essai     = $_POST['date_essai'];
        $heure_essai    = $_POST['heure_essai'];
        $id_v_a_inserer = $_POST['id_voiture_cache']; 

        if ($heure_essai < "08:00" || $heure_essai > "17:00") {
            $message = "ERREUR : CRÉNEAU DISPONIBLE UNIQUEMENT ENTRE 08H ET 17H.";
        } else {
            try {
                $sql = "INSERT INTO demande_essai (id_client, date_souhaitee, statut, id_voiture) VALUES (?, ?, 'EN ATTENTE', ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$id_client, $date_essai, $id_v_a_inserer]);
                
                $message = "VOTRE DEMANDE A ÉTÉ ENREGISTRÉE AVEC SUCCÈS.";
                $modele_selectionne = ""; 
                $id_voiture_hidden = "";
            } catch (Exception $e) {
                $message = "ERREUR : " . $e->getMessage();
            }
        }
    }
}

$isLoggedIn = isset($_SESSION['id_client']); 

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
            <p class="text-muted mt-1 mb-2">Veuillez vous identifier pour accéder au configurateur d'essai.</p>
            <div class="flex flex-col gap-2 mt-2">
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
    <section id="booking" class="section" style="border-top: 1px solid rgba(255,255,255,0.05);">
        <div class="container">
            <div class="booking-grid">

                <!-- GAUCHE : titre + message -->
                <div>
                    <h2 class="mb-2">Configurateur<br>d'essai</h2>

                    <?php if ($message): ?>
                        <div class="form-msg"><?= $message ?></div>
                    <?php endif; ?>

                    <div class="booking-number">
                        <span>01</span>
                        <div>
                            <h3>Sélection</h3>
                            <p>Choisissez votre monture et votre date.</p>
                        </div>
                    </div>
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
                            <input type="text" name="modele_affiche" 
                                   value="<?= htmlspecialchars($modele_selectionne) ?>" 
                                   placeholder="VOTRE MODÈLE" readonly required 
                                   class="form-input input-crimson">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="mobile" value="<?= $_SESSION['telephone'] ?? '' ?>" required class="form-input">
                        </div>
                    </div>

                    <div class="grid grid-2" style="gap:1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Date préférée</label>
                            <input type="date" name="date_essai" id="d" required class="form-input" style="border:1px solid rgba(255,255,255,0.1); border-radius:8px; padding:0.75rem;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Créneau (08h-17h)</label>
                            <input type="time" name="heure_essai" min="08:00" max="17:00" required class="form-input" style="border:1px solid rgba(255,255,255,0.1); border-radius:8px; padding:0.75rem;">
                        </div>
                    </div>

                    <button type="submit" name="valider_essai" class="btn btn-primary btn-block">
                        Confirmer la demande
                    </button>
                </form>

            </div>
        </div>
    </section>

    <script>
        function closeModal() { window.location.href = 'accueil.php'; }

        const dateIn = document.getElementById('d');
        if (dateIn) {
            const today = new Date();
            today.setDate(today.getDate() + 1);
            dateIn.min = today.toISOString().split('T')[0];
        }
    </script>

    <?php include 'composants_client/footer.php'; ?>