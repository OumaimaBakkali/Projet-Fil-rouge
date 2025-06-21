<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$comment_id = isset($_GET['comment_id']) ? intval($_GET['comment_id']) : 0;
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 1;

if ($comment_id > 0) {
    // Vérifier que le commentaire appartient bien à l'utilisateur
    $stmt = $db->prepare("SELECT user_id FROM comment WHERE comment_id = ?");
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($comment && $comment['user_id'] == $_SESSION['user_id']) {
        // Supprimer le commentaire
        $deleteStmt = $db->prepare("DELETE FROM comment WHERE comment_id = ?");
        $deleteStmt->execute([$comment_id]);
    }
}

header("Location: ../courseDetails.php?course_id=$course_id");
exit;
