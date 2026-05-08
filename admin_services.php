<?php
// 1. Connexion forcée (pour éviter l'erreur $conn undefined)
$host = "localhost";
$dbname = "supercar"; 
$user = "root";
$pass = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// 2. Fonction de récupération (indispensable pour l'affichage)
function getContent($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT content FROM services_content WHERE element_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ? $row['content'] : "Texte par défaut";
}

// 3. Traitement des requêtes POST (Sauvegarde)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Cas du Texte
    if (isset($_POST['id']) && isset($_POST['content'])) {
        $id = $_POST['id'];
        $content = $_POST['content'];
        
        $stmt = $conn->prepare("REPLACE INTO services_content (element_id, content) VALUES (?, ?)");
        
        if ($stmt->execute([$id, $content])) {
            echo "Succès";
        } else {
            echo "Erreur BDD";
        }
        exit;
    }

    // Cas de l'Image
    if (isset($_POST['id']) && isset($_FILES['image'])) {
        $id = $_POST['id'];
        $uploadDir = "image_url/";
        
        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
        
        $fileName = time() . '_' . basename($_FILES["image"]["name"]);
        $targetFilePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $stmt = $conn->prepare("REPLACE INTO services_content (element_id, content) VALUES (?, ?)");
            $stmt->execute([$id, $targetFilePath]);
            echo json_encode(['success' => true, 'path' => $targetFilePath]);
        } else {
            echo json_encode(['success' => false, 'message' => "Erreur transfert"]);
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN | Services SuperCar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    
    <style>
        :root { --crimson: #C1121F; }
        .edit-zone:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            box-shadow: 0 0 0 2px var(--crimson);
        }
        .btn-save {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
        .admin-badge {
            position: fixed; bottom: 20px; left: 20px; z-index: 100;
            background: var(--crimson); color: white; padding: 12px 24px;
            font-family: 'Syncopate'; font-size: 10px; border-radius: 50px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        /* STYLE DU BOUTON RETOUR */
        .btn-back {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            font-family: 'Syncopate';
            font-size: 10px;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.3s;
            display: flex;
            align-items: center;
        }
        .btn-back:hover {
            background: var(--crimson);
            border-color: var(--crimson);
        }
    </style>
</head>

<body class="split-bg text-white font-['Plus_Jakarta_Sans']">

    <!-- AJOUT DU BOUTON RETOUR -->
    <a href="tableaudebord.php" class="btn-back">
        <i class="fas fa-arrow-left mr-2"></i> RETOUR TABLEAU DE BORD
    </a>

    <div class="admin-badge"><i class="fas fa-tools mr-2"></i> ADMINISTRATION SERVICES</div>

    <section class="relative h-[80vh] flex items-center px-6 lg:px-24 overflow-hidden">
        <div class="z-10 max-w-4xl relative">
            <span class="text-gray-500 font-bold tracking-[0.5em] uppercase mb-4 block text-[10px]">Ingénierie de pointe</span>
            
            <div class="mb-8">
                <h1 id="serv_hero_title" contenteditable="true" onfocus="showBtn('btn_serv_title')" class="edit-zone font-title text-5xl md:text-7xl font-bold leading-none uppercase">
                    <?php echo getContent('serv_hero_title'); ?>
                </h1>
                <button id="btn_serv_title" onclick="saveText('serv_hero_title', 'btn_serv_title')" class="btn-save mt-4 bg-green-600 px-4 py-2 rounded text-xs font-bold uppercase tracking-widest">
                    <i class="fas fa-check mr-2"></i> Sauvegarder le titre
                </button>
            </div>

            <div class="mb-10">
                <p id="serv_hero_desc" contenteditable="true" onfocus="showBtn('btn_serv_desc')" class="edit-zone text-xl text-gray-400 font-light max-w-2xl leading-relaxed">
                    <?php echo getContent('serv_hero_desc'); ?>
                </p>
                <button id="btn_serv_desc" onclick="saveText('serv_hero_desc', 'btn_serv_desc')" class="btn-save mt-4 bg-green-600 px-4 py-2 rounded text-xs font-bold uppercase tracking-widest">
                    <i class="fas fa-check mr-2"></i> Sauvegarder la description
                </button>
            </div>
        </div>
        
        <div class="absolute inset-y-0 right-0 w-full md:w-1/2 z-0">
            <img src="garage.jfif" alt="Garage" class="w-full h-full object-cover grayscale opacity-30 [mask-image:linear-gradient(to_left,black_40%,transparent_100%)]">
        </div>
    </section>

    <section id="services-list" class="py-32 px-6 lg:px-24 relative">
        <div class="mb-24">
            <h2 class="font-title text-3xl md:text-5xl mb-4 uppercase">Notre <span class="text-[var(--crimson)]">Expertise</span></h2>
            <div class="w-24 h-1 bg-[var(--crimson)]"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            <?php
            for($i=1; $i<=4; $i++):
                $id_img = "service_".$i."_img";
                $id_title = "service_".$i."_title";
                $id_text = "service_".$i."_text";
                $btn_title = "btn_s_title_".$i;
                $btn_text = "btn_s_text_".$i;
            ?>
            <div class="service-card p-8 bg-white/5 border border-white/10 rounded-2xl">
                <div class="mb-6 overflow-hidden rounded-xl h-44 bg-gray-900 relative group">
                    <img id="img_preview_<?php echo $i; ?>" src="<?php echo getContent($id_img); ?>"
                           class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-500 cursor-pointer">
                    
                    <div class="absolute inset-0 flex items-center justify-center bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity">
                        <label for="input_<?php echo $id_img; ?>" class="cursor-pointer bg-white text-black p-4 rounded-full hover:bg-[var(--crimson)] hover:text-white transition-colors">
                            <i class="fas fa-camera text-xl"></i>
                        </label>
                    </div>
                    
                    <input type="file" id="input_<?php echo $id_img; ?>" class="hidden" accept="image/*"
                            onchange="uploadImage('<?php echo $id_img; ?>', this, 'img_preview_<?php echo $i; ?>')">
                </div>
                
                <span class="text-[var(--crimson)] font-title text-sm mb-2 block uppercase">0<?php echo $i; ?></span>
                
                <div class="mb-4">
                    <h3 id="<?php echo $id_title; ?>" contenteditable="true" onfocus="showBtn('<?php echo $btn_title; ?>')" class="edit-zone font-title text-xl leading-tight uppercase">
                        <?php echo getContent($id_title); ?>
                    </h3>
                    <button id="<?php echo $btn_title; ?>" onclick="saveText('<?php echo $id_title; ?>', '<?php echo $btn_title; ?>')" class="btn-save mt-2 bg-green-600 px-3 py-1 rounded text-[10px] font-bold">OK</button>
                </div>

                <div>
                    <p id="<?php echo $id_text; ?>" contenteditable="true" onfocus="showBtn('<?php echo $btn_text; ?>')" class="edit-zone text-gray-400 text-[14px] leading-relaxed font-light">
                        <?php echo getContent($id_text); ?>
                    </p>
                    <button id="<?php echo $btn_text; ?>" onclick="saveText('<?php echo $id_text; ?>', '<?php echo $btn_text; ?>')" class="btn-save mt-2 bg-green-600 px-3 py-1 rounded text-[10px] font-bold">OK</button>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </section>

    <script>
        function showBtn(id) {
            document.getElementById(id).style.display = "inline-block";
        }

        function saveText(elementId, btnId) {
            const el = document.getElementById(elementId);
            const content = el.innerHTML;
            
            let formData = new FormData();
            formData.append('id', elementId);
            formData.append('content', content);

            fetch('admin_services.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                if(data.trim() === "Succès") {
                    document.getElementById(btnId).style.display = "none";
                    const target = document.getElementById(elementId);
                    target.style.color = "#22c55e";
                    setTimeout(() => { target.style.color = ""; }, 1000);
                } else { alert("Erreur : " + data); }
            });
        }

        function uploadImage(id, input, previewId) {
            if (input.files && input.files[0]) {
                let formData = new FormData();
                formData.append('id', id);
                formData.append('image', input.files[0]);

                fetch('admin_services.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(previewId).src = data.path + '?t=' + new Date().getTime();
                        alert("Image mise à jour !");
                    } else {
                        alert("Erreur : " + data.message);
                    }
                })
                .catch(err => alert("Erreur lors de l'envoi."));
            }
        }
    </script>
</body>
</html>