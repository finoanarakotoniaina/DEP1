<?php 
require_once '../config.php'; 
$s = $contactSettings ?? [];
if (!$s) { die(MSG_ERREUR); }
$message_envoi = ""; $show_modal = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom=htmlspecialchars($_POST['nom']); $prenom=htmlspecialchars($_POST['prenom']);
    $email=htmlspecialchars($_POST['email']); $message=htmlspecialchars($_POST['message']);
    if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($message)) {
        try {
            $stmt=$db->prepare("INSERT INTO contact_client (nom,prenom,email,message) VALUES (:nom,:prenom,:email,:message)");
            $stmt->execute([':nom'=>$nom,':prenom'=>$prenom,':email'=>$email,':message'=>$message]);
            $message_envoi="Votre message a bien été enregistré !"; $show_modal=true;
        } catch (PDOException $e) {
            error_log("[SuperCar] Contact : " . $e->getMessage());
            die(MSG_ERREUR);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact | SuperCar Excellence</title>
<link rel="stylesheet" href="../style.css">
<style>
.contact-hero h1{margin:0;line-height:1;}
.contact-hero .hero-subtitle{margin:0 0 4px 0;line-height:1;}
.contact-top{padding-top:3rem;}
.container.hero-wrap{padding-top:0;}
section.container{padding-top:0;}
</style>
</head>
<body class="bg">

<?php if ($show_modal): ?>
<div class="toast-success">
    <div class="toast-icon">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" style="color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    </div>
    <div><p class="toast-label">Confirmation</p><p class="toast-msg"><?= $message_envoi ?></p></div>
    <span class="toast-close" onclick="this.parentElement.remove()">✕</span>
</div>
<?php endif; ?>

<?php include 'composants_client/navbar.php'; ?>

<div class="container contact-top">
    <a href="accueil.php" id="back-btn-contact" style="display:none;font-size:0.75rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--gray);">← Retour vers l'accueil</a>
</div>

<!-- HERO CONTACT -->
<section class="container hero-wrap">
    <div class="contact-hero">
        <div>
            <p class="hero-subtitle" style="color:#4ea8de;">Disponibilité 24/7</p>
            <h1><?= nl2br($s['titre_principal'] ?? 'NOUS CONTACTER') ?></h1>
        </div>
        <div class="contact-circle">
            <img src="<?= ASSETS_URL ?>/hero/<?= htmlspecialchars($s['image_supercar'] ?: 'photo_contact.webp') ?>" alt="Supercar">
        </div>
    </div>
</section>

<!-- INFOS -->
<section class="container">
    <div class="contact-info-grid">
        <div><span class="contact-info-label">Email</span><p class="contact-info-value"><?= $s['email_contact'] ?? '—' ?></p></div>
        <div><span class="contact-info-label">Tel</span><p class="contact-info-value"><?= $s['tel_contact'] ?? '—' ?></p></div>
        <div><span class="contact-info-label">Siège</span><p class="contact-info-value"><?= $s['siege_contact'] ?? '—' ?></p></div>
        <div><span class="contact-info-label">Responsable</span><p class="contact-info-value"><?= $s['responsable_contact'] ?? '—' ?></p></div>
    </div>
</section>

<!-- FORM + MAP -->
<section class="container" style="padding-bottom:3rem;">
    <div class="contact-grid-2">
        <div class="form-card">
            <form action="contact.php" method="POST">
                <div class="grid grid-2" style="gap:2rem;">
                    <div class="form-group"><label class="form-label">Nom</label><input type="text" name="nom" class="form-input" required value="<?= $_SESSION['nom'] ?? '' ?>"></div>
                    <div class="form-group"><label class="form-label">Prénom</label><input type="text" name="prenom" class="form-input" required value="<?= $_SESSION['prenom'] ?? '' ?>"></div>
                </div>
                <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-input" required value="<?= $_SESSION['email'] ?? '' ?>"></div>
                <div class="form-group"><label class="form-label">Message</label><textarea name="message" class="form-textarea" required placeholder="COMMENT POUVONS-NOUS VOUS AIDER ?"></textarea></div>
                <button type="submit" class="btn btn-primary btn-block">Envoyer le message</button>
            </form>
        </div>
        <div class="map-frame">
            <iframe src="<?= $s['map_iframe'] ?? '' ?>" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded',function(){
    const params=new URLSearchParams(window.location.search);
    if(params.get('from')==='accueil'){const btn=document.getElementById('back-btn-contact');if(btn)btn.style.display='inline-block';}
});
</script>

<?php include 'composants_client/footer.php'; ?>
</body>
</html>