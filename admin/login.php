<?php
require_once '../config.php'; 

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
    <link rel="stylesheet" href="../style.css">
</head>

<body class="login-body">

    <div class="login-card">
        <h2 class="font-title">Supercar Admin</h2>
        
        <?php if(!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label>UTILISATEUR</label>
                <input type="text" name="username" required placeholder="Identifiant" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label>MOT DE PASSE</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                    <button type="button" class="toggle-password" onclick="togglePassword()" aria-label="Afficher le mot de passe">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="brand-verification">
                <p class="verify-label">
                    SÉLECTIONNEZ LE LOGO <strong><?php echo strtoupper($instruction); ?></strong>
                </p>
                <div class="logo-options">
                    <?php foreach($marques as $key => $name): ?>
                    <label class="logo-item">
                        <input type="radio" name="brand_verify" value="<?php echo $key; ?>" required>
                        <img src="../assets/logos/logo<?php echo $key; ?>.jpg" alt="<?php echo $name; ?>">
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <button type="submit">SE CONNECTER</button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const btn = document.querySelector('.toggle-password');
            if (!input || !btn) return;

            const iconEyeOpen = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>`;

            const iconEyeClosed = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>`;

            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = iconEyeOpen;
                btn.setAttribute('aria-label', 'Masquer le mot de passe');
            } else {
                input.type = 'password';
                btn.innerHTML = iconEyeClosed;
                btn.setAttribute('aria-label', 'Afficher le mot de passe');
            }
        }
    </script>

</body>
</html>