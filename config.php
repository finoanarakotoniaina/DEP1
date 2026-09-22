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
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    error_log("Erreur BDD : " . $e->getMessage());
    die("Erreur de connexion à la base de données.");
}

// Alias globaux pour cohérence (évite les warnings dans les IDE)
$pdo  = $db;
$conn = $db;

/**
 * 3. CONSTANTES DE CHEMINS (auto-détectées)
 * Le chemin de base est calculé automatiquement à partir de l'emplacement
 * de ce fichier config.php. Ainsi, le projet fonctionne sur n'importe quel PC
 * (peu importe le nom du dossier) sans modification.
 */
$basePath = str_replace('\\', '/', dirname(__FILE__));
$docRoot  = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$autoBase = str_replace($docRoot, '', $basePath);

define('BASE_URL',   $autoBase);
define('CLIENT_URL', BASE_URL . '/client');
define('UPLOAD_URL', BASE_URL . '/image_url');
define('ASSETS_URL', BASE_URL . '/assets');

/**
 * 4. Fonction magique pour récupérer les contenus
 */
function getContent(string $element_id): string {
    global $db;
    try {
        // Recherche dans home_content
        $stmt = $db->prepare("SELECT content FROM home_content WHERE element_id = ?");
        $stmt->execute([$element_id]);
        $row = $stmt->fetch();
        if ($row) return (string) $row['content'];

        // Recherche dans services_content
        $stmt = $db->prepare("SELECT content FROM services_content WHERE element_id = ?");
        $stmt->execute([$element_id]);
        $row = $stmt->fetch();
        if ($row) return (string) $row['content'];

        return "Texte [$element_id] manquant en BDD";

    } catch (Exception $e) {
        return "Erreur SQL : " . $e->getMessage();
    }
}

/**
 * 5. RÉCUPÉRATION DES RÉGLAGES CONTACT (chargés une seule fois)
 * Utilisable partout via $contactSettings['email_contact'], etc.
 */
$contactSettings = [];
try {
    $stmt = $db->query("SELECT element_id, content FROM contact_content");
    $contactSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {
    $contactSettings = [];
}

/**
 * Helper pour récupérer un réglage contact avec fallback
 */
function getContact(string $key, string $default = '—'): string {
    global $contactSettings;
    return !empty($contactSettings[$key]) ? (string) $contactSettings[$key] : $default;
}

/**
 * 6. Vérification de session admin
 * À appeler dans les pages admin pour remplacer le bloc if (!isset(...))
 */
function requireAdmin(): void {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit();
    }
}
?>