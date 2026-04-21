<?php
/**
 * ATIERA Hotel & Restaurant - Configuration File
 */

// --- 1. EMAIL CONFIGURATION ---
define('SMTP_USER', 'linbilcelestre31@gmail.com');
define('SMTP_FROM_NAME', 'ATIERA Hotel & Restaurant');

function sendEmail($to, $name, $subject, $body)
{
    $root = dirname(__DIR__); 
    @include_once $root . '/PHPMailer/src/Exception.php';
    @include_once $root . '/PHPMailer/src/PHPMailer.php';
    @include_once $root . '/PHPMailer/src/SMTP.php';

    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return "PHPMailer Library Missing.";
    }

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // METHOD: PHPMailer isMail (Ang tanging bukas sa server mo)
        $mail->isMail();
        
        // CRITICAL FIX: Ang sender address ay DAPAT @atierahotelandrestaurant.com
        // para pagkatiwalaan ni Google at hindi i-block as spam.
        $domain_sender = 'admin@atierahotelandrestaurant.com';
        
        $mail->setFrom($domain_sender, SMTP_FROM_NAME);
        $mail->addReplyTo(SMTP_USER, SMTP_FROM_NAME); // Dito pa rin papunta ang replies sa Gmail mo
        $mail->addAddress($to, $name);
        
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        if ($mail->send()) return true;
        
    } catch (Exception $e) {
        return "PHPMailer Error: " . $mail->ErrorInfo;
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
