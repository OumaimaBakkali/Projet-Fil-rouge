<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_id = intval($_POST['comment_id']);
    $content = trim($_POST['content']);
    $course_id = intval($_POST['course_id']);

    if ($comment_id <= 0 || empty($content)) {
        header("Location: ../courseDetails.php?course_id=$course_id");
        exit;
    }

    // Vérifier que ce commentaire appartient à l'utilisateur connecté
    $stmt = $db->prepare("SELECT user_id FROM comment WHERE comment_id = ?");
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($comment && $comment['user_id'] == $_SESSION['user_id']) {
        $updateStmt = $db->prepare("UPDATE comment SET content = ? WHERE comment_id = ?");
        $updateStmt->execute([$content, $comment_id]);
    }

    header("Location: ../courseDetails.php?course_id=$course_id");
    exit;
}
