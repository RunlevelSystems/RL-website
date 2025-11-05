<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>GameServer Panel - World Domination Software</title>

    <!-- CSS -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/magnific-popup.css" rel="stylesheet">
    <link href="../../assets/css/owl.carousel.css" rel="stylesheet">
    <link href="../../assets/css/owl.carousel.theme.min.css" rel="stylesheet">
    <link href="../../assets/css/ionicons.css" rel="stylesheet">
    <!-- WDS Unified CSS - Simplified & Clean -->
    <link href="../../assets/css/wds-unified.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php 
    // Page-specific variables
    $current_page = 'projects';
    $page_subtitle = 'GameServer Panel';
    $page_description = 'Professional game server management solution';
    $page_title = 'GameServer';
    $page_title_thin = 'Panel';
    $header_class = 'projects-header inner-header';
    $show_breadcrumb = true;
    $page_breadcrumb = 'GameServer Panel';
    ?>

    <!-- Include Site Header -->
    <?php include '../includes/header.html'; ?>
    
    <!-- Include Navigation Header -->
    <?php include '../includes/navigation.php'; ?>

    <!-- Breadcrumb Navigation -->
    <nav style="background-color: #0f1419; padding: 15px 0; border-bottom: 1px solid #555555;">
        <div class="container">
            <ol style="margin: 0; padding: 0; list-style: none; display: flex; align-items: center; font-size: 14px;">
                <li><a href="../projects.php" style="color: #8B7355; text-decoration: none;">Projects</a></li>
                <li style="margin: 0 10px; color: #8B7355;"><i class="fas fa-chevron-right"></i></li>
                <li style="color: #D2B48C; font-weight: 500;">GameServer Panel</li>
            </ol>
        </div>
    </nav>

    <section class="project-hero">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2 text-center">
                    <h1 style="color: #8B4513; font-size: 48px; margin-bottom: 20px;">
                        <i class="fas fa-server" style="margin-right: 15px;"></i>
                        GameServer Panel
                    </h1>
                    <h2 style="color: #D2B48C; font-size: 24px; margin-bottom: 30px;">
                        Professional Open Game Panel Fork
                    </h2>
                    <p style="font-size: 18px; line-height: 1.8; color: #8B7355; max-width: 800px; margin: 0 auto;">
                        Our enhanced fork of OpenGamePanel (OGP) featuring commercial billing integration, 
                        professional support systems, and multi-location server management. Built for hosting 
                        providers who need enterprise-grade game server management.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section style="background-color: #2a2a2a; padding: 80px 0; color: #D2B48C;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 style="color: #8B4513; text-align: center; margin-bottom: 40px; font-size: 36px;">
                        Why GameServer Panel?
                    </h2>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <h3 style="color: #8B4513; margin-bottom: 15px;">Commercial Billing</h3>
                            <p style="color: #8B7355;">
                                Integrated billing system with automated provisioning, payment processing, 
                                and customer management. Support for multiple payment gateways and subscription models.
                            </p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <h3 style="color: #8B4513; margin-bottom: 15px;">Professional Support</h3>
                            <p style="color: #8B7355;">
                                Built-in ticket system, knowledge base integration, and customer portal. 
                                Streamlined support workflows for hosting providers.
                            </p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-globe"></i>
                            </div>
                            <h3 style="color: #8B4513; margin-bottom: 15px;">Multi-Location Management</h3>
                            <p style="color: #8B7355;">
                                Manage game servers across multiple data centers and regions from a single 
                                interface. Load balancing and failover capabilities included.
                            </p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fab fa-github"></i>
                            </div>
                            <h3 style="color: #8B4513; margin-bottom: 15px;">Open Source</h3>
                            <p style="color: #8B7355;">
                                Fully open source with comprehensive documentation. Fork, modify, and 
                                contribute back to the community while maintaining commercial licensing options.
                            </p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h3 style="color: #8B4513; margin-bottom: 15px;">Enterprise Security</h3>
                            <p style="color: #8B7355;">
                                Enhanced security model with user isolation, encrypted communications, 
                                and comprehensive audit logging for enterprise compliance.
                            </p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3 style="color: #8B4513; margin-bottom: 15px;">Analytics & Reporting</h3>
                            <p style="color: #8B7355;">
                                Comprehensive analytics dashboard with server performance metrics, 
                                usage statistics, and financial reporting for business intelligence.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Downloads and Quick Start Section -->
    <section style="background-color: #0F1419; padding: 80px 0; color: #D2B48C;">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h2 style="color: #8B4513; margin-bottom: 40px;">
                        <i class="fas fa-download" style="margin-right: 15px;"></i>
                        Download & Quick Start
                    </h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="download-section" style="background-color: #1C1C1C; padding: 30px; border-radius: 8px; border: 1px solid #555555;">
                        <h3 style="color: #8B4513; margin-bottom: 20px;">
                            <i class="fab fa-github" style="margin-right: 10px;"></i>
                            Repository Access
                        </h3>
                        <p style="color: #8B7355; margin-bottom: 25px;">
                            Access the complete source code, documentation, and installation resources.
                        </p>
                        <div style="margin-bottom: 20px;">
                            <a href="https://github.com/World-Domination-Software/GameServer-Panel" class="btn-wds" target="_blank" style="margin-right: 10px; margin-bottom: 10px;">
                                <i class="fab fa-github" style="margin-right: 8px;"></i>
                                Main Repository
                            </a>
                            <a href="https://github.com/World-Domination-Software/GameServer-Panel/releases/latest" class="btn-wds" target="_blank" style="margin-bottom: 10px;">
                                <i class="fas fa-tag" style="margin-right: 8px;"></i>
                                Latest Release
                            </a>
                        </div>
                        <div style="background-color: #0f1419; padding: 15px; border-radius: 4px; border-left: 4px solid #555555;">
                            <code style="color: #D2B48C; font-family: 'Courier New', monospace;">
                                git clone https://github.com/World-Domination-Software/GameServer-Panel.git
                            </code>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="download-section" style="background-color: #1C1C1C; padding: 30px; border-radius: 8px; border: 1px solid #555555;">
                        <h3 style="color: #8B4513; margin-bottom: 20px;">
                            <i class="fas fa-rocket" style="margin-right: 10px;"></i>
                            Quick Start Guide
                        </h3>
                        <p style="color: #8B7355; margin-bottom: 20px;">
                            Get GameServer Panel running in minutes with our automated installer.
                        </p>
                        <ol style="color: #8B7355; margin-bottom: 25px; padding-left: 20px;">
                            <li style="margin-bottom: 8px;">Download the latest release package</li>
                            <li style="margin-bottom: 8px;">Run the installation script on your Linux server</li>
                            <li style="margin-bottom: 8px;">Configure your first game server</li>
                            <li>Start managing your gaming community!</li>
                        </ol>
                        <div style="background-color: #0f1419; padding: 15px; border-radius: 4px; border-left: 4px solid #8B4513; font-family: 'Courier New', monospace; font-size: 13px;">
                            <div style="color: #8B7355; margin-bottom: 5px;"># Download and install</div>
                            <div style="color: #D2B48C;">curl -fsSL https://install.gameserver-panel.org | sudo bash</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row" style="margin-top: 40px;">
                <div class="col-md-12">
                    <div class="requirements-section" style="background-color: #2A2A2A; padding: 40px; border-radius: 8px;">
                        <h3 style="color: #8B4513; margin-bottom: 30px; text-align: center;">
                            <i class="fas fa-server" style="margin-right: 10px;"></i>
                            System Requirements
                        </h3>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="requirement-item" style="text-align: center; margin-bottom: 20px;">
                                    <div style="font-size: 32px; color: #8B4513; margin-bottom: 10px;">
                                        <i class="fab fa-linux"></i>
                                    </div>
                                    <h4 style="color: #8B4513; margin-bottom: 10px;">Operating System</h4>
                                    <p style="color: #8B7355; margin: 0;">
                                        Ubuntu 20.04+<br>
                                        CentOS 7+<br>
                                        Debian 10+
                                    </p>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="requirement-item" style="text-align: center; margin-bottom: 20px;">
                                    <div style="font-size: 32px; color: #8B4513; margin-bottom: 10px;">
                                        <i class="fas fa-microchip"></i>
                                    </div>
                                    <h4 style="color: #8B4513; margin-bottom: 10px;">Hardware</h4>
                                    <p style="color: #8B7355; margin: 0;">
                                        2+ CPU cores<br>
                                        4GB+ RAM<br>
                                        20GB+ storage
                                    </p>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="requirement-item" style="text-align: center; margin-bottom: 20px;">
                                    <div style="font-size: 32px; color: #8B4513; margin-bottom: 10px;">
                                        <i class="fas fa-database"></i>
                                    </div>
                                    <h4 style="color: #8B4513; margin-bottom: 10px;">Dependencies</h4>
                                    <p style="color: #8B7355; margin: 0;">
                                        Apache/Nginx<br>
                                        PHP 7.4+<br>
                                        MySQL 5.7+
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section style="background-color: #1C1C1C; padding: 80px 0; color: #D2B48C;">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2 style="color: #8B4513; margin-bottom: 30px;">Technical Overview</h2>
                    <p style="font-size: 18px; line-height: 1.8; color: #8B7355; margin-bottom: 25px;">
                        Built on the proven OpenGamePanel architecture, our GameServer Panel extends the 
                        core functionality with enterprise-grade features designed for commercial hosting environments.
                    </p>
                    <p style="font-size: 16px; line-height: 1.7; color: #8B7355; margin-bottom: 30px;">
                        The system maintains the distributed architecture with a central web panel and 
                        remote agents, while adding advanced billing, monitoring, and management capabilities 
                        required by modern hosting providers.
                    </p>
                    
                    <div class="tech-stack">
                        <h4 style="color: #8B4513; margin-bottom: 15px;">Technology Stack</h4>
                        <span class="tech-badge">PHP 8.x</span>
                        <span class="tech-badge">Perl</span>
                        <span class="tech-badge">MySQL/MariaDB</span>
                        <span class="tech-badge">Linux</span>
                        <span class="tech-badge">Docker</span>
                        <span class="tech-badge">Redis</span>
                        <span class="tech-badge">API Integration</span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h2 style="color: #8B4513; margin-bottom: 30px;">Gaming Industry Insights</h2>
                    <p style="font-size: 16px; line-height: 1.7; color: #8B7355; margin-bottom: 25px;">
                        The gaming industry continues its explosive growth, reaching $268 billion in 2025. 
                        Our panel is designed to help hosting providers capitalize on this growth with 
                        professional-grade server management tools.
                    </p>
                    
                    <div style="background: rgba(139, 69, 19, 0.1); border-left: 4px solid #8B4513; padding: 20px; margin: 20px 0;">
                        <h4 style="color: #8B4513; margin-bottom: 10px;">Market Opportunity</h4>
                        <p style="color: #8B7355; margin-bottom: 0;">
                            With mobile gaming at 55% market share and PC/Console maintaining strong positions, 
                            the demand for reliable game server hosting continues to grow across all platforms.
                        </p>
                    </div>
                    
                    <a href="industry-stats.php" class="btn-wds">
                        <i class="fas fa-chart-bar" style="margin-right: 8px;"></i>
                        View Industry Statistics
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section style="background-color: #2a2a2a; padding: 80px 0;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 style="color: #8B4513; text-align: center; margin-bottom: 50px; font-size: 36px;">
                        Documentation & Resources
                    </h2>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="feature-card text-center">
                                <div class="feature-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <h3 style="color: #8B4513; margin-bottom: 15px;">Admin Guide</h3>
                                <p style="color: #8B7355; margin-bottom: 25px;">
                                    Comprehensive interactive guide covering installation, configuration, 
                                    and advanced XML game definitions.
                                </p>
                                <a href="admin-guide.php" class="btn-wds">
                                    <i class="fas fa-book-open" style="margin-right: 8px;"></i>
                                    View Guide
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="feature-card text-center">
                                <div class="feature-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h3 style="color: #8B4513; margin-bottom: 15px;">Industry Stats</h3>
                                <p style="color: #8B7355; margin-bottom: 25px;">
                                    2025 gaming market analysis with hosting opportunities, 
                                    platform distribution, and growth trends.
                                </p>
                                <a href="industry-stats.php" class="btn-wds">
                                    <i class="fas fa-chart-bar" style="margin-right: 8px;"></i>
                                    View Statistics
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="feature-card text-center">
                                <div class="feature-icon">
                                    <i class="fab fa-github"></i>
                                </div>
                                <h3 style="color: #8B4513; margin-bottom: 15px;">Source Code</h3>
                                <p style="color: #8B7355; margin-bottom: 25px;">
                                    Open source repository with installation scripts, documentation, 
                                    and community contributions.
                                </p>
                                <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                                    <a href="https://github.com/World-Domination-Software/GameServer-Panel" class="btn-wds" target="_blank" style="font-size: 12px; padding: 8px 12px;">
                                        <i class="fab fa-github" style="margin-right: 5px;"></i>
                                        Main Repo
                                    </a>
                                    <a href="https://github.com/World-Domination-Software/GameServer-Panel/releases" class="btn-wds" target="_blank" style="font-size: 12px; padding: 8px 12px;">
                                        <i class="fas fa-download" style="margin-right: 5px;"></i>
                                        Releases
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section style="background-color: #2A2A2A; padding: 80px 0; color: #D2B48C;">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h2 style="color: #8B4513; margin-bottom: 50px;">
                        <i class="fas fa-quote-left" style="margin-right: 15px;"></i>
                        What Hosting Providers Say
                    </h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="testimonial-card" style="background-color: #1C1C1C; padding: 30px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #8B4513;">
                        <div style="text-align: center; margin-bottom: 20px;">
                            <i class="fas fa-user-circle" style="font-size: 48px; color: #8B4513;"></i>
                        </div>
                        <p style="color: #8B7355; font-style: italic; margin-bottom: 20px; line-height: 1.6;">
                            "The integrated billing system has completely transformed our hosting business. 
                            What used to take hours of manual setup now happens automatically."
                        </p>
                        <div style="text-align: center;">
                            <h4 style="color: #8B4513; margin-bottom: 5px;">Sarah Mitchell</h4>
                            <p style="color: #8B7355; margin: 0; font-size: 14px;">CEO, GameHost Pro</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="testimonial-card" style="background-color: #1C1C1C; padding: 30px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #8B4513;">
                        <div style="text-align: center; margin-bottom: 20px;">
                            <i class="fas fa-user-circle" style="font-size: 48px; color: #8B4513;"></i>
                        </div>
                        <p style="color: #8B7355; font-style: italic; margin-bottom: 20px; line-height: 1.6;">
                            "Migration from standard OGP was seamless. The commercial features give us 
                            the professional edge we needed to compete with larger hosting companies."
                        </p>
                        <div style="text-align: center;">
                            <h4 style="color: #8B4513; margin-bottom: 5px;">Marcus Chen</h4>
                            <p style="color: #8B7355; margin: 0; font-size: 14px;">Technical Director, ServerCraft</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="testimonial-card" style="background-color: #1C1C1C; padding: 30px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #8B4513;">
                        <div style="text-align: center; margin-bottom: 20px;">
                            <i class="fas fa-user-circle" style="font-size: 48px; color: #8B4513;"></i>
                        </div>
                        <p style="color: #8B7355; font-style: italic; margin-bottom: 20px; line-height: 1.6;">
                            "The multi-location management feature allows us to offer global coverage 
                            while maintaining centralized control. Customer satisfaction has increased 40%."
                        </p>
                        <div style="text-align: center;">
                            <h4 style="color: #8B4513; margin-bottom: 5px;">Elena Rodriguez</h4>
                            <p style="color: #8B7355; margin: 0; font-size: 14px;">Operations Manager, GameServers EU</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section style="background-color: #1C1C1C; padding: 80px 0; color: #D2B48C;">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h2 style="color: #8B4513; margin-bottom: 50px;">
                        <i class="fas fa-question-circle" style="margin-right: 15px;"></i>
                        Frequently Asked Questions
                    </h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="faq-accordion">
                        <div class="faq-item" style="margin-bottom: 20px;">
                            <div class="faq-question" style="background-color: #2A2A2A; padding: 20px; border-radius: 8px 8px 0 0; cursor: pointer; border: 1px solid #8B4513;" onclick="toggleFAQ(this)">
                                <h4 style="color: #8B4513; margin: 0; display: flex; justify-content: space-between; align-items: center;">
                                    How difficult is migration from standard OGP?
                                    <i class="fas fa-chevron-down faq-arrow" style="transition: transform 0.3s;"></i>
                                </h4>
                            </div>
                            <div class="faq-answer" style="background-color: #0F1419; padding: 0; max-height: 0; overflow: hidden; transition: max-height 0.3s, padding 0.3s; border: 1px solid #8B4513; border-top: none; border-radius: 0 0 8px 8px;">
                                <div style="padding: 20px;">
                                    <p style="color: #8B7355; margin: 0; line-height: 1.6;">
                                        Migration is typically straightforward and can be completed in a few hours. Our migration script handles 
                                        database conversion, user accounts, and server configurations. Most hosting providers experience zero downtime 
                                        during the transition process.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="faq-item" style="margin-bottom: 20px;">
                            <div class="faq-question" style="background-color: #2A2A2A; padding: 20px; border-radius: 8px 8px 0 0; cursor: pointer; border: 1px solid #8B4513;" onclick="toggleFAQ(this)">
                                <h4 style="color: #8B4513; margin: 0; display: flex; justify-content: space-between; align-items: center;">
                                    What billing systems are supported?
                                    <i class="fas fa-chevron-down faq-arrow" style="transition: transform 0.3s;"></i>
                                </h4>
                            </div>
                            <div class="faq-answer" style="background-color: #0F1419; padding: 0; max-height: 0; overflow: hidden; transition: max-height 0.3s, padding 0.3s; border: 1px solid #8B4513; border-top: none; border-radius: 0 0 8px 8px;">
                                <div style="padding: 20px;">
                                    <p style="color: #8B7355; margin: 0; line-height: 1.6;">
                                        GameServer Panel includes native integrations with WHMCS, Blesta, and HostBill. We also provide 
                                        RESTful APIs for custom billing solutions and support popular payment gateways including PayPal, 
                                        Stripe, and cryptocurrency processors.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="faq-item" style="margin-bottom: 20px;">
                            <div class="faq-question" style="background-color: #2A2A2A; padding: 20px; border-radius: 8px 8px 0 0; cursor: pointer; border: 1px solid #8B4513;" onclick="toggleFAQ(this)">
                                <h4 style="color: #8B4513; margin: 0; display: flex; justify-content: space-between; align-items: center;">
                                    Is commercial licensing required?
                                    <i class="fas fa-chevron-down faq-arrow" style="transition: transform 0.3s;"></i>
                                </h4>
                            </div>
                            <div class="faq-answer" style="background-color: #0F1419; padding: 0; max-height: 0; overflow: hidden; transition: max-height 0.3s, padding 0.3s; border: 1px solid #8B4513; border-top: none; border-radius: 0 0 8px 8px;">
                                <div style="padding: 20px;">
                                    <p style="color: #8B7355; margin: 0; line-height: 1.6;">
                                        The core GameServer Panel remains open source under GPL license. Commercial licensing is available 
                                        for businesses requiring white-label branding, priority support, or custom development services. 
                                        Contact us for enterprise pricing.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="faq-item" style="margin-bottom: 20px;">
                            <div class="faq-question" style="background-color: #2A2A2A; padding: 20px; border-radius: 8px 8px 0 0; cursor: pointer; border: 1px solid #8B4513;" onclick="toggleFAQ(this)">
                                <h4 style="color: #8B4513; margin: 0; display: flex; justify-content: space-between; align-items: center;">
                                    What level of support is provided?
                                    <i class="fas fa-chevron-down faq-arrow" style="transition: transform 0.3s;"></i>
                                </h4>
                            </div>
                            <div class="faq-answer" style="background-color: #0F1419; padding: 0; max-height: 0; overflow: hidden; transition: max-height 0.3s, padding 0.3s; border: 1px solid #8B4513; border-top: none; border-radius: 0 0 8px 8px;">
                                <div style="padding: 20px;">
                                    <p style="color: #8B7355; margin: 0; line-height: 1.6;">
                                        Community support is available through GitHub issues and our Discord server. Commercial users receive 
                                        priority email support with guaranteed response times. Enterprise customers get dedicated support 
                                        channels and direct developer access.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Projects Section -->
    <section style="padding: 60px 0; background: linear-gradient(135deg, rgba(139, 69, 19, 0.1) 0%, rgba(210, 180, 140, 0.05) 100%);">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h2 style="color: #8B4513; margin-bottom: 30px;">
                        <i class="fas fa-link" style="margin-right: 10px;"></i>
                        Related Projects
                    </h2>
                    <p style="color: #8B7355; margin-bottom: 40px; font-size: 16px;">
                        Explore our other gaming-focused initiatives and software solutions
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <h3 style="color: #8B4513; margin-bottom: 15px;">GameServers World</h3>
                        <p style="color: #8B7355; margin-bottom: 25px;">
                            Professional game server hosting services with global infrastructure 
                            and enterprise-grade support.
                        </p>
                        <a href="../gameservers-world/index.php" class="btn-wds">
                            <i class="fas fa-server" style="margin-right: 8px;"></i>
                            View Project
                        </a>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3 style="color: #8B4513; margin-bottom: 15px;">BBS Revival</h3>
                        <p style="color: #8B7355; margin-bottom: 25px;">
                            Modern bulletin board system bringing classic community 
                            features to contemporary gaming environments.
                        </p>
                        <a href="../bbs-revival/index.php" class="btn-wds">
                            <i class="fas fa-terminal" style="margin-right: 8px;"></i>
                            View Project
                        </a>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h3 style="color: #8B4513; margin-bottom: 15px;">Space 4X</h3>
                        <p style="color: #8B7355; margin-bottom: 25px;">
                            Strategic space exploration game featuring multiplayer 
                            campaigns and advanced diplomacy systems.
                        </p>
                        <a href="../space-4x/index.php" class="btn-wds">
                            <i class="fas fa-satellite" style="margin-right: 8px;"></i>
                            View Project
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="cta-section" style="margin: 80px auto; max-width: 800px;">
        <h2 style="color: #8B4513; margin-bottom: 20px;">Ready to Get Started?</h2>
        <p style="color: #8B7355; font-size: 18px; margin-bottom: 30px;">
            Join the community of hosting providers using GameServer Panel to power their 
            game server infrastructure. Professional support and commercial licensing available.
        </p>
        <a href="../contact.php" class="btn-wds">
            <i class="fas fa-envelope" style="margin-right: 8px;"></i>
            Contact Us
        </a>
        <a href="../joinus.php" class="btn-wds">
            <i class="fas fa-users" style="margin-right: 8px;"></i>
            Join Our Team
        </a>
    </div>

    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>

    <!-- JavaScript -->
    <script src="../assets/js/jquery-1.12.3.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/jquery.magnific-popup.min.js"></script>
    <script src="../assets/js/owl.carousel.min.js"></script>
    <script src="../assets/js/script.js"></script>
    
    <script>
        function toggleFAQ(element) {
            const faqItem = element.parentNode;
            const answer = faqItem.querySelector('.faq-answer');
            const arrow = element.querySelector('.faq-arrow');
            
            // Close all other FAQ items
            document.querySelectorAll('.faq-item').forEach(item => {
                if (item !== faqItem) {
                    const otherAnswer = item.querySelector('.faq-answer');
                    const otherArrow = item.querySelector('.faq-arrow');
                    otherAnswer.style.maxHeight = '0';
                    otherAnswer.style.padding = '0 20px';
                    otherArrow.style.transform = 'rotate(0deg)';
                }
            });
            
            // Toggle current FAQ item
            if (answer.style.maxHeight === '0px' || answer.style.maxHeight === '') {
                answer.style.maxHeight = answer.scrollHeight + 40 + 'px';
                answer.style.padding = '20px';
                arrow.style.transform = 'rotate(180deg)';
            } else {
                answer.style.maxHeight = '0';
                answer.style.padding = '0 20px';
                arrow.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</body>
</html>