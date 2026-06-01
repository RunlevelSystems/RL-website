<?php
if (!defined('WDS_SYSTEM')) {
    die('Access denied');
}

if (!function_exists('portalLoadAdminSettings')) {
    require_once __DIR__ . '/portal-helpers.php';
}

function rlsSetLastEmailStatus($error = '', $warning = '') {
    $GLOBALS['rls_last_email_error'] = trim((string)$error);
    $GLOBALS['rls_last_email_warning'] = trim((string)$warning);
}

function rlsGetLastEmailError() {
    return (string)($GLOBALS['rls_last_email_error'] ?? '');
}

function rlsGetLastEmailWarning() {
    return (string)($GLOBALS['rls_last_email_warning'] ?? '');
}

function rlsClearLastEmailStatus() {
    rlsSetLastEmailStatus('', '');
}

function rlsMailerSettings() {
    if (function_exists('portalLoadAdminSettings')) {
        $settings = portalLoadAdminSettings();
        if (isset($settings['email']) && is_array($settings['email'])) {
            return $settings['email'];
        }
    }
    return [
        'from_name' => 'Runlevel Systems',
        'from_email' => 'billing@runlevelsystems.com',
        'reply_to' => 'billing@runlevelsystems.com',
        'smtp_host' => 'mail.runlevelsystems.com',
        'smtp_port' => 465,
        'smtp_security' => 'ssl',
        'smtp_auth' => true,
        'smtp_username' => 'billing@runlevelsystems.com',
        'smtp_password' => '',
        'smtp_debug' => 'off',
    ];
}

function rlsMailerHasSmtpConfiguration(array $settings = null) {
    $settings = $settings ?: rlsMailerSettings();
    return trim((string)($settings['smtp_host'] ?? '')) !== ''
        && (int)($settings['smtp_port'] ?? 0) > 0
        && filter_var((string)($settings['from_email'] ?? ''), FILTER_VALIDATE_EMAIL);
}

function rlsMailerAutoloadPhpMailer() {
    static $loaded = false;
    if ($loaded) {
        return;
    }
    $autoload = dirname(__DIR__) . '/vendor/autoload.php';
    if (file_exists($autoload)) {
        require_once $autoload;
    }
    $loaded = true;
}

function rlsMailerHasPhpMailer() {
    rlsMailerAutoloadPhpMailer();
    return class_exists('\\PHPMailer\\PHPMailer\\PHPMailer');
}

function rlsMailerTransportSummary() {
    if (rlsMailerHasPhpMailer()) {
        return 'PHPMailer SMTP';
    }
    return 'Built-in SMTP mailer';
}

function rlsSanitizeEmailError($message) {
    $message = preg_replace('/\s+/', ' ', (string)$message);
    $message = str_replace(["\r", "\n"], ' ', $message);
    return trim((string)$message);
}

function rlsErrorLogEmail($message) {
    $clean = rlsSanitizeEmailError($message);
    if ($clean !== '') {
        error_log('[RLS Mailer] ' . $clean);
    }
}

function rlsHeaderValue($value) {
    return trim(preg_replace('/[\r\n]+/', ' ', (string)$value));
}

function rlsEncodeHeader($value) {
    $value = rlsHeaderValue($value);
    if ($value === '' || preg_match('/^[\x20-\x7E]+$/', $value)) {
        return $value;
    }
    if (function_exists('mb_encode_mimeheader')) {
        return mb_encode_mimeheader($value, 'UTF-8', 'B', "\r\n");
    }
    return '=?UTF-8?B?' . base64_encode($value) . '?=';
}

function rlsNormalizeRecipients($to) {
    $raw = is_array($to) ? $to : [$to];
    $valid = [];
    $attempted = [];
    foreach ($raw as $entry) {
        $email = trim((string)$entry);
        if ($email === '') {
            continue;
        }
        $attempted[] = $email;
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $valid[] = $email;
        }
    }
    return ['valid' => array_values(array_unique($valid)), 'attempted' => $attempted];
}

function rlsBuildMessage(array $recipients, $subject, $textBody, $htmlBody, array $settings, array $options = []) {
    $fromName = rlsHeaderValue((string)($options['from_name'] ?? $settings['from_name'] ?? 'Runlevel Systems'));
    $fromEmail = trim((string)($options['from_email'] ?? $settings['from_email'] ?? ''));
    $replyTo = trim((string)($options['reply_to'] ?? $settings['reply_to'] ?? $fromEmail));
    $toHeader = implode(', ', $recipients);
    $subjectHeader = rlsEncodeHeader($subject);

    $headers = [
        'Date: ' . date('r'),
        'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . preg_replace('/[^A-Za-z0-9.-]/', '', parse_url('mailto:' . $fromEmail, PHP_URL_PATH) ?: 'runlevelsystems.com') . '>',
        'MIME-Version: 1.0',
        'From: ' . ($fromName !== '' ? rlsEncodeHeader($fromName) . ' <' . $fromEmail . '>' : $fromEmail),
        'To: ' . $toHeader,
        'Subject: ' . $subjectHeader,
    ];
    if ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: ' . $replyTo;
    }

    $textBody = str_replace(["\r\n", "\r"], "\n", (string)$textBody);
    $textBody = str_replace("\n", "\r\n", $textBody);
    $htmlBody = trim((string)$htmlBody);

    if ($htmlBody !== '') {
        $boundary = 'rls_' . bin2hex(random_bytes(12));
        $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';
        $body = '--' . $boundary . "\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: 8bit\r\n\r\n"
            . $textBody . "\r\n\r\n"
            . '--' . $boundary . "\r\n"
            . "Content-Type: text/html; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: 8bit\r\n\r\n"
            . str_replace(["\r\n", "\r"], "\n", $htmlBody) . "\r\n\r\n"
            . '--' . $boundary . "--\r\n";
    } else {
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'Content-Transfer-Encoding: 8bit';
        $body = $textBody;
    }

    return [
        'headers' => $headers,
        'headers_string' => implode("\r\n", $headers),
        'body' => $body,
        'subject' => $subject,
        'from_email' => $fromEmail,
        'reply_to' => $replyTo,
    ];
}

function rlsSmtpDebug($message, array $settings, $level = 'basic') {
    $debug = strtolower(trim((string)($settings['smtp_debug'] ?? 'off')));
    if ($debug === 'off') {
        return;
    }
    if ($debug === 'basic' && $level === 'verbose') {
        return;
    }
    rlsErrorLogEmail('SMTP ' . $message);
}

function rlsSmtpReadResponse($socket, array $settings) {
    $response = '';
    while (!feof($socket)) {
        $line = fgets($socket, 515);
        if ($line === false) {
            break;
        }
        $response .= $line;
        if (isset($line[3]) && $line[3] === ' ') {
            break;
        }
    }
    rlsSmtpDebug('RECV ' . trim($response), $settings, 'verbose');
    return [
        'code' => (int)substr($response, 0, 3),
        'message' => trim($response),
    ];
}

function rlsSmtpSendCommand($socket, $command, array $settings, array $expectedCodes, &$error, $logCommand = true) {
    if ($logCommand) {
        rlsSmtpDebug('SEND ' . $command, $settings, 'verbose');
    }
    fwrite($socket, $command . "\r\n");
    $response = rlsSmtpReadResponse($socket, $settings);
    if (!in_array($response['code'], $expectedCodes, true)) {
        $error = $response['message'] !== '' ? $response['message'] : 'SMTP command failed.';
        return false;
    }
    return $response;
}

function rlsSmtpCryptoMethod() {
    $method = 0;
    foreach ([
        'STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT',
        'STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT',
        'STREAM_CRYPTO_METHOD_TLS_CLIENT',
    ] as $constant) {
        if (defined($constant)) {
            $method |= constant($constant);
        }
    }
    return $method ?: STREAM_CRYPTO_METHOD_TLS_CLIENT;
}

function rlsSendWithSmtp(array $recipients, array $message, array $settings, &$error) {
    $host = trim((string)($settings['smtp_host'] ?? ''));
    $port = (int)($settings['smtp_port'] ?? 465);
    $security = strtolower(trim((string)($settings['smtp_security'] ?? 'ssl')));
    $username = trim((string)($settings['smtp_username'] ?? ''));
    $password = (string)($settings['smtp_password'] ?? '');
    $requiresAuth = !empty($settings['smtp_auth']);

    $remoteHost = $security === 'ssl' ? 'ssl://' . $host : $host;
    $socket = @stream_socket_client($remoteHost . ':' . $port, $errno, $errstr, 15, STREAM_CLIENT_CONNECT);
    if (!$socket) {
        $error = 'Unable to connect to the SMTP server.';
        return false;
    }

    stream_set_timeout($socket, 15);
    $greeting = rlsSmtpReadResponse($socket, $settings);
    if ($greeting['code'] !== 220) {
        fclose($socket);
        $error = $greeting['message'] !== '' ? $greeting['message'] : 'SMTP server did not accept the connection.';
        return false;
    }

    $helloHost = preg_replace('/[^A-Za-z0-9.-]/', '', $_SERVER['HTTP_HOST'] ?? 'runlevelsystems.com');
    if ($helloHost === '') {
        $helloHost = 'runlevelsystems.com';
    }

    $smtpError = '';
    if (!rlsSmtpSendCommand($socket, 'EHLO ' . $helloHost, $settings, [250], $smtpError)) {
        fclose($socket);
        $error = $smtpError;
        return false;
    }

    if ($security === 'tls') {
        if (!rlsSmtpSendCommand($socket, 'STARTTLS', $settings, [220], $smtpError)) {
            fclose($socket);
            $error = 'SMTP STARTTLS failed.';
            return false;
        }
        if (!@stream_socket_enable_crypto($socket, true, rlsSmtpCryptoMethod())) {
            fclose($socket);
            $error = 'Unable to establish TLS encryption with the SMTP server.';
            return false;
        }
        if (!rlsSmtpSendCommand($socket, 'EHLO ' . $helloHost, $settings, [250], $smtpError)) {
            fclose($socket);
            $error = $smtpError;
            return false;
        }
    }

    if ($requiresAuth) {
        if ($username === '' || $password === '') {
            fclose($socket);
            $error = 'SMTP authentication is enabled but credentials are incomplete.';
            return false;
        }
        if (!rlsSmtpSendCommand($socket, 'AUTH LOGIN', $settings, [334], $smtpError)) {
            fclose($socket);
            $error = 'SMTP authentication failed.';
            return false;
        }
        if (!rlsSmtpSendCommand($socket, base64_encode($username), $settings, [334], $smtpError, false)) {
            fclose($socket);
            $error = 'SMTP username was rejected.';
            return false;
        }
        if (!rlsSmtpSendCommand($socket, base64_encode($password), $settings, [235], $smtpError, false)) {
            fclose($socket);
            $error = 'SMTP password was rejected.';
            return false;
        }
    }

    if (!rlsSmtpSendCommand($socket, 'MAIL FROM:<' . $message['from_email'] . '>', $settings, [250], $smtpError)) {
        fclose($socket);
        $error = $smtpError;
        return false;
    }

    foreach ($recipients as $recipient) {
        if (!rlsSmtpSendCommand($socket, 'RCPT TO:<' . $recipient . '>', $settings, [250, 251], $smtpError)) {
            fclose($socket);
            $error = $smtpError;
            return false;
        }
    }

    if (!rlsSmtpSendCommand($socket, 'DATA', $settings, [354], $smtpError)) {
        fclose($socket);
        $error = $smtpError;
        return false;
    }

    $payload = $message['headers_string'] . "\r\n\r\n" . $message['body'];
    $payload = preg_replace('/(?m)^\./', '..', $payload);
    fwrite($socket, $payload . "\r\n.\r\n");
    $response = rlsSmtpReadResponse($socket, $settings);
    if ($response['code'] !== 250) {
        fclose($socket);
        $error = $response['message'] !== '' ? $response['message'] : 'SMTP server rejected the message.';
        return false;
    }

    rlsSmtpSendCommand($socket, 'QUIT', $settings, [221, 250], $smtpError);
    fclose($socket);
    return true;
}

function rlsSendWithPhpMailer(array $recipients, $subject, $textBody, $htmlBody, array $settings, array $options, &$error) {
    rlsMailerAutoloadPhpMailer();
    if (!class_exists('\\PHPMailer\\PHPMailer\\PHPMailer')) {
        $error = 'PHPMailer is not available.';
        return false;
    }

    try {
        $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mailer->CharSet = 'UTF-8';
        $mailer->isSMTP();
        $mailer->Host = trim((string)($settings['smtp_host'] ?? ''));
        $mailer->Port = (int)($settings['smtp_port'] ?? 465);
        $mailer->SMTPAuth = !empty($settings['smtp_auth']);
        $mailer->Username = trim((string)($settings['smtp_username'] ?? ''));
        $mailer->Password = (string)($settings['smtp_password'] ?? '');
        $security = strtolower(trim((string)($settings['smtp_security'] ?? 'ssl')));
        if ($security === 'ssl') {
            $mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($security === 'tls') {
            $mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mailer->setFrom(
            trim((string)($options['from_email'] ?? $settings['from_email'] ?? '')),
            trim((string)($options['from_name'] ?? $settings['from_name'] ?? 'Runlevel Systems'))
        );
        $replyTo = trim((string)($options['reply_to'] ?? $settings['reply_to'] ?? ''));
        if ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $mailer->addReplyTo($replyTo);
        }
        foreach ($recipients as $recipient) {
            $mailer->addAddress($recipient);
        }
        $mailer->Subject = (string)$subject;
        $mailer->Body = (string)($htmlBody !== '' ? $htmlBody : nl2br(htmlspecialchars((string)$textBody, ENT_QUOTES, 'UTF-8')));
        $mailer->AltBody = (string)$textBody;
        $mailer->isHTML($htmlBody !== '');
        $mailer->send();
        return true;
    } catch (\Throwable $exception) {
        $error = rlsSanitizeEmailError($exception->getMessage());
        return false;
    }
}

function rlsSendWithMailFallback(array $recipients, array $message, &$error) {
    $result = @mail(implode(', ', $recipients), (string)$message['subject'], (string)$message['body'], (string)$message['headers_string']);
    if (!$result) {
        $error = 'PHP mail() fallback failed.';
        return false;
    }
    return true;
}

function send_rls_email($to, $subject, $body, $options = []) {
    rlsClearLastEmailStatus();

    $normalized = rlsNormalizeRecipients($to);
    $recipients = $normalized['valid'];
    $attempted = $normalized['attempted'];
    if (empty($recipients)) {
        $error = 'Please provide a valid recipient email address.';
        rlsSetLastEmailStatus($error);
        if (function_exists('portalAppendEmailLog')) {
            portalAppendEmailLog([
                'email_id' => bin2hex(random_bytes(8)),
                'timestamp' => date('c'),
                'to' => implode(', ', $attempted),
                'subject' => (string)$subject,
                'status' => 'failed',
                'error_message' => $error,
                'context' => (string)($options['context'] ?? 'general'),
            ]);
        }
        return false;
    }

    $settings = rlsMailerSettings();
    $subject = trim((string)$subject);
    $textBody = (string)$body;
    $htmlBody = (string)($options['html_body'] ?? '');
    $message = rlsBuildMessage($recipients, $subject, $textBody, $htmlBody, $settings, $options);
    $context = trim((string)($options['context'] ?? 'general'));
    $warning = '';
    $error = '';
    $sent = false;

    if (rlsMailerHasSmtpConfiguration($settings)) {
        if (rlsMailerHasPhpMailer()) {
            $sent = rlsSendWithPhpMailer($recipients, $subject, $textBody, $htmlBody, $settings, $options, $error);
        } else {
            $sent = rlsSendWithSmtp($recipients, $message, $settings, $error);
            if ($sent) {
                $warning = 'PHPMailer is not installed. Sent using the built-in SMTP mailer.';
            }
        }
        if (!$sent && !empty($options['allow_mail_fallback'])) {
            $warning = 'SMTP delivery failed. PHP mail() fallback was used.';
            $sent = rlsSendWithMailFallback($recipients, $message, $error);
        }
    } else {
        $warning = 'SMTP settings are incomplete. PHP mail() fallback was used.';
        $sent = rlsSendWithMailFallback($recipients, $message, $error);
    }

    if (!$sent) {
        $error = $error !== '' ? rlsSanitizeEmailError($error) : 'Email delivery failed.';
        rlsErrorLogEmail($error);
        rlsSetLastEmailStatus($error, $warning);
    } else {
        rlsSetLastEmailStatus('', $warning);
    }

    if (function_exists('portalAppendEmailLog')) {
        portalAppendEmailLog([
            'email_id' => bin2hex(random_bytes(8)),
            'timestamp' => date('c'),
            'to' => implode(', ', $recipients),
            'subject' => $subject,
            'status' => $sent ? 'sent' : 'failed',
            'error_message' => $sent ? '' : $error,
            'context' => $context,
        ]);
    }

    return $sent;
}
