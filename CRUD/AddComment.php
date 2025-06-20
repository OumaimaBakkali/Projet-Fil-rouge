<?php
session_start();
include '../config/database.php';
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_comment"])) {
    $content = trim($_POST['content']);
    $document_id = intval($_POST['document_id']);
    $user_id = $_SESSION['user_id'];
    echo $content;
    echo $document_id;
    echo $user_id;
    if (!empty($content) && !empty($document_id) && !empty($user_id)) {
        $stmt = $db->prepare("INSERT INTO comment (content, created_at , document_id,user_id) 
                              VALUES (:content, NOW(),:document_id, :user_id)");
        $success = $stmt->execute([
            'content' => $content,
            'document_id' => $document_id,
            'user_id' => $user_id
        ]);

        if ($success) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            echo "Erreur lors de l'ajout du commentaire.";
        }
    } else {
        echo "Tous les champs sont requis.";
    }
}
