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
                    <i class="toggle-password" id="eyeIcon" onclick="togglePassword()">👁</i>
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
            const p = document.getElementById('password');
            const e = document.getElementById('eyeIcon');
            if (p.type === 'password') { p.type = 'text'; e.textContent = '🙈'; }
            else { p.type = 'password'; e.textContent = '👁'; }
        }
    </script>

</body>
</html>