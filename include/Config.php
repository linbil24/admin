<?php
/**
 * ATIERA Hotel & Restaurant - Configuration File
 */

// --- 1. EMAIL CONFIGURATION (PHPMailer Only) ---
define('SMTP_USER', 'linbilcelestre31@gmail.com');
define('SMTP_PASS', 'poti vsjc wfth dzks');
define('SMTP_FROM_NAME', 'ATIERA Hotel & Restaurant');

function sendEmail($to, $name, $subject, $body)
{
    $root = dirname(__DIR__); 
    @require_once $root . '/PHPMailer/src/Exception.php';
    @require_once $root . '/PHPMailer/src/PHPMailer.php';
    @require_once $root . '/PHPMailer/src/SMTP.php';

    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return "Critical Error: PHPMailer files not found.";
    }

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // --- PREPARE EMAIl ---
        $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
        $mail->addAddress($to, $name);
        $mail->addReplyTo(SMTP_USER, SMTP_FROM_NAME);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // --- ATTEMPT 1: PHPMailer Standard SMTP (Port 465 SSL) ---
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = str_replace(' ', '', SMTP_PASS); 
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;
            $mail->Timeout    = 15;
            $mail->Hostname   = $_SERVER['HTTP_HOST'] ?? 'atierahotelandrestaurant.com';
            
            // Bypass SSL Verification issues
            $mail->SMTPOptions = [
                'ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]
            ];

            if ($mail->send()) return true;
        } catch (Exception $e) {
            // --- ATTEMPT 2: PHPMailer Fallback (Internal Mail Transport) ---
            // If SMTP is blocked by firewall (Network is unreachable), this is our only hope.
            $mail->isMail();
            $mail->setFrom('admin@atierahotelandrestaurant.com', SMTP_FROM_NAME);
            
            if ($mail->send()) return true;
        }
    } catch (Exception $eFinal) {
        return "All PHPMailer methods failed: " . $mail->ErrorInfo;
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
