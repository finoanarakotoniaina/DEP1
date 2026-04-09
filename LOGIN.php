<?php
/**
 * SUPERCAR - Page de Connexion Admin avec Captcha Aléatoire
 */
require_once 'config.php'; 

// 1. Redirection si déjà connecté
if (isset($_SESSION['admin_id'])) {
    header("Location: DashBoard.php");
    exit();
}

// 2. Préparation des marques pour le captcha
$marques = [
    'porsche'  => 'Porsche',
    'mercedes' => 'Mercedes',
    'bmw'      => 'BMW'
];

$error = "";

// 3. Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username      = trim($_POST['username'] ?? '');
    $password      = $_POST['password'] ?? '';
    $selectedBrand = $_POST['brand_verify'] ?? '';
    
    // On récupère la marque qui était attendue (stockée en session au chargement précédent)
    $targetBrand = $_SESSION['captcha_target'] ?? 'porsche';

    // --- A. VÉRIFICATION DU CAPTCHA ALÉATOIRE ---
    if ($selectedBrand !== $targetBrand) {
        $error = "Vérification échouée : vous deviez sélectionner le logo " . $marques[$targetBrand] . ".";
    } 
    elseif (empty($username) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } 
    else {
        try {
            $stmt = $db->prepare("SELECT * FROM admins WHERE username = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                session_regenerate_id();
                $_SESSION['admin_id']   = $admin['id'];
                $_SESSION['admin_name'] = $admin['username'];
                
                // On nettoie la session du captcha après réussite
                unset($_SESSION['captcha_target']);

                header("Location: DashBoard.php");
                exit();
            } else {
                $error = "Identifiants administrateur incorrects.";
            }
        } catch (PDOException $e) {
            $error = "Une erreur est survenue lors de la connexion.";
        }
    }
}

// 4. GÉNÉRATION DE LA MARQUE ALÉATOIRE POUR LE PROCHAIN AFFICHAGE
// On choisit une clé au hasard (porsche, mercedes ou bmw)
$randomKey = array_rand($marques);
$_SESSION['captcha_target'] = $randomKey; // On mémorise le choix pour la vérification au prochain POST
$instruction = $marques[$randomKey];
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
        <div class="error" style="color: #ff4d4d; background: rgba(255, 77, 77, 0.1); padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; border: 1px solid #ff4d4d;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="username">Utilisateur</label>
            <input type="text" id="username" name="username" required placeholder="Identifiant" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required placeholder="••••••••">
        </div>

        <div class="brand-verification">
            <p class="verify-label">Sélectionnez le logo <strong><?php echo $instruction; ?></strong></p>
            
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