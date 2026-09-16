<?php require_once '../config.php'; ?>
<?php
$adminName = $_SESSION['admin_nom'] ?? $_SESSION['nom'] ?? 'Administrateur';

// --- TRAITEMENT AJAX ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];
    try {
        if ($action === 'update_field') {
            $id    = intval($_POST['id_voiture']);
            $field = $_POST['field'];
            $value = $_POST['value'];
            $allowed = ['modele','prix','annee','etat','type'];
            if (!in_array($field, $allowed)) { echo json_encode(['success'=>false,'message'=>'Champ interdit']); exit; }
            $db->prepare("UPDATE voiture SET $field = ? WHERE id_voiture = ?")->execute([$value, $id]);
            echo json_encode(['success'=>true]);
            exit;
        }
        if ($action === 'update_image') {
            $id = intval($_POST['id_voiture']);
            if (!empty($_FILES['image']['name'])) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $nm  = 'car_'.$id.'_'.time().'.'.$ext;
                $dir = '../image_url/voitures/';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                move_uploaded_file($_FILES['image']['tmp_name'], $dir.$nm);
                $db->prepare("UPDATE voiture SET image_url = ? WHERE id_voiture = ?")->execute([$nm, $id]);
                echo json_encode(['success'=>true,'image'=>$nm]);
            } else { echo json_encode(['success'=>false,'message'=>'Aucun fichier']); }
            exit;
        }
        if ($action === 'archive_car') {
            $id = intval($_POST['id_voiture']);
            $db->prepare("UPDATE voiture SET archive = 1 WHERE id_voiture = ?")->execute([$id]);
            echo json_encode(['success'=>true]);
            exit;
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
            $imageName = 'default.jpg';
            if (!empty($_FILES['image']['name'])) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $imageName = 'car_new_'.time().'.'.$ext;
                $dir = '../image_url/voitures/';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                move_uploaded_file($_FILES['image']['tmp_name'], $dir.$imageName);
            }
            $db->prepare("INSERT INTO voiture (modele,prix,annee,etat,type,image_url,id_marque,archive) VALUES (?,?,?,?,?,?,?,0)")
               ->execute([$_POST['modele'],$_POST['prix'],$_POST['annee'],$_POST['etat'],$_POST['type'],$imageName,$id_marque]);
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
            echo json_encode(['success'=>true]);
            exit;
        }
        if ($action === 'update_titre') {
            $val = trim($_POST['valeur']);
            $cle = trim($_POST['cle']);
            $allowed_cles = ['titre_nos_modeles'];
            if (!in_array($cle, $allowed_cles)) { echo json_encode(['success'=>false]); exit; }
            $db->prepare("INSERT INTO config (cle,valeur) VALUES (?,?) ON DUPLICATE KEY UPDATE valeur=?")->execute([$cle,$val,$val]);
            echo json_encode(['success'=>true]);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
        exit;
    }
}

// --- CHARGEMENT ---
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
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;400;500;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
 <link rel="stylesheet" href="../style.css">
<style>
#admin-bar {
    position: fixed; top: 0; left: 0; right: 0; height: 54px;
    background: linear-gradient(90deg, #060e1e 0%, #0b1729 60%, #060e1e 100%);
    border-bottom: 2px solid var(--crimson);
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 22px; z-index: 9999;
    box-shadow: 0 4px 30px rgba(193,18,31,0.2);
}
.abl { display:flex; align-items:center; gap:16px; }
.abr { display:flex; align-items:center; gap:8px; }
.ab-logo { font-family:'Syncopate',sans-serif; font-size:14px; font-weight:700; color:#fff; letter-spacing:0.08em; }
.ab-logo span { color:var(--crimson); }
.ab-sep { width:1px; height:22px; background:rgba(255,255,255,0.1); }
.ab-user {
    display:flex; align-items:center; gap:9px;
    background: rgba(193,18,31,0.1); border:1px solid rgba(193,18,31,0.25);
    border-radius:8px; padding:5px 12px;
}
.ab-av {
    width:24px; height:24px; border-radius:50%; background:var(--crimson);
    display:flex; align-items:center; justify-content:center;
    font-family:'Syncopate',sans-serif; font-size:10px; font-weight:700; color:#fff;
}
.ab-uname { font-family:'Plus Jakarta Sans',sans-serif; font-size:12px; color:rgba(255,255,255,0.8); font-weight:500; }
.ab-live {
    display:flex; align-items:center; gap:7px;
    font-family:'Syncopate',sans-serif; font-size:8px; letter-spacing:0.18em; color:rgba(255,255,255,0.4);
}
.ab-live-dot { width:6px; height:6px; border-radius:50%; background:#22c55e; box-shadow:0 0 8px #22c55e; animation:blink 1.5s infinite; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.25} }
.ab-btn {
    display:flex; align-items:center; gap:6px; padding:7px 14px;
    border-radius:7px; border:none; cursor:pointer;
    font-family:'Syncopate',sans-serif; font-size:8px; letter-spacing:0.13em;
    text-decoration:none; transition:all .2s;
}
.ab-ghost { background:rgba(26,44,80,0.6); color:rgba(255,255,255,0.65); border:1px solid rgba(26,44,80,0.9); }
.ab-ghost:hover { background:rgba(26,44,80,.95); color:#fff; }

body { padding-top: 54px; }

#brand-add-bar {
    position: fixed; top: 54px; left: 0; right: 0; z-index: 9998;
    background: rgba(6,14,30,0.97);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(193,18,31,0.2);
    padding: 10px 24px;
    display: flex; align-items: center; gap: 14px;
}
.bab-label {
    font-family:'Syncopate',sans-serif; font-size:8px; letter-spacing:.2em;
    color:rgba(255,255,255,.35); white-space:nowrap;
}
.bab-label i { color:var(--crimson); margin-right:6px; }
.bab-input {
    background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1);
    border-radius:8px; padding:7px 14px; color:#fff;
    font-family:'Plus Jakarta Sans',sans-serif; font-size:12px;
    outline:none; width:220px; transition:border-color .2s;
}
.bab-input:focus { border-color:var(--crimson); box-shadow:0 0 0 3px rgba(193,18,31,.08); }
.bab-btn {
    background:var(--crimson); color:#fff; border:none; cursor:pointer;
    padding:7px 18px; border-radius:8px;
    font-family:'Syncopate',sans-serif; font-size:8px; letter-spacing:.13em;
    display:flex; align-items:center; gap:7px; transition:all .2s; white-space:nowrap;
}
.bab-btn:hover { background:#e01a2a; transform:translateY(-1px); }

main { padding-top: calc(40px + 44px) !important; }

.row-wrapper { position:relative; padding:0 40px; margin-bottom:60px; overflow:visible!important; }
.story-row   { display:flex; overflow-x:auto; scroll-behavior:smooth; gap:25px; padding:20px 10px; scrollbar-width:none; overflow-y:visible!important; scroll-snap-type:x mandatory; }
.story-row::-webkit-scrollbar { display:none; }
.car-card    { min-width:calc(33.333% - 20px); flex-shrink:0; position:relative; transition:transform .3s; z-index:10; scroll-snap-align:start; }
.nav-btn     { position:absolute; top:50%; transform:translateY(-50%); width:35px; height:35px; border-radius:50%; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.15); color:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; z-index:100; backdrop-filter:blur(5px); transition:.3s; font-size:12px; }
.nav-btn:hover { background:#fff; color:#000; }
.btn-prev { left:0; } .btn-next { right:0; }
.spec-item { display:flex; justify-content:space-between; border-bottom:1px solid rgba(255,255,255,.2); padding:12px 0; font-size:11px; text-transform:uppercase; }
.hover-specs { position:absolute; top:0; width:280px; height:100%; background:var(--crimson); color:#fff; padding:30px; border-radius:24px; opacity:0; visibility:hidden; transition:all .4s; display:flex; flex-direction:column; justify-content:center; pointer-events:none; }
.car-card .hover-specs { left:105%; }
.car-card:nth-child(3n) .hover-specs { left:auto!important; right:105%!important; }

.car-card:hover { transform:scale(1.03); z-index:200; }
.car-card:hover .edit-ring { opacity:1; box-shadow:0 0 0 2px var(--crimson), 0 0 28px rgba(193,18,31,.22); }
.edit-ring { position:absolute; inset:0; border-radius:24px; pointer-events:none; opacity:0; transition:all .25s; z-index:20; }

.img-wrap { position:relative; cursor:pointer; }
.img-ov {
    position:absolute; inset:0; background:rgba(193,18,31,0);
    border-radius:24px 24px 0 0; display:flex; align-items:center; justify-content:center;
    opacity:0; transition:all .25s; z-index:15;
}
.img-wrap:hover .img-ov { opacity:1; background:rgba(193,18,31,.55); }
.img-ov-inner { display:flex; flex-direction:column; align-items:center; gap:6px; color:#fff; }
.img-ov-inner i { font-size:22px; }
.img-ov-inner span { font-family:'Syncopate',sans-serif; font-size:8px; letter-spacing:.2em; }
.img-file { position:absolute; inset:0; opacity:0; cursor:pointer; z-index:16; }

[contenteditable="true"] {
    outline:none; border-bottom:1px dashed rgba(193,18,31,.45);
    cursor:text; transition:border-color .2s; min-width:10px; display:inline-block;
}
[contenteditable="true"]:hover { border-bottom-color:rgba(193,18,31,.85); }
[contenteditable="true"]:focus {
    border-bottom:2px solid var(--crimson);
    background:rgba(193,18,31,.06); border-radius:3px; padding:0 3px;
}

.titre-editable {
    font-family:'Syncopate',sans-serif;
    font-size: clamp(1.8rem, 5vw, 3rem);
    font-weight:700; text-transform:uppercase; letter-spacing:-0.02em;
    border-bottom: 1px dashed rgba(193,18,31,.3) !important;
    cursor:text;
}
.titre-editable:focus { background: rgba(193,18,31,.04); border-radius:4px; }
.titre-span-red { color:var(--crimson); }

.del-btn {
    position:absolute; top:10px; right:10px; z-index:30;
    width:30px; height:30px; border-radius:7px;
    background:rgba(5,0,0,.75); border:1px solid rgba(193,18,31,.35);
    color:rgba(255,255,255,.5); display:flex; align-items:center; justify-content:center;
    cursor:pointer; font-size:11px; opacity:0; transition:all .2s; backdrop-filter:blur(8px);
}
.car-card:hover .del-btn { opacity:1; color:#fff; border-color:var(--crimson); }
.del-btn:hover { background:var(--crimson)!important; }

.brand-wrap { display:flex; align-items:center; gap:10px; padding:0 6rem; margin-bottom:8px; }
.brand-name {
    font-family:'Syncopate',sans-serif; font-size:11px; color:#6b7280;
    letter-spacing:.3em; text-transform:uppercase;
}

.add-card {
    min-width:calc(33.333% - 20px); flex-shrink:0; border-radius:24px; min-height:360px;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:14px; cursor:pointer; scroll-snap-align:start;
    background:rgba(193,18,31,.04); border:2px dashed rgba(193,18,31,.22); transition:all .3s;
}
.add-card:hover { background:rgba(193,18,31,.09); border-color:rgba(193,18,31,.65); transform:scale(1.02); }
.add-circle {
    width:52px; height:52px; border-radius:50%; background:var(--crimson); color:#fff;
    display:flex; align-items:center; justify-content:center;
    font-size:22px; transition:all .3s; box-shadow:0 8px 24px rgba(193,18,31,.35);
}
.add-card:hover .add-circle { transform:scale(1.12); box-shadow:0 12px 32px rgba(193,18,31,.55); }
.add-label { font-family:'Syncopate',sans-serif; font-size:9px; letter-spacing:.2em; color:rgba(193,18,31,.65); text-align:center; line-height:1.9; text-transform:uppercase; }

.modal-bg { position:fixed; inset:0; background:rgba(0,0,0,.9); backdrop-filter:blur(10px); z-index:10000; display:none; align-items:center; justify-content:center; padding:20px; }
.modal-bg.open { display:flex; }
.mbox { background:#0b0b0b; border:1px solid rgba(193,18,31,.22); border-radius:22px; width:100%; max-width:620px; max-height:90vh; overflow-y:auto; box-shadow:0 40px 80px rgba(0,0,0,.6); }
.mhead { padding:24px 28px 16px; border-bottom:1px solid rgba(255,255,255,.06); display:flex; align-items:center; justify-content:space-between; }
.mhead h3 { font-family:'Syncopate',sans-serif; font-size:11px; letter-spacing:.15em; color:#fff; }
.mclose { width:30px; height:30px; border-radius:7px; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.09); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .2s; }
.mclose:hover { background:rgba(255,255,255,.12); }
.mbody { padding:24px 28px; }
.mfoot { padding:16px 28px 24px; border-top:1px solid rgba(255,255,255,.06); display:flex; gap:10px; justify-content:flex-end; }
.fg { margin-bottom:16px; }
.fl { display:block; font-family:'Syncopate',sans-serif; font-size:8px; letter-spacing:.2em; color:rgba(255,255,255,.38); margin-bottom:7px; text-transform:uppercase; }
.inp { width:100%; background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08); border-radius:10px; padding:10px 14px; color:#fff; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; outline:none; transition:border-color .2s; }
.inp:focus { border-color:var(--crimson); box-shadow:0 0 0 3px rgba(193,18,31,.07); }
.fgrid { display:grid; grid-template-columns:1fr 1fr; gap:13px; }
.mimg-zone { border:2px dashed rgba(255,255,255,.09); border-radius:12px; padding:16px; text-align:center; cursor:pointer; transition:all .2s; position:relative; overflow:hidden; }
.mimg-zone:hover { border-color:var(--crimson); background:rgba(193,18,31,.03); }
.mimg-zone img { width:100%; height:130px; object-fit:cover; border-radius:8px; display:none; }
.mimg-zone .mph { color:rgba(255,255,255,.22); font-size:12px; font-family:'Plus Jakarta Sans'; }
.mimg-zone .mph i { font-size:24px; display:block; margin-bottom:6px; }
.mimg-zone input { position:absolute; inset:0; opacity:0; cursor:pointer; }
.btn-cx { background:rgba(255,255,255,.06); color:rgba(255,255,255,.65); border:1px solid rgba(255,255,255,.1); padding:9px 18px; border-radius:7px; font-family:'Syncopate',sans-serif; font-size:8px; cursor:pointer; transition:all .2s; }
.btn-cx:hover { background:rgba(255,255,255,.1); }
.btn-sv { background:var(--crimson); color:#fff; border:none; padding:9px 20px; border-radius:7px; font-family:'Syncopate',sans-serif; font-size:8px; cursor:pointer; display:flex; align-items:center; gap:6px; transition:all .2s; }
.btn-sv:hover { background:#e01a2a; }

#confirm-ov { position:fixed; inset:0; background:rgba(0,0,0,.88); backdrop-filter:blur(6px); z-index:99999; display:none; align-items:center; justify-content:center; }
#confirm-ov.open { display:flex; }
.cbox { background:#0d0303; border:1px solid rgba(193,18,31,.4); border-radius:16px; padding:32px; max-width:320px; text-align:center; }
.cbox .ci { font-size:28px; color:var(--crimson); margin-bottom:13px; }
.cbox h4 { font-family:'Syncopate',sans-serif; font-size:10px; letter-spacing:.15em; margin-bottom:9px; }
.cbox p  { color:rgba(255,255,255,.4); font-family:'Plus Jakarta Sans'; font-size:12px; margin-bottom:20px; }
.cact { display:flex; gap:10px; justify-content:center; }
.btn-arc { background:var(--crimson); color:#fff; border:none; padding:9px 20px; border-radius:7px; font-family:'Syncopate',sans-serif; font-size:8px; cursor:pointer; display:flex; align-items:center; gap:6px; }
.btn-no  { background:rgba(255,255,255,.07); color:#fff; border:1px solid rgba(255,255,255,.12); padding:9px 20px; border-radius:7px; font-family:'Syncopate',sans-serif; font-size:8px; cursor:pointer; }

#toast { position:fixed; bottom:20px; right:20px; z-index:99999; background:#111; border:1px solid rgba(255,255,255,.09); border-radius:11px; padding:12px 17px; display:flex; align-items:center; gap:9px; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; color:#fff; min-width:230px; transform:translateY(18px); opacity:0; transition:all .3s; box-shadow:0 20px 40px rgba(0,0,0,.4); }
#toast.show { transform:translateY(0); opacity:1; }
#toast.ok   { border-color:rgba(34,197,94,.4); }
#toast.ok   .ti { color:#22c55e; font-size:15px; }
#toast.err  { border-color:rgba(193,18,31,.5); }
#toast.err  .ti { color:var(--crimson); font-size:15px; }
#toast.wait .ti { color:rgba(255,255,255,.4); animation:spin 1s linear infinite; display:inline-block; font-size:15px; }
@keyframes spin { to { transform:rotate(360deg); } }

.nav-link.active { color:var(--crimson) !important; font-weight:700; }
</style>
</head>
<body class="split-bg text-white">

<div id="admin-bar">
    <div class="abl">
        <div class="ab-logo">SUPER<span>CAR</span></div>
        <div class="ab-sep"></div>
        <div class="ab-user">
            <div class="ab-av"><?php echo strtoupper(mb_substr($adminName,0,1)); ?></div>
            <span class="ab-uname"><?php echo htmlspecialchars($adminName); ?></span>
        </div>
        <div class="ab-live"><div class="ab-live-dot"></div>LIVE EDITOR</div>
    </div>
    <div class="abr">
        <a href="tableaudebord.php" class="ab-btn ab-ghost"><i class="fas fa-gauge-high"></i> TABLEAU DE BORD</a>
        <a href="<?= BASE_URL ?>/client/voiture.php" target="_blank" class="ab-btn ab-ghost"><i class="fas fa-eye"></i> VUE CLIENT</a>
    </div>
</div>

<div id="brand-add-bar">
    <span class="bab-label"><i class="fas fa-layer-group"></i>NOUVELLE MARQUE</span>
    <input type="text" id="new-brand-input" class="bab-input" placeholder="Ex: Bugatti, Koenigsegg..." onkeydown="if(event.key==='Enter') addBrand()">
    <button class="bab-btn" onclick="addBrand()"><i class="fas fa-plus"></i> CRÉER</button>
</div>

<main class="pt-40" id="main-content">
    <div class="px-6 lg:px-24 mb-12">
        <h1 style="font-family:'Syncopate';font-size:clamp(1.8rem,5vw,3rem);font-weight:700;line-height:1;letter-spacing:-0.02em;text-transform:uppercase">
            <span
                contenteditable="true"
                class="titre-editable"
                data-field="titre_nos_modeles"
                onblur="saveTitre(this)"
                title="Cliquez pour modifier le titre"
            >NOS </span><span class="titre-span-red"
                contenteditable="true"
                data-field="titre_nos_modeles_rouge"
                onblur="saveTitre(this)"
                title="Cliquez pour modifier"
            >MODÈLES</span>
        </h1>
        <div class="w-20 h-1 mt-4" style="background:var(--crimson)"></div>
        <p class="mt-4 text-[10px] font-mono tracking-widest" style="color:rgba(193,18,31,.5)">
            <i class="fas fa-pen mr-1"></i>TEXTE · <i class="fas fa-camera mr-1 ml-2"></i>PHOTO · <i class="fas fa-plus-circle mr-1 ml-2"></i>AJOUTER
        </p>
    </div>

    <?php foreach ($voituresParMarque as $nomMarque => $voitures):
        $idMarque = $voitures[0]['id_marque'] ?? 0;
        $rowId = "row-".strtolower(preg_replace('/[^a-zA-Z0-9]/','',$nomMarque));
    ?>
    <section class="mb-10" data-brand-section="<?php echo htmlspecialchars($nomMarque); ?>">
        <div class="brand-wrap">
            <span class="brand-name"
                  contenteditable="true"
                  data-id-marque="<?php echo $idMarque; ?>"
                  data-original="<?php echo htmlspecialchars($nomMarque); ?>"
                  onblur="saveBrand(this)"
                  title="Cliquez pour renommer la marque"
            ><?php echo htmlspecialchars($nomMarque); ?></span>
            <span style="font-family:'Syncopate',sans-serif;font-size:8px;color:rgba(255,255,255,.13);letter-spacing:.15em">✎</span>
        </div>

        <div class="row-wrapper">
            <button class="nav-btn btn-prev" onclick="scrollBy3('<?php echo $rowId; ?>',-1)">❮</button>
            <div class="story-row" id="<?php echo $rowId; ?>">

                <?php foreach ($voitures as $car): ?>
                <div class="car-card"
                     data-id="<?php echo $car['id_voiture']; ?>"
                     data-modele="<?php echo htmlspecialchars($car['modele']); ?>"
                     data-prix="<?php echo $car['prix']; ?>"
                     data-annee="<?php echo $car['annee']; ?>"
                     data-etat="<?php echo htmlspecialchars($car['etat']); ?>"
                     data-type="<?php echo htmlspecialchars($car['type']); ?>"
                     data-image="<?php echo htmlspecialchars($car['image_url']); ?>">

                    <div class="edit-ring"></div>

                    <button class="del-btn"
                            onclick="confirmArchive(<?php echo $car['id_voiture']; ?>,'<?php echo htmlspecialchars(addslashes($car['modele'])); ?>')"
                            title="Archiver ce véhicule">
                        <i class="fas fa-box-archive"></i>
                    </button>

                    <div class="hover-specs">
                        <p class="font-title text-[10px] mb-4 border-b border-white/20 pb-2 text-center uppercase" style="font-family:'Syncopate'">Fiche Technique</p>
                        <div class="spec-item"><span>Année</span>
                            <span contenteditable="true"
                                  data-car-id="<?php echo $car['id_voiture']; ?>"
                                  data-field="annee"
                                  data-original="<?php echo $car['annee']; ?>"
                                  onblur="saveField(this)"
                            ><?php echo $car['annee']; ?></span>
                        </div>
                        <div class="spec-item"><span>État</span>
                            <span contenteditable="true"
                                  data-car-id="<?php echo $car['id_voiture']; ?>"
                                  data-field="etat"
                                  data-original="<?php echo htmlspecialchars($car['etat']); ?>"
                                  onblur="saveField(this)"
                            ><?php echo htmlspecialchars($car['etat']); ?></span>
                        </div>
                        <div class="spec-item"><span>Type</span>
                            <span contenteditable="true"
                                  data-car-id="<?php echo $car['id_voiture']; ?>"
                                  data-field="type"
                                  data-original="<?php echo htmlspecialchars($car['type']); ?>"
                                  onblur="saveField(this)"
                            ><?php echo htmlspecialchars($car['type']); ?></span>
                        </div>
                        <p class="text-[9px] mt-4 text-center opacity-50 italic">✎ Cliquez sur une valeur pour modifier</p>
                    </div>

                    <div class="img-wrap" title="Cliquer pour changer la photo">
                        <div class="img-ov">
                            <div class="img-ov-inner">
                                <i class="fas fa-camera"></i>
                                <span>CHANGER PHOTO</span>
                            </div>
                        </div>
                        <input type="file" class="img-file" accept="image/*"
                               data-car-id="<?php echo $car['id_voiture']; ?>"
                               onchange="uploadImg(this)">
                        <img src="<?= UPLOAD_URL ?>/voitures/<?php echo htmlspecialchars($car['image_url']); ?>"
                             class="w-full h-64 object-cover rounded-t-[24px]"
                             id="img-<?php echo $car['id_voiture']; ?>"
                             alt="<?php echo htmlspecialchars($car['modele']); ?>">
                    </div>

                    <div class="p-8 bg-white/5 rounded-b-[24px]">
                        <h3 class="font-title text-[13px] mb-4 text-white uppercase leading-tight" style="font-family:'Syncopate';min-height:40px">
                            <span contenteditable="true"
                                  data-car-id="<?php echo $car['id_voiture']; ?>"
                                  data-field="modele"
                                  data-original="<?php echo htmlspecialchars($car['modele']); ?>"
                                  onblur="saveField(this)"
                                  title="Cliquer pour modifier le modèle"
                            ><?php echo htmlspecialchars($car['modele']); ?></span>
                        </h3>
                        <div style="display:flex;align-items:center;gap:4px;flex-wrap:wrap">
                            <span contenteditable="true"
                                  class="font-bold text-xl text-white"
                                  data-car-id="<?php echo $car['id_voiture']; ?>"
                                  data-field="prix"
                                  data-original="<?php echo $car['prix']; ?>"
                                  onblur="saveField(this)"
                                  title="Cliquer pour modifier le prix"
                            ><?php echo number_format($car['prix'],0,'.', ' '); ?></span>
                            <span class="font-bold text-xl text-white">Rs</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="add-card"
                     onclick="openAddModal('<?php echo htmlspecialchars($nomMarque); ?>','<?php echo $rowId; ?>')"
                     title="Ajouter un véhicule <?php echo htmlspecialchars($nomMarque); ?>">
                    <div class="add-circle"><i class="fas fa-plus"></i></div>
                    <div class="add-label">AJOUTER<br><?php echo htmlspecialchars($nomMarque); ?></div>
                </div>
            </div>
            <button class="nav-btn btn-next" onclick="scrollBy3('<?php echo $rowId; ?>',1)">❯</button>
        </div>
    </section>
    <?php endforeach; ?>
</main>

<div class="modal-bg" id="add-modal">
    <div class="mbox">
        <div class="mhead">
            <h3 id="modal-title">AJOUTER UN VÉHICULE</h3>
            <button class="mclose" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="mbody">
            <input type="hidden" id="m-brand">
            <input type="hidden" id="m-row">
            <div class="fg">
                <label class="fl">Marque</label>
                <select id="m-marque" class="inp" style="background:#141414">
                    <?php foreach ($marquesAll as $mk): ?>
                    <option value="<?php echo htmlspecialchars($mk['nom_marque']); ?>"><?php echo htmlspecialchars($mk['nom_marque']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="fg">
                <label class="fl">Modèle</label>
                <input type="text" id="m-modele" class="inp" placeholder="Ex: Ferrari 488 GTB">
            </div>
            <div class="fgrid">
                <div class="fg"><label class="fl">Prix (Rs)</label><input type="number" id="m-prix" class="inp" placeholder="8500000"></div>
                <div class="fg"><label class="fl">Année</label><input type="number" id="m-annee" class="inp" value="<?php echo date('Y'); ?>" min="1900" max="2099"></div>
            </div>
            <div class="fgrid">
                <div class="fg">
                    <label class="fl">État</label>
                    <select id="m-etat" class="inp" style="background:#141414"><option>Neuf</option><option>Occasion</option><option>Démonstration</option></select>
                </div>
                <div class="fg">
                    <label class="fl">Type</label>
                    <select id="m-type" class="inp" style="background:#141414"><option>Coupé</option><option>Berline</option><option>SUV</option><option>Cabriolet</option><option>Break</option><option>Citadine</option></select>
                </div>
            </div>
            <div class="fg">
                <label class="fl">Photo</label>
                <div class="mimg-zone" id="m-img-zone" onclick="document.getElementById('m-img-input').click()">
                    <img id="m-img-prev" src="" alt="">
                    <div class="mph" id="m-img-ph"><i class="fas fa-cloud-upload-alt"></i>Cliquez pour ajouter une photo<br><small style="opacity:.45">JPG, PNG, WEBP</small></div>
                    <input type="file" id="m-img-input" accept="image/*" style="display:none" onchange="prevModalImg(this)">
                </div>
            </div>
        </div>
        <div class="mfoot">
            <button class="btn-cx" onclick="closeModal()">Annuler</button>
            <button class="btn-sv" onclick="submitAdd()"><i class="fas fa-save"></i> AJOUTER</button>
        </div>
    </div>
</div>

<div id="confirm-ov">
    <div class="cbox">
        <div class="ci"><i class="fas fa-box-archive"></i></div>
        <h4>ARCHIVER CE VÉHICULE ?</h4>
        <p id="confirm-text">Il sera retiré de l'inventaire et accessible dans les archives.</p>
        <div class="cact">
            <button class="btn-no" onclick="closeConfirm()">Annuler</button>
            <button class="btn-arc" id="btn-yes"><i class="fas fa-box-archive"></i> ARCHIVER</button>
        </div>
    </div>
</div>

<div id="toast"><span class="ti" id="ti"></span><span id="tm"></span></div>

<script>
function scrollBy3(id, d) {
    const c = document.getElementById(id);
    const card = c.querySelector('.car-card');
    if (card) c.scrollBy({ left: d * (card.offsetWidth + 25) * 3, behavior: 'smooth' });
}

let _tt;
function toast(msg, type='ok') {
    const t = document.getElementById('toast');
    const icons = { ok:'✓', err:'✕', wait:'↻' };
    t.className = type; t.classList.add('show');
    document.getElementById('ti').textContent = icons[type]||'•';
    document.getElementById('tm').textContent = msg;
    clearTimeout(_tt);
    if (type !== 'wait') _tt = setTimeout(()=>t.classList.remove('show'), 3000);
}

async function saveField(el) {
    const id    = el.dataset.carId;
    const field = el.dataset.field;
    let val = el.textContent.trim();
    if (field === 'prix') val = val.replace(/[\s\u00a0]/g,'').replace('Rs','').trim();
    if (!val) { el.textContent = el.dataset.original || ''; return; }

    const fd = new FormData();
    fd.append('action','update_field');
    fd.append('id_voiture', id);
    fd.append('field', field);
    fd.append('value', val);
    toast('Sauvegarde...','wait');
    try {
        const j = await fetch('admin_voitures.php',{method:'POST',body:fd}).then(r=>r.json());
        if (j.success) {
            toast('Modifié ✓','ok');
            el.dataset.original = val;
            if (field === 'prix') el.textContent = parseInt(val).toLocaleString('fr-FR');
        } else toast(j.message||'Erreur','err');
    } catch(e) { toast('Erreur réseau','err'); }
}

async function saveTitre(el) {
    const val = el.textContent.trim();
    if (!val) { el.textContent = el.dataset.original || ''; return; }
    const fd = new FormData();
    fd.append('action','update_titre');
    fd.append('cle', el.dataset.field);
    fd.append('valeur', val);
    toast('Titre sauvegardé ✓','wait');
    const j = await fetch('admin_voitures.php',{method:'POST',body:fd}).then(r=>r.json());
    if (j.success) toast('Titre mis à jour ✓','ok');
    else toast('Erreur sauvegarde titre','err');
}

async function saveBrand(el) {
    const id  = el.dataset.idMarque;
    const val = el.textContent.trim();
    if (!val || val === el.dataset.original) return;
    const fd = new FormData();
    fd.append('action','update_brand'); fd.append('id_marque',id); fd.append('nom_marque',val);
    toast('Renommage...','wait');
    const j = await fetch('admin_voitures.php',{method:'POST',body:fd}).then(r=>r.json());
    if (j.success) {
        el.dataset.original = val; toast('Marque renommée ✓','ok');
        const sec = el.closest('section');
        if (sec) {
            const lbl = sec.querySelector('.add-label');
            if (lbl) lbl.innerHTML = 'AJOUTER<br>'+val;
        }
    } else { el.textContent = el.dataset.original; toast(j.message||'Erreur','err'); }
}

async function uploadImg(input) {
    if (!input.files.length) return;
    const id = input.dataset.carId;
    const fd = new FormData();
    fd.append('action','update_image'); fd.append('id_voiture',id); fd.append('image',input.files[0]);
    toast('Envoi de la photo...','wait');
    const j = await fetch('admin_voitures.php',{method:'POST',body:fd}).then(r=>r.json());
    if (j.success) {
        document.getElementById('img-'+id).src = '<?= UPLOAD_URL ?>/voitures/'+j.image+'?t='+Date.now();
        toast('Photo mise à jour ✓','ok');
    } else toast(j.message||'Erreur','err');
}

function openAddModal(brand, rowId) {
    document.getElementById('modal-title').textContent = 'AJOUTER · '+brand.toUpperCase();
    document.getElementById('m-brand').value = brand;
    document.getElementById('m-row').value = rowId;
    const sel = document.getElementById('m-marque');
    for (let o of sel.options) if (o.value === brand) { sel.value = brand; break; }
    document.getElementById('m-img-prev').style.display = 'none';
    document.getElementById('m-img-ph').style.display = 'block';
    document.getElementById('m-img-input').value = '';
    document.getElementById('m-modele').value = '';
    document.getElementById('m-prix').value = '';
    document.getElementById('add-modal').classList.add('open');
}
function closeModal() { document.getElementById('add-modal').classList.remove('open'); }

function prevModalImg(input) {
    if (!input.files.length) return;
    const r = new FileReader();
    r.onload = e => {
        const img = document.getElementById('m-img-prev');
        img.src = e.target.result; img.style.display='block';
        document.getElementById('m-img-ph').style.display='none';
    };
    r.readAsDataURL(input.files[0]);
}

async function submitAdd() {
    const modele = document.getElementById('m-modele').value.trim();
    const prix   = document.getElementById('m-prix').value.trim();
    const marque = document.getElementById('m-marque').value;
    if (!modele || !prix) { toast('Modèle et prix requis','err'); return; }

    const fd = new FormData();
    fd.append('action','add_car');
    fd.append('nom_marque', marque);
    fd.append('modele', modele);
    fd.append('prix', prix);
    fd.append('annee', document.getElementById('m-annee').value);
    fd.append('etat', document.getElementById('m-etat').value);
    fd.append('type', document.getElementById('m-type').value);
    const fi = document.getElementById('m-img-input');
    if (fi.files.length) fd.append('image', fi.files[0]);

    toast('Ajout en cours...','wait');
    const j = await fetch('admin_voitures.php',{method:'POST',body:fd}).then(r=>r.json());
    if (j.success) {
        toast('Véhicule ajouté !','ok'); closeModal();
        injectCard(j, document.getElementById('m-row').value);
    } else toast(j.message||'Erreur','err');
}

function injectCard(d, rowId) {
    const row = document.getElementById(rowId);
    if (!row) { location.reload(); return; }
    const addBtn = row.querySelector('.add-card');
    const prix = parseInt(d.prix||0).toLocaleString('fr-FR');
    const img  = '<?= UPLOAD_URL ?>/voitures/'+(d.image||'default.jpg');
    const me   = (d.modele||'').replace(/'/g,"\\'");
    const html = `
    <div class="car-card" data-id="${d.id}" data-modele="${d.modele||''}" data-prix="${d.prix||0}" data-annee="${d.annee||''}" data-etat="${d.etat||''}" data-type="${d.type||''}" data-image="${d.image||''}">
        <div class="edit-ring"></div>
        <button class="del-btn" onclick="confirmArchive(${d.id},'${me}')" title="Archiver"><i class="fas fa-box-archive"></i></button>
        <div class="hover-specs">
            <p class="font-title text-[10px] mb-4 border-b border-white/20 pb-2 text-center uppercase" style="font-family:'Syncopate'">Fiche Technique</p>
            <div class="spec-item"><span>Année</span><span contenteditable="true" data-car-id="${d.id}" data-field="annee" data-original="${d.annee||''}" onblur="saveField(this)">${d.annee||''}</span></div>
            <div class="spec-item"><span>État</span><span contenteditable="true" data-car-id="${d.id}" data-field="etat" data-original="${d.etat||''}" onblur="saveField(this)">${d.etat||''}</span></div>
            <div class="spec-item"><span>Type</span><span contenteditable="true" data-car-id="${d.id}" data-field="type" data-original="${d.type||''}" onblur="saveField(this)">${d.type||''}</span></div>
        </div>
        <div class="img-wrap" title="Cliquer pour changer">
            <div class="img-ov"><div class="img-ov-inner"><i class="fas fa-camera"></i><span>CHANGER PHOTO</span></div></div>
            <input type="file" class="img-file" accept="image/*" data-car-id="${d.id}" onchange="uploadImg(this)">
            <img src="${img}" class="w-full h-64 object-cover rounded-t-[24px]" id="img-${d.id}" alt="${d.modele||''}">
        </div>
        <div class="p-8 bg-white/5 rounded-b-[24px]">
            <h3 class="font-title text-[13px] mb-4 text-white uppercase leading-tight" style="font-family:'Syncopate';min-height:40px">
                <span contenteditable="true" data-car-id="${d.id}" data-field="modele" data-original="${d.modele||''}" onblur="saveField(this)">${d.modele||''}</span>
            </h3>
            <div style="display:flex;align-items:center;gap:4px">
                <span contenteditable="true" class="font-bold text-xl text-white" data-car-id="${d.id}" data-field="prix" data-original="${d.prix||0}" onblur="saveField(this)">${prix}</span>
                <span class="font-bold text-xl text-white">Rs</span>
            </div>
        </div>
    </div>`;
    addBtn.insertAdjacentHTML('beforebegin', html);
    const nc = row.querySelector(`[data-id="${d.id}"]`);
    if (nc) nc.scrollIntoView({behavior:'smooth',block:'nearest',inline:'center'});
}

async function addBrand() {
    const input = document.getElementById('new-brand-input');
    const nom = input.value.trim();
    if (!nom) { toast('Saisissez un nom de marque','err'); return; }
    const fd = new FormData();
    fd.append('action','add_brand'); fd.append('nom_marque', nom);
    toast('Création...','wait');
    const j = await fetch('admin_voitures.php',{method:'POST',body:fd}).then(r=>r.json());
    if (j.success) {
        toast('Marque créée !','ok'); input.value='';
        injectBrandSection(j.nom_marque, j.id_marque);
        document.getElementById('m-marque').add(new Option(j.nom_marque, j.nom_marque));
    } else toast(j.message||'Erreur','err');
}

function injectBrandSection(nom, idMarque) {
    const rowId = 'row-'+nom.toLowerCase().replace(/[^a-z0-9]/g,'');
    const html = `
    <section class="mb-10" data-brand-section="${nom}">
        <div class="brand-wrap">
            <span class="brand-name" contenteditable="true" data-id-marque="${idMarque}" data-original="${nom}" onblur="saveBrand(this)" title="Cliquer pour renommer">${nom}</span>
            <span style="font-family:'Syncopate',sans-serif;font-size:8px;color:rgba(255,255,255,.13);letter-spacing:.15em">✎</span>
        </div>
        <div class="row-wrapper">
            <button class="nav-btn btn-prev" onclick="scrollBy3('${rowId}',-1)">❮</button>
            <div class="story-row" id="${rowId}">
                <div class="add-card" onclick="openAddModal('${nom}','${rowId}')" title="Ajouter ${nom}">
                    <div class="add-circle"><i class="fas fa-plus"></i></div>
                    <div class="add-label">AJOUTER<br>${nom}</div>
                </div>
            </div>
            <button class="nav-btn btn-next" onclick="scrollBy3('${rowId}',1)">❯</button>
        </div>
    </section>`;
    document.getElementById('main-content').insertAdjacentHTML('beforeend', html);
    setTimeout(()=>{
        const s = document.querySelector(`[data-brand-section="${nom}"]`);
        if(s) s.scrollIntoView({behavior:'smooth'});
    },100);
}

function confirmArchive(id, modele) {
    document.getElementById('confirm-text').textContent = `"${modele}" sera retiré de l'inventaire et placé en archive.`;
    document.getElementById('confirm-ov').classList.add('open');
    document.getElementById('btn-yes').onclick = async () => {
        closeConfirm();
        const fd = new FormData();
        fd.append('action','archive_car'); fd.append('id_voiture', id);
        toast('Archivage...','wait');
        const j = await fetch('admin_voitures.php',{method:'POST',body:fd}).then(r=>r.json());
        if (j.success) {
            const c = document.querySelector(`.car-card[data-id="${id}"]`);
            if(c){c.style.transition='all .35s';c.style.opacity='0';c.style.transform='scale(0.8)';setTimeout(()=>c.remove(),370);}
            toast('Véhicule archivé ✓','ok');
        } else toast(j.message||'Erreur','err');
    };
}
function closeConfirm() { document.getElementById('confirm-ov').classList.remove('open'); }

document.getElementById('add-modal').addEventListener('click',function(e){if(e.target===this)closeModal();});
document.getElementById('confirm-ov').addEventListener('click',function(e){if(e.target===this)closeConfirm();});
</script>
</body>
</html>