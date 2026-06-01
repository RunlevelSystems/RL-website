<?php
/**
 * PayPal Webhook Placeholder — Runlevel Systems
 *
 * This endpoint receives PayPal webhook payloads and logs them for review.
 * Live payment processing is NOT implemented yet.
 *
 * TODO: Verify PayPal webhook signature before trusting payment events.
 * TODO: Match PayPal invoice/transaction ID to proposal_id.
 * TODO: Update payments.json automatically after verification.
 */

define('WDS_SYSTEM', true);
require_once __DIR__ . '/includes/portal-helpers.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$settings = portalLoadAdminSettings();
$loggingEnabled = (bool)($settings['paypal']['enable_webhook_logging'] ?? false);

// Read raw POST body
$rawBody = file_get_contents('php://input');
$payload = [];
if ($rawBody !== false && trim($rawBody) !== '') {
    $decoded = json_decode($rawBody, true);
    $payload = is_array($decoded) ? $decoded : ['raw' => $rawBody];
}

$logEntry = [
    'received_at'        => date('c'),
    'remote_addr'        => $_SERVER['REMOTE_ADDR'] ?? '',
    'content_type'       => $_SERVER['CONTENT_TYPE'] ?? '',
    'paypal_event_type'  => $payload['event_type'] ?? '',
    'paypal_event_id'    => $payload['id'] ?? '',
    'payload_preview'    => array_slice($payload, 0, 20),
    'note'               => 'Webhook received but not processed. Manual review required.',
];

if ($loggingEnabled) {
    // Append to webhook log
    $logFile = PORTAL_PAYPAL_WEBHOOK_LOG;
    $log = [];
    if (file_exists($logFile)) {
        $existing = @json_decode(file_get_contents($logFile), true);
        $log = (isset($existing['events']) && is_array($existing['events'])) ? $existing['events'] : [];
    }
    // Keep last 500 entries to prevent unbounded growth
    $log[] = $logEntry;
    if (count($log) > 500) {
        $log = array_slice($log, -500);
    }
    portalSaveJson($logFile, ['events' => array_values($log)]);
}

// TODO: Verify PayPal webhook signature before trusting payment events.
// TODO: Match PayPal invoice/transaction ID to proposal_id.
// TODO: Update payments.json automatically after verification.

http_response_code(200);
header('Content-Type: application/json');
echo json_encode(['status' => 'received', 'note' => 'Webhook logging only. Not processed.']);
