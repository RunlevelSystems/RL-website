<?php
$current_page = 'payments';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="assets/images/RL-icon.png">
    <title>Payments | Runlevel Systems</title>
    <meta name="description" content="Runlevel Systems payment process overview.">
    <link href="assets/css/coreloop.css" rel="stylesheet">
    <style>
        .wrap{padding:54px 0 70px;text-align:center;}
        .box{max-width:760px;margin:0 auto;background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:24px;}
        h1{color:#ffc600;margin:0 0 10px;}p{color:#a8bedc;line-height:1.7;margin-bottom:10px;}
        .btn{display:inline-block;margin-top:10px;background:#ffc600;color:#08111f;font-weight:700;padding:11px 22px;border-radius:6px;text-decoration:none;}
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>
<section class="wrap"><div class="container"><div class="box">
<h1>Payments</h1>
<p>Payments are normally requested only after we review your project request and confirm the next step.</p>
<p>For small jobs, we may request payment before work begins.</p>
<p>For larger projects, we may use deposits, milestones, or written project agreements.</p>
<p>Payments are handled through PayPal invoices, PayPal payment links, or another approved method.</p>
<a href="/estimate.php" class="btn">Start A Project Request</a>
</div></div></section>
<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
