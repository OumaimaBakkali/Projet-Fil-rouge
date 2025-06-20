<?php
require_once '../config/database.php';
session_start();

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupère les paramètres depuis l'URL
$comment_id = isset($_GET['comment_id']) ? intval($_GET['comment_id']) : 0;
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// Si le comment_id est valide
if ($comment_id > 0) {
    // Vérifie que le commentaire appartient à l'utilisateur
    $stmt = $db->prepare("SELECT * FROM comment WHERE comment_id = :comment_id AND user_id = :user_id");
    $stmt->execute([
        'comment_id' => $comment_id,
        'user_id' => $user_id
    ]);
    $comment = $stmt->fetch();

    if ($comment) {
        // Supprime le commentaire
        $delStmt = $db->prepare("DELETE FROM comment WHERE comment_id = :comment_id");
        $delStmt->execute(['comment_id' => $comment_id]);
    }
}

// Redirection vers view.php (ou une autre page si course_id absent)
header("Location:  ?course_id=$course_id". $_SERVER['HTTP_REFERER']);
exit;
?>
