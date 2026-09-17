<?php
if (!isset($currentPage)) {
    $currentPage = basename($_SERVER['PHP_SELF']);
}
if (!isset($adminFullName)) {
    $adminFullName = "Admin";
}

$logoPath = '../assets/logos/superlogo.png';
?>
<aside class="sidebar">
    <div class="sidebar-logo">
        <a href="tableaudebord.php">
            <img src="<?= htmlspecialchars($logoPath) ?>" alt="SUPERCAR Logo">
            <span class="navbar-logo">SUPER<span>CAR</span></span>
        </a>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-section">
            <p class="sidebar-section-title">Gestion</p>
            <a href="tableaudebord.php" class="nav-link <?= ($currentPage == 'tableaudebord.php') ? 'active' : '' ?>">Tableau de bord</a>
            <a href="admin_voitures.php" class="nav-link <?= ($currentPage == 'admin_voitures.php') ? 'active' : '' ?>">Inventaire</a>
            <a href="admin_demande.php" class="nav-link <?= ($currentPage == 'admin_demande.php') ? 'active' : '' ?>">Demandes</a>
            <a href="admin_contact.php" class="nav-link <?= ($currentPage == 'admin_contact.php') ? 'active' : '' ?>">Messages</a>
        </div>

        <div class="sidebar-section">
            <p class="sidebar-section-title">Édition des pages</p>
            <a href="admin_accueil.php" class="nav-link <?= ($currentPage == 'admin_accueil.php') ? 'active' : '' ?>">Page Accueil</a>
            <a href="admin_affichage_contact.php" class="nav-link <?= ($currentPage == 'admin_affichage_contact.php') ? 'active' : '' ?>">Page Contact</a>
            <a href="admin_services.php" class="nav-link <?= ($currentPage == 'admin_services.php') ? 'active' : '' ?>">Services</a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <a href="logout.php" class="logout-btn-admin">Se déconnecter</a>
        <div class="admin-info">
            <div class="admin-avatar"><?= strtoupper(substr($adminFullName, 0, 1)) ?></div>
            <div>
                <p class="admin-name"><?= htmlspecialchars($adminFullName) ?></p>
                <p class="admin-role">Administrateur</p>
            </div>
        </div>
    </div>
</aside>