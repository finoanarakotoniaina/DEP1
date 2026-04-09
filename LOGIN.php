<?php
/**
 * SUPERCAR - Page de Connexion Admin 
 */

// 1. On inclut le fichier de configuration (qui gère session_start et la connexion $db)
require_once 'config.php'; 

// 2. Si l'admin est déjà connecté, on le redirige directement vers le Dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: DashBoard.php");
    exit();
}

$error = "";

// 3. Traitement du formulaire lors de la soumission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username      = trim($_POST['username'] ?? '');
    $password      = $_POST['password'] ?? '';
    $selectedBrand = $_POST['brand_verify'] ?? '';

    // --- A. VÉRIFICATION DU LOGO (Sécurité anti-robot simple) ---
    if ($selectedBrand !== 'porsche') {
        $error = "Vérification échouée : vous devez sélectionner le logo Porsche.";
    } 
    // --- B. VÉRIFICATION DES CHAMPS VIDES ---
    elseif (empty($username) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } 
    else {
        try {
            // --- C. RECHERCHE DE L'ADMIN DANS LA BASE ---
            // On utilise $db car c'est le nom défini dans ton config.php
            $stmt = $db->prepare("SELECT * FROM admins WHERE username = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch();

            // --- D. VÉRIFICATION DU MOT DE PASSE ---
            // password_verify compare le texte saisi avec le hash stocké en BDD
            if ($admin && password_verify($password, $admin['password'])) {
                
                // Régénérer l'ID de session pour la sécurité
                session_regenerate_id();
                
                $_SESSION['admin_id']   = $admin['id'];
                $_SESSION['admin_name'] = $admin['username'];

                header("Location: DashBoard.php");
                exit();
            } else {
                $error = "Identifiants administrateur incorrects.";
            }
        } catch (PDOException $e) {
            // En cas d'erreur SQL
            $error = "Une erreur est survenue lors de la connexion.";
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
        <div class="error" style="color: #ff4d4d; background: rgba(255, 77, 77, 0.1); padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-size: 0.9em; border: 1px solid #ff4d4d;">
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