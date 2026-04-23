<?php
// 1. Gestion de la session (indispensable pour la connexion client/admin)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Paramètres de connexion à la base de données
$host     = 'localhost';
$dbname   = 'supercar'; // Ta base de données
$username = 'root';      // Utilisateur par défaut (XAMPP/WAMP)
$password = '';          // Mot de passe par défaut (vide sur Windows)

try {
    // 3. Création de la connexion PDO avec support des accents (utf8mb4)
    $db = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            // Affiche les erreurs SQL précises pendant que tu développes
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Récupère les données sous forme de tableaux associatifs
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    // Si la connexion échoue, on arrête tout et on affiche l'erreur
    die("Erreur de connexion : " . $e->getMessage());
}

/**
 * 4. Fonction magique pour récupérer tes contenus
 * Elle cherche d'abord dans 'home_content', puis dans 'services_content'
 */
function getContent($element_id) {
    global $db;
    try {
        // --- RECHERCHE DANS LA TABLE ACCUEIL ---
        $stmt = $db->prepare("SELECT content FROM home_content WHERE element_id = ?");
        $stmt->execute([$element_id]);
        $row = $stmt->fetch();
        
        if ($row) {
            return $row['content'];
        }

        // --- RECHERCHE DANS LA TABLE SERVICES (si non trouvé dans accueil) ---
        $stmt = $db->prepare("SELECT content FROM services_content WHERE element_id = ?");
        $stmt->execute([$element_id]);
        $row = $stmt->fetch();

        if ($row) {
            return $row['content'];
        }

        // Si le texte n'existe dans aucune des deux tables
        return "Texte [$element_id] manquant en BDD";

    } catch (Exception $e) {
        return "Erreur SQL : " . $e->getMessage();
    }
}
?>