<?php
// --- 1. CONNEXION À LA BASE DE DONNÉES ---
$host = 'localhost'; 
$db   = 'supercar'; 
$user = 'root'; 
$pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// --- 2. RÉCUPÉRATION DES STATISTIQUES ---
// On compte dans la table 'voiture'
$countCars = $pdo->query("SELECT COUNT(*) FROM voiture")->fetchColumn();

// On compte dans la table 'client'
$countClients = $pdo->query("SELECT COUNT(*) FROM client")->fetchColumn();

// On compte les messages dans la table 'contact'
$countMessages = $pdo->query("SELECT COUNT(*) FROM contact")->fetchColumn();

// --- 3. RÉCUPÉRATION DES CLIENTS (POUR LE TABLEAU) ---
// On récupère les 5 derniers clients inscrits avec leur téléphone
$lastClients = $pdo->query("SELECT nom, prenom, email, telephone FROM client ORDER BY id_client DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SuperCar Admin - Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background: #f4f7f6; color: #333; }
        
        /* Sidebar */
        .sidebar { width: 260px; background: #1a1a1a; color: white; height: 100vh; position: fixed; padding: 25px; box-sizing: border-box; }
        .sidebar h2 { color: #e31e24; font-size: 1.5em; margin-bottom: 40px; text-transform: uppercase; letter-spacing: 2px; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin-bottom: 20px; }
        .sidebar ul li a { color: #bbb; text-decoration: none; display: block; transition: 0.3s; }
        .sidebar ul li a:hover { color: #e31e24; padding-left: 5px; }
        .sidebar .logout { margin-top: 50px; border-top: 1px solid #333; padding-top: 20px; }
        .sidebar .logout a { color: #ff4d4d; font-weight: bold; }

        /* Main Content */
        .main-content { margin-left: 260px; padding: 40px; width: 100%; box-sizing: border-box; }
        header h1 { margin-bottom: 40px; font-size: 2em; }

        /* Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-bottom: 50px; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-left: 6px solid #e31e24; }
        .card h3 { margin: 0; font-size: 0.9em; color: #888; text-transform: uppercase; }
        .card .value { font-size: 2.8em; font-weight: bold; margin-top: 10px; }

        /* Tableau */
        .table-section { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; border-bottom: 2px solid #eee; color: #666; font-size: 0.9em; }
        td { padding: 15px; border-bottom: 1px solid #f9f9f9; }
        .btn-view { color: #e31e24; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>SUPERCAR</h2>
        <ul>
            <li><a href="dashboard.php">📊 Accueil</a></li>
            <li><a href="admin_voitures.php">🏎️ Voitures</a></li>
            <li><a href="admin_demandes.php">📩 Demandes</a></li>
            <li><a href="admin_services.php">🛠️ Services</a></li>
            <li><a href="admin_contact.php">📞 Contact</a></li>
            <li class="logout"><a href="logout.php">Déconnexion</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>Tableau de Bord Administrateur</h1>
        </header>

        <div class="stats-grid">
            <div class="card">
                <h3>Total Voitures</h3>
                <div class="value"><?php echo $countCars; ?></div>
            </div>
            <div class="card">
                <h3>Clients Inscrits</h3>
                <div class="value"><?php echo $countClients; ?></div>
            </div>
            <div class="card">
                <h3>Messages Reçus</h3>
                <div class="value"><?php echo $countMessages; ?></div>
            </div>
        </div>

        <div class="table-section">
            <h2>Derniers clients enregistrés</h2>
            <table>
                <thead>
                    <tr>
                        <th>NOM / PRÉNOM</th>
                        <th>EMAIL</th>
                        <th>TÉLÉPHONE</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($lastClients)): ?>
                        <?php foreach($lastClients as $c): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($c['nom'] . ' ' . $c['prenom']) ?></strong></td>
                            <td><?= htmlspecialchars($c['email']) ?></td>
                            <td><?= htmlspecialchars($c['telephone']) ?></td>
                            <td><a href="voir_client.php?id=<?= htmlspecialchars($c['email']) ?>" class="btn-view">Détails</a></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4">Aucun client trouvé.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>