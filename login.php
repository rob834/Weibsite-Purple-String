<?php 
// 1. Configure secure session cookie settings BEFORE starting the session
session_set_cookie_params([
    'lifetime' => 0,                      // Expires when browser closes
    'path' => '/',
    'secure' => true,                     // Set to true if using HTTPS
    'httponly' => true,                   // Protects session cookie from XSS access
    'samesite' => 'Strict'                // Protects against CSRF attacks
]);

session_start();

include("purplestringwebsite/backend/connection.php");
include("purplestringwebsite/backend/functions.php");

$error_message = "";

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST") {
    $user_name = trim($_POST['user_name']);
    $password = $_POST['password'];

    if (!empty($user_name) && !empty($password) && !is_numeric($user_name)) {
        // reCAPTCHA Server-side Validation
        $recaptcha_secret = "6Le-du4sAAAAAPVHLA-9sjgolije8e9jnSqQQdz_";
        $recaptcha_response = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

        // Verify the response with Google APIs
        $verify_url = "https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret . "&response=" . $recaptcha_response;
        $response_call = file_get_contents($verify_url);
        $response_data = json_decode($response_call, true);

        if (!$response_data["success"]) {
            $error_message = "Please complete the reCAPTCHA verification checkpoint.";
        } else {
            // FIXED: Using Prepared Statements to prevent SQL Injection
            $query = "SELECT * FROM users WHERE user_name = ? LIMIT 1";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "s", $user_name);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                $user_data = mysqli_fetch_assoc($result);
                
                // FIXED: Verifying the securely hashed password
                if (password_verify($password, $user_data['password'])) {
                    
                    // Check if email is verified
                    if (isset($user_data['email_verified']) && $user_data['email_verified'] == 1) {
                        
                        // FIXED: Secure Session Regeneration on login to prevent Session Fixation
                        session_regenerate_id(true);
                        
                        $_SESSION['user_id'] = $user_data['user_id'];
                        $_SESSION['role'] = $user_data['role']; 

                        if ($user_data['role'] === 'admin') {
                            header("Location: purplestringwebsite/frontend/pages/admin-homepage.php");
                            exit;
                        } else {
                            header("Location: index.php");
                            exit;
                        }
                    } else {
                        $error_message = "Please verify your email before logging in.";
                    }
                } else {
                    $error_message = "Wrong username or password!";
                }
            } else {
                $error_message = "Wrong username or password!";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $error_message = "Please enter valid information.";
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
    <title>Purple String - Login</title>
    <link
      rel="stylesheet"
      href="purplestringwebsite/frontend/css/homepage.css" />
    <link
      rel="stylesheet"
      href="purplestringwebsite/frontend/css/login.css" />
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
              <h1>Welcome Back</h1>
              <p>Sign in to your Purple String account</p>
            </div>

            <?php if($error_message): ?>
              <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
                <?php echo htmlspecialchars($error_message); ?>
              </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="login-form">
              <div class="form-group">
                <label for="user_name">Username</label>
                <input 
                  type="text" 
                  id="user_name" 
                  name="user_name" 
                  placeholder="Enter your username"
                  required />
              </div>

              <div class="form-group">
                <label for="password">Password</label>
                <input 
                  type="password" 
                  id="password" 
                  name="password" 
                  placeholder="Enter your password"
                  required />
              </div>

              <div class="remember-forgot">
                <label class="remember-me">
                  <input type="checkbox" name="remember" />
                  <span>Remember me</span>
                </label>
                <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
              </div>

              <div class="form-group" style="margin-bottom: 20px; display: flex; justify-content: center;">
                <div class="g-recaptcha" data-sitekey="6Le-du4sAAAAAPe_QFxx8bvQcWm8xnFXLW_UGMfD"></div>
              </div>

              <button type="submit" value="Login" class="login-btn">Log In</button>
            </form>

            <div id="signup-link" style="margin-top: 25px;">
              Don't have an account? <a href="/Weibsite-Purple-String/signup.php">Create one here</a>
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

    -->
  </body>
</html>