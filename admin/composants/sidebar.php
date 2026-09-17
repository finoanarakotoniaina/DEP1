<?php
if (!isset($currentPage)) {
    $currentPage = basename($_SERVER['PHP_SELF']);
}
if (!isset($adminFullName)) {
    $adminFullName = "Admin";
}

// Pages considérées comme "Gestion"
$gestionPages = ['tableaudebord.php', 'admin_voitures.php', 'admin_demande.php', 'admin_contact.php'];
// Pages considérées comme "Modification de page"
$editionPages = ['admin_accueil.php', 'admin_affichage_contact.php', 'admin_services.php'];

// Chemin absolu du logo (depuis la racine web)
$logoPath = '../assets/logos/superlogo.png';
?>
<aside class="w-72 sidebar p-6 flex flex-col z-20">
    
    <!-- LOGO EN HAUT, CENTRÉ -->
    <div class="mb-4 flex items-center justify-center">
        <a href="tableaudebord.php" class="block">
            <img src="<?= htmlspecialchars($logoPath) ?>" 
                 alt="SUPERCAR Logo" 
                 class="h-32 w-auto object-contain mx-auto"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <span class="font-title text-xl tracking-tighter uppercase hidden">
                SUPER<span class="text-[var(--crimson,#c1121f)]">CAR</span>
            </span>
        </a>
    </div>
    
    <nav class="flex-1 space-y-6">

        <!-- SECTION GESTION -->
        <div>
            <p class="text-[9px] text-gray-500 font-bold uppercase tracking-[2px] mb-3 px-2">
                <i class="fa-solid fa-shield-halved mr-2 text-[var(--crimson,#c1121f)]"></i>Gestion
            </p>
            <div class="space-y-1">
                <a href="tableaudebord.php" class="nav-link-admin <?= ($currentPage == 'tableaudebord.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-gauge-high w-5"></i> Tableau de bord
                </a>
                <a href="admin_voitures.php" class="nav-link-admin <?= ($currentPage == 'admin_voitures.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-car-side w-5"></i> Inventaire
                </a>
                <a href="admin_demande.php" class="nav-link-admin <?= ($currentPage == 'admin_demande.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-calendar-check w-5"></i> Demandes
                </a>
                <a href="admin_contact.php" class="nav-link-admin <?= ($currentPage == 'admin_contact.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-envelope w-5"></i> Messages
                </a>
            </div>
        </div>

        <!-- SECTION MODIFICATION DE PAGE -->
        <div>
            <p class="text-[9px] text-gray-500 font-bold uppercase tracking-[2px] mb-3 px-2">
                <i class="fa-solid fa-pen-to-square mr-2 text-[var(--crimson,#c1121f)]"></i>Édition des pages
            </p>
            <div class="space-y-1">
                <a href="admin_accueil.php" class="nav-link-admin <?= ($currentPage == 'admin_accueil.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-house w-5"></i> Page Accueil
                </a>
                <a href="admin_affichage_contact.php" class="nav-link-admin <?= ($currentPage == 'admin_affichage_contact.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-address-book w-5"></i> Page Contact
                </a>
                <a href="admin_services.php" class="nav-link-admin <?= ($currentPage == 'admin_services.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-screwdriver-wrench w-5"></i> Services
                </a>
            </div>
        </div>

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