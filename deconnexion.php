<?php
session_start(); // On récupère la session
session_unset(); // On vide les variables de session
session_destroy(); // On détruit la session

// Redirection vers l'accueil ou la page de connexion
header("Location: accueil.php");
exit();
?>