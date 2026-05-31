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
    <title>Contract Template | Staff Portal | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .portal-wrap { padding: 40px 0 80px; }
        .portal-back { color: #36f3ff; font-size: 0.9rem; margin-bottom: 18px; display: inline-block; }
        .portal-back:hover { color: #ffc600; }
        .contract-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.18); border-radius: 10px; padding: 36px; margin-bottom: 24px; line-height: 1.8; }
        .contract-card h2 { color: #ffc600; }
        .contract-section { border-top: 1px solid rgba(54,243,255,0.1); padding-top: 20px; margin-top: 20px; }
        .contract-section h3 { color: #36f3ff; font-size: 1rem; margin-bottom: 10px; }
        .contract-field { background: #09111d; border: 1px dashed rgba(54,243,255,0.2); border-radius: 5px; padding: 8px 12px; color: #a8bedc; font-style: italic; display: inline-block; min-width: 180px; }
        .print-btn { background: #ffc600; color: #08111f; border: none; border-radius: 6px; padding: 10px 22px; font-weight: 700; cursor: pointer; margin-bottom: 20px; }
        .print-btn:hover { background: #36f3ff; }
        .notice-legal { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 8px; padding: 14px 18px; margin-bottom: 24px; color: #fca5a5; font-size: 0.875rem; }
        .sig-line { border-bottom: 1px solid rgba(54,243,255,0.25); height: 32px; margin-bottom: 6px; }
        @media print {
            .portal-back, .print-btn, .notice-legal, #header, #footer-widget, footer { display: none !important; }
            body { background: #fff; color: #000; }
            h2, h3, h4 { color: #000 !important; }
            .contract-card { border: 1px solid #ccc; background: #fff; }
            .contract-section h3 { color: #333 !important; }
            .contract-field { background: #f4f4f4; border-color: #ccc; color: #555; }
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
        <h1 style="color:#ffc600; margin-bottom: 8px;">📋 Project Agreement Template</h1>

        <div class="notice-legal">
            ⚖️ <strong>Legal Notice:</strong> This template is provided for internal business planning purposes only.
            It should be reviewed by a qualified attorney before being used in any final public or binding agreement.
        </div>

        <div class="contract-card">
            <div style="text-align:center; margin-bottom:28px; padding-bottom:20px; border-bottom:1px solid rgba(54,243,255,0.15);">
                <p style="color:#7a9ac0; text-transform:uppercase; letter-spacing:0.08em; font-size:0.85rem; margin-bottom:4px;">Runlevel Systems</p>
                <h2 style="margin:0 0 6px;">Project Agreement</h2>
                <p style="color:#5a7a9e; font-size:0.9rem; margin:0;">Effective Date: <span class="contract-field">[Date]</span></p>
            </div>

            <div class="contract-section">
                <h3>1. Parties</h3>
                <p>This agreement is between <strong>Runlevel Systems</strong> ("Developer") and
                <span class="contract-field">[Client full name or organization]</span> ("Client").</p>
            </div>

            <div class="contract-section">
                <h3>2. Project Description</h3>
                <p><span class="contract-field" style="min-width:100%; display:block; min-height:60px;">[Describe the project in plain language]</span></p>
            </div>

            <div class="contract-section">
                <h3>3. Scope of Work</h3>
                <p>The Developer will provide the following services:</p>
                <p><span class="contract-field" style="min-width:100%; display:block; min-height:60px;">[List specific tasks, features, or deliverables]</span></p>
                <p>Work not listed above is considered out of scope and may be quoted separately.</p>
            </div>

            <div class="contract-section">
                <h3>4. Deliverables</h3>
                <p><span class="contract-field" style="min-width:100%; display:block; min-height:60px;">[List specific deliverables: files, code, website, app, etc.]</span></p>
            </div>

            <div class="contract-section">
                <h3>5. Payment Terms</h3>
                <p>Total project cost: <span class="contract-field">$[Amount] USD</span></p>
                <p>Payment schedule: <span class="contract-field" style="min-width:300px;">[e.g., 50% deposit to begin / 50% on delivery]</span></p>
                <p>Payment method: PayPal invoice or approved payment link.</p>
                <p>Work begins after required deposit or payment is received.</p>
            </div>

            <div class="contract-section">
                <h3>6. Deposits and Milestones</h3>
                <p>The following deposit or milestone payments apply:</p>
                <p><span class="contract-field" style="min-width:100%; display:block; min-height:60px;">[List milestone payments if applicable, or write "Full payment before work begins" for small jobs]</span></p>
            </div>

            <div class="contract-section">
                <h3>7. Customer Responsibilities</h3>
                <p>The Client agrees to:</p>
                <ul style="color:#c7d7e8; padding-left:1.5rem; margin-bottom:0;">
                    <li>Provide accurate project requirements in a timely manner.</li>
                    <li>Respond to questions or requests for review within a reasonable time.</li>
                    <li>Provide access to any existing systems, accounts, or assets required for the project.</li>
                    <li>Review deliverables and communicate feedback during the revision period.</li>
                </ul>
            </div>

            <div class="contract-section">
                <h3>8. Third-Party Licenses</h3>
                <p>The Client is responsible for obtaining any required licenses, assets, fonts, stock media, or third-party software required for the project.
                Runlevel Systems will not include unlicensed third-party content in deliverables.</p>
            </div>

            <div class="contract-section">
                <h3>9. Source Code and Ownership</h3>
                <p>Upon final payment, the Client receives ownership of the custom code written specifically for this project,
                unless otherwise agreed in writing. Standard libraries, frameworks, and third-party components remain under their
                respective licenses.</p>
            </div>

            <div class="contract-section">
                <h3>10. Managed Development Option</h3>
                <p>For ongoing support or managed development arrangements, the Developer may retain access to the project
                for maintenance purposes. Terms of managed access are agreed separately.</p>
            </div>

            <div class="contract-section">
                <h3>11. Full Source Transfer Option</h3>
                <p>If the Client requests full source transfer and access, this must be agreed upon in writing before work begins.
                Full source transfer may affect pricing.</p>
            </div>

            <div class="contract-section">
                <h3>12. Change Requests</h3>
                <p>Changes to the scope of work after the project has started will be evaluated and quoted separately.
                The Client will be informed of any cost or timeline impact before changes are made.</p>
            </div>

            <div class="contract-section">
                <h3>13. Revisions</h3>
                <p>The project includes <span class="contract-field">[number]</span> rounds of revisions.
                Additional revision rounds will be billed at the Developer's standard rate.
                Revisions are limited to the agreed scope; new features are treated as change requests.</p>
            </div>

            <div class="contract-section">
                <h3>14. Testing and Acceptance</h3>
                <p>The Client will review and test deliverables within <span class="contract-field">[e.g., 7 business days]</span>
                of delivery. If no feedback is received within this period, the deliverable is considered accepted.
                Acceptance does not waive the right to report bugs related to the original scope.</p>
            </div>

            <div class="contract-section">
                <h3>15. Refunds and Disputes</h3>
                <p>Refunds, revisions, and disputes are handled according to the
                <a href="/runlevel-terms.php" target="_blank" rel="noopener noreferrer" style="color:#36f3ff;">Runlevel Systems Terms of Service</a>
                and the terms of this agreement. Deposit payments are non-refundable once work has begun,
                unless the Developer fails to deliver the agreed scope.</p>
            </div>

            <div class="contract-section">
                <h3>16. Confidentiality</h3>
                <p>Both parties agree to keep project-specific information, credentials, and business details confidential
                and not share them with third parties without written consent.</p>
            </div>

            <div class="contract-section">
                <h3>17. Limitation of Liability</h3>
                <p>Runlevel Systems is not liable for damages exceeding the total amount paid under this agreement.
                The Developer is not responsible for issues caused by third-party services, hosting providers,
                or changes made by the Client after delivery.</p>
            </div>

            <div class="contract-section">
                <h3>18. Termination</h3>
                <p>Either party may terminate this agreement with written notice. If the Client terminates after work has begun,
                payment for work completed to that point is due. If the Developer terminates for reasons other than non-payment,
                a pro-rated refund of unused deposit funds will be issued.</p>
            </div>

            <div class="contract-section">
                <h3>19. Governing Law</h3>
                <p>This agreement is governed by the laws of <span class="contract-field">[State / Province / Country — to be determined]</span>.
                Any disputes will be resolved through good-faith negotiation before formal proceedings.</p>
            </div>

            <div class="contract-section">
                <h3>20. Signatures</h3>
                <p style="color:#a8bedc; font-size:0.9rem; margin-bottom:20px;">
                    By signing below, both parties agree to the terms of this project agreement.
                </p>
                <div class="row" style="gap:40px;">
                    <div style="flex:1;">
                        <p style="color:#5a7a9e; font-size:0.85rem; margin-bottom:4px;">Client signature:</p>
                        <div class="sig-line"></div>
                        <p style="color:#5a7a9e; font-size:0.8rem; margin-bottom:16px;">Printed name &amp; date</p>
                    </div>
                    <div style="flex:1;">
                        <p style="color:#5a7a9e; font-size:0.85rem; margin-bottom:4px;">Runlevel Systems representative:</p>
                        <div class="sig-line"></div>
                        <p style="color:#5a7a9e; font-size:0.8rem; margin-bottom:16px;">Printed name &amp; date</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
