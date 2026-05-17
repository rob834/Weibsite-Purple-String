<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '../../../vendor/phpmailer/phpmailer/src/Exception.php';
require __DIR__ . '../../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '../../../vendor/phpmailer/phpmailer/src/SMTP.php';

// ── Shared SMTP factory ───────────────────────────────────────────────────────
function createMailer(): PHPMailer {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'u.toob.poob.noob.poop@gmail.com';
    $mail->Password   = 'tnzv ruoo fekw vajx';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->setFrom('purplestring@gmail.com', 'Purple String');
    $mail->CharSet    = 'UTF-8';
    return $mail;
}

// ── Generic mailer (used by place_order, send_receipt, mark_paid) ─────────────
function sendMail(string $to, string $subject, string $htmlBody): bool {
    $mail = createMailer();
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->isHTML(true);
    $mail->Body    = $htmlBody;
    $mail->AltBody = strip_tags($htmlBody);
    try {
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

// ── Verification email (used by signup) ───────────────────────────────────────
function sendVerificationEmail($toEmail, $toName, $token) {
    $mail = createMailer();
    try {
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

// ── Password Reset Email (NEW) ───────────────────────────────────────────────
function sendPasswordResetEmail($toEmail, $toName, $token) {
    $mail = createMailer();
    try {
        $mail->addAddress($toEmail, $toName);
        
        // This links to the reset execution page built in Step 4
        $resetUrl = "http://localhost/Weibsite-Purple-String/reset_password.php?token=" . urlencode($token);

        $mail->isHTML(true);
        $mail->Subject = 'Reset your Purple String Password';
        $mail->Body    = "Hi $toName,<br><br>
                          You requested a password reset. Click the link below to set a new password. This link expires in 30 minutes:<br><br>
                          <a href='$resetUrl' style='background:#6c5ce7; color:white; padding:10px 15px; text-decoration:none; border-radius:4px;'>Reset Password</a><br><br>
                          Alternatively, copy and paste this URL into your browser:<br>
                          <a href='$resetUrl'>$resetUrl</a><br><br>
                          If you did not request this change, please ignore this email safely.";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer error: {$mail->ErrorInfo}");
        return false;
    }
}
?>