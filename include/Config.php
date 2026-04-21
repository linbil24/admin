<?php
/**
 * ATIERA Hotel & Restaurant - Configuration File
 */

// --- 1. EMAIL CONFIGURATION (PHPMailer Focus) ---
define('SMTP_USER', 'linbilcelestre31@gmail.com');
define('SMTP_PASS', 'poti vsjc wfth dzks');
define('SMTP_FROM_NAME', 'ATIERA Hotel & Restaurant');

function sendEmail($to, $name, $subject, $body)
{
    $root = dirname(__DIR__); 
    @include_once $root . '/PHPMailer/src/Exception.php';
    @include_once $root . '/PHPMailer/src/PHPMailer.php';
    @include_once $root . '/PHPMailer/src/SMTP.php';

    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return "Critical Error: PHPMailer not found.";
    }

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // --- RESTORING CLASSIC SMTP (Port 587 TLS) ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = str_replace(' ', '', SMTP_PASS); 
        $mail->SMTPSecure = 'tls'; // Ginamit nating TLS sa halip na SSL
        $mail->Port       = 587;   // Classic Port 587
        $mail->Timeout    = 20;
        
        $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
        $mail->addAddress($to, $name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        // Anti-Block Options
        $mail->SMTPOptions = [
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]
        ];

        if ($mail->send()) return true;

    } catch (Exception $e) {
        // SECOND CHANCE: isMail with Domain Sender
        try {
            $mail2 = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail2->isMail();
            $mail2->setFrom('admin@atierahotelandrestaurant.com', SMTP_FROM_NAME);
            $mail2->addAddress($to, $name);
            $mail2->isHTML(true);
            $mail2->Subject = $subject;
            $mail2->Body    = $body;
            if ($mail2->send()) return true;
        } catch (Exception $e2) {
            return "Fail: " . $mail2->ErrorInfo;
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
