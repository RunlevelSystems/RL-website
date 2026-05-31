<?php
if (!defined('WDS_SYSTEM')) {
    die('Access denied');
}

/**
 * TODO: Replace PHP mail() with SMTP provider before production.
 * TODO: Configure SPF/DKIM/DMARC for reliable delivery.
 */
function send_email($to, $subject, $body) {
    $to = trim((string)$to);
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/plain; charset=UTF-8\r\n";
    $headers .= "From: Runlevel Systems <no-reply@runlevel.systems>\r\n";

    return @mail($to, (string)$subject, (string)$body, $headers);
}
