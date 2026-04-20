<?php 
session_start();

	include("purplestringwebsite/backend/connection.php");
	include("purplestringwebsite/backend/functions.php");


	if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST")
	{
		//something was posted
		$user_name = $_POST['user_name'];
		$password = $_POST['password'];

		if(!empty($user_name) && !empty($password) && !is_numeric($user_name))
		{

			//save to database
			$user_id = random_num(20);
			$query = "insert into users (user_id,user_name,password) values ('$user_id','$user_name','$password')";

			mysqli_query($con, $query);

			header("Location: /Weibsite-Purple-String/login.php");
			die;
		}else
		{
			echo "Please enter some valid information!";
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
                <a href="#" class="forgot-password">Forgot Password?</a>
              </div>

              <button type="submit" value="Login" class="login-btn">Log In</button>
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

    <!-- <script src="./js/login.js"></!--> -->
  </body>
</html>