<?php
// 1. Gestion de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Paramètres de connexion à la base de données
$host     = 'localhost';
$dbname   = 'supercar';
$username = 'root';
$password = '';

try {
    $db = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

/**
 * 3. CONSTANTES DE CHEMINS
 * Ces constantes centralisent TOUS les chemins d'images.
 * Si tu changes un dossier un jour, tu modifies UNIQUEMENT ici.
 */
define('BASE_URL',   '/xamp/SUPERCAR/DEP1');  // ⚠️ vérifie ce chemin
define('UPLOAD_URL', BASE_URL . '/image_url');
define('ASSETS_URL', BASE_URL . '/assets');

/**
 * 4. Fonction magique pour récupérer les contenus
 */
function getContent($element_id) {
    global $db;
    try {
        // Recherche dans home_content
        $stmt = $db->prepare("SELECT content FROM home_content WHERE element_id = ?");
        $stmt->execute([$element_id]);
        $row = $stmt->fetch();
        if ($row) return $row['content'];

        // Recherche dans services_content
        $stmt = $db->prepare("SELECT content FROM services_content WHERE element_id = ?");
        $stmt->execute([$element_id]);
        $row = $stmt->fetch();
        if ($row) return $row['content'];

        return "Texte [$element_id] manquant en BDD";

    } catch (Exception $e) {
        return "Erreur SQL : " . $e->getMessage();
    }
}
?>