<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';
portalRequireStaff();
$current_page = 'staff-portal';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../assets/images/RL-icon.png">
    <title>Proposal Template | Staff Portal | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .portal-wrap { padding: 40px 0 80px; }
        .portal-back { color: #36f3ff; font-size: 0.9rem; margin-bottom: 18px; display: inline-block; }
        .portal-back:hover { color: #ffc600; }
        .template-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.18); border-radius: 10px; padding: 32px; margin-bottom: 24px; }
        .template-field { margin-bottom: 20px; }
        .template-field label { color: #36f3ff; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 6px; display: block; }
        .template-field .field-value { background: #09111d; border: 1px dashed rgba(54,243,255,0.25); border-radius: 6px; padding: 12px; color: #a8bedc; font-style: italic; min-height: 40px; }
        .template-section { border-top: 1px solid rgba(54,243,255,0.1); padding-top: 20px; margin-top: 20px; }
        .template-section h3 { color: #ffc600; font-size: 1rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 14px; }
        .print-btn { background: #ffc600; color: #08111f; border: none; border-radius: 6px; padding: 10px 22px; font-weight: 700; cursor: pointer; margin-bottom: 20px; }
        .print-btn:hover { background: #36f3ff; }
        @media print {
            .portal-back, .print-btn, #header, #footer-widget, footer { display: none !important; }
            .template-card { border: 1px solid #ccc; }
            body { background: #fff; color: #000; }
            .template-field label { color: #333; }
            .template-field .field-value { color: #555; border-color: #ccc; background: #f9f9f9; }
            .template-section h3 { color: #333; }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<section class="portal-wrap">
    <div class="container">
        <a href="/staff/dashboard.php" class="portal-back">← Back to Dashboard</a>
        <button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>
        <h1 style="color:#ffc600; margin-bottom: 8px;">✍️ Proposal Template</h1>
        <p style="color:#7a9ac0; margin-bottom: 24px;">Use this template as a starting point. Fill in each field with the project-specific information.</p>

        <div class="template-card">
            <div style="text-align:center; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 1px solid rgba(54,243,255,0.15);">
                <p style="color:#7a9ac0; font-size:0.85rem; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.08em;">Runlevel Systems</p>
                <h2 style="color:#ffc600; margin: 0 0 6px;">Project Proposal</h2>
                <p style="color:#5a7a9e; font-size:0.9rem; margin:0;">Date: ___________________________</p>
            </div>

            <div class="template-field">
                <label>Project Name</label>
                <div class="field-value">[Enter project name]</div>
            </div>
            <div class="row" style="gap:16px;">
                <div class="template-field" style="flex:1;">
                    <label>Client Name</label>
                    <div class="field-value">[Client full name or organization]</div>
                </div>
                <div class="template-field" style="flex:1;">
                    <label>Client Email / Contact</label>
                    <div class="field-value">[Email or preferred contact]</div>
                </div>
            </div>

            <div class="template-section">
                <h3>Request Summary</h3>
                <div class="template-field">
                    <label>What the client asked for</label>
                    <div class="field-value" style="min-height: 60px;">[Summarize the client's original request in plain language]</div>
                </div>
            </div>

            <div class="template-section">
                <h3>Proposed Work</h3>
                <div class="template-field">
                    <label>What Runlevel Systems will do</label>
                    <div class="field-value" style="min-height: 80px;">[Describe exactly what will be built, fixed, or delivered]</div>
                </div>
                <div class="template-field">
                    <label>Deliverables</label>
                    <div class="field-value" style="min-height: 60px;">[List specific deliverables, e.g., working script, website pages, Unity scene, etc.]</div>
                </div>
            </div>

            <div class="template-section">
                <h3>Pricing &amp; Payment</h3>
                <div class="row" style="gap:16px;">
                    <div class="template-field" style="flex:1;">
                        <label>Estimated Total Cost</label>
                        <div class="field-value">$[Amount] USD</div>
                    </div>
                    <div class="template-field" style="flex:1;">
                        <label>Deposit Required to Begin</label>
                        <div class="field-value">$[Amount] USD &nbsp;(or N/A for small jobs)</div>
                    </div>
                    <div class="template-field" style="flex:1;">
                        <label>Payment Method</label>
                        <div class="field-value">PayPal (invoice or payment link)</div>
                    </div>
                </div>
                <div class="template-field">
                    <label>Payment Terms</label>
                    <div class="field-value">[e.g., Full payment before work begins / 50% deposit + 50% on delivery / milestone payments]</div>
                </div>
            </div>

            <div class="template-section">
                <h3>Timeline &amp; Revisions</h3>
                <div class="row" style="gap:16px;">
                    <div class="template-field" style="flex:1;">
                        <label>Estimated Completion Time</label>
                        <div class="field-value">[e.g., 2–5 business days / 1–2 weeks]</div>
                    </div>
                    <div class="template-field" style="flex:1;">
                        <label>Revision Terms</label>
                        <div class="field-value">[e.g., Up to 2 revision rounds included. Additional changes quoted separately.]</div>
                    </div>
                </div>
            </div>

            <div class="template-section">
                <h3>Assumptions &amp; Limitations</h3>
                <div class="template-field">
                    <div class="field-value" style="min-height: 60px;">[List any assumptions, known limitations, out-of-scope items, or dependencies]</div>
                </div>
            </div>

            <div class="template-section">
                <h3>Approval</h3>
                <p style="color:#a8bedc; font-size:0.9rem; margin-bottom:16px;">
                    By approving this proposal (in writing, by email, or by making the deposit payment), the client agrees to the terms described above
                    and the <a href="/runlevel-terms.php" target="_blank" rel="noopener noreferrer">Runlevel Systems Terms of Service</a>.
                </p>
                <div class="row" style="gap:24px;">
                    <div style="flex:1;">
                        <p style="color:#5a7a9e; font-size:0.85rem;">Client signature / written approval:</p>
                        <div style="border-bottom: 1px solid rgba(54,243,255,0.25); height: 32px; margin-bottom:6px;"></div>
                        <p style="color:#5a7a9e; font-size:0.8rem;">Name &amp; date</p>
                    </div>
                    <div style="flex:1;">
                        <p style="color:#5a7a9e; font-size:0.85rem;">Runlevel Systems representative:</p>
                        <div style="border-bottom: 1px solid rgba(54,243,255,0.25); height: 32px; margin-bottom:6px;"></div>
                        <p style="color:#5a7a9e; font-size:0.8rem;">Name &amp; date</p>
                    </div>
                </div>
            </div>
        </div>

        <div style="background: rgba(255,198,0,0.06); border: 1px solid rgba(255,198,0,0.2); border-radius: 8px; padding: 14px 18px;">
            <p style="margin:0; font-size:0.875rem; color:#a8a060;">
                <strong>Staff note:</strong> This is a working template for internal use. Customise each proposal for the specific project before sending to the client.
                Future versions will include editable fields and PDF generation.
            </p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
