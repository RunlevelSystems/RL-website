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

function send_project_proposal_email($to, $name, $requestId, $link) {
    $subject = 'Runlevel Systems Proposal - ' . $requestId;
    $body = "Hello {$name},\n\n"
        . "Your proposal is ready for review.\n\n"
        . "Request ID:\n{$requestId}\n\n"
        . "Please review the linked document:\n{$link}\n\n"
        . "Runlevel Systems\n"
        . "DESIGN • DEBUG • DEPLOY\n\n"
        . "TODO: Add PDF attachment generation later.\n";
    return send_email($to, $subject, $body);
}

function send_project_agreement_email($to, $name, $requestId, $link) {
    $subject = 'Runlevel Systems Project Agreement - ' . $requestId;
    $body = "Hello {$name},\n\n"
        . "Your project agreement is ready for review.\n\n"
        . "Request ID:\n{$requestId}\n\n"
        . "Please review the linked document:\n{$link}\n\n"
        . "Runlevel Systems\n"
        . "DESIGN • DEBUG • DEPLOY\n\n"
        . "TODO: Add PDF attachment generation later.\n";
    return send_email($to, $subject, $body);
}
