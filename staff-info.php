<?php
// Start session and define system constant
session_start();
define('WDS_SYSTEM', true);

// Include database configuration and require admin login
require_once 'includes/db-config.php';
requireAdminLogin();

// Page-specific variables
$current_page = 'staff-info';
$header_class = 'staff-info-header inner-header';
$page_subtitle = 'Design. Debug. Deploy.';

// Learning platform credentials (these should ideally be stored in a secure database)
$learning_platforms = [
    'zenva' => [
        'name' => 'Zenva Academy',
        'url' => 'https://zenva.com',
        'description' => 'Game development courses, Unity, Unreal Engine, and programming tutorials',
        'username' => 'wds_team_account',
        'password' => 'ZenvaAccess2024!',
        'icon' => 'ion-university',
        'features' => ['Unity Game Development', 'Unreal Engine Courses', 'Web Development', 'AI & Machine Learning']
    ],
    'mammoth' => [
        'name' => 'Mammoth Interactive',
        'url' => 'https://mammothinteractive.com',
        'description' => 'Comprehensive programming and game development courses',
        'username' => 'wds_coop_login',
        'password' => 'MammothDev2024#',
        'icon' => 'ion-code-working',
        'features' => ['Game Development', 'Mobile App Development', 'Web Technologies', 'Business Skills']
    ],
    'udemy' => [
        'name' => 'Udemy Business',
        'url' => 'https://udemy.com',
        'description' => 'Wide range of technical and business courses for professional development',
        'username' => 'worlddomsoftware@business.udemy.com',
        'password' => 'UdemyBiz2024$',
        'icon' => 'ion-android-laptop',
        'features' => ['Programming Languages', 'Cloud Technologies', 'DevOps & System Admin', 'Business Development']
    ]
];

// Additional resources
$additional_resources = [
    'cpanel' => [
        'name' => 'Partner cPanel Hosting',
        'url' => 'https://cpanel.iaregamer.com',
        'description' => 'Unlimited domains and websites for co-op partners',
        'access_note' => 'Individual accounts will be created for each partner',
        'icon' => 'ion-monitor'
    ],
    'gameservers' => [
        'name' => 'Gameservers.world Admin',
        'url' => 'https://gameservers.world/admin',
        'description' => 'Administrative access to our game hosting platform',
        'access_note' => 'Admin credentials provided separately for security',
        'icon' => 'ion-android-desktop'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>WDS | Staff Information</title>

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

        <style>
            .staff-card {
                background: rgba(255,255,255,0.1);
                border: 2px solid rgba(0,200,81,0.3);
                border-radius: 15px;
                padding: 30px;
                margin-bottom: 30px;
                transition: all 0.3s ease;
                backdrop-filter: blur(10px);
            }
            .staff-card:hover {
                border-color: #8B4513;
                background: rgba(255,255,255,0.15);
                transform: translateY(-5px);
                box-shadow: 0 10px 25px rgba(0,200,81,0.2);
            }
            .credential-box {
                background: rgba(0,0,0,0.3);
                padding: 20px;
                border-radius: 10px;
                margin: 20px 0;
                border-left: 4px solid #8B4513;
            }
            .copy-btn {
                background: rgba(0,200,81,0.2);
                border: 1px solid #8B4513;
                color: #8B4513;
                padding: 5px 15px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 12px;
                margin-left: 10px;
                transition: all 0.2s ease;
            }
            .copy-btn:hover {
                background: #8B4513;
                color: #D2B48C;
            }
            .feature-badge {
                display: inline-block;
                background: rgba(0,200,81,0.2);
                color: #8B4513;
                padding: 3px 8px;
                border-radius: 12px;
                font-size: 11px;
                margin: 2px;
                border: 1px solid rgba(0,200,81,0.3);
            }
        </style>

        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <!-- Include Site Header -->
        <?php include 'includes/header.html'; ?>
        
        <!-- Include Navigation Header -->
        <?php include 'includes/navigation.php'; ?>

        <!-- Staff Info Section -->
        <section class="staff-information">
            <div class="container page-bgc">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="title-box">
                            <p>Welcome, <?php echo htmlspecialchars($_SESSION['wds_admin_user']); ?></p>
                            <h2 class="title mt0" style="color: #8B4513;">Staff Resources & Credentials</h2>
                        </div>
                    </div>
                </div>

                <!-- Welcome Message -->
                <div class="row">
                    <div class="col-sm-12">
                        <div style="background: linear-gradient(135deg, rgba(0,200,81,0.2), rgba(0,160,67,0.2)); padding: 30px; border-radius: 15px; margin-bottom: 40px; text-align: center;">
                            <h3 style="color: #8B4513; margin-bottom: 15px;">
                                <i class="ion-checkmark-circled" style="margin-right: 10px;"></i>Access Granted
                            </h3>
                            <p style="color: #8B7355; font-size: 16px; margin: 0; max-width: 800px; margin: 0 auto;">
                                You now have access to our shared learning platforms and development resources. 
                                Use these credentials to enhance your skills and contribute to our co-op projects.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Learning Platforms -->
                <div class="row">
                    <div class="col-sm-12">
                        <h3 style="color: #8B4513; margin-bottom: 30px; text-align: center;">
                            <i class="ion-university" style="margin-right: 10px;"></i>Learning Platform Access
                        </h3>
                    </div>
                </div>

                <?php foreach ($learning_platforms as $key => $platform): ?>
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        <div class="staff-card">
                            <div class="row">
                                <div class="col-sm-2 text-center">
                                    <i class="<?php echo $platform['icon']; ?>" style="font-size: 64px; color: #8B4513; margin-bottom: 15px;"></i>
                                </div>
                                <div class="col-sm-10">
                                    <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 15px;">
                                        <h4 style="color: #8B4513; margin: 0; font-weight: bold;"><?php echo $platform['name']; ?></h4>
                                        <a href="<?php echo $platform['url']; ?>" target="_blank" 
                                           style="color: #8B4513; margin-left: auto; text-decoration: none; font-size: 14px;">
                                            <i class="ion-android-open"></i> Visit Site
                                        </a>
                                    </div>
                                    
                                    <p style="color: #8B7355; margin-bottom: 20px; line-height: 1.5;">
                                        <?php echo $platform['description']; ?>
                                    </p>

                                    <div class="credential-box">
                                        <div style="margin-bottom: 15px;">
                                            <strong style="color: #8B4513;">Username:</strong>
                                            <span style="color: #D2B48C; margin-left: 10px;" id="user-<?php echo $key; ?>"><?php echo $platform['username']; ?></span>
                                            <button class="copy-btn" onclick="copyToClipboard('user-<?php echo $key; ?>')">
                                                <i class="ion-clipboard"></i> Copy
                                            </button>
                                        </div>
                                        <div>
                                            <strong style="color: #8B4513;">Password:</strong>
                                            <span style="color: #D2B48C; margin-left: 10px;" id="pass-<?php echo $key; ?>"><?php echo $platform['password']; ?></span>
                                            <button class="copy-btn" onclick="copyToClipboard('pass-<?php echo $key; ?>')">
                                                <i class="ion-clipboard"></i> Copy
                                            </button>
                                        </div>
                                    </div>

                                    <div style="margin-top: 15px;">
                                        <strong style="color: #8B4513; font-size: 14px;">Available Courses:</strong><br>
                                        <?php foreach ($platform['features'] as $feature): ?>
                                            <span class="feature-badge"><?php echo $feature; ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Additional Resources -->
                <div class="row" style="margin-top: 50px;">
                    <div class="col-sm-12">
                        <h3 style="color: #8B4513; margin-bottom: 30px; text-align: center;">
                            <i class="ion-android-apps" style="margin-right: 10px;"></i>Additional Resources
                        </h3>
                    </div>
                </div>

                <?php foreach ($additional_resources as $key => $resource): ?>
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        <div class="staff-card">
                            <div class="row">
                                <div class="col-sm-2 text-center">
                                    <i class="<?php echo $resource['icon']; ?>" style="font-size: 48px; color: #8B4513; margin-bottom: 15px;"></i>
                                </div>
                                <div class="col-sm-10">
                                    <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 15px;">
                                        <h4 style="color: #8B4513; margin: 0; font-weight: bold;"><?php echo $resource['name']; ?></h4>
                                        <a href="<?php echo $resource['url']; ?>" target="_blank" 
                                           style="color: #8B4513; margin-left: auto; text-decoration: none; font-size: 14px;">
                                            <i class="ion-android-open"></i> Access
                                        </a>
                                    </div>
                                    
                                    <p style="color: #8B7355; margin-bottom: 15px; line-height: 1.5;">
                                        <?php echo $resource['description']; ?>
                                    </p>

                                    <div style="background: rgba(0,200,81,0.1); padding: 15px; border-radius: 8px; border-left: 3px solid #8B4513;">
                                        <i class="ion-information-circled" style="color: #8B4513; margin-right: 8px;"></i>
                                        <span style="color: #8B7355; font-size: 14px;"><?php echo $resource['access_note']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Security Notice -->
                <div class="row" style="margin-top: 40px;">
                    <div class="col-sm-10 col-sm-offset-1">
                        <div style="background: rgba(248,113,113,0.1); border: 2px solid rgba(248,113,113,0.3); padding: 25px; border-radius: 12px;">
                            <h4 style="color: #f87171; margin-bottom: 15px;">
                                <i class="ion-alert-circled" style="margin-right: 10px;"></i>Security Guidelines
                            </h4>
                            <ul style="color: #8B7355; margin: 0; line-height: 1.6;">
                                <li><strong>Do not share these credentials</strong> outside the co-op team</li>
                                <li><strong>Use strong, unique passwords</strong> for any accounts you create on these platforms</li>
                                <li><strong>Log out</strong> when finished using shared accounts</li>
                                <li><strong>Report any suspicious activity</strong> immediately to co-op management</li>
                                <li><strong>Use these resources</strong> for co-op related learning and development</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Logout -->
                <div class="row" style="margin-top: 30px;">
                    <div class="col-sm-12 text-center">
                        <a href="logout.php" 
                           style="background: rgba(248,113,113,0.2); color: #f87171; border: 2px solid rgba(248,113,113,0.3); padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; transition: all 0.3s ease;"
                           onmouseover="this.style.background='rgba(248,113,113,0.3)'; this.style.borderColor='#f87171';"
                           onmouseout="this.style.background='rgba(248,113,113,0.2)'; this.style.borderColor='rgba(248,113,113,0.3)';">
                            <i class="ion-log-out" style="margin-right: 8px;"></i>Logout
                        </a>
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

        <script>
            // Copy to clipboard function
            function copyToClipboard(elementId) {
                const element = document.getElementById(elementId);
                const text = element.textContent;
                
                // Create a temporary textarea element
                const textarea = document.createElement('textarea');
                textarea.value = text;
                document.body.appendChild(textarea);
                textarea.select();
                
                try {
                    document.execCommand('copy');
                    
                    // Show feedback
                    const button = event.target.closest('.copy-btn');
                    const originalText = button.innerHTML;
                    button.innerHTML = '<i class="ion-checkmark"></i> Copied!';
                    button.style.background = '#8B4513';
                    button.style.color = '#fff';
                    
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.style.background = 'rgba(0,200,81,0.2)';
                        button.style.color = '#8B4513';
                    }, 2000);
                } catch (err) {
                    console.error('Failed to copy text: ', err);
                }
                
                document.body.removeChild(textarea);
            }

            // Auto-logout after 2 hours of inactivity
            let inactivityTimer;
            const INACTIVITY_TIMEOUT = 2 * 60 * 60 * 1000; // 2 hours

            function resetInactivityTimer() {
                clearTimeout(inactivityTimer);
                inactivityTimer = setTimeout(() => {
                    alert('Session expired due to inactivity. You will be logged out.');
                    window.location.href = 'logout.php';
                }, INACTIVITY_TIMEOUT);
            }

            // Track user activity
            document.addEventListener('mousemove', resetInactivityTimer);
            document.addEventListener('keypress', resetInactivityTimer);
            document.addEventListener('click', resetInactivityTimer);
            
            // Start the timer
            resetInactivityTimer();
        </script>

    </body>
</html>
