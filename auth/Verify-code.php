<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudySwap - forget-password</title>
   <link rel="stylesheet" href="../CSS/auth.css">
</head>
<body>
    <div>
          <div class="baground">
            <img class="img" src="../IMG/baground.jpeg">
            <div class="logo">      
                <img src="../IMG/logo.png" >
            </div>
        <div class="forget-password-card">
            <h2 class="forget-welcome-title">Verify code</h2>
            <p class="forget-title">An authentication code has been sent to your email.</p>
            <form>
                <div class="forget-form-group">
                    <input type="email" class="forget-form-input" placeholder="Enter code" required>
                     <p>Do you have an account?<a href="../auth/login.php">Resend</a></p> 
                </div>
                <button type="submit" class="forget-button">VERIFY</button>
                <div class="form-links">
                    <a href="../auth/login.php">Back to login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>