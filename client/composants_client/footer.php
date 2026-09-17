<?php
/**
 * Footer commun à toutes les pages client
 * Utilise $contactSettings chargé dans config.php
 */
?>
<footer class="footer">
    <div class="container">
        <div class="footer-grid">

            <!-- COLONNE 1 : LOGO -->
            <div class="footer-logo">
                SUPER<span>CAR</span>
            </div>

            <!-- COLONNE 2 : REDIRECTIONS -->
            <ul class="footer-col">
                <li><a href="accueil.php"  class="footer-link">Accueil</a></li>
                <li><a href="voiture.php"  class="footer-link">Voitures</a></li>
                <li><a href="demande.php"  class="footer-link">Essai</a></li>
                <li><a href="services.php" class="footer-link">Services</a></li>
                <li><a href="contact.php"  class="footer-link">Contact</a></li>
            </ul>

            <!-- COLONNE 3 : MENTIONS + COPYRIGHT -->
            <div class="footer-col">
                <a href="mentions-legales.php" class="footer-link">Mentions légales</a>
                <a href="cgu.php" class="footer-link">Conditions Générales d'Utilisation</a>
                <p class="footer-copy">
                    &copy; 2026 SUPERCAR<br>
                    Tous droits réservés
                </p>
            </div>

            <!-- COLONNE 4 : CONTACT (BDD) -->
            <div class="footer-col">
                <a href="mailto:<?= htmlspecialchars(getContact('email_contact')) ?>" class="footer-link">
                    <?= htmlspecialchars(getContact('email_contact')) ?>
                </a>
                <a href="tel:<?= htmlspecialchars(getContact('tel_contact')) ?>" class="footer-link">
                    <?= htmlspecialchars(getContact('tel_contact')) ?>
                </a>
            </div>

        </div>
    </div>
</footer>

</body>
</html>