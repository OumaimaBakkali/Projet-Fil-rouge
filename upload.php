<?php
require_once 'config/database.php';
session_start();


if (empty($_SESSION['user_id'])) {
    header("Location: ./auth/login.php");
    exit;
}
$message = '';
$message_type = '';

// Handle AJAX requests for dynamic dropdowns
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    if ($_GET['ajax'] === 'sectors' && isset($_GET['level_id'])) {
        $level_id = (int)$_GET['level_id'];
        $query = "SELECT s.Sector_id, s.sector_name 
                  FROM sector s 
                  INNER JOIN level_sector ls ON s.Sector_id = ls.Sector_id 
                  WHERE ls.level_id = :level_id 
                  ORDER BY s.sector_name";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':level_id', $level_id);
        $stmt->execute();
        echo json_encode($stmt->fetchAll());
        exit;
    }

    if ($_GET['ajax'] === 'subjects' && isset($_GET['level_id'], $_GET['sector_id'])) {
        $level_id = (int)$_GET['level_id'];
        $sector_id = (int)$_GET['sector_id'];
        $query = "SELECT DISTINCT sub.subject_id, sub.subject_name 
                  FROM subject sub
                  INNER JOIN program p ON sub.subject_id = p.subject_id
                  WHERE p.level_id = :level_id AND p.Sector_id = :sector_id
                  ORDER BY sub.subject_name";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':level_id', $level_id);
        $stmt->bindParam(':sector_id', $sector_id);
        $stmt->execute();
        echo json_encode($stmt->fetchAll());
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $type = $_POST['type'] ?? '';
    $level_id = (int)($_POST['level_id'] ?? 0);
    $sector_id = (int)($_POST['sector_id'] ?? 0);
    $subject_id = (int)($_POST['subject_id'] ?? 0);

    // Validation
    if (empty($title) || empty($type) || !$level_id || !$sector_id || !$subject_id) {
        $message = 'Please fill in all required fields.';
        $message_type = 'error';
    } elseif (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        $message = 'Please select a valid document file.';
        $message_type = 'error';
    } else {
        $file = $_FILES['document'];
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'text/plain'];
        $max_size = 10 * 1024 * 1024; // 10MB

        if (!in_array($file['type'], $allowed_types)) {
            $message = 'Invalid file type. Please upload PDF, DOC, DOCX, PPT, PPTX, or TXT files.';
            $message_type = 'error';
        } elseif ($file['size'] > $max_size) {
            $message = 'File size must be less than 10MB.';
            $message_type = 'error';
        } else {
            try {
                // ... (previous validation and file upload logic)

                // Get or create program
                $query = "SELECT program_id FROM program WHERE level_id = :level_id AND Sector_id = :sector_id AND subject_id = :subject_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':level_id', $level_id);
                $stmt->bindParam(':sector_id', $sector_id);
                $stmt->bindParam(':subject_id', $subject_id);
                $stmt->execute();
                $program = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$program) {
                    // Create program if it doesn't exist
                    $query = "INSERT INTO program (level_id, Sector_id, subject_id) VALUES (:level_id, :sector_id, :subject_id)";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':level_id', $level_id);
                    $stmt->bindParam(':sector_id', $sector_id);
                    $stmt->bindParam(':subject_id', $subject_id);
                    $stmt->execute();
                    $program_id = $db->lastInsertId();
                } else {
                    $program_id = $program['program_id'];
                }

                // Insert course
                $query = "INSERT INTO course (tittle, program_id) VALUES (:tittle, :program_id)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':tittle', $title);
                $stmt->bindParam(':program_id', $program_id);
                $stmt->execute();
                $course_id = $db->lastInsertId(); // Get the newly inserted course_id
                //file path start here
                if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['document']['tmp_name'];
                    $fileName = $_FILES['document']['name'];
                    $fileSize = $_FILES['document']['size'];
                    $fileType = $_FILES['document']['type'];

                    // You can sanitize the filename if needed
                    $fileName = basename($fileName);

                    // Specify the directory where you want to move the uploaded file
                    $uploadDir = 'uploads/';

                    // Make sure the uploads directory exists, if not, create it
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $destPath = $uploadDir . $fileName;
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $query = "INSERT INTO document (course_id, file_name, file_path, file_size, type,user_id) VALUES (:course_id, :file_name, :file_path, :file_size, :type,:user_id)";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':course_id', $course_id);
                        $stmt->bindParam(':file_name', $file['name']);
                        $stmt->bindParam(':file_path', $destPath);
                        $stmt->bindParam(':file_size', $file['size']);
                        $stmt->bindParam(':type', $type);
                        $stmt->bindParam(':user_id', $_SESSION['user_id']);
                        $stmt->execute();

                        $message = "Document uploaded successfully!";
                        $messageType = 'success';
                    } else {
                    }
                } else {
                    $message = "No file uploaded or there was an upload error.";
                    $messageType = 'error';
                }


                //file path end here
                // Insert document

            } catch (PDOException $e) {
                $message = "Database error: " . $e->getMessage();
                $messageType = 'error';
            }
        }
    }
}

// Get levels for dropdown
$query = "SELECT * FROM level ORDER BY level_id";
$stmt = $db->prepare($query);
$stmt->execute();
$levels = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudySwap - Upload Course</title>
    <link rel="stylesheet" href="CSS/upload.css">
    <link rel="stylesheet" href="CSS/style.css">
</head>

<body>
    <?php include "header.php"; ?>

    <main class="main">
        <div class="upload-container">
            <h1 class="title">Upload Your Course</h1>

            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="form" id="uploadForm">
                <!-- First Row -->
                <div class="form-row">
                    <input type="text" name="title" class="form-input" placeholder="Course Title"
                        value="<?php echo htmlspecialchars($_POST['tittle'] ?? ''); ?>" required>

                    <select name="type" class="form-select" required>
                        <option value="">Select Type</option>
                        <option value="course" <?php echo (($_POST['type'] ?? '') === 'course') ? 'selected' : ''; ?>>Course</option>
                        <option value="exercise" <?php echo (($_POST['type'] ?? '') === 'exercise') ? 'selected' : ''; ?>>Exercise</option>
                        <option value="exam" <?php echo (($_POST['type'] ?? '') === 'exam') ? 'selected' : ''; ?>>Exam</option>
                        <option value="notes" <?php echo (($_POST['type'] ?? '') === 'notes') ? 'selected' : ''; ?>>Notes</option>
                        <option value="summary" <?php echo (($_POST['type'] ?? '') === 'summary') ? 'selected' : ''; ?>>Summary</option>
                    </select>
                </div>

                <!-- Second Row -->
                <div class="form-row">
                    <select name="level_id" id="levelSelect" class="form-select" required>
                        <option value="">Select Level</option>
                        <?php foreach ($levels as $level): ?>
                            <option value="<?php echo $level['level_id']; ?>"
                                <?php echo (($_POST['level_id'] ?? '') == $level['level_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($level['level_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="sector_id" id="sectorSelect" class="form-select" required disabled>
                        <option value="">Select Sector</option>
                    </select>
                </div>

                <!-- Third Row -->
                <select name="subject_id" id="subjectSelect" class="form-select" required disabled>
                    <option value="">Select Subject</option>
                </select>


                <!-- Upload Area -->
                <div class="upload-area" id="uploadArea">
                    <input type="file" name="document" id="fileInput" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt" required hidden>
                    <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17,8 12,3 7,8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <p class="upload-text">Click to upload or drag and drop</p>
                    <p class="upload-subtext">PDF, DOC, DOCX, PPT, PPTX, TXT (Max 10MB)</p>
                </div>

                <div id="filePreview" class="file-preview" style="display: none;">
                    <div class="file-info">
                        <span class="file-name"></span>
                        <span class="file-size"></span>
                        <button type="button" class="remove-file" onclick="removeFile()">×</button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="publish-btn" id="submitBtn">
                    <span class="btn-text">Publish Document</span>
                    <span class="btn-loading" style="display: none;">
                        <svg class="spinner" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2" stroke-dasharray="31.416" stroke-dashoffset="31.416">
                                <animate attributeName="stroke-dasharray" dur="2s" values="0 31.416;15.708 15.708;0 31.416" repeatCount="indefinite" />
                                <animate attributeName="stroke-dashoffset" dur="2s" values="0;-15.708;-31.416" repeatCount="indefinite" />
                            </circle>
                        </svg>
                        Uploading...
                    </span>
                </button>
            </form>
        </div>
    </main>

    <?php include "footer.php"; ?>

    <script>
        // File upload handling
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const filePreview = document.getElementById('filePreview');
        const form = document.getElementById('uploadForm');
        const submitBtn = document.getElementById('submitBtn');

        uploadArea.addEventListener('click', () => fileInput.click());
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('drag-over');
        });
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('drag-over');
        });
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect();
            }
        });

        fileInput.addEventListener('change', handleFileSelect);

        function handleFileSelect() {
            const file = fileInput.files[0];
            if (file) {
                const maxSize = 10 * 1024 * 1024; // 10MB
                if (file.size > maxSize) {
                    alert('File size must be less than 10MB');
                    fileInput.value = '';
                    return;
                }

                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'text/plain'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Please upload PDF, DOC, DOCX, PPT, PPTX, or TXT files.');
                    fileInput.value = '';
                    return;
                }

                uploadArea.style.display = 'none';
                filePreview.style.display = 'block';
                filePreview.querySelector('.file-name').textContent = file.name;
                filePreview.querySelector('.file-size').textContent = formatFileSize(file.size);
            }
        }

        function removeFile() {
            fileInput.value = '';
            uploadArea.style.display = 'block';
            filePreview.style.display = 'none';
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Dynamic dropdowns
        const levelSelect = document.getElementById('levelSelect');
        const sectorSelect = document.getElementById('sectorSelect');
        const subjectSelect = document.getElementById('subjectSelect');

        levelSelect.addEventListener('change', function() {
            const levelId = this.value;
            sectorSelect.innerHTML = '<option value="">Select Sector</option>';
            subjectSelect.innerHTML = '<option value="">Select Subject</option>';
            sectorSelect.disabled = true;
            subjectSelect.disabled = true;

            if (levelId) {
                fetch(`upload.php?ajax=sectors&level_id=${levelId}`)
                    .then(response => response.json())
                    .then(sectors => {
                        sectors.forEach(sector => {
                            const option = document.createElement('option');
                            option.value = sector.Sector_id;
                            option.textContent = sector.sector_name;
                            sectorSelect.appendChild(option);
                        });
                        sectorSelect.disabled = false;
                    })
                    .catch(error => console.error('Error loading sectors:', error));
            }
        });

        sectorSelect.addEventListener('change', function() {
            const levelId = levelSelect.value;
            const sectorId = this.value;
            subjectSelect.innerHTML = '<option value="">Select Subject</option>';
            subjectSelect.disabled = true;

            if (levelId && sectorId) {
                fetch(`upload.php?ajax=subjects&level_id=${levelId}&sector_id=${sectorId}`)
                    .then(response => response.json())
                    .then(subjects => {
                        subjects.forEach(subject => {
                            const option = document.createElement('option');
                            option.value = subject.subject_id;
                            option.textContent = subject.subject_name;
                            subjectSelect.appendChild(option);
                        });
                        subjectSelect.disabled = false;
                    })
                    .catch(error => console.error('Error loading subjects:', error));
            }
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');

            btnText.style.display = 'none';
            btnLoading.style.display = 'inline-flex';
            submitBtn.disabled = true;
        });

        // Auto-hide success messages
        const message = document.querySelector('.message.success');
        if (message) {
            setTimeout(() => {
                message.style.opacity = '0';
                setTimeout(() => message.remove(), 300);
            }, 5000);
        }
    </script>
</body>

</html>