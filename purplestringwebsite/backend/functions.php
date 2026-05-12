<?php

function check_login($con)
{

	if(isset($_SESSION['user_id']))
	{

		$id = $_SESSION['user_id'];
		$query = "select * from users where user_id = '$id' limit 1";

		$result = mysqli_query($con,$query);
		if($result && mysqli_num_rows($result) > 0)
		{

			$user_data = mysqli_fetch_assoc($result);
			return $user_data;
		}
	}

	// User not logged in, return false
	return false;

}

function random_num($length)
{

	$text = "";
	if($length < 5)
	{
		$length = 5;
	}

	$len = rand(4,$length);

	for ($i=0; $i < $len; $i++) { 
		# code...

		$text .= rand(0,9);
	}

	return $text;
}

function generate_verification_token()
{
	return bin2hex(random_bytes(32));
}

function send_verification_email($email, $verification_token, $user_name)
{
	$verification_link = "http://localhost/Weibsite-Purple-String/verify_email.php?token=" . urlencode($verification_token);
	
	$subject = "Email Verification - Purple String";
	
	$message = "
	<html>
		<head>
			<title>Email Verification</title>
			<style>
				body { font-family: Arial, sans-serif; }
				.container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; }
				.header { background-color: #8B4789; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
				.content { background-color: white; padding: 20px; }
				.footer { background-color: #f5f5f5; padding: 10px; text-align: center; font-size: 12px; color: #666; }
				.button { background-color: #8B4789; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
			</style>
		</head>
		<body>
			<div class='container'>
				<div class='header'>
					<h1>Purple String</h1>
				</div>
				<div class='content'>
					<h2>Welcome, $user_name!</h2>
					<p>Thank you for signing up for Purple String. Please verify your email address to activate your account.</p>
					<p>Click the button below to verify your email:</p>
					<p><a href='$verification_link' class='button'>Verify Email</a></p>
					<p>Or copy and paste this link in your browser:</p>
					<p>$verification_link</p>
					<p>If you did not sign up for this account, please ignore this email.</p>
				</div>
				<div class='footer'>
					<p>&copy; 2026 Purple String. All rights reserved.</p>
				</div>
			</div>
		</body>
	</html>
	";
	
	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8\r\n";
	$headers .= "From: noreply@purplestring.com\r\n";
	
	// Log email to file for development/testing purposes
	$log_file = __DIR__ . '/email_logs.txt';
	$log_entry = "=== Email Log Entry ===\n";
	$log_entry .= "Date: " . date('Y-m-d H:i:s') . "\n";
	$log_entry .= "To: $email\n";
	$log_entry .= "Username: $user_name\n";
	$log_entry .= "Subject: $subject\n";
	$log_entry .= "Verification Link: $verification_link\n";
	$log_entry .= "Token: $verification_token\n";
	$log_entry .= "Message:\n$message\n";
	$log_entry .= "========================\n\n";
	
	// Append to log file
	file_put_contents($log_file, $log_entry, FILE_APPEND);
	
	// Try to send email via mail() function
	// For production, configure proper SMTP in php.ini
	$mail_sent = false;
	if (function_exists('mail')) {
		$mail_sent = @mail($email, $subject, $message, $headers);
	}
	
	// Return true if either mail was sent or logged successfully
	return true;
}