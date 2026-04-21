<?php
/**
 * ATIERA Hotel & Restaurant - Configuration File
 */

// --- 1. EMAIL CONFIGURATION (PHPMailer Power) ---
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
        return "PHPMailer Error.";
    }

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // --- NEW ATTEMPT: SMTP over Localhost (Port 25) ---
        // Madalas ito ang pinalulusot ng Hostinger/cPanel firewall
        $mail->isSMTP();
        $mail->Host       = 'localhost'; // Huwag mong palitan ito, i-test natin muna
        $mail->SMTPAuth   = false;      // Kadalasan hindi kailangan ng auth pag localhost
        $mail->Port       = 25;
        $mail->Timeout    = 5;
        
        $mail->setFrom('admin@atierahotelandrestaurant.com', SMTP_FROM_NAME);
        $mail->addAddress($to, $name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        if ($mail->send()) return true;

    } catch (Exception $e) {
        // --- FALLBACK ATTEMPT: Classic Gmail SMTP ---
        try {
            $mail2 = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail2->isSMTP();
            $mail2->Host       = 'smtp.gmail.com';
            $mail2->SMTPAuth   = true;
            $mail2->Username   = SMTP_USER;
            $mail2->Password   = str_replace(' ', '', SMTP_PASS); 
            $mail2->SMTPSecure = 'ssl';
            $mail2->Port       = 465;
            $mail2->setFrom(SMTP_USER, SMTP_FROM_NAME);
            $mail2->addAddress($to, $name);
            $mail2->isHTML(true);
            $mail2->Subject = $subject;
            $mail2->Body    = $body;
            
            if ($mail2->send()) return true;
        } catch (Exception $e2) {
            // --- LAST CHANCE: isMail() Force ---
            $mail3 = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail3->isMail();
            $mail3->setFrom('admin@atierahotelandrestaurant.com', SMTP_FROM_NAME);
            $mail3->addAddress($to, $name);
            $mail3->isHTML(true);
            $mail3->Subject = $subject;
            $mail3->Body    = $body;
            return $mail3->send();
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
