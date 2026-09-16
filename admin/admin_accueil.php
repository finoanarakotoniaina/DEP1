<?php 
require_once '../config.php'; 

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn;

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
    <title>ADMIN | SuperCar Edition</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    
    
    <style>
        :root { --crimson: #C1121F; --cosmos: #000814; }
        body { background-color: var(--cosmos); }
        .sidebar { background: rgba(0, 0, 0, 0.2); border-right: 1px solid rgba(255, 255, 255, 0.05); }
        .nav-link-admin { display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1.25rem; border-radius: 14px; color: rgba(255,255,255,0.6); transition: 0.3s; font-size: 0.85rem; font-weight: 600; }
        .nav-link-admin:hover { color: white; background: rgba(255,255,255,0.05); }
        .nav-link-admin.active { color: white; background: linear-gradient(90deg, rgba(193, 18, 31, 0.2), transparent); border-left: 3px solid var(--crimson); }
        .logout-btn-admin { display: flex; align-items: center; justify-content: center; gap: 0.75rem; width: 100%; padding: 0.75rem; margin-bottom: 0.8rem; background: rgba(193, 18, 31, 0.1); border: 1px solid rgba(193, 18, 31, 0.2); border-radius: 14px; color: #ff4d4d; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
        .logout-btn-admin:hover { background: var(--crimson); color: white; }
        
        .edit-zone:focus { outline: none; background: rgba(255, 255, 255, 0.05); border-radius: 8px; box-shadow: 0 0 0 2px var(--crimson); }
        .btn-save { display: none; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .admin-badge { position: fixed; bottom: 20px; left: 300px; z-index: 100; background: var(--crimson); color: white; padding: 12px 24px; font-family: 'Syncopate'; font-size: 10px; border-radius: 50px; box-shadow: 0 10px 20px rgba(0,0,0,0.3); }
        .btn-back { position: fixed; top: 20px; left: 300px; z-index: 1000; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 10px 20px; font-family: 'Syncopate'; font-size: 10px; border-radius: 5px; text-decoration: none; transition: 0.3s; display: flex; align-items: center; }
        .btn-back:hover { background: var(--crimson); border-color: var(--crimson); }
        
        .main-content { flex: 1; overflow-y: auto; height: 100vh; }
        .hero-section { position: relative; min-height: 100vh; display: flex; align-items: center; padding: 0 6rem; overflow: hidden; }
        .hero-section video { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0; }
        .hero-content { position: relative; z-index: 20; width: 100%; margin-top: 5rem; }
    </style>
</head>

<body class="flex h-screen overflow-hidden text-white">

   <?php include 'composants/sidebar.php'; ?>

    <main class="main-content">
        <div class="admin-badge"><i class="fas fa-edit mr-2"></i> PANEL ADMINISTRATION</div>

        <section class="hero-section">
            <video autoplay muted loop playsinline>
                <source src="<?= ASSETS_URL ?>/video/mercedes.mp4" type="video/mp4">
            </video>

            <div class="hero-content">
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
                    $btn_name = "btn_brand_".$i."_name";
                    $btn_desc = "btn_brand_".$i."_desc";
                ?>
                <div class="brand-card p-8 bg-white/5 backdrop-blur-sm border border-white/10 relative">
                    
                    <div class="mb-6">
                        <h3 id="<?php echo $id_name; ?>" contenteditable="true" onfocus="showBtn('<?php echo $btn_name; ?>')" class="edit-zone font-title text-2xl mb-2">
                            <?php echo getContent($id_name); ?>
                        </h3>
                        <button id="<?php echo $btn_name; ?>" onclick="saveText('<?php echo $id_name; ?>', '<?php echo $btn_name; ?>')" class="btn-save bg-green-600 px-4 py-2 rounded text-xs font-bold uppercase tracking-widest">
                            <i class="fas fa-save mr-2"></i> Enregistrer le nom
                        </button>
                    </div>
                    
                    <div>
                        <p id="<?php echo $id_desc; ?>" contenteditable="true" onfocus="showBtn('<?php echo $btn_desc; ?>')" class="edit-zone text-gray-300 text-lg leading-relaxed mb-2">
                            <?php echo getContent($id_desc); ?>
                        </p>
                        <button id="<?php echo $btn_desc; ?>" onclick="saveText('<?php echo $id_desc; ?>', '<?php echo $btn_desc; ?>')" class="btn-save bg-green-600 px-4 py-2 rounded text-xs font-bold uppercase tracking-widest">
                            <i class="fas fa-save mr-2"></i> Enregistrer la description
                        </button>
                    </div>

                </div>
                <?php endfor; ?>

            </div>
        </section>
    </main>

    <script>
        function showBtn(id) {
            document.getElementById(id).style.display = "block";
        }

        function saveText(elementId, btnId) {
            const el = document.getElementById(elementId);
            const content = (elementId === 'hero_title') ? el.innerHTML : el.innerText;
            executeUpload(elementId, content, btnId);
        }

        function executeUpload(id, content, btnId) {
            let formData = new FormData();
            formData.append('id', id);
            formData.append('content', content);

            fetch('../updatehome.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                if(data.trim() === "Succès") {
                    if(btnId) {
                        document.getElementById(btnId).style.display = "none";
                        const target = document.getElementById(id);
                        target.style.transition = "color 0.3s";
                        target.style.color = "#22c55e"; 
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