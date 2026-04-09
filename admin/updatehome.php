<?php
include 'config2.php';

if (isset($_POST['id']) && isset($_POST['content'])) {
    $id = $_POST['id'];
    $content = $_POST['content'];

    // On prépare la requête pour mettre à jour le bon élément
    $stmt = $db->prepare("UPDATE home_content SET content = ? WHERE element_id = ?");
    $stmt->execute([$content, $id]);

    echo "Succès";
}
?>