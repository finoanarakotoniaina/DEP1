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
 * 3. CONSTANTES DE CHEMINS (auto-détectées)
 * Le chemin de base est calculé automatiquement à partir de l'emplacement
 * de ce fichier config.php. Ainsi, le projet fonctionne sur n'importe quel PC
 * (peu importe le nom du dossier) sans modification.
 */
$basePath = str_replace('\\', '/', dirname(__FILE__));         // .../htdocs/supercar
$docRoot  = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']); // .../htdocs
$autoBase = str_replace($docRoot, '', $basePath);              // /supercar

define('BASE_URL',   $autoBase);
define('CLIENT_URL', BASE_URL . '/client');
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

/**
 * 5. RÉCUPÉRATION DES RÉGLAGES CONTACT (chargés une seule fois)
 * Utilisable partout via $contactSettings['email_contact'], etc.
 * Évite de refaire une requête dans chaque page (footer, contact, etc.)
 */
$contactSettings = [];
try {
    $stmt = $db->query("SELECT element_id, content FROM contact_content");
    $contactSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {
    // Si la table n'existe pas encore, on garde un tableau vide
    $contactSettings = [];
}

/**
 * Helper pour récupérer un réglage contact avec fallback
 */
function getContact($key, $default = '—') {
    global $contactSettings;
    return !empty($contactSettings[$key]) ? $contactSettings[$key] : $default;
}
?>