<?php
// 1. Liaison avec la configuration (Session + Connexion $conn)
include 'config.php'; 

$message = ""; 

// 2. Traitement du formulaire
if (isset($_POST['inscription'])) {
    $prenom = htmlspecialchars($_POST['prenom']);
    $nom    = htmlspecialchars($_POST['nom']);
    $email  = htmlspecialchars($_POST['email']);
    $tel    = htmlspecialchars($_POST['telephone']); 
    $mdp    = $_POST['password'];
    $mdp_confirm = $_POST['confirm_password'];

    if ($mdp !== $mdp_confirm) {
        $message = "Les mots de passe ne correspondent pas.";
    } else {
        $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);

        // ÉTAPE 1 : Insertion dans la table 'client'
        $sql1 = "INSERT INTO client (nom, prenom, email, telephone) VALUES (?, ?, ?, ?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("ssss", $nom, $prenom, $email, $tel);

        if ($stmt1->execute()) {
            $id_client_cree = $conn->insert_id;

            // ÉTAPE 2 : Insertion dans la table 'login' (Lien via id_client)
            // Suppression de 'statut' dans la requête SQL et bind_param
            $sql2 = "INSERT INTO login (login, password, id_client) VALUES (?, ?, ?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("ssi", $email, $mdp_hash, $id_client_cree);

            if ($stmt2->execute()) {
                $message = "Compte créé ! Bienvenue chez SuperCar.";
            } else {
                $message = "Erreur lors de la création des identifiants.";
            }
            $stmt2->close();
        } else {
            $message = "Erreur : cet email est déjà utilisé.";
        }
        $stmt1->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | Inscription</title>
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
            padding: 0.8rem 0;
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
        
        <div class="flex flex-col justify-center px-12 md:px-24 lg:px-32 relative h-full pt-16">
            <div class="z-10 max-w-lg w-full">
                <h2 class="font-title text-3xl md:text-5xl mb-2 tracking-tighter uppercase">
                    <span class="text-[var(--crimson)]">Inscription</span>
                </h2>
                <div class="w-20 h-1 bg-[var(--crimson)] mb-8"></div>

                <?php if($message != ""): ?>
                    <div class="mb-6 p-4 bg-white/5 border-l-2 border-[var(--crimson)] text-[10px] uppercase tracking-widest">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form action="inscription.php" method="POST" class="space-y-4">
                    <div class="grid grid-cols-2 gap-8">
                        <div class="flex flex-col">
                            <label class="text-[10px] uppercase tracking-[0.4em] text-gray-500 font-bold mb-1">Prénom</label>
                            <input type="text" name="prenom" placeholder="JEAN" required class="input-essai w-full">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-[10px] uppercase tracking-[0.4em] text-gray-500 font-bold mb-1">Nom</label>
                            <input type="text" name="nom" placeholder="DUPONT" required class="input-essai w-full">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-8">
                        <div class="flex flex-col">
                            <label class="text-[10px] uppercase tracking-[0.4em] text-gray-500 font-bold mb-1">Email</label>
                            <input type="email" name="email" placeholder="VOTRE@EMAIL.COM" required class="input-essai w-full">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-[10px] uppercase tracking-[0.4em] text-gray-500 font-bold mb-1">Téléphone</label>
                            <input type="tel" name="telephone" placeholder="5000 0000" required class="input-essai w-full">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-8">
                        <div class="flex flex-col">
                            <label class="text-[10px] uppercase tracking-[0.4em] text-gray-500 font-bold mb-1">Mot de passe</label>
                            <input type="password" name="password" placeholder="••••••••" required class="input-essai w-full">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-[10px] uppercase tracking-[0.4em] text-gray-500 font-bold mb-1">Confirmation</label>
                            <input type="password" name="confirm_password" placeholder="••••••••" required class="input-essai w-full">
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 pt-6">
                        <button type="submit" name="inscription" class="bg-[var(--crimson)] py-5 font-title text-[10px] tracking-[0.5em] uppercase hover:bg-white hover:text-black transition-all duration-500">
                            Créer un profil membre
                        </button>
                        <a href="connexion.php" class="text-gray-400 font-bold text-[10px] uppercase tracking-[0.2em] hover:text-[var(--crimson)] transition-colors text-center md:text-left">
                            Déjà membre ? Se connecter
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