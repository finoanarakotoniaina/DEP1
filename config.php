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
    error_log("[SuperCar] Erreur BDD : " . $e->getMessage());
    die("Plateforme momentanément indisponible. Veuillez réessayer plus tard.");
}

// Alias globaux pour cohérence (évite les warnings dans les IDE)
$pdo  = $db;
$conn = $db;

/**
 * Message d'erreur générique (BDD / système)
 * Utilisé partout pour uniformiser et ne pas exposer de détails techniques
 */
define('MSG_ERREUR', "Plateforme momentanément indisponible. Veuillez réessayer plus tard.");

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
        error_log("[SuperCar] getContent : " . $e->getMessage());
        return MSG_ERREUR;
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
    error_log("[SuperCar] Contact settings : " . $e->getMessage());
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

/**
 * 7. CONVERSION AUTOMATIQUE DES IMAGES EN WEBP
 */
function convertToWebp(string $source, string $destination, int $quality = 80): bool {
    if (!function_exists('imagewebp')) return false;

    $info = @getimagesize($source);
    if (!$info) return false;

    $img = false;
    switch ($info['mime']) {
        case 'image/jpeg': $img = @imagecreatefromjpeg($source); break;
        case 'image/png':  $img = @imagecreatefrompng($source);  break;
        case 'image/gif':  $img = @imagecreatefromgif($source);  break;
        case 'image/webp': $img = @imagecreatefromwebp($source); break;
        case 'image/bmp':  $img = @imagecreatefrombmp($source);  break;
        default: return false;
    }
    if (!$img) return false;

    imagepalettetotruecolor($img);
    imagealphablending($img, true);
    imagesavealpha($img, true);

    $ok = imagewebp($img, $destination, $quality);
    imagedestroy($img);
    return $ok;
}

function uploadAsWebp(array $file, string $dir, string $prefix = 'img'): ?string {
    if (!isset($file['tmp_name']) || $file['error'] !== 0) return null;
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    $newName = $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.webp';
    $dest = rtrim($dir, '/') . '/' . $newName;

    if (convertToWebp($file['tmp_name'], $dest)) return $newName;
    return null;
}
?>