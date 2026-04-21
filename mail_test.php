<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>ATIERA Mail Diagnostic Tool</h2>";

// 1. Check PHP mail()
echo "Checking native mail() function... ";
if (function_exists('mail')) {
    echo "<b style='color:green'>ENABLED</b><br>";
    $to = "atiera41001@gmail.com";
    $subject = "Diagnostic Test";
    $body = "Testing native mail() function.";
    $headers = "From: linbilcelestre31@gmail.com";
    if (mail($to, $subject, $body, $headers)) {
        echo "Attempt to send via mail(): <b style='color:green'>SUCCESS (Returned True)</b><br>";
    } else {
        echo "Attempt to send via mail(): <b style='color:red'>FAILED</b><br>";
    }
} else {
    echo "<b style='color:red'>DISABLED</b><br>";
}

echo "<hr>";

// 2. Check Port Connectivity
$ports = [587, 465, 25, 2525];
echo "Checking Port Connectivity to smtp.gmail.com...<br>";
foreach ($ports as $port) {
    echo "Port $port: ";
    $fp = @fsockopen('smtp.gmail.com', $port, $errno, $errstr, 5);
    if ($fp) {
        echo "<b style='color:green'>OPEN</b><br>";
        fclose($fp);
    } else {
        echo "<b style='color:red'>CLOSED</b> ($errstr)<br>";
    }
}

echo "<hr>";

// 3. Check curl (for API based sending)
echo "Checking curl (for API fallback)... ";
if (function_exists('curl_init')) {
    echo "<b style='color:green'>ENABLED</b><br>";
} else {
    echo "<b style='color:red'>DISABLED</b><br>";
}
