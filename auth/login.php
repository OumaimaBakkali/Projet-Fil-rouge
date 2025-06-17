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
            <form>
                <div class="login-form-group">
                    <input type="email" class="login-form-input" placeholder="Email" required>
                </div>
                <div class="login-form-group">
                    <input type="password" class="login-form-input" placeholder="Password" required>
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