<?php
/**
 * Payment Configuration Placeholder
 * TODO: Move PayPal credentials and mode to server environment variables or a private config file
 *       that is NOT stored in version control before going live.
 */

// TODO: Replace with your actual PayPal business email before enabling payments.
$paypal_business_email = "payments@runlevelsystems.com";

// Payment mode: manual = send invoice/link manually; invoice = PayPal invoice tool; checkout_api = full API
$paypal_mode = "manual"; // manual, invoice, checkout_api

// PayPal.Me link (set once your PayPal.Me link is configured)
$paypal_me_link = "";

// PayPal invoicing note shown to customers
$paypal_invoice_note = "Thank you for choosing Runlevel Systems. Please review the proposal before payment.";
