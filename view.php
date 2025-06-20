<?php
require_once 'config/database.php';
session_start();

// Vérification de course_id
if (!isset($_GET['course_id'])) {
    $level_id = $_GET['level_id'] ?? 1;
    $sector_id = $_GET['sector_id'] ?? 1;
    header("Location: list.php?level_id=$level_id&sector_id=$sector_id");
    exit;
}

$course_id = (int)$_GET['course_id'];

// Récupération des infos du cours
$query = "SELECT c.tittle, c.description, sub.subject_name, l.level_name, s.sector_name
          FROM course c
          INNER JOIN program p ON c.program_id = p.program_id
          INNER JOIN subject sub ON p.subject_id = sub.subject_id
          INNER JOIN level l ON p.level_id = l.level_id
          INNER JOIN sector s ON p.Sector_id = s.Sector_id
          WHERE c.course_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    echo "<p>Cours non trouvé</p>";
    exit;
}

// Récupérer le document lié au cours
$docStmt = $db->prepare("SELECT * FROM document WHERE course_id = ? LIMIT 1");
$docStmt->execute([$course_id]);
$doc = $docStmt->fetch(PDO::FETCH_ASSOC);
$document_id = $doc['document_id'] ?? 0;

// Télécharger le document si demandé
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["download"])) {
    if ($doc) {
        $fileName = basename($doc["file_name"]);
        $filePath = $doc["file_path"];

        if (file_exists($filePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            flush();
            readfile($filePath);
            exit;
        } else {
            http_response_code(404);
            echo "Fichier introuvable.";
        }
    }
}

// Récupérer les commentaires liés au document

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($course['tittle']) ?> - StudySwap</title>
    <link rel="stylesheet" href="CSS/view.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <main class="main">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><?= htmlspecialchars($course['tittle']); ?></h2>
            </div>

            <div class="card-content">
                <!-- Section Téléchargement -->
                <div class="download-section">
                    <form method="POST">
                        <button type="submit" name="download" class="download-btn">⬇️ Télécharger le PDF</button>
                    </form>
                </div>

                <!-- Détails du cours -->
                <div class="course-details">
                    <div class="detail-item">
                        <div class="detail-icon">📄</div>
                        <span><strong>Matière:</strong> <?= htmlspecialchars($course['subject_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">💬</div>
                        <span><strong>Niveau:</strong> <?= htmlspecialchars($course['level_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">👤</div>
                        <span><strong>Secteur:</strong> <?= htmlspecialchars($course['sector_name']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Commentaires -->
        <div class="comments-section">
            <div class="comments-count">
                <span><?= count($comments) ?> Commentaire(s)</span>
            </div>

            <div class="comment_container">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <strong><?= htmlspecialchars($comment['nom'] ?? 'Utilisateur') ?></strong><br>
                        <small><?= htmlspecialchars($comment['created_at']) ?></small>
                        <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>

                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                            <a href="CRUD/updateComment.php?comment_id=<?= $comment['comment_id'] ?>&course_id=<?= $course_id ?>" class="btn-update">Modifier</a>
                            <a href="CRUD/deleteComment.php?comment_id=<?= $comment['comment_id'] ?>&course_id=<?= $course_id ?>"
                                onclick="return confirm('Voulez-vous vraiment supprimer ce commentaire ?');"
                                class="btn-delete">Supprimer</a>
                        <?php endif; ?>

                        <hr>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Formulaire d'ajout de commentaire -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="./CRUD/AddComment.php">
                    <textarea name="content" placeholder="Votre commentaire ici..." required></textarea><br>
                    <input type="hidden" name="document_id" value="<?= $document_id ?>">
                    <button type="submit" name="add_comment">Ajouter un commentaire</button>
                </form>
            <?php else: ?>
                <p>🔒 Connectez-vous pour laisser un commentaire.</p>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>

</html>