<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>StudySwap - Courses</title>
  <link rel="stylesheet" href="../CSS/levels.css">
</head>
<body>
  <!-- Header -->
  <?php include '../includes/header.php'; ?>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <h1>COURSES</h1>
    </div>
  </section>

  <!-- Course Categories -->
  <section class="categories">
    <div class="container">
      <div class="category-tabs">
        <div class="category-tab inactive">Primary</div>
        <div class="category-tab inactive">Middle School</div>
        <div class="category-tab active">High School</div>
      </div>
    </div>
  </section>

  <!-- Course Cards -->
  <section class="courses">
    <div class="container">
      <div class="course-grid">
        <!-- Common Core Card -->
        <div class="course-card">
          <div class="icon-container">
           <img src="../IMG/computer.png">
          </div>
          <h3 class="course-title">Common core</h3>
          <button class="arrow-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="white">
              <path d="M5 12h14M12 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>

        <!-- 1st Degree Card -->
        <div class="course-card card-unique">
          <div class="icon-container">
               <img src="../IMG/school.png">
          </div>
          <h3 class="course-title">1st Degree</h3>
          <button class="arrow-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="white">
              <path d="M5 12h14M12 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>

        <!-- 2nd Degree Card -->
        <div class="course-card">
          <div class="icon-container">
               <img src="../IMG/community.png">
          </div>
          <h3 class="course-title">2nd Degree</h3>
          <button class="arrow-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="white">
              <path d="M5 12h14M12 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
 <?php include '../includes/footer.php'; ?>
</body>
</html>