<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Robot | Contact</title>

        <!-- CSS -->

        <!-- google fonts -->
        <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>

        <!-- files -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/css/magnific-popup.css" rel="stylesheet">
        <link href="assets/css/owl.carousel.css" rel="stylesheet">
        <link href="assets/css/owl.carousel.theme.min.css" rel="stylesheet">
        <link href="assets/css/ionicons.css" rel="stylesheet">
        <link href="<!-- main.css removed -->" rel="stylesheet">
        <link href="assets/css/wds-unified.css" rel="stylesheet">

        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <?php 
        // Page-specific variables
        $current_page = 'contact';
        $header_class = 'contact-header inner-header';
        $page_subtitle = 'Design. Debug. Deploy.';
        
        // Handle form submission
        $message_sent = false;
        $error_message = '';
        
        if ($_POST) {
            $fname = $_POST['fname'] ?? '';
            $lname = $_POST['lname'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $message = $_POST['message'] ?? '';
            
            if (!empty($fname) && !empty($email) && !empty($message)) {
                // Here you would normally send the email
                // For now, we'll just show a success message
                $message_sent = true;
            } else {
                $error_message = 'Please fill in all required fields (First Name, Email, and Message).';
            }
        }
        ?>
    <!-- Include Site Header -->
    <?php include 'includes/header.php'; ?>
        
    <!-- Include Navigation Header -->
    <?php include 'includes/navigation.php'; ?>

    <!-- Contact -->
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
                            <p class="inner-p">
                                We'd love to hear from you! Whether you have questions about our projects, want to collaborate, 
                                or need technical support, don't hesitate to reach out. Join our Discord community for real-time chat 
                                or send us a message using the form below.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="boxed">
                        <!-- Email Form Section - Left Side -->
                        <div class="col-sm-6">
                            <div style="background: rgba(212, 207, 192, 0.8); padding: 40px; border-radius: 12px; border: 1px solid rgba(139, 69, 19, 0.3);">
                                <h3 style="color: #8B4513; margin-bottom: 20px; font-size: 28px;">Get in Touch</h3>
                                <p style="color: #1a1a1a; margin-bottom: 30px; font-size: 16px; line-height: 1.6;">
                                    Have a question or want to collaborate? Fill out the form below and we'll get back to you as soon as possible.
                                </p>
                                <p style="color: #1a1a1a; margin-bottom: 30px; font-size: 14px; line-height: 1.6;">
                                    <em>Note: This form submits to our Discord server. You can also join our server directly using the link on the right.</em>
                                </p>
                                
                                <?php if ($message_sent): ?>
                                    <div style="background-color: rgba(139, 69, 19, 0.2); color: #1a1a1a; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center; border: 2px solid #8B4513;">
                                        <strong>Thank you!</strong> Your message has been sent successfully. We'll get back to you soon!
                                    </div>
                                <?php elseif ($error_message): ?>
                                    <div style="background-color: rgba(248, 113, 113, 0.2); color: #1a1a1a; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 2px solid #f87171;">
                                        <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form action="contact.php" class="contact-form" id="contactForm" method="post">
                                    <div style="margin-bottom: 25px;">
                                        <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold; font-size: 16px;">Name *</label>
                                        <input type="text" name="fname" required 
                                               style="width: 100%; padding: 14px; border-radius: 8px; background: #FFFFFF; color: #1a1a1a; font-size: 16px; border: 1px solid rgba(139, 69, 19, 0.3);"
                                               placeholder="Your name">
                                    </div>
                                    
                                    <div style="margin-bottom: 25px;">
                                        <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold; font-size: 16px;">Discord UserID <span style="color: #666; font-weight: normal;">(optional)</span></label>
                                        <input type="text" name="discord_id" 
                                               style="width: 100%; padding: 14px; border-radius: 8px; background: #FFFFFF; color: #1a1a1a; font-size: 16px; border: 1px solid rgba(139, 69, 19, 0.3);"
                                               placeholder="YourDiscordName#1234">
                                    </div>
                                    
                                    <div style="margin-bottom: 25px;">
                                        <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold; font-size: 16px;">Email *</label>
                                        <input type="email" name="email" required 
                                               style="width: 100%; padding: 14px; border-radius: 8px; background: #FFFFFF; color: #1a1a1a; font-size: 16px; border: 1px solid rgba(139, 69, 19, 0.3);"
                                               placeholder="your@email.com">
                                    </div>
                                    
                                    <div style="margin-bottom: 30px;">
                                        <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold; font-size: 16px;">Question / Comment *</label>
                                        <textarea name="message" rows="6" required 
                                                  style="width: 100%; padding: 14px; border-radius: 8px; background: #FFFFFF; color: #1a1a1a; font-size: 16px; resize: vertical; border: 1px solid rgba(139, 69, 19, 0.3);"
                                                  placeholder="Tell us about your project, question, or how we can help..."></textarea>
                                    </div>
                                    
                                    <div style="text-align: center;">
                                        <button type="submit" class="contact-submit-btn"
                                                style="background: transparent; color: #8B4513; border: 2px solid #8B4513; padding: 15px 40px; font-size: 18px; font-weight: bold; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                            Send Message
                                        </button>
                                    </div>
                                </form>
                                <div id="contactFormResponse"></div>
                            </div>
                        </div> <!-- /.col-sm-6 -->
                        
                        <!-- Discord Section - Right Side -->
                        <div class="col-sm-6">
                            <div style="background: rgba(212, 207, 192, 0.8); padding: 40px; border-radius: 12px; border: 1px solid rgba(139, 69, 19, 0.3);">
                                <h3 style="color: #8B4513; margin-bottom: 20px; font-size: 28px;">Join Our Community</h3>
                                <p style="color: #1a1a1a; margin-bottom: 30px; font-size: 16px; line-height: 1.6;">
                                    <i class="ion-chatbubbles" style="margin-right: 8px; color: #8B4513;"></i>
                                    Chat with us directly on Discord! Ask questions, share ideas, and collaborate in real-time.
                                </p>
                                
                                <!-- Discord Widget -->
                                <div style="background: rgba(139, 69, 19, 0.15); border: 2px solid #8B4513; padding: 30px; border-radius: 10px; margin: 20px 0; text-align: center;">
                                    <div style="margin-bottom: 20px;">
                                        <i class="ion-social-discord" style="font-size: 48px; color: #8B4513;"></i>
                                    </div>
                                    <h4 style="margin-bottom: 15px; color: #8B4513; font-size: 22px;">
                                        World Domination Dev
                                    </h4>
                                    <p style="margin-bottom: 25px; color: #1a1a1a; font-size: 15px; line-height: 1.6;">
                                        Join our Discord community for real-time discussions, project updates, and collaboration opportunities!
                                    </p>
                                    <a href="https://discord.gg/XPFnNdWGyW" target="_blank" class="discord-join-btn"
                                       style="background: transparent; color: #8B4513; border: 2px solid #8B4513; padding: 14px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block; transition: all 0.3s ease; font-size: 16px;">
                                        Join Discord Server
                                    </a>
                                </div>
                                
                                <p style="margin-top: 20px; font-size: 14px; color: #1a1a1a; text-align: center; line-height: 1.6;">
                                    New to Discord? Click the button above to get started! Discord is free and works on desktop and mobile.
                                </p>
                            </div>
                        </div> <!-- /.col-sm-6 -->
                    </div>
                </div>
            </div>
        </section>

    <!-- Include Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Custom Styles for Contact Page -->
    <style>
        .contact-submit-btn:hover,
        .discord-join-btn:hover {
            background: #8B4513 !important;
            color: #E8E4D8 !important;
            text-decoration: none !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 69, 19, 0.4);
        }
    </style>

    <!-- Scripts -->
        <script src="assets/js/jquery-1.12.3.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.magnific-popup.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
        <script src="assets/js/script.js"></script>
        
        <!-- Widgetbot HTML Embed Script -->
        <script src="https://cdn.jsdelivr.net/npm/@widgetbot/html-embed"></script>

    </body>
</html>

