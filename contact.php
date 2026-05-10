<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/icon.png">

        <title>Core Loop Development | Contact</title>

        <link href="assets/css/coreloop.css" rel="stylesheet">
    </head>
    <body>
        <?php
        $current_page = 'contact';
        $header_class = 'contact-header inner-header';
        $page_subtitle = 'Engineer • Ship • Scale';

        $message_sent = false;
        $error_message = '';

        if ($_POST) {
            $fname = $_POST['fname'] ?? '';
            $email = $_POST['email'] ?? '';
            $message = $_POST['message'] ?? '';

            if (!empty($fname) && !empty($email) && !empty($message)) {
                $message_sent = true;
            } else {
                $error_message = 'Please fill in all required fields (Name, Email, and Message).';
            }
        }
        ?>

        <?php include 'includes/header.php'; ?>
        <?php include 'includes/navigation.php'; ?>

        <section class="contact">
            <div class="container page-bgc">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="title-box">
                            <h2 class="title mt0">Get in touch with us</h2>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="boxed">
                        <div class="col-sm-12">
                            <p class="inner-p">We'd love to hear from you. Questions, collaboration requests, and support inquiries are all welcome.</p>
                            <p class="inner-p">This form submits to our Discord server. You can also join directly using the invite on this page.</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="boxed">
                        <div class="col-sm-6">
                            <div class="wds-card">
                                <h3>Get in Touch</h3>

                                <?php if ($message_sent): ?>
                                    <div class="info-box">
                                        <strong>Thank you.</strong> Your message has been sent successfully.
                                    </div>
                                <?php elseif ($error_message): ?>
                                    <div class="info-box">
                                        <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
                                    </div>
                                <?php endif; ?>

                                <form action="contact.php" class="contact-form" id="contactForm" method="post">
                                    <div>
                                        <label for="contact-name">Name *</label>
                                        <input id="contact-name" type="text" name="fname" required placeholder="Your name">
                                    </div>
                                    <div>
                                        <label for="contact-discord">Discord UserID (optional)</label>
                                        <input id="contact-discord" type="text" name="discord_id" placeholder="YourDiscordName#1234">
                                    </div>
                                    <div>
                                        <label for="contact-email">Email *</label>
                                        <input id="contact-email" type="email" name="email" required placeholder="your@email.com">
                                    </div>
                                    <div>
                                        <label for="contact-message">Question / Comment *</label>
                                        <textarea id="contact-message" name="message" rows="6" required placeholder="Tell us about your project, question, or how we can help..."></textarea>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn">Send Message</button>
                                    </div>
                                </form>
                                <div id="contactFormResponse"></div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="wds-card">
                                <h3>Join Our Community</h3>
                                <p>Chat with us directly on Discord. Ask questions, share ideas, and collaborate in real time.</p>
                                <div class="wds-card text-center">
                                    <i class="ion-social-discord" aria-hidden="true"></i>
                                    <h4>Core Loop Development</h4>
                                    <p>Join our Discord community for project updates and collaboration opportunities.</p>
                                    <a href="https://discord.gg/XPFnNdWGyW" target="_blank" class="btn" rel="noopener">Join Discord Server</a>
                                </div>
                                <p>New to Discord? The invite works on desktop and mobile.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php include 'includes/footer.php'; ?>

        <script src="assets/js/jquery-1.12.3.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.magnific-popup.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
        <script src="assets/js/script.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@widgetbot/html-embed"></script>
    </body>
</html>
