<?php 
include 'config.php'; 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN | SuperCar Edition</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    
    <style>
        /* Zone de texte quand on clique dessus */
        .edit-zone:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            box-shadow: 0 0 0 2px #C1121F; /* Couleur Crimson */
        }
        /* Cache les boutons par défaut */
        .btn-save {
            display: none; 
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
        .admin-badge {
            position: fixed; bottom: 20px; left: 20px; z-index: 100;
            background: #C1121F; color: white; padding: 12px 24px;
            font-family: 'Syncopate'; font-size: 10px; border-radius: 50px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }
    </style>
</head>

<body class="home-bg text-white">

    <div class="admin-badge"><i class="fas fa-edit mr-2"></i> PANEL ADMINISTRATION</div>

    <section class="relative h-screen w-full flex items-center px-6 lg:px-24 overflow-hidden group">
        <div class="absolute top-0 left-0 w-full h-full z-0">
            <video id="video_preview" autoplay muted loop playsinline class="w-full h-full object-cover">
                <source src="<?php echo getContent('hero_video'); ?>" type="video/mp4">
            </video>
        </div>

        <div class="relative z-20 w-full mt-20">
            <div class="relative mb-8 max-w-4xl">
                <h1 id="hero_title" contenteditable="true" onfocus="showBtn('btn_title')" class="edit-zone font-title text-4xl md:text-7xl font-bold leading-none">
                    <?php echo getContent('hero_title'); ?>
                </h1>
                <button id="btn_title" onclick="saveText('hero_title', 'btn_title')" class="btn-save mt-4 bg-green-600 px-4 py-2 rounded text-xs font-bold uppercase tracking-widest">
                    <i class="fas fa-check mr-2"></i> Enregistrer le titre
                </button>
            </div>

            <div class="relative max-w-xl">
                <p id="hero_subtitle" contenteditable="true" onfocus="showBtn('btn_subtitle')" class="edit-zone text-lg text-gray-200 font-light leading-relaxed">
                    <?php echo getContent('hero_subtitle'); ?>
                </p>
                <button id="btn_subtitle" onclick="saveText('hero_subtitle', 'btn_subtitle')" class="btn-save mt-4 bg-green-600 px-4 py-2 rounded text-xs font-bold uppercase tracking-widest">
                    <i class="fas fa-check mr-2"></i> Enregistrer le texte
                </button>
            </div>
        </div>
    </section>

    <section class="py-32 px-6 lg:px-24 relative">
        <div class="relative z-10 flex flex-col gap-16 max-w-3xl mx-auto">
            
            <?php for($i=1; $i<=4; $i++): 
                $id_name = "brand_".$i."_name";
                $id_desc = "brand_".$i."_desc";
                $btn_id = "btn_brand_".$i;
            ?>
            <div class="brand-card p-8 bg-white/5 backdrop-blur-sm border border-white/10 relative">
                <h3 id="<?php echo $id_name; ?>" contenteditable="true" onfocus="showBtn('<?php echo $btn_id; ?>')" class="edit-zone font-title text-2xl mb-4">
                    <?php echo getContent($id_name); ?>
                </h3>
                
                <p id="<?php echo $id_desc; ?>" contenteditable="true" onfocus="showBtn('<?php echo $btn_id; ?>')" class="edit-zone text-gray-300 text-lg leading-relaxed mb-6">
                    <?php echo getContent($id_desc); ?>
                </p>

                <button id="<?php echo $btn_id; ?>" onclick="saveBrandGroup(<?php echo $i; ?>)" class="btn-save bg-green-600 px-6 py-2 rounded text-xs font-bold uppercase tracking-widest">
                    <i class="fas fa-save mr-2"></i> Valider les modifs <?php echo $i; ?>
                </button>
            </div>
            <?php endfor; ?>

        </div>
    </section>

    <script>
        // Affiche le bouton vert quand on clique sur le texte
        function showBtn(id) {
            document.getElementById(id).style.display = "block";
        }

        // Sauvegarde pour Titre / Sous-titre
        function saveText(elementId, btnId) {
            const el = document.getElementById(elementId);
            // On prend innerHTML pour le titre (si il y a des <span>) et innerText pour le reste
            const content = (elementId === 'hero_title') ? el.innerHTML : el.innerText;
            executeUpload(elementId, content, btnId);
        }

        // Sauvegarde pour les blocs Marques (Nom + Description)
        function saveBrandGroup(index) {
            const nameId = "brand_" + index + "_name";
            const descId = "brand_" + index + "_desc";
            const btnId = "btn_brand_" + index;

            executeUpload(nameId, document.getElementById(nameId).innerText, null);
            executeUpload(descId, document.getElementById(descId).innerText, btnId);
        }

        // Le coeur du système : Envoi AJAX vers updatehome.php
        function executeUpload(id, content, btnId) {
            let formData = new FormData();
            formData.append('id', id);
            formData.append('content', content);

            fetch('updatehome.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                if(data.trim() === "Succès") {
                    console.log("Sauvegarde " + id + " : OK");
                    if(btnId) {
                        document.getElementById(btnId).style.display = "none";
                        // Effet visuel de confirmation
                        const target = document.getElementById(id);
                        target.style.transition = "color 0.3s";
                        target.style.color = "#22c55e"; // Vert
                        setTimeout(() => { target.style.color = ""; }, 1000);
                    }
                } else {
                    alert("Erreur retournée : " + data);
                }
            })
            .catch(err => alert("Erreur de connexion au serveur"));
        }
    </script>

</body>
</html>