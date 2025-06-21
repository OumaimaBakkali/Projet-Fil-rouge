<?php
require_once 'config/database.php';
session_start();

if (!isset($_GET['course_id'])) {
    $level_id = $_GET['level_id'] ?? 1;
    $sector_id = $_GET['sector_id'] ?? 1;
    header("Location: list.php?level_id=$level_id&sector_id=$sector_id");
    exit;
}

$course_id = (int)$_GET['course_id'];
$user_id = $_SESSION['user_id'] ?? 0;

$query = "SELECT c.tittle, c.description, sub.subject_name, l.level_name, s.sector_name
          FROM course c
          JOIN program p ON c.program_id = p.program_id
          JOIN subject sub ON p.subject_id = sub.subject_id
          JOIN level l ON p.level_id = l.level_id
          JOIN sector s ON p.sector_id = s.sector_id
          WHERE c.course_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$course) {
    echo "Cours introuvable";
    exit;
}

$docStmt = $db->prepare("SELECT * FROM document WHERE course_id = ? LIMIT 1");
$docStmt->execute([$course_id]);
$doc = $docStmt->fetch(PDO::FETCH_ASSOC);
$document_id = $doc['document_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download']) && $doc) {
    $filePath = $doc['file_path'];
    $fileName = basename($doc['file_name']);
    if (file_exists($filePath)) {
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Content-Length: " . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        echo "Fichier introuvable.";
        exit;
    }
}

$comments = [];
if ($document_id > 0) {
    $stmt = $db->prepare("
        SELECT comment.*, CONCAT(users.first_name, ' ', users.last_name) AS nom
        FROM comment
        JOIN users ON comment.user_id = users.user_id
        WHERE comment.document_id = :document_id
        ORDER BY comment.created_at DESC
    ");
    $stmt->execute(['document_id' => $document_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($course['tittle']) ?> - StudySwap</title>
    <link rel="stylesheet" href="CSS/courseDetails.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <main class="main">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><?= htmlspecialchars($course['tittle']); ?></h2>
            </div>

            <div class="card-content">
                <div class="download-section">
                    <form method="POST">
                        <button type="submit" name="download" class="download-btn">⬇️ Download PDF</button>
                    </form>
                </div>

                <div class="course-details">
                    <div class="detail-item">
                        <div class="detail-icon">📄</div>
                        <span><strong>Subject:</strong> <?= htmlspecialchars($course['subject_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">💬</div>
                        <span><strong>Level:</strong> <?= htmlspecialchars($course['level_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">👤</div>
                        <span><strong>Sector:</strong> <?= htmlspecialchars($course['sector_name']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="comments-section">
            <div class="comments-count">
                <span><?= count($comments) ?> Comment(s)</span>
            </div>

            <div class="comment_container">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <strong><?= htmlspecialchars($comment['nom'] ?? 'Utilisateur') ?></strong><br>
                        <small><?= htmlspecialchars($comment['created_at']) ?></small>

                        <?php if (isset($_GET['edit_comment']) && $_GET['edit_comment'] == $comment['comment_id'] && $user_id == $comment['user_id']): ?>
                            <form method="POST" action="CRUD/updateComment.php">
                                <textarea name="content" required><?= htmlspecialchars($comment['content']) ?></textarea>
                                <input type="hidden" name="comment_id" value="<?= $comment['comment_id'] ?>">
                                <input type="hidden" name="course_id" value="<?= $course_id ?>">
                                <button type="submit">🖊</button>
                                <a href="courseDetails.php?course_id=<?= $course_id ?>">✖</a>
                            </form>
                        <?php else: ?>
                            <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                            <?php if ($user_id == $comment['user_id']): ?>
                                <a href="courseDetails.php?course_id=<?= $course_id ?>&edit_comment=<?= $comment['comment_id'] ?>" class="btn-update">🖊</a>
                                <a href="CRUD/deletComment.php?comment_id=<?= $comment['comment_id'] ?>&course_id=<?= $course_id ?>" class="btn-delete" onclick="return confirm('Supprimer ce commentaire ?');">🗑️</a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <hr>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="CRUD/AddComment.php">
                    <textarea name="content" placeholder="Your comment here..." required></textarea><br>
                    <input type="hidden" name="document_id" value="<?= $document_id ?>">
                    <input type="hidden" name="course_id" value="<?= $course_id ?>">
                    <button type="submit" name="add_comment">Add Comment</button>
                </form>
            <?php else: ?>
                <p>🔒 Connectez-vous pour laisser un commentaire.</p>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>

</html>