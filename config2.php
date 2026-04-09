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
 * Elle cherche dans la table 'home_content' l'identifiant (ex: hero_title)
 */
function getContent($element_id) {
    global $db;
    try {
        $stmt = $db->prepare("SELECT content FROM home_content WHERE element_id = ?");
        $stmt->execute([$element_id]);
        $row = $stmt->fetch();
        
        // Si le texte existe en base, on le donne, sinon on affiche une alerte
        return $row ? $row['content'] : "Texte [$element_id] manquant en BDD";
    } catch (Exception $e) {
        return "Erreur SQL : " . $e->getMessage();
    }
}
?>