<?php
session_start();

include("purplestringwebsite/backend/connection.php");
include("purplestringwebsite/backend/functions.php");
include("purplestringwebsite/backend/mailer.php"); // ← add this

$error_message = "";
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if all required fields are present
    if (isset($_POST['user_name']) && isset($_POST['email']) && isset($_POST['password'])) {
        
        $user_name = $_POST['user_name'];
        $email     = $_POST['email'];
        $password  = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password!

        // 1. Generate a unique token
        $token = bin2hex(random_bytes(32));

        // 2. Use prepared statement to prevent SQL injection and properly insert the token
        $query = "INSERT INTO users (user_name, email, password, email_verified, verification_token)
                  VALUES (?, ?, ?, 0, ?)";
        
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ssss", $user_name, $email, $password, $token);
        
        if (mysqli_stmt_execute($stmt)) {

            // 3. Send the verification email
            $sent = sendVerificationEmail($email, $user_name, $token);

            if ($sent) {
                $success_message = "Account created successfully! Please check your email to verify your account.";
            } else {
                $success_message = "Account created! However, verification email could not be sent. Please contact support.";
            }
        } else {
            $error_message = "Error creating account. Please try again. " . mysqli_error($con);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Please enter all required information!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0" />
    <title>Purple String - Sign Up</title>
    <link
      rel="stylesheet"
      href="purplestringwebsite/frontend/css/homepage.css" />
    <link
      rel="stylesheet"
      href="purplestringwebsite/frontend/css/login.css" />
  </head>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');
  </style>
  <body>
    <div id="page-container">
      <div id="login-container">
        <div id="login-content">
          <div id="login-card">
            <div id="login-header">
              <h1>Create Account</h1>
              <p>Sign up for Purple String</p>
            </div>

            <?php if($error_message): ?>
              <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
                <?php echo htmlspecialchars($error_message); ?>
              </div>
            <?php endif; ?>

            <?php if($success_message): ?>
              <div style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #c3e6cb;">
                <?php echo htmlspecialchars($success_message); ?>
              </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="login-form">
              <div class="form-group">
                <label for="user_name">Username</label>
                <input 
                  type="text" 
                  id="user_name" 
                  name="user_name" 
                  placeholder="Choose a username"
                  required />
              </div>

              <div class="form-group">
                <label for="email">Email</label>
                <input 
                  type="email" 
                  id="email" 
                  name="email" 
                  placeholder="Enter your email address"
                  required />
              </div>

              <div class="form-group">
                <label for="password">Password</label>
                <input 
                  type="password" 
                  id="password" 
                  name="password" 
                  placeholder="Create a password"
                  required />
              </div>

              <button type="submit" value="Sign Up" class="login-btn">Sign Up</button>
            </form>

            <div class="divider">OR</div>

            <button class="social-btn google-btn">
              <span>🔍</span> Continue with Google
            </button>
            <button class="social-btn facebook-btn">
              <span>f</span> Continue with Facebook
            </button>

            <div id="signup-link">
              Already have an account? <a href="/Weibsite-Purple-String/login.php">Sign in here</a>
            </div>
          </div>

          <div id="login-decoration">
            <div class="decoration-circle circle-1"></div>
            <div class="decoration-circle circle-2"></div>
            <div class="decoration-thread"></div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>