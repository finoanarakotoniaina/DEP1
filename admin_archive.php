<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Archives Supercar</title>
    
    <!-- Tes fichiers CSS globaux existants (à adapter selon tes vrais dossiers) -->
    <link rel="stylesheet" href="style.css"> 
    
    <!-- Font Awesome pour les icônes (V6) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<style>
    /* ==========================================================================
   PAGE ARCHIVES - LAYOUT & PARTICULARITÉS
   ========================================================================== */

/* En-tête de la page */
.archive-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #1f2937;
    padding-bottom: 1.5rem;
    margin-bottom: 2rem;
}
.archive-header h2 { font-size: 1.5rem; font-weight: bold; margin: 0; }
.archive-header .sub-text { color: #9ca3af; font-size: 0.875rem; margin-top: 0.25rem; }
.btn-export { background: #1f2937; color: #e5e7eb; border: 1px solid #374151; padding: 0.5rem 1rem; border-radius: 0.5rem; cursor: pointer; font-size: 0.75rem; transition: background 0.2s; }
.btn-export:hover { background: #374151; }
.btn-export i { margin-right: 0.5rem; }

/* 1. Compteurs de Statistiques (KPI) */
.archive-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}
.stat-card {
    background-color: #111827;
    border: 1px solid #1f2937;
    padding: 1.25rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
}
.stat-icon {
    padding: 0.75rem;
    border-radius: 0.5rem;
    margin-right: 1rem;
    font-size: 1.25rem;
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.stat-data { display: flex; flex-direction: column; }
.stat-label { font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; }
.stat-number { font-size: 1.5rem; font-weight: bold; margin-top: 0.25rem; color: #fff; }

/* Couleurs des icônes de stats */
.stat-icon.vendues { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
.stat-icon.contrats { background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.stat-icon.valeur { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.color-blue { color: #3b82f6 !important; }
.color-gold { color: #f59e0b !important; }

/* 2. Système d'Onglets (Tabs) */
.archive-tabs-container {
    display: flex;
    gap: 0.5rem;
    border-bottom: 1px solid #1f2937;
    margin-bottom: 1.5rem;
}
.tab-btn {
    padding: 0.625rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #9ca3af;
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
}
.tab-btn i { margin-right: 0.5rem; }
.tab-btn:hover { color: #e5e7eb; }
.tab-btn.active { border-bottom-color: #ef4444; color: #ffffff; font-weight: 600; }

/* 3. Conteneur et Filtres du Tableau */
.archive-table-container { background: #111827; border: 1px solid #1f2937; border-radius: 0.75rem; overflow: hidden; }
.table-filters-bar { padding: 1rem; border-bottom: 1px solid #1f2937; display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
.search-box { position: relative; flex-grow: 1; max-width: 300px; }
.search-box i { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #4b5563; font-size: 0.85rem; }
.search-box input { width: 100%; background: #0b0f19; border: 1px solid #1f2937; border-radius: 0.5rem; padding: 0.5rem 0.5rem 0.5rem 2.2rem; color: #fff; font-size: 0.85rem; outline: none; }
.search-box input:focus { border-color: #ef4444; }
.filter-box select { background: #0b0f19; border: 1px solid #1f2937; color: #d1d5db; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.85rem; outline: none; }

/* 4. Tableau Styles de Données */
.archive-data-table { width: 100%; border-collapse: collapse; text-align: left; }
.archive-data-table th { background: rgba(11, 15, 25, 0.4); padding: 1rem; font-size: 0.75rem; text-transform: uppercase; color: #9ca3af; letter-spacing: 0.5px; border-bottom: 1px solid #1f2937; }
.archive-data-table td { padding: 1rem; border-bottom: 1px solid rgba(31, 41, 55, 0.6); font-size: 0.875rem; color: #e5e7eb; vertical-align: middle; }
.archive-data-table tbody tr:hover { background: rgba(31, 41, 55, 0.2); }

.car-cell { display: flex; align-items: center; gap: 0.75rem; }
.car-name { display: block; color: #fff; }
.car-details, .client-type { display: block; font-size: 0.75rem; color: #6b7280; margin-top: 0.15rem; }
.client-name { display: block; }
.font-mono { font-family: monospace; font-size: 0.75rem; color: #9ca3af; }
.price-text { font-weight: bold; }
.text-emerald { color: #10b981; }
.date-text { font-size: 0.75rem; color: #9ca3af; }
.text-right { text-align: right; }

/* Vignettes Marques */
.car-badge { width: 3.2rem; height: 1.8rem; border-radius: 0.25rem; background-color: #1f2937; border: 1px solid #374151; display: flex; align-items: center; justify-content: center; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.5px; }
.car-badge.porsche { color: #9ca3af; }
.car-badge.ferrari { color: #ef4444; border-color: rgba(239, 68, 68, 0.3); }

/* Boutons d'actions */
.actions-cell { display: flex; justify-content: flex-end; gap: 0.5rem; }
.btn-view, .btn-restore { padding: 0.5rem; border: none; border-radius: 0.375rem; cursor: pointer; transition: transform 0.2s, background 0.2s; font-size: 0.85rem; }
.btn-view { background: #1f2937; color: #d1d5db; }
.btn-view:hover { background: #374151; }
.btn-restore { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
.btn-restore:hover { background-color: rgba(239, 68, 68, 0.2); transform: scale(1.05); }

/* 5. Pagination */
.table-pagination { padding: 1rem; border-top: 1px solid #1f2937; display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: #9ca3af; }
.pagination-buttons { display: flex; gap: 0.25rem; }
.btn-pag { background: #1f2937; color: #d1d5db; border: none; padding: 0.4rem 0.75rem; border-radius: 0.25rem; cursor: pointer; font-size: 0.75rem; }
.btn-pag:hover:not(.disabled) { background: #374151; }
.btn-pag.active { background: #ef4444; color: white; font-weight: bold; }
.btn-pag.disabled { opacity: 0.4; cursor: not-allowed; }
</style>

<body class="admin-body"> <!-- Utilise ta classe de body globale si tu en as une -->

    <!-- STRUCTURE GLOBALE (Sidebar + Main) -->
    <div class="admin-wrapper"> <!-- Adapte le nom à ta structure existante -->

        <!-- SIDEBAR -->
        <aside class="admin-sidebar">
            <div class="sidebar-logo">
                <h1>SUPERCAR <span>ADMIN</span></h1>
            </div>
            
            <!-- Ton menu de navigation mis à jour -->
            <nav class="flex-1 space-y-1">
                <p class="text-[9px] text-gray-500 font-bold uppercase tracking-[2px] mb-4 px-2">Menu Principal</p>
                <a href="tableaudebord.php" class="nav-link-admin"><i class="fa-solid fa-gauge-high w-5"></i> Tableau de bord</a>
                <a href="admin_voitures.php" class="nav-link-admin"><i class="fa-solid fa-car-side w-5"></i> Inventaire</a>
                <a href="admin_demande.php" class="nav-link-admin"><i class="fa-solid fa-calendar-check w-5"></i> Demandes</a>
                <a href="admin_contact.php" class="nav-link-admin"><i class="fa-solid fa-envelope w-5"></i> Messages</a>
                <a href="admin_services.php" class="nav-link-admin"><i class="fa-solid fa-screwdriver-wrench w-5"></i> Services</a>
                <!-- Ligne archive active -->
                <a href="admin_archive.php" class="nav-link-admin active"><i class="fa-solid fa-box-archive w-5"></i> Archives</a>
            </nav>
        </aside>

        <!-- CONTENU PRINCIPAL -->
        <main class="admin-main-content">
            
            <!-- EN-TÊTE DE PAGE -->
            <div class="archive-header">
                <div>
                    <h2>Coffre-fort des Archives</h2>
                    <p class="sub-text">Consultez l'historique complet de votre concession de prestige (véhicules vendus, anciens contrats et messages).</p>
                </div>
                <div class="header-actions">
                    <button class="btn-export">
                        <i class="fa-solid fa-download"></i> Exporter l'historique (CSV)
                    </button>
                </div>
            </div>

            <!-- GRILLE DES STATISTIQUES DES ARCHIVES -->
            <div class="archive-stats-grid">
                <div class="stat-card">
                    <div class="stat-icon vendues"><i class="fa-solid fa-handshake"></i></div>
                    <div class="stat-data">
                        <span class="stat-label">Supercars Vendues</span>
                        <span class="stat-number">142</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon contrats"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <div class="stat-data">
                        <span class="stat-label">Contrats Clôturés</span>
                        <span class="stat-number color-blue">589</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon valeur"><i class="fa-solid fa-chart-line"></i></div>
                    <div class="stat-data">
                        <span class="stat-label">Valeur Totale Archivée</span>
                        <span class="stat-number color-gold">34.8M €</span>
                    </div>
                </div>
            </div>

            <!-- SYSTÈME D'ONGLETS (FILTRES ARCHIVES) -->
            <div class="archive-tabs-container">
                <button class="tab-btn active">
                    <i class="fa-solid fa-car"></i> Véhicules Vendus (2)
                </button>
                <button class="tab-btn">
                    <i class="fa-solid fa-file-contract"></i> Demandes & Locations (12)
                </button>
                <button class="tab-btn">
                    <i class="fa-solid fa-envelope-open-text"></i> Messages Traités
                </button>
            </div>

            <!-- CONTENEUR DU TABLEAU -->
            <div class="archive-table-container">
                
                <!-- Barre de filtres et recherche du tableau -->
                <div class="table-filters-bar">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Rechercher par modèle, VIN, client...">
                    </div>
                    <div class="filter-box">
                        <select>
                            <option>Toutes les marques</option>
                            <option>Ferrari</option>
                            <option>Porsche</option>
                            <option>Lamborghini</option>
                        </select>
                    </div>
                </div>

                <!-- Le Tableau de données -->
                <table class="archive-data-table">
                    <thead>
                        <tr>
                            <th>Véhicule</th>
                            <th>Châssis (VIN)</th>
                            <th>Prix de Vente</th>
                            <th>Acheteur</th>
                            <th>Date Archive</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Ligne 1 : Porsche -->
                        <tr>
                            <td>
                                <div class="car-cell">
                                    <div class="car-badge porsche">PORSCHE</div>
                                    <div>
                                        <strong class="car-name">Porsche 911 GT3 RS</strong>
                                        <span class="car-details">Année 2023 · Gris Quartz</span>
                                    </div>
                                </div>
                            </td>
                            <td class="font-mono text-xs">WP0ZZZ99ZNS2XXXXX</td>
                            <td class="price-text text-emerald">265 000 €</td>
                            <td>
                                <strong class="client-name">Jean-Pierre M.</strong>
                                <span class="client-type">Client Premium</span>
                            </td>
                            <td class="date-text">14/02/2026</td>
                            <td class="text-right actions-cell">
                                <button title="Voir la fiche historique" class="btn-view"><i class="fa-solid fa-eye"></i></button>
                                <button title="Restaurer vers l'inventaire actif" class="btn-restore"><i class="fa-solid fa-arrow-rotate-left"></i></button>
                            </td>
                        </tr>

                        <!-- Ligne 2 : Ferrari -->
                        <tr>
                            <td>
                                <div class="car-cell">
                                    <div class="car-badge ferrari">FERRARI</div>
                                    <div>
                                        <strong class="car-name">Ferrari SF90 Stradale</strong>
                                        <span class="car-details">Année 2022 · Rosso Corsa</span>
                                    </div>
                                </div>
                            </td>
                            <td class="font-mono text-xs">ZFF89BHE000XXXXXX</td>
                            <td class="price-text text-emerald">490 000 €</td>
                            <td>
                                <strong class="client-name">Al-Thani Luxury Rent</strong>
                                <span class="client-type">Société Pro</span>
                            </td>
                            <td class="date-text">08/01/2026</td>
                            <td class="text-right actions-cell">
                                <button title="Voir la fiche historique" class="btn-view"><i class="fa-solid fa-eye"></i></button>
                                <button title="Restaurer vers l'inventaire actif" class="btn-restore"><i class="fa-solid fa-arrow-rotate-left"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- PAGINATION -->
                <div class="table-pagination">
                    <span>Affichage de 1 à 2 sur 2 véhicules archivés</span>
                    <div class="pagination-buttons">
                        <button class="btn-pag disabled" disabled>Précédent</button>
                        <button class="btn-pag active">1</button>
                        <button class="btn-pag">Suivant</button>
                    </div>
                </div>
            </div>

        </main>
    </div>

</body>
</html>