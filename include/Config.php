<?php
/**
 * ATIERA Hotel & Restaurant - Configuration File
 */

// --- 1. EMAIL CONFIGURATION (PHPMailer Focus) ---
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'linbilcelestre31@gmail.com');
define('SMTP_PASS', 'poti vsjc wfth dzks');
define('SMTP_FROM_EMAIL', 'linbilcelestre31@gmail.com');
define('SMTP_FROM_NAME', 'ATIERA Hotel & Restaurant');

function sendEmail($to, $name, $subject, $body)
{
    $root = dirname(__DIR__); 
    @include_once $root . '/PHPMailer/src/Exception.php';
    @include_once $root . '/PHPMailer/src/PHPMailer.php';
    @include_once $root . '/PHPMailer/src/SMTP.php';

    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return "PHPMailer Missing.";
    }

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // TRY SMTP FIRST
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = str_replace(' ', '', SMTP_PASS); 
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;
        $mail->Timeout    = 5;
        
        $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
        $mail->addAddress($to, $name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        if ($mail->send()) return true;
    } catch (Exception $e) {
        // FALLBACK: Internal Transport (isMail)
        // This is usually what triggers the GREEN success message on blocked hosts
        try {
            $mailFallback = new PHPMailer\PHPMailer\PHPMailer(true);
            $mailFallback->isMail();
            
            // CRITICAL: Use your domain email here to bypass server filters
            $sender = 'no-reply@atierahotelandrestaurant.com'; 
            $mailFallback->setFrom($sender, SMTP_FROM_NAME);
            $mailFallback->addReplyTo(SMTP_USER, SMTP_FROM_NAME);
            $mailFallback->addAddress($to, $name);
            
            $mailFallback->isHTML(true);
            $mailFallback->Subject = $subject;
            $mailFallback->Body    = $body;
            
            if ($mailFallback->send()) return true;
        } catch (Exception $e2) {
            return "Final Fail: " . $mailFallback->ErrorInfo;
        }
    }
    return false;
}

// --- 2. BASE URL DETECTION ---
if (!function_exists('getBaseUrl')) {
    function getBaseUrl() {
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . "://" . $host . "/admin";
    }
}
