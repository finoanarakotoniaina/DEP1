<?php
// Ce composant suppose que la session est démarrée dans config.php
// et que la fonction e() existe déjà.

// Détection du dossier courant pour construire les liens correctement
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
$basePath = ($currentDir === 'pages') ? '' : 'pages/';
// Si accueil.php est à la racine, basePath = 'pages/', sinon basePath = ''

// Détection de la page active
$currentPage = basename($_SERVER['PHP_SELF']);
function isActive($page, $currentPage) {
    return $page === $currentPage ? 'active' : '';
}
?>
<nav class="fixed top-0 w-full z-50 px-6 lg:px-10 py-6 flex justify-between items-center backdrop-blur-md border-b border-white/5">
    <div class="font-title text-xl tracking-tighter">
        SUPER<span class="text-[var(--crimson)]">CAR</span>
    </div>
    
    <ul class="hidden md:flex gap-8 font-title text-[10px] tracking-[0.2em] uppercase items-center">
        <li><a href="<?= $basePath ?>accueil.php" class="nav-link <?= isActive('accueil.php', $currentPage) ?>">Accueil</a></li>
        <li><a href="<?= $basePath ?>voiture.php" class="nav-link <?= isActive('voiture.php', $currentPage) ?>">Voitures</a></li>
        <li><a href="<?= $basePath ?>demande.php" class="nav-link <?= isActive('demande.php', $currentPage) ?>">Essai</a></li>
        <li><a href="<?= $basePath ?>services.php" class="nav-link <?= isActive('services.php', $currentPage) ?>">Services</a></li>
        <li><a href="<?= $basePath ?>contact.php" class="nav-link <?= isActive('contact.php', $currentPage) ?>">Contact</a></li>
        |

        <?php if (isset($_SESSION['id_client'])): ?>
            <li>
                <a href="<?= $basePath ?>deconnexion.php" class="bg-[var(--crimson)] px-4 py-2 rounded text-white hover:bg-white hover:text-black transition-all duration-300 flex items-center gap-2">
                    <i class="fa-solid fa-door-open text-[18px]"></i> Déconnexion
                </a>
            </li>
        <?php else: ?>
            <li>
                <strong>
                    <a href="<?= $basePath ?>connexion.php" class="nav-link flex items-center gap-2">
                        <i class="fa-solid fa-user text-[var(--crimson)] text-[18px]"></i> Connexion
                    </a>
                </strong>
            </li>
        <?php endif; ?>
    </ul>
</nav>