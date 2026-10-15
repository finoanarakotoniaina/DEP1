<?php
require_once '../config.php';
requireAdmin();

/** @var PDO $db */

try {
    // Infos admin connecté
    $stmtAdmin = $db->prepare("SELECT nom, prenom FROM admin WHERE id_admin = ?");
    $stmtAdmin->execute([$_SESSION['admin_id']]);
    $resAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $adminFullName = $resAdmin ? $resAdmin['prenom'] . " " . $resAdmin['nom'] : "Admin";

    // 1. Véhicules disponibles (non archivés)
    $countCars = $db->query("SELECT COUNT(*) FROM voiture WHERE archive = 0 OR archive IS NULL")->fetchColumn() ?: 0;

    // 2. Clients inscrits
    $countClients = $db->query("SELECT COUNT(*) FROM client")->fetchColumn() ?: 0;

    // 3. Messages reçus
    $countMessages = $db->query("SELECT COUNT(*) FROM contact_client")->fetchColumn() ?: 0;

    // 4. Demandes d'essai en attente
    $countDemandesAttente = $db->query("
        SELECT COUNT(*) FROM demande_essai 
        WHERE statut IS NULL 
           OR statut = 'en attente' 
           OR statut = 'en_attente'
    ")->fetchColumn() ?: 0;

    // 5. Données du graphique — inscriptions par mois
    $stmtChart = $db->query("
        SELECT 
            DATE_FORMAT(date_inscription, '%Y-%m') AS mois,
            COUNT(*) AS total
        FROM client
        WHERE date_inscription IS NOT NULL
        GROUP BY DATE_FORMAT(date_inscription, '%Y-%m')
        ORDER BY mois ASC
    ");
    $chartData = $stmtChart->fetchAll(PDO::FETCH_ASSOC);

    // Préparer les données pour JS
    $chartLabels = [];
    $chartValues = [];
    $totalInscriptions = 0;

    foreach ($chartData as $row) {
        // Formater le mois : "2026-01" → "Jan 2026"
        $date = DateTime::createFromFormat('Y-m', $row['mois']);
        $moisFr = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
        $label = $date ? $moisFr[(int)$date->format('n') - 1] . ' ' . $date->format('Y') : $row['mois'];
        
        $chartLabels[] = $label;
        $chartValues[] = (int) $row['total'];
        $totalInscriptions += (int) $row['total'];
    }

    // Liste des clients inscrits (pour la vue "Clients")
    $stmtClients = $db->query("
        SELECT id_client, nom, prenom, email, telephone
        FROM client
        ORDER BY id_client DESC
    ");
    $listeClients = $stmtClients->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("[SuperCar] tableaudebord : " . $e->getMessage());
    die(MSG_ERREUR);
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord | SuperCar Admin</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="admin-body">

    <?php include 'composants/sidebar.php'; ?>

    <main class="admin-main">

        <!-- =============================================
             VUE 1 : Tableau de bord (par défaut)
             ============================================= -->
        <div id="view-dashboard">

            <header class="admin-header">
                <div>
                    <h2 class="font-title">TABLEAU DE <span class="title-accent">BORD</span></h2>
                </div>
            </header>

            <div class="admin-stats">

                <!-- 1. Véhicules -->
                <a href="admin_voitures.php" class="stat-card stat-card-link">
                    <p class="stat-label">Véhicules disponibles</p>
                    <h3 class="stat-value"><?= $countCars ?></h3>
                    <p class="stat-sub">En stock actuellement</p>
                    <div class="stat-bar"></div>
                </a>

                <!-- 2. Clients → bascule vers vue clients -->
                <a href="javascript:void(0)" onclick="showClients()" class="stat-card stat-card-link">
                    <p class="stat-label">Clients inscrits</p>
                    <h3 class="stat-value"><?= $countClients ?></h3>
                    <p class="stat-sub">Comptes enregistrés</p>
                    <div class="stat-bar"></div>
                </a>

                <!-- 3. Messages -->
                <a href="admin_contact.php" class="stat-card stat-card-link">
                    <p class="stat-label">Messages reçus</p>
                    <h3 class="stat-value"><?= $countMessages ?></h3>
                    <p class="stat-sub">Depuis le formulaire de contact</p>
                    <div class="stat-bar"></div>
                </a>

                <!-- 4. Demandes en attente -->
                <a href="admin_demande.php" class="stat-card stat-card-accent stat-card-link">
                    <p class="stat-label">Demandes en attente</p>
                    <h3 class="stat-value"><?= $countDemandesAttente ?></h3>
                    <p class="stat-sub">À confirmer</p>
                    <div class="stat-bar"></div>
                </a>

            </div>

            <!-- Graphique : évolution des inscriptions -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3>Évolution des inscriptions clients</h3>
                    <span class="chart-badge"><?= $totalInscriptions ?> AU TOTAL</span>
                </div>

                <div class="chart-wrapper">
                    <canvas id="chartInscriptions"></canvas>
                </div>
            </div>

        </div>

        <!-- =============================================
             VUE 2 : Clients inscrits (cachée par défaut)
             ============================================= -->
        <div id="view-clients" class="hidden">

            <header class="admin-header">
                <div>
                    <button class="back-link" onclick="showDashboard()" title="Retour">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                    </button>
                    <div style="margin-top: 0.75rem;">
                        <h2 class="font-title">CLIENTS <span class="title-accent">INSCRITS</span></h2>
                        <p class="admin-subtitle"><?= $countClients ?> client(s) enregistré(s)</p>
                    </div>
                </div>
            </header>

            <div class="clients-list">
                <?php if (empty($listeClients)): ?>
                    <div class="table-empty" style="background: var(--white); border-radius: 16px; padding: 3rem;">
                        Aucun client inscrit
                    </div>
                <?php else: ?>
                    <?php foreach($listeClients as $c): ?>
                    <div class="client-card">
                        <div class="client-card-avatar">
                            <?= strtoupper(substr($c['prenom'], 0, 1) . substr($c['nom'], 0, 1)) ?>
                        </div>
                        <div class="client-card-info">
                            <p class="client-card-name"><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></p>
                            <p class="client-card-meta">
                                <span class="client-card-label">Email</span>
                                <span class="client-card-value"><?= htmlspecialchars($c['email']) ?></span>
                            </p>
                            <p class="client-card-meta">
                                <span class="client-card-label">Téléphone</span>
                                <span class="client-card-value"><?= htmlspecialchars($c['telephone'] ?? '—') ?></span>
                            </p>
                        </div>
                        <div class="client-card-action">
                            <a href="admin_client_detail.php?id=<?= $c['id_client'] ?>" class="btn btn-outline">Voir la fiche</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>

    </main>

    <script>
        function showClients() {
            document.getElementById('view-dashboard').classList.add('hidden');
            document.getElementById('view-clients').classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function showDashboard() {
            document.getElementById('view-clients').classList.add('hidden');
            document.getElementById('view-dashboard').classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // ============================================
        // Graphique Chart.js — Évolution des inscriptions
        // ============================================
        (function() {
            const ctx = document.getElementById('chartInscriptions');
            if (!ctx) return;

            const labels = <?= json_encode($chartLabels) ?>;
            const values = <?= json_encode($chartValues) ?>;

            // Si aucune donnée → message
            if (labels.length === 0) {
                ctx.parentElement.innerHTML = '<div class="chart-empty">Aucune inscription enregistrée pour le moment</div>';
                return;
            }

            // Créer un dégradé sous la courbe
            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 320);
            gradient.addColorStop(0, 'rgba(86, 124, 141, 0.4)');
            gradient.addColorStop(1, 'rgba(86, 124, 141, 0.02)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Inscriptions',
                        data: values,
                        borderColor: '#567C8D',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#2F4156',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#567C8D',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#2F4156',
                            titleColor: '#F5EFEB',
                            bodyColor: '#FFFFFF',
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                            titleFont: { size: 12, weight: '600' },
                            bodyFont: { size: 13, weight: '700' },
                            callbacks: {
                                label: function(context) {
                                    const v = context.parsed.y;
                                    return v + (v > 1 ? ' inscriptions' : ' inscription');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                color: '#64748B',
                                font: { size: 11, weight: '600' }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(47, 65, 86, 0.08)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#64748B',
                                font: { size: 11, weight: '600' },
                                stepSize: 1,
                                precision: 0
                            }
                        }
                    }
                }
            });
        })();
    </script>

</body>
</html>