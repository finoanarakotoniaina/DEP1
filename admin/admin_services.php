<?php
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn;

function getServiceContent($id) {
    global $db;
    $stmt = $db->prepare("SELECT content FROM services_content WHERE element_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ? $row['content'] : "Texte par défaut";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && isset($_POST['content'])) {
        $stmt = $db->prepare("REPLACE INTO services_content (element_id, content) VALUES (?, ?)");
        echo $stmt->execute([$_POST['id'], $_POST['content']]) ? "Succès" : "Erreur BDD";
        exit;
    }
    if (isset($_POST['id']) && isset($_FILES['image'])) {
        $uploadDir = '../assets/services/';

        $oldStmt = $db->prepare("SELECT content FROM services_content WHERE element_id = ?");
        $oldStmt->execute([$_POST['id']]);
        $oldImg = $oldStmt->fetchColumn();

        $newFile = uploadAsWebp($_FILES['image'], $uploadDir, 'service');
        if ($newFile) {
            $stmt = $db->prepare("REPLACE INTO services_content (element_id, content) VALUES (?, ?)");
            $stmt->execute([$_POST['id'], $newFile]);
            if ($oldImg && file_exists($uploadDir . $oldImg)) @unlink($uploadDir . $oldImg);
            echo json_encode(['success' => true, 'path' => $newFile]);
        } else {
            echo json_encode(['success' => false, 'message' => "Conversion WebP échouée"]);
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
    <title>ADMIN | Services SuperCar</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        html, body.admin-body {
            margin: 0 !important;
            padding: 0 !important;
        }
        .admin-main {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        .admin-main .hero,
        .admin-main > .hero {
            margin-top: 0 !important;
            padding-top: 0 !important;
            min-height: auto !important;
        }
        .hero-content {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        .hero-content h1,
        .hero-content .edit-zone {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        .hero-content .edit-block:first-of-type,
        .hero-content > .edit-block:first-child {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        .admin-main .hero + .section,
        .admin-main section.hero + section.section,
        .admin-main > .section {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        .admin-main .section .section-header,
        .admin-main .section-header {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        .section-header h2 {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        .services-grid {
            margin-top: 0 !important;
        }
    </style>
</head>

<body class="admin-body">

    <?php include 'composants/sidebar.php'; ?>

    <main class="admin-main">

        <section class="hero">
            <div class="hero-content">
                
                <div class="edit-block">
                    <h1 id="serv_hero_title" contenteditable="true" onfocus="showBtn('btn_serv_title')" class="edit-zone font-title">
                        <?php echo getServiceContent('serv_hero_title'); ?>
                    </h1>
                    <button id="btn_serv_title" onclick="saveText('serv_hero_title', 'btn_serv_title')" class="btn-save">✓ Sauvegarder</button>
                </div>

                <div class="edit-block">
                    <p id="serv_hero_desc" contenteditable="true" onfocus="showBtn('btn_serv_desc')" class="edit-zone hero-desc">
                        <?php echo getServiceContent('serv_hero_desc'); ?>
                    </p>
                    <button id="btn_serv_desc" onclick="saveText('serv_hero_desc', 'btn_serv_desc')" class="btn-save">✓ Sauvegarder</button>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="section-header">
                <h2 class="font-title">Notre <span class="title-accent">Expertise</span></h2>
                <div class="title-bar"></div>
            </div>

            <div class="services-grid">
                <?php for($i=1; $i<=4; $i++):
                    $id_img = "service_".$i."_img";
                    $id_title = "service_".$i."_title";
                    $id_text = "service_".$i."_text";
                    $btn_title = "btn_s_title_".$i;
                    $btn_text = "btn_s_text_".$i;
                ?>
                <div class="service-card">
                    <div class="service-img-wrap">
                        <img id="img_preview_<?php echo $i; ?>" src="<?= ASSETS_URL ?>/services/<?php echo getServiceContent($id_img); ?>" class="service-img">
                        <label for="input_<?php echo $id_img; ?>" class="service-img-overlay">📷 Changer</label>
                        <input type="file" id="input_<?php echo $id_img; ?>" class="hidden" accept="image/*"
                                onchange="uploadImage('<?php echo $id_img; ?>', this, 'img_preview_<?php echo $i; ?>')">
                    </div>
                    
                    <span class="service-num">0<?php echo $i; ?></span>
                    
                    <div class="edit-block">
                        <h3 id="<?php echo $id_title; ?>" contenteditable="true" onfocus="showBtn('<?php echo $btn_title; ?>')" class="edit-zone font-title">
                            <?php echo getServiceContent($id_title); ?>
                        </h3>
                        <button id="<?php echo $btn_title; ?>" onclick="saveText('<?php echo $id_title; ?>', '<?php echo $btn_title; ?>')" class="btn-save">OK</button>
                    </div>

                    <div class="edit-block">
                        <p id="<?php echo $id_text; ?>" contenteditable="true" onfocus="showBtn('<?php echo $btn_text; ?>')" class="edit-zone">
                            <?php echo getServiceContent($id_text); ?>
                        </p>
                        <button id="<?php echo $btn_text; ?>" onclick="saveText('<?php echo $id_text; ?>', '<?php echo $btn_text; ?>')" class="btn-save">OK</button>
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
            let formData = new FormData();
            formData.append('id', elementId);
            formData.append('content', el.innerHTML);
            fetch('admin_services.php', { method: 'POST', body: formData })
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
                fetch('admin_services.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(previewId).src = '<?= ASSETS_URL ?>/services/' + data.path + '?t=' + new Date().getTime();
                        alert("Image mise à jour !");
                    } else { alert("Erreur : " + data.message); }
                })
                .catch(err => alert("Erreur lors de l'envoi."));
            }
        }
    </script>
</body>
</html>