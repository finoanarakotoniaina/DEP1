<?php
// 1. CONFIGURATION ET CONNEXION À LA BASE DE DONNÉES
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "supercar";

$conn = new mysqli($host, $user, $pass, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// 2. TRAITEMENT DU FORMULAIRE
$status_message = "";
if (isset($_POST['submit_contact'])) {
    // Nettoyage des données pour éviter les failles XSS
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // Requête préparée pour éviter les injections SQL
    $stmt = $conn->prepare("INSERT INTO contact (nom, prenom, email, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nom, $prenom, $email, $message);

    if ($stmt->execute()) {
        $status_message = "<div class='p-4 mb-4 text-sm text-green-400 bg-green-900/30 border border-green-500/50 rounded-lg'>Message envoyé avec succès !</div>";
    } else {
        $status_message = "<div class='p-4 mb-4 text-sm text-red-400 bg-red-900/30 border border-red-500/50 rounded-lg'>Erreur : " . $conn->error . "</div>";
    }
    $stmt->close();
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
    <style>
        :root {
            --crimson: #dc143c;
            --marble: #f5f5f5;
        }
        body { 
            padding-top: 80px; 
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-title { font-family: 'Syncopate', sans-serif; }
        .outline-text {
            -webkit-text-stroke: 1px rgba(255,255,255,0.2);
            color: transparent;
        }
    </style>
</head>
<body class="min-h-screen bg-black text-white">
    
    <nav class="fixed top-0 w-full z-50 px-6 lg:px-10 py-4 flex justify-between items-center backdrop-blur-md border-b border-white/5">
        <div class="font-title text-xl tracking-tighter">SUPER<span class="text-[var(--crimson)]">CAR</span></div>
        
        <ul class="hidden md:flex gap-8 font-title text-[10px] tracking-[0.2em] uppercase items-center">
            <li><a href="accueil.html" class="hover:text-[var(--crimson)] transition">Accueil</a></li>
            <li><a href="voiture.html" class="hover:text-[var(--crimson)] transition">Voitures</a></li>
            <li><a href="demande.html" class="hover:text-[var(--crimson)] transition">Essai</a></li>
            <li><a href="services.html" class="hover:text-[var(--crimson)] transition">Services</a></li>
            <li><a href="contact.php" class="text-[var(--crimson)]">Contact</a></li>
            <li><a href="connexion.html">Connexion</a></li>
        </ul>
    </nav>

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
                <span class="text-[var(--crimson)] font-title text-xs tracking-widest uppercase">Directeur :</span>
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
                
                <?php echo $status_message; ?>

                <form method="POST" action="" class="space-y-6">
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

                    <button type="submit" name="submit_contact" class="w-full bg-[var(--crimson)] py-4 font-title text-xs tracking-[0.4em] uppercase hover:bg-white hover:text-black transition-all duration-500 shadow-xl">
                        Envoyer la demande
                    </button>
                </form>
            </div>

            <div class="min-h-[400px] grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-700 bg-zinc-900 rounded-[40px] overflow-hidden border border-white/10">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3744.123456789!2d57.487!3d-20.245!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjDCsDE0JzQyLjAiUyA1N8KwMjknMTMuMiJF!5e0!3m2!1sfr!2smu!4v1234567890" 
                    class="w-full h-full min-h-[400px] border-none"
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </section>

    <footer class="py-10 border-t border-white/5 text-center">
        <div class="font-title text-[8vw] outline-text opacity-10 leading-none mb-4 uppercase">SUPERCAR</div>
        <p class="text-white opacity-40 text-[9px] tracking-[0.5em] uppercase">© 2026 Expertise Automobile - Ebène, Maurice</p>
    </footer>

</body>
</html>