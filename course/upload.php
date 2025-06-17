<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/upload.css">
    <title>StudySwap - Upload a file</title>
</head>
<body>
    <!-- Header -->
   <?php include '../includes/header.php'; ?>

    <!-- Main Content -->
    <main class="main">
        <div class="upload-container">
            <h1 class="title">Upload a file</h1>

            <form class="form">
                <!-- First Row -->
                <div class="form-row">
                    <input type="text" class="form-input" placeholder="Title">
                    <select class="form-select">
                        <option value="">Type</option>
                        <option value="notes">Notes</option>
                        <option value="assignment">Assignment</option>
                        <option value="exam">Exam</option>
                    </select>
                </div>

                <!-- Second Row -->
                <div class="form-row">
                    <input type="text" class="form-input" placeholder="School level">
                    <input type="text" class="form-input" placeholder="Subject">
                </div>

                <!-- Third Row -->
                <select class="form-select">
                    <option value="">Sector</option>
                    <option value="science">Science</option>
                    <option value="arts">Arts</option>
                    <option value="commerce">Commerce</option>
                </select>

                <!-- Upload Area -->
                <div class="upload-area">
                    <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7,10 12,15 17,10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <p class="upload-text">Upload from here</p>
                </div>

                <!-- Publish Button -->
                <button type="submit" class="publish-btn">Publish</button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
</body>
</html>