<?php
require '../config/database.php';
session_start();

// Redirection si déjà connecté
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$errors = [];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation champs
    if (empty($email)) {
        $errors['email'] = "Le champ 'Email' est requis.";
    }
    if (empty($password)) {
        $errors['password'] = "Le champ 'Mot de passe' est requis.";
    }

    if (empty($errors)) {
        // Vérifier utilisateur en base
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
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>StudySwap - Login</title>
    <link rel="stylesheet" href="../CSS/auth.css" />
</head>

<body>
    <div>
        <div class="baground">
            <img class="img" src="../IMG/baground.jpeg" alt="Background Image" />
            <div class="logo">
                <img src="../IMG/logo.png" alt="Logo" />
            </div>

            <div class="login-card">
                <h2 class="login-welcome-title">Welcome!</h2>

                <!-- Erreur générale -->
                <?php if ($error): ?>
                    <div class="error-message" style="color: red; margin-bottom: 1rem;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <div class="login-form-group">
                        <input
                            type="email"
                            name="email"
                            class="login-form-input"
                            placeholder="Email"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
                        <?php if (!empty($errors['email'])): ?>
                            <div class="error-message" style="color: red; font-size: 0.9rem; margin-top: 4px;">
                                <?= htmlspecialchars($errors['email']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="login-form-group">
                        <input
                            type="password"
                            name="password"
                            class="login-form-input"
                            placeholder="Password" />
                        <?php if (!empty($errors['password'])): ?>
                            <div class="error-message" style="color: red; font-size: 0.9rem; margin-top: 4px;">
                                <?= htmlspecialchars($errors['password']) ?>
                            </div>
                        <?php endif; ?>
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
    </div>
</body>

</html>