<?php
require_once '../config.php'; 

$message = ""; 

if (isset($_POST['inscription'])) {
    $prenom = htmlspecialchars($_POST['prenom']);
    $nom    = htmlspecialchars($_POST['nom']);
    $email  = htmlspecialchars($_POST['email']);
    $tel    = htmlspecialchars($_POST['telephone']); 
    $mdp    = $_POST['password'];
    $mdp_confirm = $_POST['confirm_password'];

    if ($mdp !== $mdp_confirm) {
        $message = "Les mots de passe ne correspondent pas.";
    } else {
        $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);
        try {
            $db->beginTransaction();
            $sql1 = "INSERT INTO login (login, password) VALUES (?, ?)";
            $stmt1 = $db->prepare($sql1);
            $stmt1->execute([$email, $mdp_hash]);
            $id_login_cree = $db->lastInsertId();

            $sql2 = "INSERT INTO client (nom, prenom, email, telephone, id_login) VALUES (?, ?, ?, ?, ?)";
            $stmt2 = $db->prepare($sql2);
            $stmt2->execute([$nom, $prenom, $email, $tel, $id_login_cree]);

            $db->commit();
            $message = "Compte créé avec succès !";
        } catch (Exception $e) {
            $db->rollBack();
            $message = ($e->getCode() == 23000) ? "Erreur : cet identifiant existe déjà." : "Erreur système : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Inscription</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="bg">

    <?php include 'composants_client/navbar.php'; ?>

    <main class="auth-page">
        <!-- Colonne gauche : formulaire -->
        <div class="auth-form-col">
            <div class="auth-content">
                <h2>INSCRIPTION</h2>
                <div class="title-bar"></div>

                <?php if ($message != ""): ?>
                    <div class="form-msg"><?= $message ?></div>
                <?php endif; ?>

                <form action="inscription.php" method="POST">
                    <div class="grid grid-2" style="gap:1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" placeholder="JEAN" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" placeholder="DUPONT" required class="form-input">
                        </div>
                    </div>

                    <div class="grid grid-2" style="gap:1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" placeholder="VOTRE@EMAIL.COM" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="telephone" placeholder="5000 0000" required class="form-input">
                        </div>
                    </div>

                    <div class="grid grid-2" style="gap:1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" placeholder="••••••••" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirmation</label>
                            <input type="password" name="confirm_password" placeholder="••••••••" required class="form-input">
                        </div>
                    </div>

                    <div class="auth-actions">
                        <button type="submit" name="inscription" class="btn btn-primary btn-block">
                            Créer un profil membre
                        </button>
                        <a href="connexion.php" class="auth-alt-link">
                            Déjà membre ? Se connecter
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
