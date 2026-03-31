<?php
// 1. CONFIGURATION DE LA CONNEXION
$host = 'localhost';
$dbname = 'supercar';
$username = 'root'; // Utilisateur par défaut sur XAMPP/WAMP
$password = '';     // Mot de passe vide par défaut sur XAMPP/WAMP

$message_envoi = "";
$show_modal = false;

try {
    // Connexion à MySQL avec PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Configuration pour afficher les erreurs SQL si elles surviennent
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. TRAITEMENT DU FORMULAIRE (Si la méthode est POST)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Récupération sécurisée des données du formulaire
        $nom = htmlspecialchars($_POST['nom']);
        $prenom = htmlspecialchars($_POST['prenom']);
        $email = htmlspecialchars($_POST['email']);
        $message = htmlspecialchars($_POST['message']);

        // Vérification que les champs ne sont pas vides
        if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($message)) {
            
            // Préparation de la requête SQL (Protection contre les injections SQL)
            $sql = "INSERT INTO contact (nom, prenom, email, message) VALUES (:nom, :prenom, :email, :message)";
            $stmt = $pdo->prepare($sql);
            
            // Exécution avec les données nettoyées
            $stmt->execute([
                ':nom'    => $nom,
                ':prenom' => $prenom,
                ':email'  => $email,
                ':message' => $message
            ]);

            // Paramètres pour afficher la notification de succès
            $message_envoi = "Votre message a bien été enregistré dans la base de données !";
            $show_modal = true;
        }
    }
} catch (PDOException $e) {
    // En cas d'erreur de connexion, on l'affiche
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact | SuperCar Excellence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --crimson: #dc143c;
            --marble: #f5f5f5;
        }
        .outline-text {
            -webkit-text-stroke: 1px rgba(255,255,255,0.2);
            color: transparent;
        }

        /* Style de l'onglet de notification */
        #success-modal {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body class="split-bg min-h-screen bg-black text-white">
    
    <?php if ($show_modal): ?>
    <div id="success-modal" style="display: block;">
        <div class="bg-black/80 border-l-4 border-[var(--crimson)] backdrop-blur-md p-6 shadow-2xl rounded-r-lg flex items-center gap-4 border border-white/10">
            <div class="bg-[var(--crimson)] rounded-full p-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <h4 class="font-title text-[10px] tracking-widest uppercase text-[var(--crimson)]">Confirmation</h4>
                <p class="text-xs tracking-wider font-light"><?php echo $message_envoi; ?></p>
            </div>
            <button onclick="document.getElementById('success-modal').style.display='none'" class="text-white/40 hover:text-white ml-4">✕</button>
        </div>
    </div>
    <?php endif; ?>

   <nav class="fixed top-0 w-full z-50 px-6 lg:px-10 py-6 flex justify-between items-center backdrop-blur-md border-b border-white/5">
        <div class="font-title text-xl tracking-tighter">SUPER<span class="text-[var(--crimson)]">CAR</span></div>
        
        <ul class="hidden md:flex gap-8 font-title text-[10px] tracking-[0.2em] uppercase items-center">
            <li><a href="accueil.php" class="nav-link">Accueil</a></li>
            <li><a href="voiture.php" class="nav-link">Voitures</a></li>
            <li><a href="demande.php" class="nav-link">Essai</a></li>
            <li><a href="services.php" class="nav-link">Services</a></li>
            <li><a href="contact.php" class="nav-link active">Contact</a></li>

            <?php if (isset($_SESSION['id_client'])): ?>
                <li>
                    <a href="deconnexion.php" class="bg-[var(--crimson)] px-4 py-2 rounded text-white hover:bg-white hover:text-black transition-all duration-300">
                        Déconnexion
                    </a>
                </li>
            <?php else: ?>
                <li><a href="connexion.php" class="nav-link">Connexion</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="relative z-[60] px-6 lg:px-24 pt-10">
        <a href="accueil.php" id="back-btn-contact" style="display: none;" class="inline-flex items-center text-[10px] font-title uppercase tracking-[0.2em] text-gray-400 hover:text-[var(--crimson)] transition-all group">
            <span class="mr-3 transition-transform group-hover:-translate-x-1"></span> Retour vers l'accueil
        </a>
    </div>

    <section class="relative pt-4 pb-10 px-6 lg:px-24 overflow-hidden">
        <div class="flex flex-col md:flex-row items-center justify-between w-full py-4 px-8">
            <div class="flex-1">
                <p class="text-blue-400 tracking-[0.3em] font-bold mb-2 text-[10px] uppercase">Disponibilité 24/7</p>
                <h1 class="font-title text-5xl md:text-7xl font-bold leading-none mb-0 uppercase">
                    RESTONS <br> 
                    <span class="text-[var(--crimson)]">CONNECTÉS.</span>
                </h1>
            </div>
            <div class="flex-1 flex justify-center md:justify-end mt-8 md:mt-0">
                <div class="relative w-64 h-64 md:w-96 md:h-96 border-4 border-white/10 rounded-full overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)]">
                    <img src="mercedes.jfif" alt="Supercar" class="w-full h-full object-cover"/>
                </div>
            </div>
        </div>
    </section>

    <section class="px-6 lg:px-24 mb-10">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 border-y border-white/5 py-8">
            <div class="group">
                <span class="text-[var(--crimson)] font-title text-[10px] tracking-widest uppercase block mb-1">Email</span>
                <p class="text-base font-light">supercar@gmail.com</p>
            </div>
            <div class="group">
                <span class="text-[var(--crimson)] font-title text-[10px] tracking-widest uppercase block mb-1">Tel</span>
                <p class="text-base font-light">+230 454 8950</p>
            </div>
            <div class="group">
                <span class="text-[var(--crimson)] font-title text-[10px] tracking-widest uppercase block mb-1">Siège</span>
                <p class="text-base font-light">Ebène CyberCity</p>
            </div>
            <div class="group">
                <span class="text-[var(--crimson)] font-title text-[10px] tracking-widest uppercase block mb-1">Responsable</span>
                <p class="text-base font-light">M. Carter</p>
            </div>
        </div>
    </section>

    <section class="pb-24 px-6 lg:px-24 relative">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-stretch">
            <div class="bg-white/5 p-8 lg:p-12 rounded-[40px] border border-white/10 backdrop-blur-sm">
                <form action="contact.php" method="POST" class="space-y-8">
                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase tracking-[0.3em] text-gray-500">Nom</label>
                            <input type="text" name="nom" placeholder="VOTRE NOM" required class="bg-transparent border-b border-white/20 w-full py-2 text-xs tracking-widest focus:border-[var(--crimson)] outline-none transition-colors">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase tracking-[0.3em] text-gray-500">Prénom</label>
                            <input type="text" name="prenom" placeholder="VOTRE PRÉNOM" required class="bg-transparent border-b border-white/20 w-full py-2 text-xs tracking-widest focus:border-[var(--crimson)] outline-none transition-colors">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-[0.3em] text-gray-500">Email</label>
                        <input type="email" name="email" placeholder="VOTRE@EMAIL.COM" required class="bg-transparent border-b border-white/20 w-full py-2 text-xs tracking-widest focus:border-[var(--crimson)] outline-none transition-colors">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-[0.3em] text-gray-500">Message</label>
                        <textarea name="message" rows="4" placeholder="COMMENT POUVONS-NOUS VOUS AIDER ?" required class="bg-transparent border-b border-white/20 w-full py-2 text-xs tracking-widest focus:border-[var(--crimson)] outline-none transition-colors"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-[var(--crimson)] py-5 font-title text-[10px] tracking-[0.4em] uppercase hover:bg-white hover:text-black transition-all duration-500 shadow-xl">
                        Envoyer le message
                    </button>
                </form>
            </div>

            <div class="min-h-[450px] grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-1000 bg-zinc-900 rounded-[40px] overflow-hidden border border-white/10">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14975.000000000002!2d57.485!3d-20.245!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x217c5af569500001%3A0x6000000000000000!2sEbene!5e0!3m2!1sfr!2smu!4v1620000000000!5m2!1sfr!2smu" class="w-full h-full border-none" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('from') === 'accueil') {
            const btn = document.getElementById('back-btn-contact');
            if (btn) btn.style.display = 'flex';
        }
    });
    </script>
</body>
</html>