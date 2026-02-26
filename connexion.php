<?php
<<<<<<< HEAD
// 1. Liaison avec config.php (inclut session_start et la connexion $conn)
=======
// Liaison fichier configuration
>>>>>>> c3d4c5406b3b52e2332dd557b1a99ad14029eaff
include 'config.php'; 

$message = ""; 

// 2. Traitement du formulaire
if (isset($_POST['connexion'])) {
    $email_saisi = htmlspecialchars($_POST['email']);
    $mdp_saisi = $_POST['password'];

    // Requête ajustée : on ne sélectionne plus 'statut'
    $sql = "SELECT l.password, c.id_client, c.prenom, c.nom 
            FROM login l
            INNER JOIN client c ON l.id_client = c.id_client 
            WHERE l.login = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email_saisi);
    $stmt->execute();
    $resultat = $stmt->get_result();

    if ($user = $resultat->fetch_assoc()) {
        // Vérification du mot de passe haché
        if (password_verify($mdp_saisi, $user['password'])) {
            
            // Initialisation des variables de session (sans le statut)
            $_SESSION['id_client'] = $user['id_client'];
            $_SESSION['prenom']    = $user['prenom'];
            $_SESSION['nom']       = $user['nom'];

            // Redirection vers l'accueil
            header("Location: accueil.php");
            exit();
        } else {
            $message = "Mot de passe incorrect.";
        }
    } else {
        $message = "Identifiant inconnu.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="style.css">

    <style>
        .image-mask-gradient {
            mask-image: linear-gradient(to right, transparent, black 50%);
            -webkit-mask-image: linear-gradient(to right, transparent, black 50%);
        }
        .input-essai {
            background: transparent;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 0;
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            transition: border-color 0.5s ease;
            color: white;
            border-radius: 0;
        }
        .input-essai:focus {
            border-color: var(--crimson);
            outline: none;
        }
    </style>
</head>

<body class="split-bg h-screen w-full flex flex-col overflow-hidden text-white">

    <nav class="fixed top-0 w-full z-50 px-6 lg:px-10 py-6 flex justify-between items-center backdrop-blur-md border-b border-white/5">
        <div class="font-title text-xl tracking-tighter">SUPER<span class="text-[var(--crimson)]">CAR</span></div>
        
        <ul class="hidden md:flex gap-8 font-title text-[10px] tracking-[0.2em] uppercase items-center">
            <li><a href="accueil.php" class="nav-link">Accueil</a></li>
            <li><a href="voiture.php" class="nav-link">Voitures</a></li>
            <li><a href="demande.php" class="nav-link">Essai</a></li>
            <li><a href="services.php" class="nav-link">Services</a></li>
            <li><a href="contact.php" class="nav-link">Contact</a></li>
            <li><a href="connexion.php" class="nav-link active">Connexion</a></li>
        </ul>
    </nav>

    <main class="flex-1 grid lg:grid-cols-2 w-full h-full">
        <div class="flex flex-col justify-center px-12 md:px-24 lg:px-32 relative h-full">
            <div class="z-10 max-w-lg w-full">
                <h2 class="font-title text-3xl md:text-5xl mb-2 tracking-tighter uppercase">
                    <span class="text-[var(--crimson)]">Connexion</span>
                </h2>
                <div class="w-20 h-1 bg-[var(--crimson)] mb-12"></div>

                <?php if($message != ""): ?>
                    <div class="mb-6 p-4 bg-white/5 border-l-2 border-[var(--crimson)] text-[10px] uppercase tracking-widest">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form action="connexion.php" method="POST" class="space-y-8">
                    <div class="flex flex-col">
                        <label class="text-[10px] uppercase tracking-[0.4em] text-gray-500 font-bold mb-1">Identifiant (Email)</label>
                        <input type="email" name="email" placeholder="VOTRE@EMAIL.COM" required class="input-essai w-full">
                    </div>

                    <div class="flex flex-col">
                        <label class="text-[10px] uppercase tracking-[0.4em] text-gray-500 font-bold mb-1">Mot de passe</label>
                        <input type="password" name="password" placeholder="••••••••••••" required class="input-essai w-full">
                    </div>

                    <div class="flex flex-col gap-6 pt-10">
                        <button type="submit" name="connexion" class="bg-[var(--crimson)] py-5 font-title text-[10px] tracking-[0.5em] uppercase hover:bg-white hover:text-black transition-all duration-500">
                            Se connecter
                        </button>
                        <a href="inscription.php" class="text-gray-400 font-bold text-[10px] uppercase tracking-[0.2em] hover:text-[var(--crimson)] text-center md:text-left">
                            Créer un profil membre
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="hidden lg:block relative h-full">
            <img src="https://images.unsplash.com/photo-1583121274602-3e2820c69888?q=80&w=2070"
                 class="w-full h-full object-cover image-mask-gradient" alt="Ferrari">
        </div>
    </main>

</body>
</html>