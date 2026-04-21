<?php
/**
 * ATIERA Central Configuration & Mail Engine
 * Version 5.2
 */

// --- 1. SMTP SERVER SETTINGS ---
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 465);
define('SMTP_USER', 'linbilcelestre31@gmail.com');
define('SMTP_FROM_EMAIL', 'linbilcelestre31@gmail.com');
define('SMTP_FROM_NAME', 'ATIERA Hotel & Restaurant');

/**
 * IMPORTANT: Generate a NEW App Password from your Google Account
 * (Security -> 2-Step Verification -> App Passwords)
 * Link: https://myaccount.google.com/apppasswords
 */
define('SMTP_PASS', 'poti vsjc wfth dzks'); // <--- PALITAN MO ITO NG BAGO


// --- 2. BASE URL DETECTION ---
function getBaseUrl()
{
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
    $host = $_SERVER['HTTP_HOST'];
    $currentDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $parts = explode('/', trim($currentDir, '/'));

    if (in_array('include', $parts)) {
        $projectRoot = '/' . implode('/', array_slice($parts, 0, array_search('include', $parts)));
    } elseif (in_array('auth', $parts)) {
        $projectRoot = '/' . implode('/', array_slice($parts, 0, array_search('auth', $parts)));
    } else {
        $projectRoot = $currentDir;
    }
    return $protocol . "://" . $host . rtrim($projectRoot, '/');
}

// --- 3. PREMIUM EMAIL API (Brevo) ---
// Sign up for a free account at brevo.com if native mail() fails.
define('BREVO_API_KEY', 'PASTE_YOUR_API_KEY_HERE');

function sendEmail($to, $name, $subject, $body, $altBody = '')
{
    // METHOD 1: Brevo API (Uses Port 443 - NEVER Blocked)
    if (defined('BREVO_API_KEY') && BREVO_API_KEY !== 'PASTE_YOUR_API_KEY_HERE') {
        $url = 'https://api.brevo.com/v3/smtp/email';
        $data = [
            'sender' => ['name' => SMTP_FROM_NAME, 'email' => SMTP_FROM_EMAIL],
            'to' => [['email' => $to, 'name' => $name]],
            'subject' => $subject,
            'htmlContent' => $body
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'api-key: ' . BREVO_API_KEY,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) return true;
    }

    // METHOD 2: Native Mail Fallback (Uses local server)
    $sender_email = 'no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'atiera.site');
    $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: ".SMTP_FROM_NAME." <$sender_email>\r\n";
    $headers .= "Reply-To: ".SMTP_FROM_EMAIL."\r\n";

    if (@mail($to, $subject, $body, $headers, "-f" . $sender_email)) {
        return true; 
    }

    // METHOD 3: SMTP (Gmail) - Usually blocked on your host
    $root = dirname(__DIR__); 
    @require_once $root . '/PHPMailer/src/Exception.php';
    @require_once $root . '/PHPMailer/src/PHPMailer.php';
    @require_once $root . '/PHPMailer/src/SMTP.php';

    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = str_replace(' ', '', SMTP_PASS);
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->Timeout = 5;
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($to, $name);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->send();
            return true;
        } catch (Exception $e) { /* silent fail */ }
    }

    return "All methods failed. Please get a Brevo API Key.";
}