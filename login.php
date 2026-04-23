<?php
/**
 * SUPERCAR - Page de Connexion Admin
 */
require_once 'config.php'; 

if (isset($_SESSION['admin_id'])) {
    header("Location: tableaudebord.php");
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
        $error = "Vérification échouée : sélectionnez le logo " . $marques[$targetBrand] . ".";
    } elseif (empty($username) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        try {
            $stmt = $db->prepare("SELECT * FROM admin WHERE identifiant = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch();

            if ($admin && $password === $admin['motdepasse']) {
                session_regenerate_id();
                $_SESSION['admin_id']   = $admin['id_admin'];
                $_SESSION['admin_name'] = $admin['identifiant'];
                unset($_SESSION['captcha_target']);
                header("Location: tableaudebord.php");
                exit();
            } else {
                $error = "Identifiants incorrects.";
            }
        } catch (PDOException $e) {
            $error = "Erreur système.";
        }
    }
}

if (!isset($_SESSION['captcha_target']) || $error !== "") {
    $_SESSION['captcha_target'] = array_rand($marques);
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
    <link rel="stylesheet" href="style.css?v=3.0">
</head>

<body class="login-body">

    <div class="login-card">
        <h2 class="font-title">Supercar Admin</h2>
        
        <?php if(!empty($error)): ?>
            <div class="error" style="color: #ff4d4d; margin-bottom: 15px; font-size: 0.8rem;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label>UTILISATEUR</label>
                <input type="text" name="username" required placeholder="Identifiant" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label>MOT DE PASSE</label>
                <div class="password-container" style="position: relative;">
                    <input type="password" id="password" name="password" required placeholder="••••••••" style="width: 100%;">
                    <i class="fa-solid fa-eye toggle-password" id="eyeIcon" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: rgba(255,255,255,0.5);"></i>
                </div>
            </div>

            <div class="brand-verification">
                <p class="verify-label" style="color: white; font-size: 0.75rem; margin-bottom: 15px; letter-spacing: 1px;">
                    SÉLECTIONNEZ LE LOGO <strong><?php echo strtoupper($instruction); ?></strong>
                </p>
                <div class="logo-options">
                    <?php foreach($marques as $key => $name): ?>
                    <label class="logo-item">
                        <input type="radio" name="brand_verify" value="<?php echo $key; ?>" required>
                        <img src="logo<?php echo $key; ?>.jpg" alt="<?php echo $name; ?>">
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <button type="submit">SE CONNECTER</button>
        </form>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        eyeIcon.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>

</body>
</html>