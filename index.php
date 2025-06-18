<?php
require_once 'config/database.php';



try {
    $query = "SELECT COUNT(*) as total FROM level";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $levels_count = $stmt->fetch()['total'];

    $query = "SELECT COUNT(*) as total FROM sector";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $sectors_count = $stmt->fetch()['total'];

    $query = "SELECT COUNT(*) as total FROM course";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $courses_count = $stmt->fetch()['total'];

    $query = "SELECT COUNT(*) as total FROM document";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $documents_count = $stmt->fetch()['total'];

    $query = "SELECT * FROM level ORDER BY level_id";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $levels = $stmt->fetchAll();

} catch (PDOException $e) {
    $levels_count = 0;
    $sectors_count = 0;
    $courses_count = 0;
    $documents_count = 0;
    $levels = [];
}
?>
<!DOCTYPE html>
 <html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudySwap - Study smarter & Succeed together</title>
    <link rel="stylesheet" href="CSS/index.css">
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
    
    <?php include "header.php"?>

    <main class="hero" id="home">
        <div class="hero-content">
            <h1 class="hero-title">
                Study Smarter.<br>
                & Succeed Together.
            </h1>
            <p class="hero-subtitle">
                StudySwap is here for you, providing a platform to share courses, exercises, and revision notes among students.
            </p>
            <button class="learn-more-btn" >Learn More</button>
        </div>
        <div class="hero-illustration">
            <img src="IMG/hero-section.png" alt="Hero illustration">
        </div>
    </main> 
    <section>
        <div class="content" id="level">
            <h2 class="levels-main-heading">
                Choose Your Level &<br>
                Get Started
            </h2>
            <div class="blue-container">
                    <div class="cards-grid">
                         <div class="level-card">
                            <div class="card-content">
                                <h3 class="card-title">Primary</h3>
                            </div>
                            <div class="card-footer">
                                <button class="arrow-button">
                                    <svg class="arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="level-card">
                            <div class="card-content">
                                <h3 class="card-title">Middle School</h3>
                            </div>
                            <div class="card-footer">
                                <button class="arrow-button">
                                    <svg class="arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                         <div class="level-card"onclick="goToLevel(3)">
                            <div class="card-content">
                                <h3 class="card-title">High school</h3>
                            </div>
                            <div class="card-footer">
                                <button class="arrow-button">
                                    <svg class="arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
            </div>
        </div>

    </section>
    <div class="about-container" >
    <div class="about-header">
        <div class="about-header-wrapper">
            <div class="about-header-line"></div>
            <h1 class="about-main-title">About Us</h1>
            <div class="about-header-line"></div>
        </div>
    </div>
    <div class="about-content-section"id="about">
        <div class="about-text-content">
            <p>
                At StudySwap, we support students from elementary to high school by connecting them 
                to share resources, exchange courses, and collaborate on revision materials.
            </p>
        </div>
        <div class="about-image-container">
            <img
                src="IMG/about-left.png"
                alt="Students collaborating and sharing resources"
                class="about-illustration" />
        </div>
    </div>
    <div class="about-content-section reverse">
        <div class="image-container">
            <img
                src="IMG/about-right.png"
                alt="Student working on a laptop with peer collaboration"
                class="about-illustration" />
        </div>
        <div class="about-text-content">
            <p>
                By publishing their courses, students receive help and feedback from their peers. 
                We're building a community where learning is shared and success is a team effort.
            </p>
        </div>
    </div>
</div>

    <section class="body">
        <div class="ready-container">
            <div class="content">
                <div class="text-section">
                    <h1 class="main-heading">
                        Ready to Share<br>
                        Your Knowledge?
                    </h1>
                    <p class="description">
                        Click the button to go to the upload page where you can submit your course materials.
                    </p>
                </div>
                <button class="upload-button" onclick="window.location.href='upload.php'">
                    Upload Here
                </button>
            </div>
            <div class="illustration-container">
                <img src="IMG/girl study.png" alt="Students sharing knowledge" class="illustration">
            </div>
        </div>
    </section>
    <section class="social-section" id="contact">
    <div class="cont">
        <div class="social-grid">
            <div class="image-container">
                <img
                    src="IMG/social-media.png"
                    alt="StudySwap learning platform"
                    class="social-image" />
            </div>
            <div class="social-content">
                <div class="social-content-text">
                    <h1 class="social-heading">Join Our Online Learning Community</h1>
                    <p class="sub-heading">Share notes. Grow together.</p>
                </div>
                <div class="social-icons">
                    <a href="#" class="social-icon" aria-label="Facebook">
                        <svg viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                    <a href="#" class="social-icon" aria-label="Instagram">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                        </svg>
                    </a>
                    <a href="#" class="social-icon" aria-label="Twitter">
                        <svg viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                        </svg>
                    </a>
                    <a href="#" class="social-icon" aria-label="YouTube">
                        <svg viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

      <?php include "footer.php"?>

    <script>
        function goToLevel(levelId) {
            window.location.href = 'levels.php?level_id=' + levelId;
        }

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>
