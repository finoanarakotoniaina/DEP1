<?php
// 1. INCLUSION DE LA CONNEXION
include 'config.php'; // Vérifie bien que le fichier est dans le même dossier

// 2. LOGIQUE DE SUPPRESSION
if (isset($_GET['delete_id'])) {
    $id_a_supprimer = $_GET['delete_id'];
    try {
        // On utilise $db au lieu de $pdo
        $stmt_del = $db->prepare("DELETE FROM contact WHERE id_contact = :id"); // <-- ICI
        $stmt_del->execute([':id' => $id_a_supprimer]);
        header("Location: admin_contact.php?status=deleted");
        exit();
    } catch (PDOException $e) {
        $error = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// 3. RÉCUPÉRATION DES MESSAGES
try {
    // On utilise $db au lieu de $pdo
    $stmt = $db->query("SELECT * FROM contact ORDER BY id_contact DESC"); // <-- ICI
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin | Gestion des Contacts SuperCar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0a0a0a; color: #eee; }
        .font-title { font-family: 'Syncopate', sans-serif; }
        .crimson-text { color: #dc143c; }
        .crimson-bg { background-color: #dc143c; }
    </style>
</head>
<body class="p-4 md:p-10">

    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h4 class="crimson-text font-title text-[10px] tracking-[0.4em] uppercase mb-2">Backoffice</h4>
                <h1 class="font-title text-4xl font-bold text-white uppercase">Messages <span class="crimson-text">Clients</span></h1>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 uppercase tracking-widest">Total</p>
                <span class="text-4xl font-bold text-white"><?php echo count($messages); ?></span>
            </div>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <div class="mb-6 p-4 bg-green-500/10 border-l-4 border-green-500 text-green-500 text-xs uppercase tracking-widest">
                Action effectuée avec succès.
            </div>
        <?php endif; ?>

        <div class="bg-[#111] border border-white/5 rounded-2xl overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/5 text-[10px] uppercase tracking-widest text-gray-400">
                        <th class="p-6">ID</th>
                        <th class="p-6">Client</th>
                        <th class="p-6">Email</th>
                        <th class="p-6 w-1/3">Message</th>
                        <th class="p-6 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php foreach ($messages as $msg): ?>
                        <tr class="hover:bg-white/[0.02] transition-all">
                            <td class="p-6 text-gray-600 font-mono text-xs">#<?php echo $msg['id_contact']; ?></td>
                            <td class="p-6 font-bold uppercase text-xs tracking-wider"><?php echo htmlspecialchars($msg['prenom'] . ' ' . $msg['nom']); ?></td>
                            <td class="p-6 text-xs text-blue-400"><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td class="p-6 text-xs text-gray-400 italic">"<?php echo nl2br(htmlspecialchars($msg['message'])); ?>"</td>
                            <td class="p-6 text-center">
                                <a href="admin_contact.php?delete_id=<?php echo $msg['id_contact']; ?>" 
                                   onclick="return confirm('Supprimer ?')"
                                   class="text-red-500 hover:text-white p-2">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>