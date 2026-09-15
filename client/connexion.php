<?php
require_once '../config.php'; 

if (!isset($_SESSION['captcha_reponse'])) {
    $n1 = rand(0, 3);
    $n2 = rand(0, 3);
    $_SESSION['captcha_reponse'] = $n1 + $n2;
    $_SESSION['captcha_question'] = "$n1 + $n2";
}

$message = ""; 

if (isset($_POST['connexion'])) {
    $email_saisi = htmlspecialchars($_POST['email']);
    $mdp_saisi = $_POST['password'];
    $captcha_saisi = isset($_POST['captcha']) ? intval($_POST['captcha']) : -1;
    
    if ($captcha_saisi !== $_SESSION['captcha_reponse']) {
        $message = "CAPTCHA incorrect.";
    } else {
        try {
            $sql = "SELECT l.password, c.id_client, c.prenom, c.nom, c.email, c.telephone 
                    FROM login l
                    INNER JOIN client c ON l.id_login = c.id_login 
                    WHERE l.login = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$email_saisi]);
            $user = $stmt->fetch();

            if ($user) {
                if (password_verify($mdp_saisi, $user['password'])) {
                    $_SESSION['id_client'] = $user['id_client'];
                    $_SESSION['prenom']    = $user['prenom'];
                    $_SESSION['nom']       = $user['nom'];
                    $_SESSION['email']     = $user['email'];
                    $_SESSION['telephone'] = $user['telephone'];

                    $final_redirect = 'accueil.php';
                    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'redirect=demande.php') !== false) {
                        $final_redirect = 'demande.php';
                    }

                    header("Location: " . $final_redirect);
                    exit();
                } else {
                    $message = "Mot de passe incorrect.";
                }
            } else {
                $message = "Identifiant inconnu.";
            }
        } catch (PDOException $e) {
            $message = "Erreur de base de données : " . $e->getMessage();
        }
    }
    $n1 = rand(0, 3); $n2 = rand(0, 3);
    $_SESSION['captcha_reponse'] = $n1 + $n2;
    $_SESSION['captcha_question'] = "$n1 + $n2";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SuperCar | Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
    <style>
        :root { --crimson: #C1121F; }
        body, html { height: 100vh; overflow: hidden; }
        .image-mask-gradient { mask-image: linear-gradient(to right, transparent, black 50%); -webkit-mask-image: linear-gradient(to right, transparent, black 50%); }
        .input-essai { background: transparent; border-bottom: 1px solid rgba(255, 255, 255, 0.2); padding: 0.8rem 0; font-size: 0.75rem; letter-spacing: 0.1em; color: white; border-radius: 0; transition: border-color 0.4s; }
        .input-essai:focus { border-color: var(--crimson); outline: none; }
    </style>
</head>
<body class="split-bg flex flex-col text-white">
    <nav class="fixed top-0 w-full z-50 px-6 lg:px-10 py-6 flex justify-between items-center backdrop-blur-md border-b border-white/5">
        <div class="font-title text-xl tracking-tighter">SUPER<span class="text-[var(--crimson)]">CAR</span></div>
        <ul class="hidden md:flex gap-8 font-title text-[10px] tracking-[0.2em] uppercase items-center">
            <li><a href="accueil.php" class="nav-link">Accueil</a></li>
            <li><a href="voiture.php" class="nav-link">Voitures</a></li>
            <li><a href="demande.php" class="nav-link">Essai</a></li>
            <li><a href="services.php" class="nav-link">Services</a></li>
            <li><a href="contact.php" class="nav-link">Contact</a></li> |
            <li><strong><a href="connexion.php" class="nav-link active flex items-center gap-2"><i class="fa-solid fa-user text-[var(--crimson)] text-[18px]"></i> Connexion</a></strong></li>
        </ul>
    </nav>
    <main class="flex-1 grid lg:grid-cols-2 w-full h-full overflow-hidden">
        <div class="flex flex-col justify-center px-12 md:px-24 h-full pt-12">
            <div class="z-10 max-w-lg w-full">
                <h2 class="font-title text-3xl md:text-5xl mb-2 tracking-tighter uppercase"><span class="text-[var(--crimson)]">Connexion</span></h2>
                <div class="w-20 h-1 bg-[var(--crimson)] mb-8"></div>
                <?php if($message != ""): ?>
                    <div class="mb-4 p-4 bg-white/5 border-l-2 border-[var(--crimson)] text-[10px] uppercase tracking-widest text-red-400"><?php echo $message; ?></div>
                <?php endif; ?>
                <form action="connexion.php" method="POST" class="space-y-6">
                    <div class="flex flex-col">
                        <label class="text-[10px] uppercase tracking-[0.4em] text-gray-500 font-bold mb-1">Email</label>
                        <input type="email" name="email" placeholder="VOTRE@EMAIL.COM" required class="input-essai w-full">
                    </div>
                    <div class="grid grid-cols-2 gap-8">
                        <div class="flex flex-col">
                            <label class="text-[10px] uppercase tracking-[0.4em] text-gray-500 font-bold mb-1">Mot de passe</label>
                            <input type="password" name="password" placeholder="••••••••" required class="input-essai w-full">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-[10px] uppercase tracking-[0.4em] text-[var(--crimson)] font-bold mb-1">Calcul : <?php echo $_SESSION['captcha_question']; ?></label>
                            <input type="number" name="captcha" placeholder="RÉPONSE" required class="input-essai w-full">
                        </div>
                    </div>
                    <div class="flex flex-col gap-6 pt-4">
                        <button type="submit" name="connexion" class="bg-[var(--crimson)] py-5 font-title text-[10px] tracking-[0.5em] uppercase hover:bg-white hover:text-black transition-all duration-500">Se connecter</button>
                        <a href="inscription.php" class="text-gray-400 font-bold text-[10px] uppercase tracking-[0.2em] hover:text-[var(--crimson)] text-center md:text-left">Créer un profil membre</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="hidden lg:block relative h-full">
            <img src="<?= ASSETS_URL ?>/hero/connexionimg.jpg" class="w-full h-full object-cover image-mask-gradient" alt="Ferrari">
        </div>
    </main>
</body>
</html>