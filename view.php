<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudySwap - Swap Notes, Sharpen Minds</title>
    <link rel="stylesheet" href="CSS/view.css">
</head>
<body>
    <!-- Header -->
  <?php include 'header.php'; ?>
    <!-- Main Content -->
    <main class="main">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Level : Level Name</h2>
            </div>

            <div class="card-content">
                <!-- Download Section -->
                <div class="download-section">
                    <button class="download-btn">
                        <span>⬇️</span>
                        download pdf
                    </button>
                </div>

                <!-- Course Details -->
                <div class="course-details">
                    <div class="detail-item">
                        <div class="detail-icon">📄</div>
                        <span class="detail-text">Mathematics</span>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon">💬</div>
                        <span class="detail-text">Limites et dérivation</span>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon">👤</div>
                        <span class="detail-text">Posted by user name</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="comments-section">
            <div class="comments-count">
                <span>0 Comment</span>
            </div>

            <div class="comment-form">
                <input type="text" class="comment-input" placeholder="Write a comment here">
                <button class="add-btn">Add</button>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>