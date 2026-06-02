<?php
$current_page = 'contracts';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="assets/images/RL-icon.png">
    <title>Project Agreements | Runlevel Systems</title>
    <meta name="description" content="How Runlevel Systems handles project agreements.">
    <link href="assets/css/coreloop.css?v=clean-20260602" rel="stylesheet">
    <style>
        .wrap{padding:54px 0 70px;}
        .box{max-width:860px;margin:0 auto;background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:24px;}
        h1{color:#ffc600;margin:0 0 10px;}h3{color:#36f3ff;margin:16px 0 8px;}p{color:#a8bedc;line-height:1.7;}
        .row{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;}
        .btn{display:inline-block;padding:10px 16px;border-radius:6px;text-decoration:none;font-weight:700;font-size:.9rem;}
        .btn-gold{background:#ffc600;color:#08111f;}.btn-teal{border:1px solid rgba(54,243,255,.3);color:#36f3ff;background:rgba(54,243,255,.08);}
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>
<section class="wrap"><div class="container"><div class="box">
<h1>Project Agreements</h1>
<p>Small jobs may only need an accepted proposal and the Terms of Service.</p>
<p>Larger or more detailed work may use a written Project Agreement.</p>
<p>This agreement helps both sides understand scope, deliverables, cost, timeline, ownership, and responsibilities.</p>
<h3>Why this exists</h3>
<p>We keep the public explanation short and clear. Detailed legal text is handled in staff workflow documents when needed for a specific project.</p>
<div class="row">
    <a href="/estimate.php" class="btn btn-gold">Start A Project Request</a>
    <a href="/proposals.php" class="btn btn-teal">How Proposals Work</a>
</div>
</div></div></section>
<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
