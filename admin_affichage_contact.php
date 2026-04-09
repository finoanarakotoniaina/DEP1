<?php
include 'config.php'; // contient $db

// 1. SAUVEGARDE DES MODIFICATIONS
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['settings'] as $cle => $valeur) {
        $stmt = $db->prepare("UPDATE contact_settings SET valeur = ? WHERE cle = ?");
        $stmt->execute([$valeur, $cle]);
    }
    $success = "Modifications enregistrées !";
}

// 2. RÉCUPÉRATION DES RÉGLAGES ACTUELS
$stmt = $db->query("SELECT * FROM contact_settings");
// Remplace ta ligne 44 (ou celle qui fait le fetchAll) par :
$settings = $db->query("SELECT cle, valeur FROM contact_settings")->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin | Éditeur Page Contact</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white p-10">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-red-500 uppercase">Éditeur de la page Contact</h1>

        <?php if(isset($success)) echo "<p class='bg-green-600 p-4 mb-4 rounded'>$success</p>"; ?>

        <form method="POST" class="space-y-6 bg-gray-800 p-8 rounded-xl border border-white/10">
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs uppercase text-gray-400 mb-2">Titre Principal</label>
                    <input type="text" name="settings[titre_principal]" value="<?= $settings['titre_principal'] ?>" class="w-full bg-gray-700 p-3 rounded outline-none border border-transparent focus:border-red-500">
                </div>
                <div>
                    <label class="block text-xs uppercase text-gray-400 mb-2">Nom de l'image (ex: mercedes.jfif)</label>
                    <input type="text" name="settings[image_supercar]" value="<?= $settings['image_supercar'] ?>" class="w-full bg-gray-700 p-3 rounded outline-none border border-transparent focus:border-red-500">
                </div>
            </div>

            <hr class="border-white/5">

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs uppercase text-gray-400 mb-2">Email</label>
                    <input type="text" name="settings[email_contact]" value="<?= $settings['email_contact'] ?>" class="w-full bg-gray-700 p-3 rounded">
                </div>
                <div>
                    <label class="block text-xs uppercase text-gray-400 mb-2">Téléphone</label>
                    <input type="text" name="settings[tel_contact]" value="<?= $settings['tel_contact'] ?>" class="w-full bg-gray-700 p-3 rounded">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs uppercase text-gray-400 mb-2">Siège Social</label>
                    <input type="text" name="settings[siege_contact]" value="<?= $settings['siege_contact'] ?>" class="w-full bg-gray-700 p-3 rounded">
                </div>
                <div>
                    <label class="block text-xs uppercase text-gray-400 mb-2">Responsable</label>
                    <input type="text" name="settings[responsable_contact]" value="<?= $settings['responsable_contact'] ?>" class="w-full bg-gray-700 p-3 rounded">
                </div>
            </div>

            <div>
                <label class="block text-xs uppercase text-gray-400 mb-2">Lien Iframe Google Maps (src uniquement)</label>
                <textarea name="settings[map_iframe]" rows="3" class="w-full bg-gray-700 p-3 rounded"><?= $settings['map_iframe'] ?></textarea>
            </div>

            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 py-4 rounded font-bold uppercase tracking-widest transition">
                Mettre à jour la page client
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <a href="contact.php" target="_blank" class="text-gray-500 hover:text-white underline text-sm">Voir le résultat sur la page client</a>
        </div>
    </div>
</body>
</html>