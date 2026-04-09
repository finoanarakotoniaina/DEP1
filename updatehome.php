<?php
// 1. On inclut la connexion
include 'config.php';

// 2. On vérifie la réception des données
if (isset($_POST['id']) && isset($_POST['content'])) {
    
    $id = $_POST['id'];
    $content = $_POST['content'];

    // --- LA CORRECTION EST ICI ---
    // On force l'utilisation de la variable $db qui vient de config2.php
    global $db; 

    // Si jamais ta variable dans config2.php ne s'appelle pas $db, 
    // vérifie son nom (ça peut être $conn, $pdo, etc.)
    
    if (!$db) {
        die("Erreur : La connexion à la base de données est introuvable.");
    }

    try {
        $stmt = $db->prepare("UPDATE home_content SET content = ? WHERE element_id = ?");
        $result = $stmt->execute([$content, $id]);

        if ($result) {
            echo "Succès"; 
        } else {
            echo "Erreur lors de la mise à jour";
        }
        
    } catch (PDOException $e) {
        echo "Erreur SQL : " . $e->getMessage();
    }
} else {
    echo "Données manquantes";
}
?>