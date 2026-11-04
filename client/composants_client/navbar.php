<?php
// Détection de la page active
$currentPage = basename($_SERVER['PHP_SELF']);

function isActive($page, $currentPage) {
    return $page === $currentPage ? 'active' : '';
}
?>

<style>
@media (max-width: 768px) {
    .navbar-menu.mobile-open {
        display: flex !important;
        flex-direction: column !important;
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
        right: 0 !important;
        background: var(--beige) !important;
        padding: 1.5rem !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        z-index: 9999 !important;
        margin-top: 10px !important;
    }
}
</style>

<nav class="navbar">
    <a href="accueil.php" class="navbar-logo">
        <img src="<?= ASSETS_URL ?>/logos/superlogo.webp" alt="SuperCar" class="logo-img">
    </a>

    <ul class="navbar-menu">
        <li><a href="accueil.php"  class="nav-link <?= isActive('accueil.php',  $currentPage) ?>">Accueil</a></li>
        <li><a href="voiture.php"  class="nav-link <?= isActive('voiture.php',  $currentPage) ?>">Voitures</a></li>
        <li><a href="demande.php"  class="nav-link <?= isActive('demande.php',  $currentPage) ?>">Essai</a></li>
        <li><a href="services.php" class="nav-link <?= isActive('services.php', $currentPage) ?>">Services</a></li>
        <li><a href="contact.php"  class="nav-link <?= isActive('contact.php',  $currentPage) ?>">Contact</a></li>

        <?php if (isset($_SESSION['id_client'])): ?>
            <li>
                <a href="deconnexion.php" class="nav-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                    </svg>
                    Déconnexion
                </a>
            </li>
        <?php else: ?>
            <li>
                <a href="connexion.php" class="nav-link" style="display:inline-flex; align-items:center; gap:0.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="color: var(--crimson);">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                    </svg>
                    Connexion
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <button class="nav-toggle" onclick="document.querySelector('.navbar-menu').classList.toggle('mobile-open')">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
</nav>