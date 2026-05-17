<?php
session_start();
include("purplestringwebsite/backend/connection.php");
include("purplestringwebsite/backend/mailer.php"); 

$message = "";
$message_type = ""; // 'error' or 'success'

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = mysqli_real_escape_string($con, $_POST['email']);

    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // CHANGED: 'user_email' changed to 'email' to match standard DB columns
        $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            $user_name = $user_data['user_name'];

            // Generate secure unique random token and 30-minute expiration timestamp
            $token = bin2hex(random_bytes(32));
            $expiry = date("Y-m-d H:i:s", strtotime("+30 minutes"));

            // CHANGED: 'user_email' changed to 'email' here as well
            $update_query = "UPDATE users SET reset_token = '$token', reset_expiry = '$expiry' WHERE email = '$email'";
            mysqli_query($con, $update_query);

            // Send password recovery link
            if (sendPasswordResetEmail($email, $user_name, $token)) {
                $message = "A reset link has been dispatched to your email address!";
                $message_type = "success";
            } else {
                $message = "Failed sending email. Please check server mail configurations.";
                $message_type = "error";
            }
        } else {
            // Secure design pattern: don't reveal explicitly if email doesn't exist
            $message = "If that email matches an account, a reset link has been dispatched.";
            $message_type = "success";
        }
    } else {
        $message = "Please enter a valid email address.";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purple String - Forgot Password</title>
    <link rel="stylesheet" href="purplestringwebsite/frontend/css/homepage.css" />
    <link rel="stylesheet" href="purplestringwebsite/frontend/css/login.css" />
</head>
<body>
    <div id="page-container">
      <div id="login-container">
        <div id="login-content">
          <div id="login-card">
            <div id="login-header">
              <h1>Reset Password</h1>
              <p>Enter your email to obtain a recovery link</p>
            </div>

            <?php if($message): ?>
              <div style="background-color: <?php echo $message_type == 'success' ? '#d4edda' : '#f8d7da'; ?>; 
                          color: <?php echo $message_type == 'success' ? '#155724' : '#721c24'; ?>; 
                          padding: 12px; border-radius: 4px; margin-bottom: 15px; 
                          border: 1px solid <?php echo $message_type == 'success' ? '#c3e6cb' : '#f5c6cb'; ?>;">
                <?php echo htmlspecialchars($message); ?>
              </div>
            <?php endif; ?>
            
            <form method="POST" action="">
              <div class="form-group">
                <label for="email">Account Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your registered email" required />
              </div>
              <button type="submit" class="login-btn">Send Recovery Link</button>
            </form>

            <div id="signup-link" style="margin-top: 25px;">
              Remembered your credentials? <a href="login.php">Back to Login</a>
            </div>
          </div>
        </div>
      </div>
    </div>
</body>
</html>