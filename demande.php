<?php 
include 'config.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Récupération des données (ID pour la BDD et Nom pour l'affichage)
$id_voiture_hidden = $_GET['id_v'] ?? $_POST['id_voiture_cache'] ?? '';
$modele_selectionne = $_GET['modele'] ?? $_POST['modele_affiche'] ?? '';

$message = "";

// 2. Traitement de la réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_essai'])) {
    $id_client = $_SESSION['id_client'] ?? null;
    
    if ($id_client) {
        $date_essai = $_POST['date_essai'];
        $heure_essai = $_POST['heure_essai'];
        $id_v_a_inserer = $_POST['id_voiture_cache']; 

        if ($heure_essai < "08:00" || $heure_essai > "17:00") {
            $message = "ERREUR : CRÉNEAU DISPONIBLE UNIQUEMENT ENTRE 08H ET 17H.";
        } else {
            try {
                $sql = "INSERT INTO demande_essai (id_client, date_souhaitee, statut, id_voiture) VALUES (?, ?, 'EN ATTENTE', ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$id_client, $date_essai, $id_v_a_inserer]);
                
                $message = "VOTRE DEMANDE A ÉTÉ ENREGISTRÉE AVEC SUCCÈS.";
                $modele_selectionne = ""; 
                $id_voiture_hidden = "";
            } catch (Exception $e) {
                $message = "ERREUR : " . $e->getMessage();
            }
        }
    }
}

$isLoggedIn = isset($_SESSION['id_client']); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | L'Inévitable</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        :root { --crimson: #C1121F; }
        .modal-blur { backdrop-filter: blur(10px); }
        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="time"]::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }
        .no-caps { text-transform: none !important; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .animate-fade-in { animation: fadeIn 0.6s ease-out forwards; }
        body, html { background-color: #0a0a0a; scroll-behavior: smooth; }
    </style>
</head>
<body class="split-bg text-white">

    <div id="authModal" class="<?php echo $isLoggedIn ? 'hidden' : 'flex'; ?> fixed inset-0 z-[100] items-center justify-center modal-blur px-6 animate-fade-in">
        <div class="bg-[#0a0a0a] border border-white/10 p-10 rounded-[20px] max-w-md w-full text-center relative shadow-2xl">
            <button onclick="closeModal()" class="absolute top-5 right-5 text-gray-500 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <h2 class="font-title text-2xl mb-4 uppercase tracking-tighter">Accès Réservé</h2>
            <p class="text-gray-400 text-sm mb-8 leading-relaxed">Veuillez vous identifier pour accéder au configurateur d'essai.</p>
            <div class="flex flex-col gap-4">
                <a href="connexion.php?redirect=demande.php" class="bg-[var(--crimson)] py-4 font-title text-[10px] tracking-[0.2em] uppercase hover:bg-white hover:text-black transition-all">Connexion</a>
                <a href="inscription.php" class="border border-white/20 py-4 font-title text-[10px] tracking-[0.2em] uppercase hover:border-[var(--crimson)] transition-all">Inscription</a>
            </div>
        </div>
    </div>

    <nav class="fixed top-0 w-full z-50 px-6 lg:px-10 py-6 flex justify-between items-center backdrop-blur-md border-b border-white/5">
        <div class="font-title text-xl tracking-tighter">SUPER<span class="text-[var(--crimson)]">CAR</span></div>
        
        <ul class="hidden md:flex gap-8 font-title text-[10px] tracking-[0.2em] uppercase items-center">
            <li><a href="accueil.php" class="nav-link">Accueil</a></li>
            <li><a href="voiture.php" class="nav-link">Voitures</a></li>
            <li><a href="demande.php" class="nav-link active">Essai</a></li>
            <li><a href="services.php" class="nav-link">Services</a></li>
            <li><a href="contact.php" class="nav-link">Contact</a></li> |

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

    <section class="relative h-screen flex items-center px-6 lg:px-24 overflow-hidden">
        <div class="z-10 max-w-4xl">
            <span class="text-gray-500 font-bold tracking-[0.5em] uppercase mb-4 block text-[10px]">Expérience Ultime</span>
            <h1 class="font-title text-5xl md:text-7xl font-bold leading-none mb-8 uppercase">DEMANDE <br> <span class="text-[var(--crimson)]">D'ESSAI.</span></h1>
            
            <p class="text-gray-400 max-w-md mb-12 text-sm leading-relaxed border-l border-[var(--crimson)] pl-6">
                Prenez les commandes de l'exceptionnel. Choisissez votre modèle, définissez votre créneau et laissez-vous transporter par une ingénierie hors du commun.
            </p>

            <a href="#booking" class="inline-flex items-center gap-8 group">
                <div class="w-16 h-16 rounded-full border border-white/20 flex items-center justify-center group-hover:border-[var(--crimson)] transition-all duration-500">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="group-hover:text-[var(--crimson)] transform group-hover:translate-y-1 transition-all">
                        <path d="M7 13l5 5 5-5M7 6l5 5 5-5" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <span class="font-title text-[10px] tracking-[0.2em] uppercase">Réserver mon créneau</span>
            </a>
        </div>
        <div class="absolute inset-y-0 right-0 w-full md:w-1/2 z-0">
            <img src="photo_essai.jpeg" alt="Essai" class="w-full h-full object-cover grayscale opacity-30">
        </div>
    </section>

    <section id="booking" class="min-h-screen pt-10 pb-24 px-6 lg:px-24 border-t border-white/5 flex flex-col justify-center">
        <div class="max-w-5xl mx-auto w-full grid lg:grid-cols-2 gap-20">
            <div class="space-y-12">
                <h2 class="font-title text-4xl uppercase">Configurateur <br> d'essai</h2>
                <?php if($message): ?>
                    <div class="p-4 bg-white/5 border-l-2 border-[var(--crimson)] text-[10px] tracking-widest uppercase animate-pulse"><?php echo $message; ?></div>
                <?php endif; ?>
                <div class="flex items-start gap-4">
                    <span class="text-[var(--crimson)] font-title text-2xl">01</span>
                    <div><h3 class="font-bold text-lg uppercase text-white">Sélection</h3><p class="text-gray-500 text-sm">Choisissez votre monture et votre date.</p></div>
                </div>
            </div>

            <form action="demande.php#booking" method="POST" class="space-y-8 bg-white/5 p-10 rounded-[40px] border border-white/10 backdrop-blur-sm">
                <input type="hidden" name="id_voiture_cache" value="<?php echo htmlspecialchars($id_voiture_hidden); ?>">

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-1 border-b border-white/10">
                        <label class="text-[10px] text-gray-500 font-bold uppercase">Nom</label>
                        <input type="text" value="<?php echo strtoupper($_SESSION['nom'] ?? ''); ?>" readonly class="bg-transparent w-full p-2 text-xs outline-none text-gray-400 cursor-default">
                    </div>
                    <div class="space-y-1 border-b border-white/10">
                        <label class="text-[10px] text-gray-500 font-bold uppercase">Prénom</label>
                        <input type="text" value="<?php echo ucfirst(strtolower($_SESSION['prenom'] ?? '')); ?>" readonly class="bg-transparent w-full p-2 text-xs outline-none no-caps text-gray-400 cursor-default">
                    </div>
                </div>

                <div class="space-y-1 border-b border-white/10">
                    <label class="text-[10px] text-gray-500 font-bold uppercase">Email</label>
                    <input type="email" value="<?php echo $_SESSION['email'] ?? ''; ?>" readonly class="w-full bg-transparent p-2 text-xs text-gray-400 outline-none cursor-default">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 font-bold uppercase">Modèle</label>
                        <div class="flex items-center gap-2 border-b border-white/20">
                            <input type="text" name="modele_affiche" 
                                   value="<?php echo htmlspecialchars($modele_selectionne); ?>" 
                                   placeholder="VOTRE MODÈLE" 
                                   readonly required 
                                   class="bg-transparent w-full p-4 text-xs outline-none text-[var(--crimson)] font-bold tracking-widest">
                            <a href="voiture.php" class="pr-2 text-[var(--crimson)] hover:scale-125 transition-transform text-xl" title="Ouvrir le catalogue">
                                <i class="fa-solid fa-circle-plus"></i>
                            </a>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 font-bold uppercase">Téléphone</label>
                        <input type="tel" name="mobile" value="<?php echo $_SESSION['telephone'] ?? ''; ?>" required class="bg-transparent border-b border-white/20 w-full p-4 text-xs outline-none focus:border-[var(--crimson)] transition-colors">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 font-bold uppercase">Date préférée</label>
                        <input type="date" name="date_essai" id="d" required class="bg-transparent border border-white/10 rounded-lg w-full p-3 text-xs focus:border-[var(--crimson)] outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 font-bold uppercase">Créneau (08h-17h)</label>
                        <input type="time" name="heure_essai" id="h" min="08:00" max="17:00" required class="bg-transparent border border-white/10 rounded-lg w-full p-3 text-xs focus:border-[var(--crimson)] outline-none">
                    </div>
                </div>

                <button type="submit" name="valider_essai" class="w-full bg-[var(--crimson)] py-6 font-title text-xs tracking-[0.4em] uppercase hover:bg-white hover:text-black transition-all duration-500">
                    Confirmer la demande
                </button>
            </form>
        </div>
    </section>

    <script>
        function closeModal() { window.location.href = 'accueil.php'; }
        const dateIn = document.getElementById('d');
        if(dateIn) {
            const today = new Date();
            today.setDate(today.getDate() + 1);
            dateIn.min = today.toISOString().split('T')[0];
        }
    </script>
</body>
</html>