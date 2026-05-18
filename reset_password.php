<?php
session_start();
include("purplestringwebsite/backend/connection.php");

$message = "";
$message_type = "";
$valid_token = false;
$token = "";

// 1. Check token validity via incoming GET parameter
if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($con, $_GET['token']);
    $current_time = date("Y-m-d H:i:s");

    $query = "SELECT * FROM users WHERE reset_token = '$token' AND reset_expiry > '$current_time' LIMIT 1";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $valid_token = true;
        $user_data = mysqli_fetch_assoc($result);
    } else {
        $message = "This password reset link is invalid or has expired.";
        $message_type = "error";
    }
} else {
    $message = "No valid security token was detected.";
    $message_type = "error";
}

// 2. Handle Password update submission via POST execution
if ($_SERVER['REQUEST_METHOD'] == "POST" && $valid_token) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($new_password) && !empty($confirm_password)) {
        if ($new_password === $confirm_password) {
            $user_id = $user_data['user_id'];

            // FIXED: Hash the password before saving — plain text would break password_verify() in login.php
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $escaped_hashed = mysqli_real_escape_string($con, $hashed_password);

            // Update user password and wipe out token parameters so it can't be reused
            $update_query = "UPDATE users SET password = '$escaped_hashed', reset_token = NULL, reset_expiry = NULL WHERE user_id = '$user_id'";

            if (mysqli_query($con, $update_query)) {
                $message = "Password successfully updated! You can now log in.";
                $message_type = "success";
                $valid_token = false; // Collapse form view on success
            } else {
                $message = "Database execution error occurred. Try again later.";
                $message_type = "error";
            }
        } else {
            $message = "Passwords do not match. Please try again.";
            $message_type = "error";
        }
    } else {
        $message = "Please fill in all required fields.";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purple String - Choose New Password</title>
    <link rel="stylesheet" href="purplestringwebsite/frontend/css/homepage.css" />
    <link rel="stylesheet" href="purplestringwebsite/frontend/css/login.css" />
</head>
<body>
    <div id="page-container">
      <div id="login-container">
        <div id="login-content">
          <div id="login-card">
            <div id="login-header">
              <h1>Change Password</h1>
              <p>Type and confirm your new password</p>
            </div>

            <?php if($message): ?>
              <div style="background-color: <?php echo $message_type == 'success' ? '#d4edda' : '#f8d7da'; ?>; 
                          color: <?php echo $message_type == 'success' ? '#155724' : '#721c24'; ?>; 
                          padding: 12px; border-radius: 4px; margin-bottom: 15px; 
                          border: 1px solid <?php echo $message_type == 'success' ? '#c3e6cb' : '#f5c6cb'; ?>;">
                <?php echo htmlspecialchars($message); ?>
              </div>
            <?php endif; ?>
            
            <?php if($valid_token): ?>
            <form method="POST" action="">
              <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required />
              </div>

              <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Type password again" required />
              </div>

              <button type="submit" class="login-btn">Update Password</button>
            </form>
            <?php endif; ?>

            <div id="signup-link" style="margin-top: 25px;">
              <a href="login.php">Return to Sign In Page</a>
            </div>
          </div>
        </div>
      </div>
    </div>
</body>
</html>