<?php
/**
 * SUPERCAR - Page de Connexion Admin (Corrigée pour MySQLi)
 */
session_start();
require 'config_admin.php'; 

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $selectedBrand = $_POST['brand_verify'] ?? '';

    // --- 1. VÉRIFICATION DU LOGO ---
    if ($selectedBrand !== 'porsche') {
        $error = "Vérification échouée : vous devez sélectionner le logo Porsche.";
    } else {
        // --- 2. VÉRIFICATION DES IDENTIFIANTS ---
        if (!empty($username) && !empty($password)) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
                $stmt->execute([$username]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password'])) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['username'];
                    header("Location: DashBoard.php");
                    exit();
                } else {
                    $error = "Identifiants administrateur incorrects.";
                }
            } catch (PDOException $e) {
                $error = "Erreur de connexion à la base de données.";
            }
        } else {
            $error = "Veuillez remplir tous les champs.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration | Supercar</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600&family=Syncopate:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style_admin.css">
</head>
<body>

<div class="login-card">
    <h2>Supercar Admin</h2>
    
    <?php if(!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Utilisateur</label>
            <input type="text" name="username" required placeholder="Identifiant">
        </div>
        
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>

        <div class="brand-verification">
            <p class="verify-label">Sélectionnez le logo <strong>Porsche</strong></p>
            <div class="logo-options">
                
                <label class="logo-item">
                    <input type="radio" name="brand_verify" value="mercedes" required>
                    <img src="logomercedes.jpg" alt="Mercedes">
                </label>
                
                <label class="logo-item">
                    <input type="radio" name="brand_verify" value="porsche">
                    <img src="logoporsche.jpg" alt="Porsche">
                </label>
                
                <label class="logo-item">
                    <input type="radio" name="brand_verify" value="bmw">
                    <img src="logobmw.jpg" alt="BMW">
                </label>

            </div>
        </div>
        
        <button type="submit">Se connecter</button>
    </form>
</div>

</body>
</html>