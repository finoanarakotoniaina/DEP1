<?php
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn;

// Fonction de récupération depuis services_content
function getServiceContent($id) {
    global $db;
    $stmt = $db->prepare("SELECT content FROM services_content WHERE element_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ? $row['content'] : "Texte par défaut";
}

// Traitement des requêtes POST (Sauvegarde)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Cas du Texte
    if (isset($_POST['id']) && isset($_POST['content'])) {
        $id = $_POST['id'];
        $content = $_POST['content'];
        
        $stmt = $db->prepare("REPLACE INTO services_content (element_id, content) VALUES (?, ?)");
        
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
        $uploadDir = '../assets/services/';
        
        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
        
        $fileName = time() . '_' . basename($_FILES["image"]["name"]);
        $targetFilePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $stmt = $db->prepare("REPLACE INTO services_content (element_id, content) VALUES (?, ?)");
            $stmt->execute([$id, $fileName]);
            echo json_encode(['success' => true, 'path' => $fileName]);
        } else {
            echo json_encode(['success' => false, 'message' => "Erreur transfert"]);
        }
        exit;
    }
}

try {
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";
} catch (PDOException $e) {
    $adminFullName = "Admin";
}

$currentPage = basename($_SERVER['PHP_SELF']);
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
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
    
    <style>
        :root { --crimson: #C1121F; --cosmos: #000814; }
        body { background-color: var(--cosmos); }
        .sidebar { background: rgba(0, 0, 0, 0.2); border-right: 1px solid rgba(255, 255, 255, 0.05); }
        .nav-link-admin { display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1.25rem; border-radius: 14px; color: rgba(255,255,255,0.6); transition: 0.3s; font-size: 0.85rem; font-weight: 600; }
        .nav-link-admin:hover { color: white; background: rgba(255,255,255,0.05); }
        .nav-link-admin.active { color: white; background: linear-gradient(90deg, rgba(193, 18, 31, 0.2), transparent); border-left: 3px solid var(--crimson); }
        .logout-btn-admin { display: flex; align-items: center; justify-content: center; gap: 0.75rem; width: 100%; padding: 0.75rem; margin-bottom: 0.8rem; background: rgba(193, 18, 31, 0.1); border: 1px solid rgba(193, 18, 31, 0.2); border-radius: 14px; color: #ff4d4d; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
        .logout-btn-admin:hover { background: var(--crimson); color: white; }
        
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
            position: fixed; bottom: 20px; left: 300px; z-index: 100;
            background: var(--crimson); color: white; padding: 12px 24px;
            font-family: 'Syncopate'; font-size: 10px; border-radius: 50px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }
    </style>
</head>

<body class="flex h-screen overflow-hidden text-white">

    <?php include 'composants/sidebar.php'; ?>

    <main class="flex-1 overflow-y-auto">
        <div class="admin-badge"><i class="fas fa-tools mr-2"></i> ADMINISTRATION SERVICES</div>

        <section class="relative h-[80vh] flex items-center px-6 lg:px-24 overflow-hidden">
            <div class="z-10 max-w-4xl relative">
                <span class="text-gray-500 font-bold tracking-[0.5em] uppercase mb-4 block text-[10px]">Ingénierie de pointe</span>
                
                <div class="mb-8">
                    <h1 id="serv_hero_title" contenteditable="true" onfocus="showBtn('btn_serv_title')" class="edit-zone font-title text-5xl md:text-7xl font-bold leading-none uppercase">
                        <?php echo getServiceContent('serv_hero_title'); ?>
                    </h1>
                    <button id="btn_serv_title" onclick="saveText('serv_hero_title', 'btn_serv_title')" class="btn-save mt-4 bg-green-600 px-4 py-2 rounded text-xs font-bold uppercase tracking-widest">
                        <i class="fas fa-check mr-2"></i> Sauvegarder le titre
                    </button>
                </div>

                <div class="mb-10">
                    <p id="serv_hero_desc" contenteditable="true" onfocus="showBtn('btn_serv_desc')" class="edit-zone text-xl text-gray-400 font-light max-w-2xl leading-relaxed">
                        <?php echo getServiceContent('serv_hero_desc'); ?>
                    </p>
                    <button id="btn_serv_desc" onclick="saveText('serv_hero_desc', 'btn_serv_desc')" class="btn-save mt-4 bg-green-600 px-4 py-2 rounded text-xs font-bold uppercase tracking-widest">
                        <i class="fas fa-check mr-2"></i> Sauvegarder la description
                    </button>
                </div>
            </div>
            
            <div class="absolute inset-y-0 right-0 w-full md:w-1/2 z-0">
                <img src="<?= ASSETS_URL ?>/hero/connexionimg.jpg" alt="Garage" class="w-full h-full object-cover grayscale opacity-30 [mask-image:linear-gradient(to_left,black_40%,transparent_100%)]">
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
                        <img id="img_preview_<?php echo $i; ?>" src="<?= ASSETS_URL ?>/services/<?php echo getServiceContent($id_img); ?>"
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
                            <?php echo getServiceContent($id_title); ?>
                        </h3>
                        <button id="<?php echo $btn_title; ?>" onclick="saveText('<?php echo $id_title; ?>', '<?php echo $btn_title; ?>')" class="btn-save mt-2 bg-green-600 px-3 py-1 rounded text-[10px] font-bold">OK</button>
                    </div>

                    <div>
                        <p id="<?php echo $id_text; ?>" contenteditable="true" onfocus="showBtn('<?php echo $btn_text; ?>')" class="edit-zone text-gray-400 text-[14px] leading-relaxed font-light">
                            <?php echo getServiceContent($id_text); ?>
                        </p>
                        <button id="<?php echo $btn_text; ?>" onclick="saveText('<?php echo $id_text; ?>', '<?php echo $btn_text; ?>')" class="btn-save mt-2 bg-green-600 px-3 py-1 rounded text-[10px] font-bold">OK</button>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </section>
    </main>

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
                        document.getElementById(previewId).src = '<?= ASSETS_URL ?>/services/' + data.path + '?t=' + new Date().getTime();
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