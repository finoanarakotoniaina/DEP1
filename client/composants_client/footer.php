<?php
/**
 * Footer commun à toutes les pages client
 * Utilise $contactSettings chargé dans config.php
 */
?>
<footer class="border-t border-white/5 mt-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-12">
        <div class="flex flex-col md:flex-row justify-between items-start gap-10">

            <!-- COLONNE 1 : LOGO -->
            <div class="font-title text-lg tracking-tighter">
                SUPER<span class="text-[var(--crimson)]">CAR</span>
            </div>

            <!-- COLONNE 2 : REDIRECTIONS -->
            <ul class="flex flex-col gap-3 font-title text-[10px] tracking-[0.2em] uppercase text-gray-400">
                <li><a href="accueil.php"  class="hover:text-[var(--crimson)] transition-colors">Accueil</a></li>
                <li><a href="voiture.php"  class="hover:text-[var(--crimson)] transition-colors">Voitures</a></li>
                <li><a href="demande.php"  class="hover:text-[var(--crimson)] transition-colors">Essai</a></li>
                <li><a href="services.php" class="hover:text-[var(--crimson)] transition-colors">Services</a></li>
                <li><a href="contact.php"  class="hover:text-[var(--crimson)] transition-colors">Contact</a></li>
            </ul>

            <!-- COLONNE 3 : MENTIONS LÉGALES + COPYRIGHT -->
            <div class="flex flex-col gap-3 font-title text-[10px] tracking-[0.2em] uppercase text-gray-400">
                <a href="mentions-legales.php" class="hover:text-[var(--crimson)] transition-colors">
                    Mentions légales
                </a>
                <p class="text-gray-500">
                    &copy; 2026 SUPERCAR<br>
                    Tous droits réservés
                </p>
            </div>

            <!-- COLONNE 4 : CONTACT (BDD) -->
            <div class="flex flex-col gap-2 font-title text-[10px] tracking-[0.2em] uppercase text-gray-400">
                <a href="mailto:<?= htmlspecialchars(getContact('email_contact')) ?>" 
                   class="hover:text-[var(--crimson)] transition-colors normal-case tracking-normal">
                    <?= htmlspecialchars(getContact('email_contact')) ?>
                </a>
                <a href="tel:<?= htmlspecialchars(getContact('tel_contact')) ?>" 
                   class="hover:text-[var(--crimson)] transition-colors">
                    <?= htmlspecialchars(getContact('tel_contact')) ?>
                </a>
            </div>

        </div>
    </div>
</footer>

</body>
</html>