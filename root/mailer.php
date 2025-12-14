<?php
// mailer.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php'; // Composer autoload

function getMailer() {
    // === Edit these with your Gmail account / app password ===
    $SMTP_HOST = 'smtp.gmail.com';
    $SMTP_PORT = 587;
    $SMTP_USER = 'your@gmail.com';          // <-- your Gmail address
    $SMTP_PASS = 'your_app_password_here';  // <-- App password (NOT your normal Gmail password)
    $FROM_EMAIL = 'your@gmail.com';
    $FROM_NAME  = 'TravelSys Notifications';

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = $SMTP_USER;
    $mail->Password = $SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $SMTP_PORT;
    $mail->setFrom($FROM_EMAIL, $FROM_NAME);
    $mail->isHTML(false);
    return $mail;
}

function sendEmail($to, $subject, $body) {
    try {
        $mail = getMailer();
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail error: " . $e->getMessage());
        return false;
    }
}
