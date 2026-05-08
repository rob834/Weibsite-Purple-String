                  <?php

                    session_start();

                    if(isset($_SESSION['user_id']))
                      {
	                      unset($_SESSION['user_id']);

                      }

                    header("Location: /Weibsite-Purple-String/login.php");
                    die;

                  ?>