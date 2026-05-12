<?php
session_start();

include("purplestringwebsite/backend/connection.php");
include("purplestringwebsite/backend/functions.php");

$message = "";
$message_type = ""; // "success" or "error"

if(isset($_GET['token']) && !empty($_GET['token']))
{
	$token = $_GET['token'];

	// Find user with this verification token
	$query = "select * from users where verification_token = '$token' limit 1";
	$result = mysqli_query($con, $query);

	if($result && mysqli_num_rows($result) > 0)
	{
		$user_data = mysqli_fetch_assoc($result);

		// Update user to mark email as verified
		$update_query = "update users set email_verified = 1, verification_token = NULL where user_id = '{$user_data['user_id']}'";

		if(mysqli_query($con, $update_query))
		{
			$message = "Email verified successfully! You can now log in to your account.";
			$message_type = "success";
		}
		else
		{
			$message = "An error occurred while verifying your email. Please try again later.";
			$message_type = "error";
		}
	}
	else
	{
		$message = "Invalid or expired verification link.";
		$message_type = "error";
	}
}
else
{
	$message = "No verification token provided.";
	$message_type = "error";
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0" />
    <title>Email Verification - Purple String</title>
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
              <h1>Email Verification</h1>
              <p>Purple String</p>
            </div>

            <?php if($message_type === "success"): ?>
              <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #c3e6cb;">
                <strong>Success!</strong> <?php echo htmlspecialchars($message); ?>
              </div>
              <a href="/Weibsite-Purple-String/login.php" class="login-btn" style="display: inline-block; text-align: center; text-decoration: none;">Go to Login</a>
            <?php else: ?>
              <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
                <strong>Error!</strong> <?php echo htmlspecialchars($message); ?>
              </div>
              <p style="text-align: center; margin-top: 20px;">
                <a href="/Weibsite-Purple-String/signup.php">Back to Sign Up</a>
              </p>
            <?php endif; ?>

            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #666; font-size: 12px;">
              <p>If you didn't request this email verification, please ignore this message.</p>
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
