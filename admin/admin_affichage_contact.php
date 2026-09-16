<?php
/**
 * SUPERCAR - Cockpit de Gestion & Éditeur Live
 * Module : Configuration Visuelle & Textes de la Page Contact
 */
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn;
$message_status = "";

// --- LOGIQUE DE SAUVEGARDE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_ui'])) {
    try {
        $stmt_text = $db->prepare("UPDATE contact_settings SET valeur = ? WHERE cle = ?");
        
        $stmt_text->execute([trim($_POST['txt_1'] ?? ''), 'titre_principal']);
        $stmt_text->execute([trim($_POST['txt_2'] ?? ''), 'responsable_contact']); 
        $stmt_text->execute([trim($_POST['txt_3'] ?? ''), 'siege_contact']);
        $stmt_text->execute([trim($_POST['txt_4'] ?? ''), 'tel_contact']);
        $stmt_text->execute([trim($_POST['txt_5'] ?? ''), 'email_contact']);

        $message_status = "✅ INTERFACE MISE À JOUR AVEC SUCCÈS";
    } catch (Exception $e) {
        $message_status = "❌ ERREUR : " . $e->getMessage();
    }
}

try {
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";

    $settings = $db->query("SELECT cle, valeur FROM contact_settings")->fetchAll(PDO::FETCH_KEY_PAIR);

} catch (PDOException $e) {
    die("Erreur Système : " . $e->getMessage());
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SuperCar | Contact Config</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Syncopate:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    
    <style>
        body { background-color: var(--cosmos); }
        .sidebar { background: rgba(0, 0, 0, 0.2); border-right: 1px solid rgba(255, 255, 255, 0.05); }
        .nav-link-admin { display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1.25rem; border-radius: 14px; color: rgba(255,255,255,0.6); transition: 0.3s; font-size: 0.85rem; font-weight: 600; }
        .nav-link-admin:hover { color: white; background: rgba(255,255,255,0.05); }
        .nav-link-admin.active { color: white; background: linear-gradient(90deg, rgba(193, 18, 31, 0.2), transparent); border-left: 3px solid var(--crimson); }
        .logout-btn-admin { display: flex; align-items: center; justify-content: center; gap: 0.75rem; width: 100%; padding: 0.75rem; margin-bottom: 0.8rem; background: rgba(193, 18, 31, 0.1); border: 1px solid rgba(193, 18, 31, 0.2); border-radius: 14px; color: #ff4d4d; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
        .logout-btn-admin:hover { background: var(--crimson); color: white; }
        
        .live-wrap { background-color: var(--cosmos); border-radius: 32px; position: relative; overflow: hidden; min-height: 400px; border: 1px solid rgba(255,255,255,0.05); width: 100%; }
        .live-field { background: transparent; border: 1px dashed rgba(255,255,255,0.1); color: white; outline: none; transition: 0.2s; width: 100%; }
        .live-field:hover { border-color: rgba(255,255,255,0.4); }
        .live-field:focus { border-color: var(--crimson); border-style: solid; background: rgba(255,255,255,0.05); }
    </style>
</head>
<body class="flex h-screen overflow-hidden text-white">

    <aside class="w-72 sidebar p-6 flex flex-col">
        <div class="font-title text-xl tracking-tighter uppercase mb-10">
            SUPER<span class="text-[var(--crimson)]">CAR</span>
        </div>
        
        <nav class="flex-1 space-y-1">
            <p class="text-[9px] text-gray-500 font-bold uppercase tracking-[2px] mb-4 px-2">Menu Principal</p>
            <a href="tableaudebord.php" class="nav-link-admin <?= ($currentPage == 'tableaudebord.php') ? 'active' : '' ?>"><i class="fa-solid fa-gauge-high w-5"></i> Tableau de bord</a>
            <a href="admin_accueil.php" class="nav-link-admin <?= ($currentPage == 'admin_accueil.php') ? 'active' : '' ?>"><i class="fa-solid fa-house w-5"></i> Page Accueil</a>
            <a href="admin_affichage_contact.php" class="nav-link-admin <?= ($currentPage == 'admin_affichage_contact.php') ? 'active' : '' ?>"><i class="fa-solid fa-address-book w-5"></i> Page Contact</a>
            <a href="admin_voitures.php" class="nav-link-admin <?= ($currentPage == 'admin_voitures.php') ? 'active' : '' ?>"><i class="fa-solid fa-car-side w-5"></i> Inventaire</a>
            <a href="admin_demande.php" class="nav-link-admin <?= ($currentPage == 'admin_demande.php') ? 'active' : '' ?>"><i class="fa-solid fa-calendar-check w-5"></i> Demandes</a>
            <a href="admin_contact.php" class="nav-link-admin <?= ($currentPage == 'admin_contact.php') ? 'active' : '' ?>"><i class="fa-solid fa-envelope w-5"></i> Messages</a>
            <a href="admin_services.php" class="nav-link-admin <?= ($currentPage == 'admin_services.php') ? 'active' : '' ?>"><i class="fa-solid fa-screwdriver-wrench w-5"></i> Services</a>
        </nav>

        <div class="mt-auto pt-6 border-t border-white/5">
            <a href="logout.php" class="logout-btn-admin">
                <i class="fa-solid fa-power-off text-xs"></i> Se déconnecter
            </a>
            
            <div class="bg-white/5 p-4 rounded-2xl flex items-center justify-between border border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-xs font-bold border border-white/10 text-white">
                        <?= strtoupper(substr($adminFullName, 0, 1)) ?>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white"><?= htmlspecialchars($adminFullName) ?></p>
                        <p class="text-[10px] text-gray-400 font-medium italic">Administrateur</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 p-10 overflow-y-auto split-bg">
        <header class="mb-8">
            <h2 class="text-4xl font-title font-black italic uppercase tracking-tighter">
                CONTACT <span style="color: var(--crimson)">LIVE EDITOR</span>
            </h2>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[3px] mt-2">Cliquez directement dans les zones pointillées pour modifier les textes de la page contact</p>
        </header>

        <?php if (!empty($message_status)): ?>
            <div class="mb-6 p-4 bg-white/5 border-l-4 border-[var(--crimson)] rounded-r-xl text-sm flex items-center gap-3 font-semibold tracking-wide">
                <?= $message_status ?>
            </div>
        <?php endif; ?>

        <section id="tab-editor" class="tab-content">
            <form method="POST">
                <div class="live-wrap shadow-2xl mx-auto max-w-4xl p-12 flex flex-col gap-8">
                    
                    <div class="border-b border-white/5 pb-6 space-y-3">
                        <input type="text" name="txt_1"
                               value="<?= htmlspecialchars($settings['titre_principal'] ?? '') ?>"
                               class="live-field font-title text-4xl font-black uppercase tracking-tight">

                        <textarea name="txt_2" rows="2"
                                  class="live-field text-sm text-gray-400 leading-relaxed resize-none"><?= htmlspecialchars($settings['responsable_contact'] ?? '') ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                        
                        <div class="bg-white/[0.02] border border-white/5 p-6 rounded-2xl flex flex-col gap-2">
                            <span class="text-[10px] uppercase tracking-widest text-[var(--crimson)] font-bold"><i class="fa-solid fa-location-dot mr-2"></i>Adresse</span>
                            <textarea name="txt_3" rows="2"
                                      class="live-field text-xs text-gray-300 font-medium resize-none"><?= htmlspecialchars($settings['siege_contact'] ?? '') ?></textarea>
                        </div>

                        <div class="bg-white/[0.02] border border-white/5 p-6 rounded-2xl flex flex-col gap-2">
                            <span class="text-[10px] uppercase tracking-widest text-[var(--crimson)] font-bold"><i class="fa-solid fa-phone mr-2"></i>Téléphone</span>
                            <input type="text" name="txt_4"
                                   value="<?= htmlspecialchars($settings['tel_contact'] ?? '') ?>"
                                   class="live-field text-xs text-gray-300 font-medium">
                        </div>

                        <div class="bg-white/[0.02] border border-white/5 p-6 rounded-2xl flex flex-col gap-2">
                            <span class="text-[10px] uppercase tracking-widest text-[var(--crimson)] font-bold"><i class="fa-solid fa-envelope mr-2"></i>E-mail</span>
                            <input type="text" name="txt_5"
                                   value="<?= htmlspecialchars($settings['email_contact'] ?? '') ?>"
                                   class="live-field text-xs text-gray-300 font-medium">
                        </div>

                    </div>
                </div>

                <div class="mt-8 flex justify-center">
                    <button type="submit" name="save_ui" class="bg-[var(--crimson)] px-14 py-4 font-title text-[10px] font-black uppercase tracking-[0.2em] hover:bg-white hover:text-black transition-all rounded-xl shadow-lg">
                        Mettre à jour l'affichage
                    </button>
                </div>
            </form>
        </section>
    </main>

</body>
</html>