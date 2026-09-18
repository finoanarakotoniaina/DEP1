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
    <link rel="stylesheet" href="../style.css">
</head>

<body class="admin-body">

   <?php include 'composants/sidebar.php'; ?>

    <main class="admin-main">
        <div class="admin-badge">✎ PANEL ADMINISTRATION</div>

        <section class="hero">
            <div class="hero-bg">
                <video autoplay muted loop playsinline>
                    <source src="<?= ASSETS_URL ?>/video/mercedes.mp4" type="video/mp4">
                </video>
                <div class="hero-video-overlay"></div>
            </div>

            <div class="hero-content">
                <div class="edit-block">
                    <h1 id="hero_title" contenteditable="true" onfocus="showBtn('btn_title')" class="edit-zone font-title">
                        <?php echo getContent('hero_title'); ?>
                    </h1>
                    <button id="btn_title" onclick="saveText('hero_title', 'btn_title')" class="btn-save">
                        ✓ Enregistrer le titre
                    </button>
                </div>

                <div class="edit-block">
                    <p id="hero_subtitle" contenteditable="true" onfocus="showBtn('btn_subtitle')" class="edit-zone hero-desc">
                        <?php echo getContent('hero_subtitle'); ?>
                    </p>
                    <button id="btn_subtitle" onclick="saveText('hero_subtitle', 'btn_subtitle')" class="btn-save">
                        ✓ Enregistrer le texte
                    </button>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="brands-container">
                <?php for($i=1; $i<=4; $i++): 
                    $id_name = "brand_".$i."_name";
                    $id_desc = "brand_".$i."_desc";
                    $btn_name = "btn_brand_".$i."_name";
                    $btn_desc = "btn_brand_".$i."_desc";
                ?>
                <div class="brand-card">
                    <div class="edit-block">
                        <h3 id="<?php echo $id_name; ?>" contenteditable="true" onfocus="showBtn('<?php echo $btn_name; ?>')" class="edit-zone font-title">
                            <?php echo getContent($id_name); ?>
                        </h3>
                        <button id="<?php echo $btn_name; ?>" onclick="saveText('<?php echo $id_name; ?>', '<?php echo $btn_name; ?>')" class="btn-save">
                            ✓ Enregistrer le nom
                        </button>
                    </div>
                    
                    <div class="edit-block">
                        <p id="<?php echo $id_desc; ?>" contenteditable="true" onfocus="showBtn('<?php echo $btn_desc; ?>')" class="edit-zone">
                            <?php echo getContent($id_desc); ?>
                        </p>
                        <button id="<?php echo $btn_desc; ?>" onclick="saveText('<?php echo $id_desc; ?>', '<?php echo $btn_desc; ?>')" class="btn-save">
                            ✓ Enregistrer la description
                        </button>
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
            const content = (elementId === 'hero_title') ? el.innerHTML : el.innerText;
            executeUpload(elementId, content, btnId);
        }

        function executeUpload(id, content, btnId) {
            let formData = new FormData();
            formData.append('id', id);
            formData.append('content', content);

            fetch('../updatehome.php', { method: 'POST', body: formData })
            .then(res => res.text())
            .then(data => {
                if(data.trim() === "Succès") {
                    if(btnId) {
                        document.getElementById(btnId).style.display = "none";
                        const target = document.getElementById(id);
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