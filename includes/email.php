<?php
if (!defined('WDS_SYSTEM')) {
    die('Access denied');
}

require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/email-templates.php';

function rlsSiteBaseUrl() {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'https';
    $host = trim((string)($_SERVER['HTTP_HOST'] ?? 'runlevel.systems'));
    if ($host === '') {
        $host = 'runlevel.systems';
    }
    return $scheme . '://' . $host;
}

function rlsInternalNotificationEmail() {
    $settings = portalLoadAdminSettings();
    $candidates = [
        (string)($settings['site']['support_email'] ?? ''),
        (string)($settings['email']['reply_to'] ?? ''),
        (string)($settings['email']['from_email'] ?? ''),
        (string)($settings['email']['smtp_username'] ?? ''),
    ];
    foreach ($candidates as $candidate) {
        $candidate = trim($candidate);
        if ($candidate !== '' && filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
            return $candidate;
        }
    }
    return '';
}

function send_email($to, $subject, $body, $options = []) {
    return send_rls_email($to, $subject, $body, is_array($options) ? $options : []);
}

function rlsSendTemplateEmail($template, $to, array $data = [], array $options = []) {
    $payload = rlsEmailTemplate($template, $data);
    $options['context'] = (string)($options['context'] ?? $template);
    if (!empty($payload['html'])) {
        $options['html_body'] = $payload['html'];
    }
    return send_rls_email($to, $payload['subject'], $payload['text'], $options);
}

function send_project_request_confirmation_email($to, $name, $requestId, $projectUrl = '') {
    return rlsSendTemplateEmail('request-received', $to, [
        'name' => $name,
        'request_id' => $requestId,
        'dashboard_url' => $projectUrl !== '' ? $projectUrl : rlsSiteBaseUrl() . '/dashboard.php',
    ], [
        'context' => 'request-received',
    ]);
}

function send_staff_new_request_email($requestId, $name, $email, $projectType, $projectUrl = '') {
    $staffEmail = rlsInternalNotificationEmail();
    if ($staffEmail === '') {
        return false;
    }
    return rlsSendTemplateEmail('staff-new-request', $staffEmail, [
        'request_id' => $requestId,
        'name' => $name,
        'email' => $email,
        'project_type' => $projectType,
        'project_url' => $projectUrl !== '' ? $projectUrl : rlsSiteBaseUrl() . '/staff/estimate-requests.php',
    ], [
        'context' => 'staff-new-request',
    ]);
}

function send_project_proposal_email($to, $name, $requestId, $link, $proposalId = '') {
    return rlsSendTemplateEmail('proposal-sent', $to, [
        'name' => $name,
        'request_id' => $requestId,
        'proposal_id' => $proposalId,
        'proposal_url' => $link !== '' ? $link : rlsSiteBaseUrl() . '/client/proposals.php',
    ], [
        'context' => 'proposal-sent',
    ]);
}

function send_project_agreement_email($to, $name, $requestId, $link) {
    return rlsSendTemplateEmail('project-agreement', $to, [
        'name' => $name,
        'request_id' => $requestId,
        'project_url' => $link !== '' ? $link : rlsSiteBaseUrl() . '/dashboard.php',
    ], [
        'context' => 'project-agreement',
    ]);
}

function send_proposal_change_requested_email($proposalId, $requestId, $customerName, $changeMessage, $projectUrl = '') {
    $staffEmail = rlsInternalNotificationEmail();
    if ($staffEmail === '') {
        return false;
    }
    return rlsSendTemplateEmail('proposal-change-requested', $staffEmail, [
        'proposal_id' => $proposalId,
        'request_id' => $requestId,
        'name' => $customerName,
        'change_message' => $changeMessage,
        'proposal_url' => $projectUrl !== '' ? $projectUrl : rlsSiteBaseUrl() . '/staff/proposals.php',
    ], [
        'context' => 'proposal-change-requested',
    ]);
}

function send_proposal_accepted_customer_email($to, $name, $requestId, $proposalId, $projectUrl = '') {
    return rlsSendTemplateEmail('proposal-accepted', $to, [
        'name' => $name,
        'request_id' => $requestId,
        'proposal_id' => $proposalId,
        'project_url' => $projectUrl !== '' ? $projectUrl : rlsSiteBaseUrl() . '/dashboard.php',
        'audience' => 'customer',
    ], [
        'context' => 'proposal-accepted-customer',
    ]);
}

function send_proposal_accepted_staff_email($requestId, $proposalId, $customerName, $projectUrl = '') {
    $staffEmail = rlsInternalNotificationEmail();
    if ($staffEmail === '') {
        return false;
    }
    return rlsSendTemplateEmail('proposal-accepted', $staffEmail, [
        'name' => $customerName,
        'request_id' => $requestId,
        'proposal_id' => $proposalId,
        'project_url' => $projectUrl !== '' ? $projectUrl : rlsSiteBaseUrl() . '/staff/proposals.php',
        'audience' => 'staff',
    ], [
        'context' => 'proposal-accepted-staff',
    ]);
}

function send_payment_recorded_customer_email($to, $name, $requestId, $proposalId, $paymentId, $amount, $status, $projectUrl = '') {
    return rlsSendTemplateEmail('payment-recorded', $to, [
        'name' => $name,
        'request_id' => $requestId,
        'proposal_id' => $proposalId,
        'payment_id' => $paymentId,
        'amount' => $amount,
        'status' => $status,
        'project_url' => $projectUrl !== '' ? $projectUrl : rlsSiteBaseUrl() . '/dashboard.php',
        'audience' => 'customer',
    ], [
        'context' => 'payment-recorded-customer',
    ]);
}

function send_payment_recorded_staff_email($requestId, $proposalId, $paymentId, $amount, $status, $projectUrl = '') {
    $staffEmail = rlsInternalNotificationEmail();
    if ($staffEmail === '') {
        return false;
    }
    return rlsSendTemplateEmail('payment-recorded', $staffEmail, [
        'request_id' => $requestId,
        'proposal_id' => $proposalId,
        'payment_id' => $paymentId,
        'amount' => $amount,
        'status' => $status,
        'project_url' => $projectUrl !== '' ? $projectUrl : rlsSiteBaseUrl() . '/staff/payment-record.php',
        'audience' => 'staff',
    ], [
        'context' => 'payment-recorded-staff',
    ]);
}

function send_account_created_email($to, $name, $username, $dashboardUrl = '') {
    return rlsSendTemplateEmail('account-created', $to, [
        'name' => $name,
        'username' => $username,
        'dashboard_url' => $dashboardUrl !== '' ? $dashboardUrl : rlsSiteBaseUrl() . '/client/login.php',
    ], [
        'context' => 'account-created',
    ]);
}

function send_verification_email($to, $name, $verifyUrl) {
    return rlsSendTemplateEmail('verify-email', $to, [
        'name' => $name,
        'verify_url' => $verifyUrl,
    ], [
        'context' => 'verify-email',
    ]);
}

function send_smtp_test_email($to) {
    return rlsSendTemplateEmail('test-email', $to, [], [
        'context' => 'test-email',
    ]);
}
