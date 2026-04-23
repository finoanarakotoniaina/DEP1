<?php
session_start();
include 'config.php'; 

$notification = "";

// 1. TRAITEMENT DE LA MISE À JOUR
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sauvegarder'])) {
    foreach ($_POST['settings'] as $cle => $valeur) {
        $stmt = $db->prepare("UPDATE contact_settings SET valeur = :valeur WHERE cle = :cle");
        $stmt->execute([':valeur' => $valeur, ':cle' => $cle]);
    }
    
    if (!empty($_FILES['image_file']['name'])) {
        $nom_image = basename($_FILES['image_file']['name']);
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $nom_image)) {
            $stmt = $db->prepare("UPDATE contact_settings SET valeur = :valeur WHERE cle = 'image_supercar'");
            $stmt->execute([':valeur' => $nom_image]);
        }
    }
    $notification = "Modifications enregistrées !";
}

$stmt = $db->query("SELECT cle, valeur FROM contact_settings");
$s = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Edit | SuperCar Excellence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --crimson: #dc143c;
            --marble: #f5f5f5;
        }
        body { background-color: #003049; color: white; font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-title { font-family: 'Syncopate', sans-serif; }

        /* Style des inputs d'édition pour qu'ils soient invisibles au repos */
        .admin-edit-input {
            background: transparent;
            border: 1px dashed rgba(59, 156, 47, 0.1);
            color: inherit;
            font-family: inherit;
            font-size: inherit;
            font-weight: inherit;
            width: 100%;
            padding: 4px;
            transition: all 0.3s;
        }
        .admin-edit-input:focus {
            border: 1px solid var(--crimson);
            outline: none;
            background: rgba(220, 20, 60, 0.05);
        }

        /* Toolbar Admin persistante */
        .admin-nav {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(15px);
            border-bottom: 2px solid var(--crimson);
        }
    </style>
</head>
<body class="min-h-screen">

    <form action="" method="POST" enctype="multipart/form-data">

        <nav class="admin-nav fixed top-0 w-full z-[100] px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 bg-red-600 rounded-full animate-ping"></div>
                <div class="font-title text-sm tracking-widest uppercase">Éditeur <span class="text-[var(--crimson)]">Live</span></div>
            </div>
            <div class="flex gap-4">
                <a href="contact.php" class="text-[10px] font-title uppercase tracking-widest py-2 px-4 border border-white/20 hover:bg-white/10 transition">Aperçu Client</a>
                <button type="submit" name="sauvegarder" class="bg-[var(--crimson)] text-white text-[10px] font-title uppercase tracking-widest py-2 px-8 hover:bg-white hover:text-black transition-all duration-500">
                    Publier les changements
                </button>
            </div>
        </nav>

        <?php if($notification): ?>
            <div class="fixed top-24 right-10 z-[110] bg-white text-black px-6 py-3 rounded shadow-2xl font-title text-[9px] tracking-[0.2em] animate-bounce">
                <i class="fa-solid fa-check mr-2"></i> <?php echo $notification; ?>
            </div>
        <?php endif; ?>

        <section class="relative pt-32 pb-10 px-6 lg:px-24 overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between w-full py-4 px-8">
                <div class="flex-1">
                    <p class="text-blue-400 tracking-[0.3em] font-bold mb-2 text-[10px] uppercase">Disponibilité 24/7</p>
                    <h1 class="font-title text-5xl md:text-7xl font-bold leading-none mb-0 uppercase">
                        <textarea name="settings[titre_principal]" class="admin-edit-input uppercase" rows="2"><?php echo $s['titre_principal']; ?></textarea>
                    </h1>
                </div>
                <div class="flex-1 flex justify-center md:justify-end mt-8 md:mt-0">
                    <div class="relative group w-64 h-64 md:w-96 md:h-96 border-4 border-white/10 rounded-full overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)]">
                        <img id="preview" src="<?php echo $s['image_supercar']; ?>" class="w-full h-full object-cover transition-transform group-hover:scale-105"/>
                        <label class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center cursor-pointer transition-opacity">
                            <i class="fa-solid fa-camera text-2xl mb-2"></i>
                            <span class="font-title text-[8px] tracking-widest">Changer l'image</span>
                            <input type="file" name="image_file" onchange="previewImage(this)" class="hidden">
                        </label>
                    </div>
                </div>
            </div>
        </section>

        <section class="px-6 lg:px-24 mb-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 border-y border-white/5 py-8">
                <div class="space-y-1">
                    <span class="text-[var(--crimson)] font-title text-[10px] tracking-widest uppercase block mb-1">Email</span>
                    <input type="text" name="settings[email_contact]" value="<?php echo $s['email_contact']; ?>" class="admin-edit-input text-base font-light">
                </div>
                <div class="space-y-1">
                    <span class="text-[var(--crimson)] font-title text-[10px] tracking-widest uppercase block mb-1">Tel</span>
                    <input type="text" name="settings[tel_contact]" value="<?php echo $s['tel_contact']; ?>" class="admin-edit-input text-base font-light">
                </div>
                <div class="space-y-1">
                    <span class="text-[var(--crimson)] font-title text-[10px] tracking-widest uppercase block mb-1">Siège</span>
                    <input type="text" name="settings[siege_contact]" value="<?php echo $s['siege_contact']; ?>" class="admin-edit-input text-base font-light">
                </div>
                <div class="space-y-1">
                    <span class="text-[var(--crimson)] font-title text-[10px] tracking-widest uppercase block mb-1">Responsable</span>
                    <input type="text" name="settings[responsable_contact]" value="<?php echo $s['responsable_contact']; ?>" class="admin-edit-input text-base font-light">
                </div>
            </div>
        </section>

        <section class="pb-24 px-6 lg:px-24 relative">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-stretch">
                <div class="bg-white/5 p-8 lg:p-12 rounded-[40px] border border-white/10 backdrop-blur-sm">
                    <h3 class="font-title text-[10px] tracking-[0.3em] text-[var(--crimson)] mb-6 uppercase">Configuration Carte</h3>
                    <div class="space-y-4">
                        <label class="text-[9px] uppercase tracking-[0.2em] text-gray-500">Lien iFrame (Source Google Maps)</label>
                        <textarea name="settings[map_iframe]" rows="5" class="admin-edit-input font-mono text-[10px] bg-black/30" placeholder="Insérer l'URL src..."><?php echo $s['map_iframe']; ?></textarea>
                        <div class="pt-4 border-t border-white/5">
                            <p class="text-[10px] text-gray-400 italic">L'aperçu à droite se mettra à jour automatiquement lors de la modification de l'URL.</p>
                        </div>
                    </div>
                </div>

                <div class="min-h-[450px] grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-1000 bg-zinc-900 rounded-[40px] overflow-hidden border border-white/10">
                    <iframe id="map-preview" src="<?php echo $s['map_iframe']; ?>" class="w-full h-full border-none" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </section>

    </form>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        const mapInput = document.querySelector('textarea[name="settings[map_iframe]"]');
        const mapIframe = document.getElementById('map-preview');
        mapInput.addEventListener('input', function() {
            mapIframe.src = this.value;
        });
    </script>
</body>
</html>