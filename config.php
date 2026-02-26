<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Paramètres de connexion
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "supercar"; 

// Création de la connexion
$conn = new mysqli($host, $user, $pass, $dbname);

// Vérification
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Optionnel : Forcer l'UTF-8 pour les accents
$conn->set_charset("utf8mb4");
?>