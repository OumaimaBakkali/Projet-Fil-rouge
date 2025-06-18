<?php
require_once 'config/database.php';

if (!isset($_GET['course_id'])) {
    header('Location: index.php');
    exit;
}

$course_id = (int)$_GET['course_id'];

// Get course details with all related information
$query = "SELECT c.course_id, c.title, c.description, c.created_at,
                 l.level_name, s.sector_name, sub.subject_name,
                 d.document_id, d.file_name, d.file_path, d.file_size, d.type as doc_type
          FROM course c
          INNER JOIN program p ON c.program_id = p.program_id
          INNER JOIN level l ON p.level_id = l.level_id
          INNER JOIN sector s ON p.Sector_id = s.Sector_id
          INNER JOIN subject sub ON p.subject_id = sub.subject_id
          LEFT JOIN document d ON c.course_id = d.course_id
          WHERE c.course_id = :course_id";

$stmt = $db->prepare($query);
$stmt->bindParam(':course_id', $course_id);
$stmt->execute();
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    header('Location: index.php');
    exit;
}

// Get comments for this course
$query = "SELECT comment_id, content, author_name, created_at 
          FROM comment 
          WHERE course_id = :course_id 
          ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':course_id', $course_id);
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $content = trim($_POST['content'] ?? '');
    $author_name = trim($_POST['author_name'] ?? 'Anonymous');
    
    if (!empty($content)) {
        $query = "INSERT INTO comment (course_id, content, author_name, created_at) VALUES (:course_id, :content, :author_name, NOW())";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':author_name', $author_name);
        
        if ($stmt->execute()) {
            header("Location: course_details.php?course_id=$course_id");
            exit;
        }
    }
}

// Handle comment deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    $comment_id = (int)$_POST['comment_id'];
    
    $query = "DELETE FROM comment WHERE comment_id = :comment_id AND course_id = :course_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':comment_id', $comment_id);
    $stmt->bindParam(':course_id', $course_id);
    
    if ($stmt->execute()) {
        header("Location: course_details.php?course_id=$course_id");
        exit;
    }
}

function formatFileSize($bytes) {
    if ($bytes == 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    
    return date('M j, Y', strtotime($datetime));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudySwap - <?php echo htmlspecialchars($course['title']); ?></title>
    <link rel="stylesheet" href="CSS/view.css">
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
    <?php include "header.php"; ?>

    <main class="main">
        <!-- Course Information Card -->
        <div class="course-card">
            <div class="course-header">
                <div class="breadcrumb">
                    <a href="index.php">Home</a> > 
                    <a href="levels.php">Levels</a> > 
                    <span><?php echo htmlspecialchars($course['level_name']); ?></span> > 
                    <span><?php echo htmlspecialchars($course['sector_name']); ?></span>
                </div>
                <h1 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h1>
                <p class="course-meta">
                    📚 <?php echo htmlspecialchars($course['subject_name']); ?> • 
                    🎓 <?php echo htmlspecialchars($course['level_name']); ?> • 
                    📅 <?php echo timeAgo($course['created_at']); ?>
                </p>
            </div>

            <?php if ($course['description']): ?>
                <div class="course-description">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                </div>
            <?php endif; ?>

            <!-- Document Section -->
            <?php if ($course['document_id']): ?>
                <div class="document-section">
                    <h3>📄 Document</h3>
                    <div class="document-card">
                        <div class="document-info">
                            <div class="document-icon">
                                <?php
                                $extension = strtolower(pathinfo($course['file_name'], PATHINFO_EXTENSION));
                                switch($extension) {
                                    case 'pdf': echo '📕'; break;
                                    case 'doc':
                                    case 'docx': echo '📘'; break;
                                    case 'ppt':
                                    case 'pptx': echo '📊'; break;
                                    default: echo '📄'; break;
                                }
                                ?>
                            </div>
                            <div class="document-details">
                                <h4><?php echo htmlspecialchars($course['file_name']); ?></h4>
                                <p class="document-meta">
                                    <?php echo ucfirst($course['doc_type']); ?> • 
                                    <?php echo formatFileSize($course['file_size']); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="document-actions">
                            <?php if (strtolower(pathinfo($course['file_name'], PATHINFO_EXTENSION)) === 'pdf'): ?>
                                <button class="preview-btn" onclick="previewPDF('<?php echo $course['file_path']; ?>')">
                                    👁️ Preview
                                </button>
                            <?php endif; ?>
                            <a href="<?php echo $course['file_path']; ?>" download class="download-btn">
                                ⬇️ Download
                            </a>
                        </div>
                    </div>

                    <!-- PDF Preview Modal -->
                    <?php if (strtolower(pathinfo($course['file_name'], PATHINFO_EXTENSION)) === 'pdf'): ?>
                        <div id="pdfModal" class="pdf-modal">
                            <div class="pdf-modal-content">
                                <div class="pdf-modal-header">
                                    <h3>PDF Preview</h3>
                                    <button class="close-modal" onclick="closePDFModal()">&times;</button>
                                </div>
                                <div class="pdf-container">
                                    <iframe id="pdfViewer" src="" width="100%" height="600px"></iframe>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Comments Section -->
        <div class="comments-section">
            <div class="comments-header">
                <h3>💬 Comments (<?php echo count($comments); ?>)</h3>
            </div>

            <!-- Add Comment Form -->
            <div class="add-comment-form">
                <form method="POST" class="comment-form">
                    <div class="form-row">
                        <input type="text" name="author_name" placeholder="Your name" class="author-input" required>
                    </div>
                    <div class="form-row">
                        <textarea name="content" placeholder="Write your comment here..." class="comment-textarea" required></textarea>
                    </div>
                    <button type="submit" name="add_comment" class="add-comment-btn">Add Comment</button>
                </form>
            </div>

            <!-- Comments List -->
            <div class="comments-list">
                <?php if (empty($comments)): ?>
                    <div class="no-comments">
                        <p>💭 No comments yet. Be the first to comment!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-item">
                            <div class="comment-header">
                                <div class="comment-author">
                                    <span class="author-avatar">👤</span>
                                    <span class="author-name"><?php echo htmlspecialchars($comment['author_name']); ?></span>
                                </div>
                                <div class="comment-actions">
                                    <span class="comment-time"><?php echo timeAgo($comment['created_at']); ?></span>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this comment?')">
                                        <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                                        <button type="submit" name="delete_comment" class="delete-comment-btn" title="Delete comment">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="comment-content">
                                <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include "footer.php"; ?>

    <script>
        function previewPDF(filePath) {
            const modal = document.getElementById('pdfModal');
            const viewer = document.getElementById('pdfViewer');
            viewer.src = filePath;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closePDFModal() {
            const modal = document.getElementById('pdfModal');
            const viewer = document.getElementById('pdfViewer');
            modal.style.display = 'none';
            viewer.src = '';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('pdfModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closePDFModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePDFModal();
            }
        });

        // Auto-resize textarea
        const textarea = document.querySelector('.comment-textarea');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        }
    </script>
</body>
</html>