<?php
include 'config.php';

// Optionnel : Pour voir les erreurs si ça ne marche toujours pas
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['id']) && isset($_POST['content'])) {
    $id = $_POST['id'];
    $content = $_POST['content'];

    // On utilise $db car c'est le nom choisi dans ton config.php
    try {
        // 1. On vérifie si l'élément existe déjà
        $check = $db->prepare("SELECT COUNT(*) FROM home_content WHERE element_id = ?");
        $check->execute([$id]);

        if ($check->fetchColumn() > 0) {
            // 2. Si oui, on met à jour
            $sql = "UPDATE home_content SET content = ? WHERE element_id = ?";
        } else {
            // 3. Si non (pour brand 2, 3, 4), on l'insère proprement
            $sql = "INSERT INTO home_content (content, element_id) VALUES (?, ?)";
        }

        $stmt = $db->prepare($sql);
        
        if ($stmt->execute([$content, $id])) {
            echo "Succès";
        } else {
            echo "Erreur lors de l'enregistrement";
        }

    } catch (PDOException $e) {
        echo "Erreur SQL : " . $e->getMessage();
    }
} else {
    echo "Données manquantes";
}
?>