<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendVerificationEmail($toEmail, $toName, $token) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'u.toob.poob.noob.poop@gmail.com'; // Your Gmail address
        $mail->Password   = 'tnzv ruoo fekw vajx'; // App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('purplestring@gmail.com', 'Purple String');
        $mail->addAddress($toEmail, $toName);

$verifyUrl = "http://localhost/Weibsite-Purple-String/verify_email.php?token=" . urlencode($token);

        $mail->isHTML(true);
        $mail->Subject = 'Verify your Purple String email';
        $mail->Body    = "Hi $toName,<br><br>Click below to verify your email:<br><br>
                          <a href='$verifyUrl'>$verifyUrl</a><br><br>
                          If you didn't sign up, ignore this.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer error: {$mail->ErrorInfo}");
        return false;
    }
}