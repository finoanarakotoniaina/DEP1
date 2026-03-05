<?php
session_start();
include 'config.php'; 

$show_modal = false; // Variable pour déclencher le message

if (isset($_SESSION['status_message'])) {
    $show_modal = true; // On active le déclencheur
    $message_envoi = $_SESSION['status_message'];
    unset($_SESSION['status_message']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = mysqli_real_escape_string($conn, $_POST['nom']);
    $prenom = mysqli_real_escape_string($conn, $_POST['prenom']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO contact (nom, prenom, email, message) VALUES ('$nom', '$prenom', '$email', '$message')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['status_message'] = "Votre message a été envoyé avec succès !";
    } else {
        $_SESSION['status_message'] = "Erreur : " . mysqli_error($conn);
    }

    header("Location: contact.php");
    exit();
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
        body { padding-top: 80px; }

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
    
    <div id="success-modal">
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

    <nav class="fixed top-0 w-full z-50 px-6 lg:px-10 py-4 flex justify-between items-center backdrop-blur-md border-b border-white/5">
        <div class="font-title text-xl tracking-tighter">SUPER<span class="text-[var(--crimson)]">CAR</span></div>
        <ul class="hidden md:flex gap-8 font-title text-[10px] tracking-[0.2em] uppercase items-center">
            <li><a href="accueil.php" class="nav-link">Accueil</a></li>
            <li><a href="voiture.php" class="nav-link">Voitures</a></li>
            <li><a href="demande.php" class="nav-link">Essai</a></li>
            <li><a href="services.php" class="nav-link">Services</a></li>
            <li><a href="contact.php" class="nav-link active">Contact</a></li>
            <li><a href="connexion.php">Connexion</a></li>
        </ul>
    </nav>
    <!-- Bouton de retour spécifique pour Contact -->
<div class="relative z-[60] px-6 lg:px-24 pt-32">
    <a href="accueil.php" id="back-btn-contact" style="display: none;" class="inline-flex items-center text-[10px] font-title uppercase tracking-[0.2em] text-gray-400 hover:text-[#C1121F] transition-all group">
        <span class="mr-3 transition-transform group-hover:-translate-x-1">❮</span> Retour vers l'accueil
    </a>
</div>

    <section class="relative pt-12 pb-10 px-6 lg:px-24 overflow-hidden">
        <div class="flex flex-col md:flex-row items-center justify-between w-full py-4 px-8">
            <div class="flex-1">
                <p class="text-blue-400 tracking-[0.3em] font-bold mb-1 text-xs uppercase">Disponibilité 24/7</p>
                <h1 class="font-title text-5xl md:text-6xl font-bold leading-none mb-0">
                    RESTONS <br> 
                    <span class="text-[var(--crimson)]">CONNECTÉS.</span>
                </h1>
            </div>
            <div class="flex-1 flex justify-center md:justify-end mt-8 md:mt-0">
                <div class="relative w-56 h-56 md:w-80 md:h-80 border-4 border-white rounded-full overflow-hidden shadow-2xl">
                    <img src="mercedes.jfif" alt="Supercar" class="w-full h-full object-cover"/>
                </div>
            </div>
        </div>
    </section>

    <section class="px-6 lg:px-24 mb-10">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 border-y border-white/5 py-6">
            <div class="group flex items-baseline gap-2">
                <span class="text-[var(--crimson)] font-title text-xs tracking-widest uppercase">Email :</span>
                <p class="text-lg font-light">supercar@gmail.com</p>
            </div>
            <div class="group flex items-baseline gap-2">
                <span class="text-[var(--crimson)] font-title text-xs tracking-widest uppercase">Tel :</span>
                <p class="text-lg font-light">+230 454 8950</p>
            </div>
            <div class="group flex items-baseline gap-2">
                <span class="text-[var(--crimson)] font-title text-xs tracking-widest uppercase">Siège :</span>
                <p class="text-lg font-light">Ebène CyberCity</p>
            </div>
            <div class="group flex items-baseline gap-2">
                <span class="text-[var(--crimson)] font-title text-xs tracking-widest uppercase">Responsable :</span>
                <p class="text-lg font-light">M.Carter</p>
            </div>
        </div>
    </section>

    <section class="pb-12 px-6 lg:px-24 relative">
        <div class="absolute left-0 top-1/2 h-48 w-12 border-r border-white/5 flex items-center justify-center -translate-y-1/2">
            <span style="writing-mode: vertical-rl;" class="uppercase tracking-[1em] text-[10px] text-gray-600 font-bold rotate-180">MESSAGE</span>
        </div>

        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-10 items-stretch">
            <div class="bg-white/5 p-8 lg:p-10 rounded-[40px] border border-white/10 backdrop-blur-sm">
                <form action="contact.php" method="POST" class="space-y-6">
                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase tracking-[0.3em] text-[var(--marble)]">Nom</label>
                            <input type="text" name="nom" placeholder="VOTRE NOM" required class="bg-transparent border-b border-white/20 w-full p-2 text-xs tracking-widest focus:border-[var(--crimson)] outline-none transition-colors">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase tracking-[0.3em] text-[var(--marble)]">Prénom</label>
                            <input type="text" name="prenom" placeholder="VOTRE PRÉNOM" required class="bg-transparent border-b border-white/20 w-full p-2 text-xs tracking-widest focus:border-[var(--crimson)] outline-none transition-colors">
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] uppercase tracking-[0.3em] text-[var(--marble)]">Email</label>
                        <input type="email" name="email" placeholder="VOTRE@EMAIL.COM" required class="bg-transparent border-b border-white/20 w-full p-2 text-xs tracking-widest focus:border-[var(--crimson)] outline-none transition-colors">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] uppercase tracking-[0.3em] text-[var(--marble)]">Message</label>
                        <textarea name="message" rows="3" placeholder="COMMENT POUVONS-NOUS VOUS AIDER ?" required class="bg-transparent border-b border-white/20 w-full p-2 text-xs tracking-widest focus:border-[var(--crimson)] outline-none transition-colors"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-[var(--crimson)] py-4 font-title text-xs tracking-[0.4em] uppercase hover:bg-white hover:text-black transition-all duration-500 shadow-xl">
                        Envoyer le message
                    </button>
                </form>
            </div>

            <div class="map-container min-h-[400px] grayscale opacity-80 hover:grayscale-0 hover:opacity-100 transition-all duration-700 bg-zinc-900 rounded-[40px] overflow-hidden border border-white/10">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3744.123456789!2d57.48!3d-20.24!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjDCsDE0JzI0LjAiUyA1N8KwMjgnNDguMCJF!5e0!3m2!1sfr!2smu!4v123456789" class="w-full h-full min-h-[400px] border-none" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </section>

    <footer class="py-10 border-t border-white/5 text-center">
        <div class="font-title text-[8vw] outline-text opacity-10 leading-none mb-4 uppercase">SUPERCAR</div>
        <p class="text-white opacity-40 text-[9px] tracking-[0.5em] uppercase">© 2026 Expertise Automobile - Ebène, Maurice</p>
    </footer>

    <script>
        <?php if($show_modal): ?>
            const modal = document.getElementById('success-modal');
            modal.style.display = 'block';
            // L'onglet disparaît tout seul après 5 secondes
            setTimeout(() => {
                modal.style.opacity = '0';
                setTimeout(() => modal.style.display = 'none', 500);
            }, 5000);
        <?php endif; ?>
    </script>

</body>
</html>