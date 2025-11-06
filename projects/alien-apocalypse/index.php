<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>World Domination Software | Alien Apocalypse</title>

        <!-- CSS -->
        <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>
        <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="../assets/css/magnific-popup.css" rel="stylesheet">
        <link href="../assets/css/owl.carousel.css" rel="stylesheet">
        <link href="../assets/css/owl.carousel.theme.min.css" rel="stylesheet">
        <link href="../assets/css/ionicons.css" rel="stylesheet">
        <link href="../assets/css/main.css" rel="stylesheet">
        <link href="../assets/css/readability-improvements.css" rel="stylesheet">
    </head>
    <body>
        <?php 
        $current_page = 'projects';
        $header_class = 'projects-header inner-header';
        $page_subtitle = 'Design. Debug. Deploy.';
        $current_project_slug = 'alien-apocalypse';
        ?>

    <?php include '../includes/header.html'; ?>
    <?php include '../includes/navigation.php'; ?>

    <section class="about">
        <div class="container page-bgc">
            <div class="row">
                <div class="col-sm-12">
                    <div class="title-box">
                        <p>Upcoming Project</p>
                        <h2 class="title mt0">Alien Apocalypse</h2>
                    </div>
                </div>
            </div>
            
            <!-- MAIN CONTENT AREA - EASY TO EDIT -->
            <div class="row">
                <div class="boxed">
                    <div class="col-sm-12">
                        
                        <!-- Project Overview -->
                        <div style="background: #D4CFC0; border: 1px solid #333; border-radius: 8px; padding: 40px; margin-bottom: 40px;">
                            <h3 style="color: #8B4513; margin-bottom: 20px;">Survival Against the Impossible</h3>
                            <p style="color: #4a4a4a; font-size: 18px; line-height: 1.8; margin-bottom: 20px;">
                                When the invasion began, humanity wasn't ready. Alien Apocalypse throws you into humanity's darkest hour, where survival means more than just staying alive—it means rebuilding civilization from the ashes while fighting an enemy beyond comprehension.
                            </p>
                            <p style="color: #4a4a4a; font-size: 16px; line-height: 1.6;">
                                Combining intense survival shooter mechanics with deep base-building and crafting systems, this isn't just another zombie game with aliens. Every bullet counts, every resource matters, and every decision could mean the difference between extinction and hope.
                            </p>
                        </div>

                        <!-- Core Gameplay -->
                        <div style="background: #D4CFC0; border: 1px solid #333; border-radius: 8px; padding: 40px; margin-bottom: 40px;">
                            <h3 style="color: #8B4513; margin-bottom: 30px;">Dual-Layer Survival</h3>
                            <div class="row">
                                <div class="col-sm-6">
                                    <h4 style="color: #D2B48C; margin-bottom: 15px;">Combat & Scavenging</h4>
                                    <ul style="color: #4a4a4a; line-height: 1.8;">
                                        <li>Tactical shooter mechanics with realistic weapon handling</li>
                                        <li>Alien AI that learns and adapts to player behavior</li>
                                        <li>Scavenging in dangerous territories for rare resources</li>
                                        <li>Day/night cycle affecting alien behavior and visibility</li>
                                        <li>Stealth mechanics for avoiding overwhelming encounters</li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <h4 style="color: #D2B48C; margin-bottom: 15px;">Base Building & Survival</h4>
                                    <ul style="color: #4a4a4a; line-height: 1.8;">
                                        <li>Fortified settlement construction and management</li>
                                        <li>Resource production, food cultivation, and water purification</li>
                                        <li>Survivor recruitment and community management</li>
                                        <li>Research trees unlocking advanced technologies</li>
                                        <li>Defense systems and automated turret placement</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Call to Action -->
                        <div style="text-align: center; padding: 40px;">
                            <h3 style="color: #8B4513; margin-bottom: 20px;">Coming 2026</h3>
                            <p style="color: #4a4a4a; font-size: 16px; margin-bottom: 20px;">
                                Join our mailing list for exclusive development updates and early access opportunities.
                            </p>
                            <p style="color: #4a4a4a; font-size: 14px; margin-bottom: 16px;">
                                Technical foundation: Unity (C#) client builds, headless Linux servers for multiplayer sessions, and containerized tooling for reproducible server deployments.
                            </p>
                            <a href="/contact.php" class="btn btn-wds">
                                Stay Updated
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/project-navigation.php'; ?>
    <?php include '../includes/footer.html'; ?>

    <script src="../assets/js/jquery-1.12.3.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/owl.carousel.min.js"></script>
    <script src="../assets/js/jquery.magnific-popup.min.js"></script>
    <script src="../assets/js/script.js"></script>
    </body>
</html>

