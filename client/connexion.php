<?php
require_once '../config.php'; 

// Génération captcha
if (!isset($_SESSION['captcha_reponse'])) {
    $n1 = rand(0, 3);
    $n2 = rand(0, 3);
    $_SESSION['captcha_reponse'] = $n1 + $n2;
    $_SESSION['captcha_question'] = "$n1 + $n2";
}

$message = ""; 

if (isset($_POST['connexion'])) {
    $email_saisi    = htmlspecialchars($_POST['email']);
    $mdp_saisi      = $_POST['password'];
    $captcha_saisi  = isset($_POST['captcha']) ? intval($_POST['captcha']) : -1;
    
    if ($captcha_saisi !== $_SESSION['captcha_reponse']) {
        $message = "CAPTCHA incorrect.";
    } else {
        try {
            $sql = "SELECT l.password, c.id_client, c.prenom, c.nom, c.email, c.telephone 
                    FROM login l
                    INNER JOIN client c ON l.id_login = c.id_login 
                    WHERE l.login = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$email_saisi]);
            $user = $stmt->fetch();

            if ($user) {
                if (password_verify($mdp_saisi, $user['password'])) {
                    $_SESSION['id_client'] = $user['id_client'];
                    $_SESSION['prenom']    = $user['prenom'];
                    $_SESSION['nom']       = $user['nom'];
                    $_SESSION['email']     = $user['email'];
                    $_SESSION['telephone'] = $user['telephone'];

                    $final_redirect = 'accueil.php';
                    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'redirect=demande.php') !== false) {
                        $final_redirect = 'demande.php';
                    }

                    header("Location: " . $final_redirect);
                    exit();
                } else {
                    $message = "Mot de passe incorrect.";
                }
            } else {
                $message = "Identifiant inconnu.";
            }
        } catch (PDOException $e) {
            $message = "Erreur de base de données : " . $e->getMessage();
        }
    }
    // Régénérer le captcha
    $n1 = rand(0, 3); $n2 = rand(0, 3);
    $_SESSION['captcha_reponse'] = $n1 + $n2;
    $_SESSION['captcha_question'] = "$n1 + $n2";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Connexion</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="bg">

    <?php include 'composants_client/navbar.php'; ?>

    <main class="auth-page">
        <!-- Colonne gauche : formulaire -->
        <div class="auth-form-col">
            <div class="auth-content">
                <h2>CONNEXION</h2>
                <div class="title-bar"></div>

                <?php if ($message != ""): ?>
                    <div class="form-msg"><?= $message ?></div>
                <?php endif; ?>

                <form action="connexion.php" method="POST">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" placeholder="VOTRE@EMAIL.COM" required class="form-input">
                    </div>

                    <div class="grid grid-2" style="gap:2rem;">
                        <div class="form-group">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" placeholder="••••••••" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="color:var(--crimson);">
                                Calcul : <?= $_SESSION['captcha_question'] ?>
                            </label>
                            <div class="number-input-wrapper">
                                <input type="number" name="captcha" id="captcha" placeholder="RÉPONSE" required class="form-input">
                                <div class="number-arrows">
                                    <button type="button" class="arrow-up" onclick="document.getElementById('captcha').stepUp()">▲</button>
                                    <button type="button" class="arrow-down" onclick="document.getElementById('captcha').stepDown()">▼</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="auth-actions">
                        <button type="submit" name="connexion" class="btn btn-primary btn-block">
                            Se connecter
                        </button>
                        <a href="inscription.php" class="auth-alt-link">
                            Créer un profil membre
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Colonne droite : image -->
        <div class="auth-image-col">
            <img src="<?= ASSETS_URL ?>/hero/connexionimg.jpg" alt="Ferrari">
        </div>
    </main>
</body>
</html>