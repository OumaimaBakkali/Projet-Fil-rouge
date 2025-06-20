<?php
require '../config/database.php';
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];


    if (empty($nom)) {
        $errors['nom'] = "Le champ 'Nom' est requis.";
    } elseif (!preg_match("/^[a-zA-ZÀ-ÿ' -]{2,30}$/", $nom)) {
        $errors['nom'] = "Le nom doit contenir uniquement des lettres (2 à 30 caractères).";
    }


    if (empty($prenom)) {
        $errors['prenom'] = "Le champ 'Prénom' est requis.";
    } elseif (!preg_match("/^[a-zA-ZÀ-ÿ' -]{2,30}$/", $prenom)) {
        $errors['prenom'] = "Le prénom doit contenir uniquement des lettres (2 à 30 caractères).";
    }

    if (empty($email)) {
        $errors['email'] = "Le champ 'Email' est requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "L'email n'est pas valide.";
    } else {
        $query = $db->prepare("SELECT * FROM users WHERE email = :email");
        $query->execute(['email' => $email]);
        if ($query->rowCount() > 0) {
            $errors['email'] = "Un compte avec cet email existe déjà.";
        }
    }

    if (empty($telephone)) {
        $errors['telephone'] = "Le champ 'Téléphone' est requis.";
    } elseif (!preg_match("/^\+?[0-9]{9,15}$/", $telephone)) {
        $errors['telephone'] = "Le numéro de téléphone n'est pas valide.";
    }

    if (empty($password)) {
        $errors['password'] = "Le champ 'Mot de passe' est requis.";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors['password'] = "Le mot de passe doit contenir au moins une majuscule.";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors['password'] = "Le mot de passe doit contenir au moins un chiffre.";
    }


    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Les mots de passe ne correspondent pas.";
    }


    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = $db->prepare("INSERT INTO users (first_name, last_name, email, phone, password) VALUES (:prenom, :nom, :email, :telephone, :password)");
        $query->execute([
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'telephone' => $telephone,
            'password' => $hashed_password,
        ]);

        header('Location: ../index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudySwap - Create Account</title>
    <link rel="stylesheet" href="../CSS/auth.css">
</head>

<body>

  
        <div class="baground">
            <img class="img" src="../IMG/baground.jpeg">

            <div class="logo">
                <img src="../IMG/logo.png">
            </div>

            <div class="register-form-container">
                <h2 class="register-form-title">Welcome!</h2>

                <form class="registration-form" method="POST" action="">
                    <!-- First Name and Last Name Row -->
                    <div class="register-form-row">
                        <div class="register-form-group">
                            <input type="text" name="prenom" placeholder="First Name" class="register-form-input" value="<?= htmlspecialchars($prenom ?? '') ?>">
                            <?php if (isset($errors['prenom'])): ?>
                                <small class="error"><?= $errors['prenom'] ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="register-form-group">
                            <input type="text" name="nom" placeholder="Last Name" class="register-form-input" value="<?= htmlspecialchars($nom ?? '') ?>">
                            <?php if (isset($errors['nom'])): ?>
                                <small class="error"><?= $errors['nom'] ?></small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div class="register-form-group full-width">
                        <input type="email" name="email" placeholder="Email Address" class="register-form-input" value="<?= htmlspecialchars($email ?? '') ?>">
                        <?php if (isset($errors['email'])): ?>
                            <small class="error"><?= $errors['email'] ?></small>
                        <?php endif; ?>
                    </div>

                    <!-- Phone Number -->
                    <div class="register-form-group full-width">
                        <input type="tel" name="telephone" placeholder="Phone Number" class="register-form-input" value="<?= htmlspecialchars($telephone ?? '') ?>">
                        <?php if (isset($errors['telephone'])): ?>
                            <small class="error"><?= $errors['telephone'] ?></small>
                        <?php endif; ?>
                    </div>

                    <!-- Password and Confirm Password Row -->
                    <div class="register-form-row">
                        <div class="register-form-group">
                            <input type="password" name="password" placeholder="Password" class="register-form-input">
                            <?php if (isset($errors['password'])): ?>
                                <small class="error"><?= $errors['password'] ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="register-form-group">
                            <input type="password" name="confirm_password" placeholder="Confirm Password" class="register-form-input">
                            <?php if (isset($errors['confirm_password'])): ?>
                                <small class="error"><?= $errors['confirm_password'] ?></small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Create Account Button -->
                    <div class="button-container">
                        <button type="submit" class="create-account-btn">Create Account</button>
                    </div>

                    <p>Do you have an account? <a href="../auth/login.php">Login</a></p>
                </form>
            </div>
        </div>

   



</body>

</html>