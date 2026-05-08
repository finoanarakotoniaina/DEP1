<?php
/**
 * SUPERCAR - Cockpit de Gestion & Éditeur Live
 */
session_start();
require_once 'config.php';

// Vérification de la session
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = $pdo ?? $db ?? $conn;
$message_status = "";

// --- TRAITEMENT ACCEPTER / REFUSER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id_demande'])) {
    $new_statut = $_POST['action'] === 'accepte' ? 'accepte' : 'refuse';
    $db->prepare("UPDATE demande_essai SET statut = ? WHERE id_demande = ?")
       ->execute([$new_statut, $_POST['id_demande']]);
    header('Location: admin_demande.php');
    exit();
}

// --- LOGIQUE DE SAUVEGARDE ÉDITEUR LIVE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_ui'])) {
    try {
        $stmt_text = $db->prepare("UPDATE essai_content SET contenu = ? WHERE id = ?");
        $stmt_text->execute([$_POST['txt_1'] ?? '', 1]);
        $stmt_text->execute([$_POST['txt_2'] ?? '', 2]);
        $stmt_text->execute([$_POST['txt_3'] ?? '', 3]);
        $stmt_text->execute([$_POST['txt_4'] ?? '', 4]);
        $stmt_text->execute([$_POST['txt_5'] ?? '', 5]);

        if (isset($_FILES['image_hero']) && $_FILES['image_hero']['error'] === 0) {
            if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }
            $extension = pathinfo($_FILES['image_hero']['name'], PATHINFO_EXTENSION);
            $new_file_name = 'hero_essai_' . time() . '.' . $extension;
            $destination = 'uploads/' . $new_file_name;
            if (move_uploaded_file($_FILES['image_hero']['tmp_name'], $destination)) {
                $db->prepare("UPDATE essai_content SET contenu = ? WHERE id = 6")->execute([$destination]);
            }
        }
        $message_status = "✅ INTERFACE MISE À JOUR";
    } catch (Exception $e) {
        $message_status = "❌ ERREUR : " . $e->getMessage();
    }
}

try {
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";

    $ui = $db->query("SELECT id, contenu FROM essai_content")->fetchAll(PDO::FETCH_KEY_PAIR);
    $img_path = $ui[6] ?? 'photo_essai.jpeg';

    $totalDemandes = $db->query("SELECT COUNT(*) FROM demande_essai")->fetchColumn();
    $enCours = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut = 'en_attente' OR statut IS NULL")->fetchColumn();
    $acceptees = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut = 'accepte'")->fetchColumn();
    $refusees = $db->query("SELECT COUNT(*) FROM demande_essai WHERE statut = 'refuse'")->fetchColumn();

    // Remplace ton bloc SELECT par celui-ci :
    $demandes = $db->query("
        SELECT 
            de.id_demande,
            de.date_souhaitee,
            de.statut,
            CONCAT(c.prenom, ' ', c.nom) AS nom_complet,
            c.email,
            c.telephone,
            v.modele
        FROM demande_essai de
        LEFT JOIN client c ON de.id_client = c.id_client
        LEFT JOIN voiture v ON de.id_voiture = v.id_voiture
        ORDER BY de.id_demande DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SuperCar | Command Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Syncopate:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    
    <style>
        body { background-color: var(--cosmos); }
        .sidebar { background: rgba(0, 0, 0, 0.2); border-right: 1px solid rgba(255, 255, 255, 0.05); }
        .nav-link-admin { display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1.25rem; border-radius: 14px; color: rgba(255,255,255,0.6); transition: 0.3s; font-size: 0.85rem; font-weight: 600; }
        .nav-link-admin:hover { color: white; background: rgba(255,255,255,0.05); }
        .nav-link-admin.active { color: white; background: linear-gradient(90deg, rgba(193, 18, 31, 0.2), transparent); border-left: 3px solid var(--crimson); }
        .logout-btn-admin { display: flex; align-items: center; justify-content: center; gap: 0.75rem; width: 100%; padding: 0.75rem; margin-bottom: 0.8rem; background: rgba(193, 18, 31, 0.1); border: 1px solid rgba(193, 18, 31, 0.2); border-radius: 14px; color: #ff4d4d; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
        .logout-btn-admin:hover { background: var(--crimson); color: white; }
        .stat-icon { position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.05; transform: rotate(-15deg); transition: all 0.5s ease; pointer-events: none; }
        .group:hover .stat-icon { opacity: 0.15; transform: rotate(0deg) scale(1.1); color: white; }
        .table-container { background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); }
        .status-badge { background: rgba(193, 18, 31, 0.15); color: var(--crimson); border: 1px solid rgba(193, 18, 31, 0.3); }

        .tab-btn { padding: 12px 24px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; border-bottom: 2px solid transparent; transition: 0.3s; opacity: 0.5; cursor: pointer; color: white; }
        .tab-btn.active { opacity: 1; border-color: var(--crimson); color: var(--crimson); }

        .live-wrap { background-color: #003049; border-radius: 32px; position: relative; overflow: hidden; min-height: 400px; border: 1px solid rgba(255,255,255,0.1); width: 100%; }
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
            <a href="tableaudebord.php" class="nav-link-admin"><i class="fa-solid fa-gauge-high w-5"></i> Tableau de bord</a>
            <a href="admin_voitures.php" class="nav-link-admin"><i class="fa-solid fa-car-side w-5"></i> Inventaire</a>
            <a href="admin_demande.php" class="nav-link-admin active"><i class="fa-solid fa-calendar-check w-5"></i> Demandes</a>
            <a href="admin_contact.php" class="nav-link-admin"><i class="fa-solid fa-envelope w-5"></i> Messages</a>
            <a href="admin_services.php" class="nav-link-admin"><i class="fa-solid fa-screwdriver-wrench w-5"></i> Services</a>
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
            <div class="flex justify-between items-center">
                <h2 class="text-4xl font-title font-black italic uppercase tracking-tighter">
                    COCKPIT <span style="color: var(--crimson)">SYSTEM</span>
                </h2>
            </div>
            
            <div class="flex gap-8 mt-8 border-b border-white/5">
                <div onclick="switchTab('radar')" id="btn-radar" class="tab-btn active">Radar Demandes</div>
                <div onclick="switchTab('editor')" id="btn-editor" class="tab-btn">Éditeur Live</div>
            </div>
        </header>

        <section id="tab-radar" class="tab-content">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <div class="bg-white/5 border border-white/10 p-6 rounded-3xl backdrop-blur-md relative overflow-hidden group">
                    <i class="fa-solid fa-chart-line stat-icon"></i>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Flux Total</p>
                    <h3 class="text-3xl font-title font-black italic text-white"><?= $totalDemandes ?></h3>
                </div>
                <div class="bg-white/5 border border-white/10 p-6 rounded-3xl backdrop-blur-md relative overflow-hidden group">
                    <i class="fa-solid fa-bolt-lightning stat-icon"></i>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">En Attente</p>
                    <h3 class="text-3xl font-title font-black italic text-white"><?= $enCours ?></h3>
                </div>
                <div class="bg-white/5 border border-white/10 p-6 rounded-3xl backdrop-blur-md relative overflow-hidden group border-l-4 border-l-[#00ff88]">
                    <i class="fa-solid fa-check-double stat-icon"></i>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Approuvées</p>
                    <h3 class="text-3xl font-title font-black italic text-white"><?= $acceptees ?></h3>
                </div>
                <div class="bg-white/5 border border-white/10 p-6 rounded-3xl backdrop-blur-md relative overflow-hidden group border-l-4 border-l-[var(--crimson)]">
                    <i class="fa-solid fa-ban stat-icon"></i>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Refusées</p>
                    <h3 class="text-3xl font-title font-black italic text-white"><?= $refusees ?></h3>
                </div>
            </div>

            <!-- Légende feu de circulation -->
            <div class="flex items-center justify-end gap-10 mb-8 mt-2">
                <!-- Feu de circulation -->
                <div class="flex items-center gap-6 bg-white/5 border border-white/10 rounded-2xl px-8 py-4 backdrop-blur-sm">
                    <!-- Feu -->
                    <div class="flex flex-col items-center gap-1.5 bg-black/40 px-3 py-3 rounded-xl border border-white/10">
                        <span class="w-4 h-4 rounded-full bg-green-400 shadow-[0_0_8px_2px_rgba(74,222,128,0.6)]"></span>
                        <span class="w-4 h-4 rounded-full bg-orange-400 shadow-[0_0_8px_2px_rgba(251,146,60,0.6)]"></span>
                        <span class="w-4 h-4 rounded-full bg-red-500 shadow-[0_0_8px_2px_rgba(239,68,68,0.6)]"></span>
                    </div>
                    <!-- Labels -->
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-400"></span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-green-400">Accepté</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-orange-400">En attente</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-red-400">Refusé</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-container rounded-[32px] overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-white/5 text-[10px] uppercase font-title tracking-widest text-gray-400">
                        <tr>
                            <th class="p-5">Client</th>
                            <th class="p-5">Modèle</th>
                            <th class="p-5">Téléphone</th>
                            <th class="p-5">Date</th>
                            <th class="p-5">Heure</th>
                            <th class="p-5">Statut</th>
                            <th class="p-5 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach($demandes as $d): 
                            $statut = strtolower(trim($d['statut'] ?? ''));
                            if ($statut === 'accepte' || $statut === 'accepté') {
                                $dot = 'bg-green-400';
                                $label = 'Accepté';
                                $labelColor = 'text-green-400';
                            } elseif ($statut === 'refuse' || $statut === 'refusé') {
                                $dot = 'bg-red-500';
                                $label = 'Refusé';
                                $labelColor = 'text-red-400';
                            } else {
                                $dot = 'bg-orange-400';
                                $label = 'En attente';
                                $labelColor = 'text-orange-400';
                            }
                        ?>
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <!-- Client -->
                            <td class="p-5">
                                <p class="font-bold text-sm uppercase tracking-tight text-white"><?= htmlspecialchars($d['nom_complet'] ?? 'Anonyme') ?></p>
                                <p class="text-[9px] text-gray-500 uppercase tracking-widest">#<?= $d['id_demande'] ?></p>
                            </td>
                            <!-- Modèle -->
                            <td class="p-5">
                                <span class="text-[11px] font-bold uppercase tracking-widest text-[var(--crimson)]">
                                    <?= htmlspecialchars($d['modele'] ?? '—') ?>
                                </span>
                            </td>
                            <!-- Contact -->
                            <td class="p-5">
                                <p class="text-[11px] text-gray-300"><?= htmlspecialchars($d['telephone'] ?? '—') ?></p>
                            </td>
                            <!-- Date -->
                            <td class="p-5">
                                <span class="text-[11px] text-gray-300">
                                    <?= $d['date_souhaitee'] ? date('d/m/Y', strtotime($d['date_souhaitee'])) : '—' ?>
                                </span>
                            </td>
                            <!-- Heure -->
                            <td class="p-5">
                                <span class="text-[11px] text-gray-300">
                                    <?= $d['heure_souhaitee'] ? date('H\hi', strtotime($d['heure_souhaitee'])) : '—' ?>
                                </span>
                            </td>
                            <!-- Statut : cercle coloré uniquement -->
                            <td class="p-5">
                                <div class="flex items-center justify-center">
                                    <span class="w-4 h-4 rounded-full <?= $dot ?> shadow-lg"></span>
                                </div>
                            </td>
                            <!-- Actions : icônes centrées -->
                            <td class="p-5">
                                <div class="flex items-center justify-center gap-3">
                                    <form method="POST">
                                        <input type="hidden" name="id_demande" value="<?= $d['id_demande'] ?>">
                                        <button type="submit" name="action" value="accepte"
                                                class="w-8 h-8 flex items-center justify-center bg-green-500/10 border border-green-500/30 text-green-400 rounded-full hover:bg-green-500 hover:text-white transition-all"
                                                title="Accepter">
                                            <i class="fa-solid fa-check text-xs"></i>
                                        </button>
                                    </form>
                                    <form method="POST">
                                        <input type="hidden" name="id_demande" value="<?= $d['id_demande'] ?>">
                                        <button type="submit" name="action" value="refuse"
                                                class="w-8 h-8 flex items-center justify-center bg-red-500/10 border border-red-500/30 text-red-400 rounded-full hover:bg-red-500 hover:text-white transition-all"
                                                title="Refuser">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="tab-editor" class="tab-content hidden">
            <form method="POST" enctype="multipart/form-data">
                <!-- Reproduction fidèle du hero de demande.php -->
                <div class="live-wrap shadow-2xl mx-auto max-w-5xl" style="min-height: 420px; position: relative; overflow: hidden;">

                    <!-- IMAGE pleine largeur en arrière-plan (comme demande.php) -->
                    <div class="absolute inset-0 z-0">
                        <img id="preview_img" src="<?= $img_path ?>" class="w-full h-full object-cover grayscale opacity-50" style="object-position: right center;">
                        <!-- Fondu identique à demande.php -->
                        <div class="absolute inset-0" style="background: linear-gradient(to right, #003049 0%, #003049 40%, rgba(0,48,73,0.5) 58%, transparent 72%);"></div>
                    </div>

                    <!-- TEXTE au premier plan à gauche (comme demande.php) -->
                    <div class="relative z-10 w-1/2 h-full p-10 flex flex-col justify-center gap-6">

                        <!-- ID 1 : Accroche -->
                        <input type="text" name="txt_1"
                               value="<?= htmlspecialchars($ui[1] ?? '') ?>"
                               class="live-field text-[9px] font-bold tracking-[0.5em] uppercase text-gray-400">

                        <!-- ID 2 : Titre ligne 1 (blanc) -->
                        <textarea name="txt_2" rows="1"
                                  class="live-field font-title text-4xl font-black uppercase leading-none resize-none"><?= htmlspecialchars($ui[2] ?? '') ?></textarea>

                        <!-- ID 3 : Titre ligne 2 (toujours crimson) -->
                        <textarea name="txt_3" rows="1"
                                  class="live-field font-title text-4xl font-black uppercase leading-none resize-none"
                                  style="color: var(--crimson);"><?= htmlspecialchars($ui[3] ?? '') ?></textarea>

                        <!-- ID 4 : Description -->
                        <div class="flex gap-4">
                            <div class="w-1 flex-shrink-0 bg-[var(--crimson)]"></div>
                            <textarea name="txt_4" rows="3"
                                      class="live-field text-[11px] text-gray-400 leading-relaxed resize-none"><?= htmlspecialchars($ui[4] ?? '') ?></textarea>
                        </div>

                        <!-- ID 5 : Bouton -->
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-angles-down text-[10px]"></i>
                            </div>
                            <input type="text" name="txt_5"
                                   value="<?= htmlspecialchars($ui[5] ?? '') ?>"
                                   class="live-field text-[9px] font-black uppercase tracking-[0.3em]">
                        </div>
                    </div>

                    <!-- Bouton upload photo -->
                    <label for="image_hero_file" class="absolute bottom-6 right-6 w-12 h-12 bg-black/60 backdrop-blur-md rounded-full flex items-center justify-center cursor-pointer hover:bg-[var(--crimson)] transition-all z-20" title="Changer l'image">
                        <i class="fa-solid fa-camera text-white"></i>
                        <input type="file" id="image_hero_file" name="image_hero" class="hidden" accept="image/*" onchange="previewHero(event)">
                    </label>
                </div>

                <div class="mt-8 flex justify-center">
                    <button type="submit" name="save_ui" class="bg-[var(--crimson)] px-14 py-4 font-title text-[10px] font-black uppercase tracking-[0.2em] hover:bg-white hover:text-black transition-all">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </section>
    </main>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            document.getElementById('btn-' + tabId).classList.add('active');
        }

        function previewHero(event) {
            const reader = new FileReader();
            reader.onload = function(){
                const output = document.getElementById('preview_img');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>
