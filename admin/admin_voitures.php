<?php require_once '../config.php'; ?>
<?php
requireAdmin();

/** @var PDO $db */

try {
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";
    $adminName = $adminFullName;
} catch (PDOException $e) {
    $adminFullName = "Admin";
    $adminName = "Admin";
}

$currentPage = basename($_SERVER['PHP_SELF']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];
    try {
        if ($action === 'update_field') {
            $id = intval($_POST['id_voiture']);
            $field = $_POST['field'];
            $value = $_POST['value'];
            $allowed = ['modele','prix','annee','etat','type','kilometrage','description'];
            if (!in_array($field, $allowed)) { echo json_encode(['success'=>false,'message'=>'Champ interdit']); exit; }
            $db->prepare("UPDATE voiture SET $field = ? WHERE id_voiture = ?")->execute([$value, $id]);
            echo json_encode(['success'=>true]); exit;
        }
        if ($action === 'update_image') {
            $id = intval($_POST['id_voiture']);
            if (!empty($_FILES['image']['name'])) {
                $dir = '../image_url/voitures/';

                $oldImg = $db->prepare("SELECT image_url FROM voiture WHERE id_voiture = ?");
                $oldImg->execute([$id]);
                $oldImgName = $oldImg->fetchColumn();

                $newName = uploadAsWebp($_FILES['image'], $dir, 'car_' . $id);
                if ($newName) {
                    $db->prepare("UPDATE voiture SET image_url = ? WHERE id_voiture = ?")->execute([$newName, $id]);
                    if ($oldImgName && file_exists($dir . $oldImgName)) @unlink($dir . $oldImgName);
                    echo json_encode(['success'=>true,'image'=>$newName]);
                } else {
                    echo json_encode(['success'=>false,'message'=>'Conversion WebP échouée']);
                }
            } else { echo json_encode(['success'=>false,'message'=>'Aucun fichier']); }
            exit;
        }
        if ($action === 'archive_car') {
            $id = intval($_POST['id_voiture']);
            $db->prepare("UPDATE voiture SET archive = 1 WHERE id_voiture = ?")->execute([$id]);
            echo json_encode(['success'=>true]); exit;
        }
        if ($action === 'add_car') {
            $nomM = trim($_POST['nom_marque']);
            $stmt = $db->prepare("SELECT id_marque FROM marque WHERE nom_marque = ?");
            $stmt->execute([$nomM]);
            $m = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$m) {
                $db->prepare("INSERT INTO marque (nom_marque) VALUES (?)")->execute([$nomM]);
                $id_marque = $db->lastInsertId();
            } else { $id_marque = $m['id_marque']; }

            $imageName = 'default.webp';
            if (!empty($_FILES['image']['name'])) {
                $dir = '../image_url/voitures/';
                $uploaded = uploadAsWebp($_FILES['image'], $dir, 'car_new');
                if ($uploaded) $imageName = $uploaded;
            }

            $db->prepare("INSERT INTO voiture (modele,prix,annee,etat,type,kilometrage,description,image_url,id_marque,archive) VALUES (?,?,?,?,?,?,?,?,?,0)")
               ->execute([
                   $_POST['modele'],
                   $_POST['prix'],
                   $_POST['annee'],
                   $_POST['etat'],
                   $_POST['type'],
                   $_POST['kilometrage'] ?? 0,
                   $_POST['description'] ?? '',
                   $imageName,
                   $id_marque
               ]);
            $newId = $db->lastInsertId();
            echo json_encode(['success'=>true,'id'=>$newId,'image'=>$imageName,
                'modele'=>$_POST['modele'],'prix'=>$_POST['prix'],'annee'=>$_POST['annee'],
                'etat'=>$_POST['etat'],'type'=>$_POST['type'],'nom_marque'=>$nomM]);
            exit;
        }
        if ($action === 'add_brand') {
            $nomM = trim($_POST['nom_marque']);
            $stmt = $db->prepare("SELECT id_marque FROM marque WHERE nom_marque = ?");
            $stmt->execute([$nomM]);
            if ($stmt->fetch()) { echo json_encode(['success'=>false,'message'=>'Marque déjà existante']); exit; }
            $db->prepare("INSERT INTO marque (nom_marque) VALUES (?)")->execute([$nomM]);
            echo json_encode(['success'=>true,'id_marque'=>$db->lastInsertId(),'nom_marque'=>$nomM]);
            exit;
        }
        if ($action === 'update_brand') {
            $db->prepare("UPDATE marque SET nom_marque = ? WHERE id_marque = ?")->execute([$_POST['nom_marque'], intval($_POST['id_marque'])]);
            echo json_encode(['success'=>true]); exit;
        }
        if ($action === 'update_titre') {
            $val = trim($_POST['valeur']);
            $cle = trim($_POST['cle']);
            $allowed = ['titre_nos_modeles', 'titre_nos_modeles_rouge'];
            if (!in_array($cle, $allowed)) { echo json_encode(['success'=>false]); exit; }
            $db->prepare("INSERT INTO home_content (element_id, content) VALUES (?,?) ON DUPLICATE KEY UPDATE content=?")->execute([$cle,$val,$val]);
            echo json_encode(['success'=>true]); exit;
        }
    } catch (PDOException $e) {
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]); exit;
    }
}

try {
    $toutes_voitures = $db->query("SELECT v.*, m.nom_marque, m.id_marque FROM voiture v LEFT JOIN marque m ON v.id_marque = m.id_marque WHERE v.archive = 0 OR v.archive IS NULL ORDER BY m.nom_marque, v.modele")->fetchAll(PDO::FETCH_ASSOC);
    $voituresParMarque = [];
    foreach ($toutes_voitures as $v) {
        $nomM = !empty($v['nom_marque']) ? $v['nom_marque'] : 'Autres';
        $voituresParMarque[$nomM][] = $v;
    }
    $marquesAll = $db->query("SELECT * FROM marque ORDER BY nom_marque")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { die("Erreur : ".$e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Éditeur Live | SuperCar Admin</title>
<link rel="stylesheet" href="../style.css">
</head>
<body class="admin-body">

    <?php include 'composants/sidebar.php'; ?>

    <main class="admin-main">
        <div class="brand-add-bar">
            <span class="bab-label">NOUVELLE MARQUE</span>
            <input type="text" id="new-brand-input" class="bab-input" placeholder="Ex: Bugatti..." onkeydown="if(event.key==='Enter') addBrand()">
            <button class="bab-btn" onclick="addBrand()">+ CRÉER</button>
        </div>

        <div class="section-header">
            <h1>
                <span contenteditable="true" class="titre-editable" data-field="titre_nos_modeles" onblur="saveTitre(this)">NOS </span><span class="titre-span-red" contenteditable="true" data-field="titre_nos_modeles_rouge" onblur="saveTitre(this)">MODÈLES</span>
            </h1>
            <div class="title-bar"></div>
        </div>

        <?php foreach ($voituresParMarque as $nomMarque => $voitures):
            $idMarque = $voitures[0]['id_marque'] ?? 0;
            $rowId = "row-".strtolower(preg_replace('/[^a-zA-Z0-9]/','',$nomMarque));
        ?>
        <section class="brand-section" data-brand-section="<?php echo htmlspecialchars($nomMarque); ?>">
            <div class="brand-wrap">
                <span class="brand-name" contenteditable="true" data-id-marque="<?php echo $idMarque; ?>" data-original="<?php echo htmlspecialchars($nomMarque); ?>" onblur="saveBrand(this)"><?php echo htmlspecialchars($nomMarque); ?></span>
                <span class="brand-edit-icon">✎</span>
            </div>

            <div class="row-wrapper">
                <button class="nav-arrow prev" onclick="scrollBy3('<?php echo $rowId; ?>',-1)">❮</button>
                <div class="story-row" id="<?php echo $rowId; ?>">
                    <?php foreach ($voitures as $car): ?>
                    <div class="car-card" data-id="<?php echo $car['id_voiture']; ?>">

                        <div class="hover-specs">
                            <p class="spec-title">Fiche Technique</p>
                            <div class="spec-item">
                                <span>Année</span>
                                <span contenteditable="true" data-car-id="<?php echo $car['id_voiture']; ?>" data-field="annee" onblur="saveField(this)"><?php echo $car['annee']; ?></span>
                            </div>
                            <div class="spec-item">
                                <span>État</span>
                                <span contenteditable="true" data-car-id="<?php echo $car['id_voiture']; ?>" data-field="etat" onblur="saveField(this)"><?php echo htmlspecialchars($car['etat']); ?></span>
                            </div>
                            <div class="spec-item">
                                <span>Type</span>
                                <span contenteditable="true" data-car-id="<?php echo $car['id_voiture']; ?>" data-field="type" onblur="saveField(this)"><?php echo htmlspecialchars($car['type']); ?></span>
                            </div>
                            <div class="spec-item">
                                <span>Kilométrage</span>
                                <span contenteditable="true" data-car-id="<?php echo $car['id_voiture']; ?>" data-field="kilometrage" onblur="saveField(this)"><?php echo $car['kilometrage']; ?></span>
                            </div>
                            <p class="spec-desc"
                               contenteditable="true"
                               data-car-id="<?php echo $car['id_voiture']; ?>"
                               data-field="description"
                               onblur="saveField(this)">
                                <?= htmlspecialchars($car['description'] ?? 'Ajouter une description...') ?>
                            </p>
                        </div>

                        <div class="img-wrap">
                            <img src="<?= UPLOAD_URL ?>/voitures/<?php echo htmlspecialchars($car['image_url']); ?>" id="img-<?php echo $car['id_voiture']; ?>" alt="<?php echo htmlspecialchars($car['modele']); ?>">

                            <div class="img-overlay">
                                <label for="file-<?php echo $car['id_voiture']; ?>" class="img-overlay-btn" title="Changer la photo">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                        <circle cx="12" cy="13" r="4"></circle>
                                    </svg>
                                </label>
                                <input type="file" id="file-<?php echo $car['id_voiture']; ?>" class="hidden" accept="image/*" data-car-id="<?php echo $car['id_voiture']; ?>" onchange="uploadImg(this)">
                            </div>
                        </div>

                        <div class="car-info">
                            <h3>
                                <span contenteditable="true" data-car-id="<?php echo $car['id_voiture']; ?>" data-field="modele" onblur="saveField(this)"><?php echo htmlspecialchars($car['modele']); ?></span>
                            </h3>
                            <div class="car-price-row">
                                <div class="car-price">
                                    <span contenteditable="true" data-car-id="<?php echo $car['id_voiture']; ?>" data-field="prix" onblur="saveField(this)"><?php echo number_format($car['prix'],0,'.', ' '); ?></span> Rs
                                </div>
                                <button class="archive-btn" onclick="confirmArchive(<?php echo $car['id_voiture']; ?>,'<?php echo htmlspecialchars(addslashes($car['modele'])); ?>')" title="Archiver ce véhicule">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="21 8 21 21 3 21 3 8"></polyline>
                                        <rect x="1" y="3" width="22" height="5"></rect>
                                        <line x1="10" y1="12" x2="14" y2="12"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="add-card" onclick="openAddModal('<?php echo htmlspecialchars($nomMarque); ?>','<?php echo $rowId; ?>')">
                        <div class="add-circle">+</div>
                        <div class="add-label">AJOUTER<br><?php echo htmlspecialchars($nomMarque); ?></div>
                    </div>
                </div>
                <button class="nav-arrow next" onclick="scrollBy3('<?php echo $rowId; ?>',1)">❯</button>
            </div>
        </section>
        <?php endforeach; ?>

        <!-- Modal ajout -->
        <div class="modal-bg" id="add-modal">
            <div class="modal-box-admin">
                <div class="modal-head">
                    <h3 id="modal-title">AJOUTER UN VÉHICULE</h3>
                    <button class="modal-close" onclick="closeModal()">✕</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="m-brand">
                    <input type="hidden" id="m-row">
                    <div class="form-group">
                        <label>Marque</label>
                        <select id="m-marque" class="form-select">
                            <?php foreach ($marquesAll as $mk): ?>
                            <option value="<?php echo htmlspecialchars($mk['nom_marque']); ?>"><?php echo htmlspecialchars($mk['nom_marque']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Modèle</label><input type="text" id="m-modele" class="form-input"></div>
                    <div class="form-row">
                        <div class="form-group"><label>Prix (Rs)</label><input type="number" id="m-prix" class="form-input"></div>
                        <div class="form-group"><label>Année</label><input type="number" id="m-annee" class="form-input" value="<?php echo date('Y'); ?>"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>État</label>
                            <select id="m-etat" class="form-select"><option>Neuf</option><option>Occasion</option><option>Démonstration</option></select>
                        </div>
                        <div class="form-group"><label>Type</label>
                            <select id="m-type" class="form-select"><option>Coupé</option><option>Berline</option><option>SUV</option><option>Cabriolet</option><option>Break</option><option>Citadine</option></select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Kilométrage</label>
                            <input type="number" id="m-kilometrage" class="form-input" value="0">
                        </div>
                        <div class="form-group"><label>Description</label>
                            <input type="text" id="m-description" class="form-input" placeholder="Courte description...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Photo</label>
                        <div class="mimg-zone" onclick="document.getElementById('m-img-input').click()">
                            <img id="m-img-prev" src="" class="hidden">
                            <div id="m-img-ph">📷 Cliquez pour ajouter une photo</div>
                            <input type="file" id="m-img-input" class="hidden" accept="image/*" onchange="prevModalImg(this)">
                        </div>
                    </div>
                </div>
                <div class="modal-foot">
                    <button class="btn btn-outline" onclick="closeModal()">Annuler</button>
                    <button class="btn btn-primary" onclick="submitAdd()">AJOUTER</button>
                </div>
            </div>
        </div>

        <div id="confirm-ov" class="modal-bg">
            <div class="modal-box-admin">
                <h4>ARCHIVER CE VÉHICULE ?</h4>
                <p id="confirm-text"></p>
                <div class="modal-foot">
                    <button class="btn btn-outline" onclick="closeConfirm()">Annuler</button>
                    <button class="btn btn-primary" id="btn-yes">ARCHIVER</button>
                </div>
            </div>
        </div>

        <div id="toast" class="toast"><span id="tm"></span></div>
    </main>

<script>
function scrollBy3(id, d) {
    const c = document.getElementById(id);
    const card = c.querySelector('.car-card');
    if (card) c.scrollBy({ left: d * (card.offsetWidth + 25) * 3, behavior: 'smooth' });
}
let _tt;
function toast(msg, type='ok') {
    const t = document.getElementById('toast');
    t.className = 'toast show ' + type;
    document.getElementById('tm').textContent = msg;
    clearTimeout(_tt);
    if (type !== 'wait') _tt = setTimeout(()=>t.classList.remove('show'), 3000);
}
async function saveField(el) {
    const id = el.dataset.carId, field = el.dataset.field;
    let val = el.textContent.trim();

    if (field === 'prix')        val = val.replace(/[\s\u00a0]/g,'').replace('Rs','').trim();
    if (field === 'kilometrage') val = val.replace(/[\s\u00a0]/g,'').replace('km','').trim();
    if (!val && field !== 'description') return;

    const fd = new FormData();
    fd.append('action','update_field');
    fd.append('id_voiture', id);
    fd.append('field', field);
    fd.append('value', val);

    toast('Sauvegarde...','wait');
    const j = await fetch('admin_voitures.php',{method:'POST',body:fd}).then(r=>r.json());
    if (j.success) toast('Modifié ✓','ok'); else toast(j.message||'Erreur','err');
}
async function saveTitre(el) {
    const val = el.textContent.trim();
    if (!val) return;
    const fd = new FormData();
    fd.append('action','update_titre'); fd.append('cle', el.dataset.field); fd.append('valeur', val);
    await fetch('admin_voitures.php',{method:'POST',body:fd});
    toast('Titre sauvegardé ✓','ok');
}
async function saveBrand(el) {
    const id = el.dataset.idMarque, val = el.textContent.trim();
    if (!val || val === el.dataset.original) return;
    const fd = new FormData();
    fd.append('action','update_brand'); fd.append('id_marque',id); fd.append('nom_marque',val);
    const j = await fetch('admin_voitures.php',{method:'POST',body:fd}).then(r=>r.json());
    if (j.success) { el.dataset.original = val; toast('Marque renommée ✓','ok'); }
    else { el.textContent = el.dataset.original; toast('Erreur','err'); }
}
async function uploadImg(input) {
    if (!input.files.length) return;
    const id = input.dataset.carId;
    const fd = new FormData();
    fd.append('action','update_image'); fd.append('id_voiture',id); fd.append('image',input.files[0]);
    toast('Envoi...','wait');
    const j = await fetch('admin_voitures.php',{method:'POST',body:fd}).then(r=>r.json());
    if (j.success) { document.getElementById('img-'+id).src = '<?= UPLOAD_URL ?>/voitures/'+j.image+'?t='+Date.now(); toast('Photo mise à jour ✓','ok'); }
    else toast(j.message||'Erreur','err');
}
function openAddModal(brand, rowId) {
    document.getElementById('modal-title').textContent = 'AJOUTER · '+brand.toUpperCase();
    document.getElementById('m-brand').value = brand;
    document.getElementById('m-row').value = rowId;
    const sel = document.getElementById('m-marque');
    for (let o of sel.options) if (o.value === brand) { sel.value = brand; break; }
    document.getElementById('add-modal').classList.add('open');
}
function closeModal() { document.getElementById('add-modal').classList.remove('open'); }
function prevModalImg(input) {
    if (!input.files.length) return;
    const r = new FileReader();
    r.onload = e => {
        const img = document.getElementById('m-img-prev');
        img.src = e.target.result; img.classList.remove('hidden');
        document.getElementById('m-img-ph').classList.add('hidden');
    };
    r.readAsDataURL(input.files[0]);
}
async function submitAdd() {
    const modele = document.getElementById('m-modele').value.trim();
    const prix = document.getElementById('m-prix').value.trim();
    if (!modele || !prix) { toast('Modèle et prix requis','err'); return; }
    const fd = new FormData();
    fd.append('action','add_car');
    fd.append('nom_marque', document.getElementById('m-marque').value);
    fd.append('modele', modele); fd.append('prix', prix);
    fd.append('annee', document.getElementById('m-annee').value);
    fd.append('etat', document.getElementById('m-etat').value);
    fd.append('type', document.getElementById('m-type').value);
    fd.append('kilometrage', document.getElementById('m-kilometrage').value);
    fd.append('description', document.getElementById('m-description').value);
    const fi = document.getElementById('m-img-input');
    if (fi.files.length) fd.append('image', fi.files[0]);
    toast('Ajout...','wait');
    const j = await fetch('admin_voitures.php',{method:'POST',body:fd}).then(r=>r.json());
    if (j.success) { toast('Véhicule ajouté !','ok'); closeModal(); location.reload(); }
    else toast(j.message||'Erreur','err');
}
async function addBrand() {
    const input = document.getElementById('new-brand-input');
    const nom = input.value.trim();
    if (!nom) { toast('Saisissez un nom','err'); return; }
    const fd = new FormData();
    fd.append('action','add_brand'); fd.append('nom_marque', nom);
    const j = await fetch('admin_voitures.php',{method:'POST',body:fd}).then(r=>r.json());
    if (j.success) { toast('Marque créée !','ok'); location.reload(); }
    else toast(j.message||'Erreur','err');
}
function confirmArchive(id, modele) {
    document.getElementById('confirm-text').textContent = `"${modele}" sera archivé.`;
    document.getElementById('confirm-ov').classList.add('open');
    document.getElementById('btn-yes').onclick = async () => {
        closeConfirm();
        const fd = new FormData();
        fd.append('action','archive_car'); fd.append('id_voiture', id);
        const j = await fetch('admin_voitures.php',{method:'POST',body:fd}).then(r=>r.json());
        if (j.success) { location.reload(); }
    };
}
function closeConfirm() { document.getElementById('confirm-ov').classList.remove('open'); }

document.querySelectorAll('.story-row .car-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        const rect = this.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const specsWidth = 280;

        if (rect.right + specsWidth > viewportWidth) {
            this.classList.add('specs-left');
        } else {
            this.classList.remove('specs-left');
        }
    });
});
</script>
</body>
</html>