<?php
/**
 * SUPERCAR - Page de Connexion Admin (Corrigée pour MySQLi)
 */
session_start();
require 'config_admin.php'; // On suppose que ce fichier crée la variable $conn

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Récupération des données du formulaire
    // Note : on utilise 'username' car c'est le nom dans ton <input> plus bas
    $input_user = $_POST['username'] ?? '';
    $input_pass = $_POST['password'] ?? '';
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? "";

    // --- 2. VERIFICATION DU CAPTCHA ---
    $secretKey = "6Lflz6ssAAAAAH-aWHXeAE1UH9EZSEVSJ8BmeBKP";
    $verifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptchaResponse";
    
    // Utilisation de file_get_contents pour vérifier le captcha
    $response = file_get_contents($verifyUrl);
    $responseData = json_decode($response, true);

    if (!$responseData["success"]) {
        $error = "Veuillez valider le Captcha (Je ne suis pas un robot).";
    } else {
        // --- 3. VERIFICATION DES IDENTIFIANTS ---
        if (!empty($input_user) && !empty($input_pass)) {
            
            // CORRECTION : On utilise la table 'admin' et la colonne 'identifiant'
            // On utilise $conn (MySQLi) au lieu de $pdo
            $sql = "SELECT * FROM admin WHERE identifiant = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $input_user);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();

            // Vérification du mot de passe
            // Note : Comme 'didison' est en clair dans ta base, on compare directement.
            // Si tu haches tes mots de passe plus tard, utilise password_verify().
            if ($admin && $input_pass === $admin['motdepasse']) {
                
                // On stocke les infos de Madison en session
                $_SESSION['admin_id'] = $admin['id_admin'];
                $_SESSION['admin_name'] = $admin['identifiant'];
                $_SESSION['nom'] = $admin['nom'];
                $_SESSION['prenom'] = $admin['prenom'];
                
                // Redirection vers ton dashboard
                header("Location: admin_accueil.php");
                exit();
            } else {
                $error = "Identifiant ou mot de passe incorrect.";
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
    <title>Connexion Admin - Supercar</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #0a0a0a; 
            color: #fff;
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0;
        }
        .login-card { 
            background: #161616; 
            padding: 2.5rem; 
            border-radius: 12px; 
            border: 1px solid #333;
            box-shadow: 0 10px 40px rgba(0,0,0,0.7); 
            width: 350px; 
        }
        h2 { text-align: center; margin-bottom: 1.5rem; color: #e31e24; text-transform: uppercase; letter-spacing: 2px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-size: 0.8rem; color: #888; text-transform: uppercase; }
        input { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #333; 
            border-radius: 6px; 
            background: #222; 
            color: white; 
            box-sizing: border-box; 
        }
        input:focus { outline: none; border-color: #e31e24; }
        .g-recaptcha { margin: 15px 0; display: flex; justify-content: center; }
        button { 
            width: 100%; 
            padding: 14px; 
            background: #e31e24; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            font-weight: bold; 
            cursor: pointer; 
            transition: all 0.3s;
            text-transform: uppercase;
        }
        button:hover { background: #ff1e24; transform: translateY(-2px); }
        .error { 
            background: rgba(227, 30, 36, 0.1); 
            color: #ff4d4d; 
            padding: 10px; 
            border: 1px solid rgba(227, 30, 36, 0.3);
            border-radius: 4px; 
            font-size: 0.85rem; 
            text-align: center; 
            margin-bottom: 15px; 
        }
    </style>
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
            <input type="text" name="username" required placeholder="Ex: MadisonCarter">
        </div>
        
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>

        <div class="g-recaptcha" data-sitekey="6Lflz6ssAAAAAO3BaSjjgVpCScOJTXDTAO7SExxy"></div> 
        
        <button type="submit">Se connecter</button>
    </form>
</div>

</body>
</html>