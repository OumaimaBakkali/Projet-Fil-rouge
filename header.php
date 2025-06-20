
<!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="./CSS/style.css">
     <title>Document</title>
 </head>

 <body>
     <header>
         <div class="header-container">
             <div class="header-logo">
                 <img src="IMG/logo.png">
             </div>
             <nav class="nav-menu">
                 <a href="index.php" class="nav-link">HOME</a>
                 <a href="#about" class="nav-link">ABOUT</a>
                 <a href="#level" class="nav-link">COURSES</a>
                 <a href="#contact" class="nav-link">CONTACT</a>
                 
             </nav>
          <div class="buttons">
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="auth/logout.php">
                LogOut</a>
        <?php else: ?>
            <button><a href="auth/login.php">Login</a></button>
            <button><a href="auth/register.php">Sign Up</a></button>
            <a href="./my_space.php">
                <i class="fa-solid fa-user"></i>
            </a>
        <?php endif; ?>
    </div>
         </div>
     </header>

 </body>

 </html>