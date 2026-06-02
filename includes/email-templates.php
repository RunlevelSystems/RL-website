<?php
if (!defined('WDS_SYSTEM')) {
    die('Access denied');
}

function rlsEmailTextToHtml($text) {
    return nl2br(htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8'));
}

function rlsEmailCompanyName() {
    if (function_exists('portalLoadAdminSettings')) {
        $settings = portalLoadAdminSettings();
        $company = trim((string)($settings['site']['company_name'] ?? ''));
        if ($company !== '') {
            return $company;
        }
    }
    return 'Runlevel Systems';
}

function rlsEmailTemplate($template, array $data = []) {
    $company = rlsEmailCompanyName();
    $dashboardUrl = trim((string)($data['dashboard_url'] ?? 'https://runlevel.systems/dashboard.php'));
    $projectUrl = trim((string)($data['project_url'] ?? $dashboardUrl));
    $proposalUrl = trim((string)($data['proposal_url'] ?? $projectUrl));
    $verifyUrl = trim((string)($data['verify_url'] ?? ''));
    $recipientName = trim((string)($data['name'] ?? 'there'));
    $requestId = trim((string)($data['request_id'] ?? ''));
    $proposalId = trim((string)($data['proposal_id'] ?? ''));
    $paymentId = trim((string)($data['payment_id'] ?? ''));
    $amount = trim((string)($data['amount'] ?? ''));
    $status = trim((string)($data['status'] ?? ''));
    $changeMessage = trim((string)($data['change_message'] ?? ''));
    $username = trim((string)($data['username'] ?? ''));
    $audience = trim((string)($data['audience'] ?? 'customer'));

    switch ($template) {
        case 'request-received':
            $subject = 'We received your Runlevel Systems request';
            $text = "Hello {$recipientName},\n\n"
                . "Thanks for contacting {$company}. We received your request and will review it before preparing an estimate or proposal.\n\n"
                . ($requestId !== '' ? "Request ID: {$requestId}\n\n" : '')
                . "You can log in to your dashboard to view updates when available:\n{$dashboardUrl}\n\n"
                . "{$company}";
            break;

        case 'staff-new-request':
            $subject = 'New Runlevel Systems project request';
            if ($requestId !== '') {
                $subject .= ' - ' . $requestId;
            }
            $text = "A new project request was submitted on the {$company} website.\n\n"
                . ($requestId !== '' ? "Request ID: {$requestId}\n" : '')
                . "Customer: {$recipientName}\n"
                . (!empty($data['email']) ? 'Email: ' . trim((string)$data['email']) . "\n" : '')
                . (!empty($data['project_type']) ? 'Project Type: ' . trim((string)$data['project_type']) . "\n" : '')
                . "\nReview it here:\n{$projectUrl}\n";
            break;

        case 'proposal-sent':
            $subject = 'Your Runlevel Systems proposal is ready';
            if ($requestId !== '') {
                $subject .= ' - ' . $requestId;
            }
            $text = "Hello {$recipientName},\n\n"
                . "Your proposal is ready for review.\n\n"
                . ($requestId !== '' ? "Request ID: {$requestId}\n" : '')
                . ($proposalId !== '' ? "Proposal ID: {$proposalId}\n" : '')
                . "\nView your proposal here:\n{$proposalUrl}\n\n"
                . "{$company}";
            break;

        case 'proposal-change-requested':
            $subject = 'Proposal changes requested';
            if ($requestId !== '') {
                $subject .= ' - ' . $requestId;
            }
            $text = "A customer requested changes to a proposal.\n\n"
                . ($requestId !== '' ? "Request ID: {$requestId}\n" : '')
                . ($proposalId !== '' ? "Proposal ID: {$proposalId}\n" : '')
                . (!empty($data['name']) ? 'Customer: ' . $recipientName . "\n" : '')
                . ($changeMessage !== '' ? "\nRequested changes:\n{$changeMessage}\n" : '')
                . "\nReview the proposal here:\n{$proposalUrl}\n";
            break;

        case 'proposal-accepted':
            if ($audience === 'staff') {
                $subject = 'Proposal accepted';
                if ($requestId !== '') {
                    $subject .= ' - ' . $requestId;
                }
                $text = "A customer accepted a proposal.\n\n"
                    . ($requestId !== '' ? "Request ID: {$requestId}\n" : '')
                    . ($proposalId !== '' ? "Proposal ID: {$proposalId}\n" : '')
                    . (!empty($data['name']) ? 'Customer: ' . $recipientName . "\n" : '')
                    . "\nOpen the project here:\n{$projectUrl}\n";
            } else {
                $subject = 'Your Runlevel Systems proposal was accepted';
                if ($requestId !== '') {
                    $subject .= ' - ' . $requestId;
                }
                $text = "Hello {$recipientName},\n\n"
                    . "Thanks for accepting your proposal. We will follow up with next steps and payment details.\n\n"
                    . ($requestId !== '' ? "Request ID: {$requestId}\n" : '')
                    . ($proposalId !== '' ? "Proposal ID: {$proposalId}\n" : '')
                    . "\nYou can review your project here:\n{$projectUrl}\n\n"
                    . "{$company}";
            }
            break;

        case 'payment-recorded':
            if ($audience === 'staff') {
                $subject = 'Payment recorded';
                if ($requestId !== '') {
                    $subject .= ' - ' . $requestId;
                }
                $text = "A payment was recorded in the portal.\n\n"
                    . ($requestId !== '' ? "Request ID: {$requestId}\n" : '')
                    . ($proposalId !== '' ? "Proposal ID: {$proposalId}\n" : '')
                    . ($paymentId !== '' ? "Payment ID: {$paymentId}\n" : '')
                    . ($amount !== '' ? "Amount: {$amount}\n" : '')
                    . ($status !== '' ? "Status: {$status}\n" : '')
                    . "\nOpen the project here:\n{$projectUrl}\n";
            } else {
                $subject = 'Payment received by Runlevel Systems';
                if ($requestId !== '') {
                    $subject .= ' - ' . $requestId;
                }
                $text = "Hello {$recipientName},\n\n"
                    . "We recorded your payment.\n\n"
                    . ($requestId !== '' ? "Request ID: {$requestId}\n" : '')
                    . ($paymentId !== '' ? "Payment ID: {$paymentId}\n" : '')
                    . ($amount !== '' ? "Amount: {$amount}\n" : '')
                    . ($status !== '' ? "Status: {$status}\n" : '')
                    . "\nYou can review project updates here:\n{$projectUrl}\n\n"
                    . "{$company}";
            }
            break;

        case 'test-email':
            $subject = 'Runlevel Systems SMTP Test';
            $text = "This is a test email from the {$company} website using the configured SMTP settings.";
            break;

        case 'account-created':
            $subject = 'Your Runlevel Systems account is ready';
            $text = "Hello {$recipientName},\n\n"
                . "Your {$company} account has been created.\n\n"
                . ($username !== '' ? "Username: {$username}\n" : '')
                . "You can sign in here:\n{$dashboardUrl}\n\n"
                . "{$company}";
            break;

        case 'verify-email':
            $subject = 'Verify your Runlevel Systems account';
            $text = "Hello {$recipientName},\n\n"
                . "Thanks for creating a Runlevel Systems account.\n\n"
                . "Please verify your email address using the link below so we can contact you about project requests, proposals, files, and updates.\n\n"
                . ($verifyUrl !== '' ? "{$verifyUrl}\n\n" : '')
                . "If you did not create this account, you can ignore this email.\n\n"
                . "{$company}";
            break;

        case 'project-agreement':
            $subject = 'Your Runlevel Systems project agreement is ready';
            if ($requestId !== '') {
                $subject .= ' - ' . $requestId;
            }
            $text = "Hello {$recipientName},\n\n"
                . "Your project agreement is ready for review.\n\n"
                . ($requestId !== '' ? "Request ID: {$requestId}\n" : '')
                . "\nView it here:\n{$projectUrl}\n\n"
                . "{$company}";
            break;

        default:
            $subject = trim((string)($data['subject'] ?? 'Runlevel Systems Notification'));
            $text = trim((string)($data['body'] ?? ''));
            break;
    }

    return [
        'subject' => $subject,
        'text' => $text,
        'html' => rlsEmailTextToHtml($text),
    ];
}
