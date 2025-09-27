<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-U                                                       <div style="margin-bottom: 25px;">
                                        <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold; font-size: 18px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">Email</label>
                                        <input type="email" name="email" required 
                                               style="width: 100%; padding: 14px; border-radius: 8px; background: rgba(255,255,255,0.18); color: #D2B48C; font-size: 17px; font-weight: 500;"
                                               placeholder="your@email.com">
                                    </div>           <div style="margin-bottom: 25px;">
                                        <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold; font-size: 18px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">Name</label>
                                        <input type="text" name="name" required 
                                               style="width: 100%; padding: 14px; border-radius: 8px; background: rgba(255,255,255,0.18); color: #D2B48C; font-size: 17px; font-weight: 500;"
                                               placeholder="Your full name">
                                    </div>atible" content="IE=edge">
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
        <link href="assets/css/main.css" rel="stylesheet">
        <link href="assets/css/readability-improvements.css" rel="stylesheet">

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
        $show_breadcrumb = true;
        $page_breadcrumb = 'Contact';
        
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
    <?php include 'includes/header.html'; ?>
        
    <!-- Include Navigation Header -->
    <?php include 'includes/navigation.php'; ?>

    <!-- Contact -->
        <section class="contact">
            <div class="container page-bgc">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="title-box">
                            <p>Get in touch</p>
                            <h2 class="title mt0">With us</h2>
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
                                                        <div style="background: rgba(255,255,255,0.15); padding: 40px; border-radius: 12px;">
                                <h3 style="color: #8B4513; margin-bottom: 20px; font-size: 28px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">Get in Touch</h3>
                                <p style="color: #D2B48C; margin-bottom: 30px; font-size: 18px; font-weight: 500; line-height: 1.6;">Have a question or want to collaborate? Fill out the form below and we'll get back to you as soon as possible.</p>
                                
                                <?php if ($message_sent): ?>
                                    <div style="background-color: #8B4513; color: #0f1419; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                                        <strong>Thank you!</strong> Your message has been sent successfully. We'll get back to you soon!
                                    </div>
                                <?php elseif ($error_message): ?>
                                    <div style="background-color: #f87171; color: #D2B48C; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                                        <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form action="contact.php" class="contact-form" id="contactForm" method="post">
                                    <div style="margin-bottom: 25px;">
                                        <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold; font-size: 18px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">First Name *</label>
                                        <input type="text" name="fname" required 
                                               style="width: 100%; padding: 14px; border-radius: 8px; background: rgba(255,255,255,0.18); color: #D2B48C; font-size: 17px; font-weight: 500;"
                                               placeholder="Your first name">
                                    </div>
                                    
                                    <div style="margin-bottom: 25px;">
                                        <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold;">Last Name</label>
                                        <input type="text" name="lname" 
                                               style="width: 100%; padding: 12px; border-radius: 6px; background: rgba(255,255,255,0.1); color: #D2B48C; font-size: 16px;"
                                               placeholder="Your last name">
                                    </div>
                                    
                                    <div style="margin-bottom: 25px;">
                                        <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold;">Email *</label>
                                        <input type="email" name="email" required 
                                               style="width: 100%; padding: 12px; border-radius: 6px; background: rgba(255,255,255,0.1); color: #D2B48C; font-size: 16px;"
                                               placeholder="your@email.com">
                                    </div>
                                    
                                    <div style="margin-bottom: 25px;">
                                        <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold;">Phone Number</label>
                                        <input type="text" name="phone" 
                                               style="width: 100%; padding: 12px; border-radius: 6px; background: rgba(255,255,255,0.1); color: #D2B48C; font-size: 16px;"
                                               placeholder="Your phone number">
                                    </div>
                                    
                                    <div style="margin-bottom: 30px;">
                                        <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold;">Message *</label>
                                        <textarea name="message" rows="6" required 
                                                  style="width: 100%; padding: 12px; border-radius: 6px; background: rgba(255,255,255,0.1); color: #D2B48C; font-size: 16px; resize: vertical;"
                                                  placeholder="Tell us about your project, question, or how we can help..."></textarea>
                                    </div>
                                    
                                    <div style="text-align: center;">
                                        <button type="submit" 
                                                style="background: #8B4513; color: #0f1419; border: none; padding: 15px 30px; font-size: 18px; font-weight: bold; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                            Send Message
                                        </button>
                                    </div>
                                </form>
                                <div id="contactFormResponse"></div>
                            </div>
                        </div> <!-- /.col-sm-6 -->
                        
                        <!-- Discord Section - Right Side -->
                        <div class="col-sm-6">
                            <h4>Join Our Community</h4>
                            <p style="color: #666; margin-bottom: 20px;">
                                <i class="ion-chatbubbles" style="margin-right: 8px;"></i>
                                Chat with us directly on Discord! Ask questions, share ideas, and collaborate in real-time.
                            </p>
                            <div style="text-align: center;">
                                <!-- Widgetbot embed - commented out for testing -->
                                <!--
                                <widgetbot
                                    server="409776353274232832"
                                    channel="1123589255671861299"
                                    width="800"
                                    height="600"
                                ></widgetbot>
                                -->
                                
                                <!-- Discord Widget for testing -->
                                <div style="background-color: #5865F2; color: white; padding: 30px; border-radius: 10px; margin: 20px 0;">
                                    <h3 style="margin-bottom: 15px; color: white;">
                                        <i class="ion-social-discord" style="margin-right: 10px; font-size: 24px;"></i>
                                        World Domination Dev
                                    </h3>
                                    <p style="margin-bottom: 20px; color: #e3e5e8;">
                                        Join our Discord community for real-time discussions, project updates, and collaboration opportunities!
                                    </p>
                                    <a href="https://discord.gg/XPFnNdWGyW" target="_blank" 
                                       style="background-color: #4752c4; color: white; padding: 12px 25px; border-radius: 5px; text-decoration: none; font-weight: bold; display: inline-block; transition: background-color 0.3s;">
                                        Join Discord Server
                                    </a>
                                </div>
                                
                                <p style="margin-top: 15px; font-size: 14px; color: #666;">
                                    New to Discord? Click the button above to get started!
                                </p>
                            </div>
                        </div> <!-- /.col-sm-6 -->
                    </div>
                </div>
            </div>
        </section>

    <!-- Include Footer -->
    <?php include 'includes/footer.php'; ?>

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
