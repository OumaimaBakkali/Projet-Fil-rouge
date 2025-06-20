<?php
require '../config/database.php';
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $query = $db->prepare("SELECT * FROM users WHERE email = :email");
        $query->execute(['email' => $email]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['nom'] = $user['first_name'];
            header('Location: ../index.php');
            exit;
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudySwap - Login</title>
   <link rel="stylesheet" href="../CSS/auth.css">
</head>
<body>
    <div>
          <div class="baground">
            <img class="img" src="../IMG/baground.jpeg">
            <div class="logo">                
                <img src="../IMG/logo.png" >
            </div>
        <div class="login-card">
            <h2 class="login-welcome-title">Welcome!</h2>
            <form method="post">
                <div class="login-form-group">
                    <input type="email" name="email" class="login-form-input" placeholder="Email" required>
                </div>
                <div class="login-form-group">
                    <input type="password" name="password" class="login-form-input" placeholder="Password" required>
                </div>
                <button type="submit" class="login-button">LOGIN</button>
                <div class="form-links">
                    <a href="../auth/register.php">Create account</a>
                    <span class="separator">|</span>
                    <a href="../auth/forgot_password.php">Forgot password</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>