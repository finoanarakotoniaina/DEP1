<?php
/**
 * SUPERCAR - Page de Connexion Admin Adaptée à ta BDD
 */
require_once 'config.php'; 

if (isset($_SESSION['admin_id'])) {
    header("Location: DashBoard.php");
    exit();
}

$marques = [
    'porsche'  => 'Porsche',
    'mercedes' => 'Mercedes',
    'bmw'      => 'BMW'
];

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username      = trim($_POST['username'] ?? '');
    $password      = $_POST['password'] ?? '';
    $selectedBrand = $_POST['brand_verify'] ?? '';
    $targetBrand   = $_SESSION['captcha_target'] ?? 'porsche';

    if ($selectedBrand !== $targetBrand) {
        $error = "Vérification échouée : vous deviez sélectionner le logo " . $marques[$targetBrand] . ".";
    } elseif (empty($username) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        try {
            // Changement ici : On utilise 'identifiant' car c'est le nom dans ta BDD
            $stmt = $db->prepare("SELECT * FROM admin WHERE identifiant = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch();

            if ($admin) {
                // IMPORTANT : Puisque ton mot de passe est en clair "didison" dans la BDD,
                // on compare directement les textes. 
                // Note : C'est moins sécurisé, mais ça marchera avec ta photo !
                if ($password === $admin['motdepasse']) {
                    session_regenerate_id();
                    $_SESSION['admin_id']   = $admin['id_admin']; // Nom de ta colonne ID
                    $_SESSION['admin_name'] = $admin['identifiant'];
                    unset($_SESSION['captcha_target']);
                    header("Location: DashBoard.php");
                    exit();
                } else {
                    $error = "Mot de passe incorrect.";
                }
            } else {
                $error = "Utilisateur inconnu.";
            }
        } catch (PDOException $e) {
            $error = "Erreur de connexion : " . $e->getMessage();
        }
    }
}

// Logique du captcha (on garde la même)
if (!isset($_SESSION['captcha_target']) || $error !== "") {
    $randomKey = array_rand($marques);
    $_SESSION['captcha_target'] = $randomKey;
}
$instruction = $marques[$_SESSION['captcha_target']];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration | Supercar</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600&family=Syncopate:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }
        .password-container input {
            width: 100%;
            padding-right: 40px;
        }
        .toggle-password {
            position: absolute;
            right: 12px;
            cursor: pointer;
            color: #000000; 
            font-size: 1.1em;
            transition: none; 
        }
        .toggle-password:hover {
            color: #000000;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Supercar Admin</h2>
    
    <?php if(!empty($error)): ?>
        <div class="error" style="color: #ff4d4d; border: 1px solid #ff4d4d; padding: 10px; margin-bottom: 20px; text-align: center; border-radius: 5px;">
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
            <div class="password-container">
                <input type="password" id="password" name="password" required placeholder="••••••••">
                <i class="fa-solid fa-eye toggle-password" id="eyeIcon"></i>
            </div>
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

<script>
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    eyeIcon.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Alterne entre l'oeil ouvert et l'oeil barré
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
</script>

</body>
</html>