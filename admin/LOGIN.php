<?php
session_start();
require 'config_admin.php'; // Vérifie que l'extension .php est bien présente sur le fichier

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Récupération de la réponse du captcha
    $recaptchaResponse = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : "";

    // --- 1. VERIFICATION DU CAPTCHA ---
    $secretKey = "6Lflz6ssAAAAAH-aWHXeAE1UH9EZSEVSJ8BmeBKP"; // <--- TA CLÉ SECRÈTE GOOGLE
    $verifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptchaResponse";
    
    $response = file_get_contents($verifyUrl);
    $responseData = json_decode($response, true);

    if (!$responseData["success"]) {
        $error = "Veuillez valider le Captcha (Je ne suis pas un robot).";
    } else {
        // --- 2. VERIFICATION DES IDENTIFIANTS ---
        if (!empty($username) && !empty($password)) {
            try {
                // On cherche l'admin dans la table 'admins'
                $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
                $stmt->execute([$username]);
                $admin = $stmt->fetch();

                // On vérifie le mot de passe (doit être haché en BDD avec password_hash)
                if ($admin && password_verify($password, $admin['password'])) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['username'];
                    
                    // Redirection (Vérifie bien que le fichier s'appelle DashBoard.php avec ces majuscules)
                    header("Location: DashBoard.php");
                    exit();
                } else {
                    $error = "Identifiants incorrects.";
                }
            } catch (PDOException $e) {
                $error = "Erreur de base de données : " . $e->getMessage();
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
            background: #1a1a1a; 
            color: #fff;
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0;
        }
        .login-card { 
            background: #2c2c2c; 
            padding: 2.5rem; 
            border-radius: 12px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.5); 
            width: 350px; 
        }
        h2 { text-align: center; margin-bottom: 1.5rem; color: #e74c3c; text-transform: uppercase; letter-spacing: 2px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-size: 0.9rem; color: #bbb; }
        input { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #444; 
            border-radius: 6px; 
            background: #3d3d3d; 
            color: white; 
            box-sizing: border-box; 
        }
        input:focus { outline: none; border-color: #e74c3c; }
        .g-recaptcha { margin: 15px 0; display: flex; justify-content: center; }
        button { 
            width: 100%; 
            padding: 12px; 
            background: #e74c3c; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            font-weight: bold; 
            cursor: pointer; 
            transition: background 0.3s;
            text-transform: uppercase;
        }
        button:hover { background: #c0392b; }
        .error { 
            background: #ff7675; 
            color: #fff; 
            padding: 10px; 
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
            <input type="text" name="username" required placeholder="Ex: admin">
        </div>
        
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>

        <div class="g-recaptcha" data-sitekey="6Lflz6ssAAAAAO3BaSjjgVpCScOJTXDTAO7SExxy"></div> <button type="submit">Se connecter</button>
    </form>
</div>

</body>
</html>