<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudySwap - Create Account</title>
    <link rel="stylesheet" href="../CSS/auth.css">
</head>
<body>

    <div>
        <div class="baground">
            <img class="img" src="../IMG/baground.jpeg">
            
            <div class="logo">
                <img src="../IMG/logo.png" >
            </div>
            
               <div class="register-form-container">
                <h2 class="register-form-title">Welcome!</h2>
                
                <form class="registration-form">
                    <!-- First Name and Last Name Row -->
                    <div class="register-form-row">
                        <div class="register-form-group">
                            <input type="text" placeholder="First Name" class="register-form-input">
                        </div>
                        <div class="register-form-group">
                            <input type="text" placeholder="Last Name" class="register-form-input">
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div class="register-form-group full-width">
                        <input type="email" placeholder="Email Address" class="register-form-input">
                    </div>

                    <!-- Phone Number -->
                    <div class="register-form-group full-width">
                        <input type="tel" placeholder="Phone Number" class="register-form-input">
                    </div>

                    <!-- Password and Confirm Password Row -->
                    <div class="register-form-row">
                        <div class="register-form-group">
                            <input type="password" placeholder="Password" class="register-form-input">
                        </div>
                        <div class="register-form-group">
                            <input type="password" placeholder="Confirm Password" class="register-form-input">
                        </div>
                          
                    </div>

                    <!-- Create Account Button -->
                    <div class="button-container">
                        <button type="submit" class="create-account-btn">Create Account</button>
                    </div>
                         <p>Do you have an account?<a href="../auth/login.php">login</a></p> 
                </form>
            </div>
          
        </div>
    </div>
   
</body>
</html>