<?php 
require_once '../config.php'; 

// Récupération des réglages dynamiques (colonnes cle/valeur)
$stmt_settings = $db->query("SELECT cle, valeur FROM contact_settings");
$s = $stmt_settings->fetchAll(PDO::FETCH_KEY_PAIR);

if (!$s) {
    die("Erreur : Les réglages de contact ne sont pas configurés dans la base de données.");
}

$message_envoi = "";
$show_modal = false;

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($message)) {
        try {
            $sql = "INSERT INTO contact_client (nom, prenom, email, message) VALUES (:nom, :prenom, :email, :message)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':nom'     => $nom,
                ':prenom'  => $prenom,
                ':email'   => $email,
                ':message' => $message
            ]);

            $message_envoi = "Votre message a bien été enregistré !";
            $show_modal = true;
        } catch (PDOException $e) {
            die("Erreur lors de l'enregistrement : " . $e->getMessage());
        }
    }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
    <style>
        :root {
            --crimson: #dc143c;
            --marble: #f5f5f5;
        }
        .outline-text {
            -webkit-text-stroke: 1px rgba(255,255,255,0.2);
            color: transparent;
        }

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
            <li><a href="contact.php" class="nav-link active">Contact</a></li> |

            <?php if (isset($_SESSION['id_client'])): ?>
                <li>
                    <a href="deconnexion.php" class="bg-[var(--crimson)] px-4 py-2 rounded text-white hover:bg-white hover:text-black transition-all duration-300 flex items-center gap-2">
                        <i class="fa-solid fa-door-open text-[18px]"></i> Déconnexion
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <strong>
                        <a href="connexion.php" class="nav-link flex items-center gap-2">
                            <i class="fa-solid fa-user text-[var(--crimson)] text-[18px]"></i> Connexion
                        </a>
                    </strong>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="relative z-[60] px-6 lg:px-24 pt-10">
        <a href="accueil.php" id="back-btn-contact" style="display: none;" class="inline-flex items-center text-[10px] font-title uppercase tracking-[0.2em] text-gray-400 hover:text-[var(--crimson)] transition-all group">
            <span class="mr-3 transition-transform group-hover:-translate-x-1">←</span> Retour vers l'accueil
        </a>
    </div>

    <section class="relative pt-4 pb-10 px-6 lg:px-24 overflow-hidden">
        <div class="flex flex-col md:flex-row items-center justify-between w-full py-4 px-8">
            <div class="flex-1">
                <p class="text-blue-400 tracking-[0.3em] font-bold mb-2 text-[10px] uppercase">Disponibilité 24/7</p>
                <h1 class="font-title text-5xl md:text-7xl font-bold leading-none mb-0 uppercase">
                    <?php echo nl2br($s['titre_principal'] ?? 'NOUS CONTACTER'); ?>
                </h1>
            </div>
            <div class="flex-1 flex justify-center md:justify-end mt-8 md:mt-0">
                <div class="relative w-64 h-64 md:w-96 md:h-96 border-4 border-white/10 rounded-full overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)]">
                    <img src="<?= ASSETS_URL ?>/hero/<?php echo htmlspecialchars($s['image_supercar'] ?? 'connexionimg.jpg'); ?>" alt="Supercar" class="w-full h-full object-cover"/>
                </div>
            </div>
        </div>
    </section>

    <section class="px-6 lg:px-24 mb-10">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 border-y border-white/5 py-8">
            <div class="group">
                <span class="text-[var(--crimson)] font-title text-[10px] tracking-widest uppercase block mb-1">Email</span>
                <p class="text-base font-light"><?php echo $s['email_contact'] ?? '—'; ?></p>
            </div>
            <div class="group">
                <span class="text-[var(--crimson)] font-title text-[10px] tracking-widest uppercase block mb-1">Tel</span>
                <p class="text-base font-light"><?php echo $s['tel_contact'] ?? '—'; ?></p>
            </div>
            <div class="group">
                <span class="text-[var(--crimson)] font-title text-[10px] tracking-widest uppercase block mb-1">Siège</span>
                <p class="text-base font-light"><?php echo $s['siege_contact'] ?? '—'; ?></p>
            </div>
            <div class="group">
                <span class="text-[var(--crimson)] font-title text-[10px] tracking-widest uppercase block mb-1">Responsable</span>
                <p class="text-base font-light"><?php echo $s['responsable_contact'] ?? '—'; ?></p>
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
                            <input type="text" name="nom" placeholder="VOTRE NOM" required 
                                   value="<?php echo isset($_SESSION['nom']) ? $_SESSION['nom'] : ''; ?>" 
                                   class="bg-transparent border-b border-white/20 w-full py-2 text-xs tracking-widest focus:border-[var(--crimson)] outline-none transition-colors">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase tracking-[0.3em] text-gray-500">Prénom</label>
                            <input type="text" name="prenom" placeholder="VOTRE PRÉNOM" required 
                                   value="<?php echo isset($_SESSION['prenom']) ? $_SESSION['prenom'] : ''; ?>" 
                                   class="bg-transparent border-b border-white/20 w-full py-2 text-xs tracking-widest focus:border-[var(--crimson)] outline-none transition-colors">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-[0.3em] text-gray-500">Email</label>
                        <input type="email" name="email" placeholder="VOTRE@EMAIL.COM" required 
                               value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" 
                               class="bg-transparent border-b border-white/20 w-full py-2 text-xs tracking-widest focus:border-[var(--crimson)] outline-none transition-colors">
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
                <iframe src="<?php echo $s['map_iframe'] ?? ''; ?>" class="w-full h-full border-none" allowfullscreen="" loading="lazy"></iframe>
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