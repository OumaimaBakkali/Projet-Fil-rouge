<?php
require_once '../config/database.php';
session_start();

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_id = intval($_POST['comment_id']);
    $content = trim($_POST['content']);
    $course_id = intval($_POST['course_id']);
    $user_id = $_SESSION['user_id'];

    if ($comment_id && !empty($content)) {
        // Vérifie que le commentaire appartient à l'utilisateur
        $stmt = $db->prepare("SELECT user_id FROM comment WHERE comment_id = :comment_id");
        $stmt->execute(['comment_id' => $comment_id]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($comment && $comment['user_id'] == $user_id) {
            // Mise à jour
            $update = $db->prepare("UPDATE comment SET content = :content WHERE comment_id = :comment_id");
            $update->execute([
                'content' => $content,
                'comment_id' => $comment_id
            ]);
        }
    }

    header("Location: ../view.php?course_id=$course_id");
    exit;
}
