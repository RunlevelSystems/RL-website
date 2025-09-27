<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Robot | Projects</title>

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
        $current_page = 'projects';
        $header_class = 'projects-header inner-header';
        $show_breadcrumb = true;
        $page_breadcrumb = 'Projects';
        ?>

    <!-- Include Site Header -->
    <?php include 'includes/header.html'; ?>
        
    <!-- Include Navigation Header -->
    <?php include 'includes/navigation.php'; ?>

    <!-- Projects -->
        <section class="about">
            <div class="container page-bgc">
                <!-- Project Detail Section (Hidden by default) -->
                <div id="project-detail" style="display: none; margin-bottom: 60px;">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="title-box">
                                <a href="#" onclick="hideProject(); return false;" id="back-to-projects" style="color: #8B4513; text-decoration: none; font-size: 14px; display: inline-block; margin-bottom: 10px;">
                                    ← Back to All Projects
                                </a>
                                <p id="project-category">Current Project</p>
                                <h2 class="title mt0" id="project-title">Project Title</h2>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Orange HR before project content -->
                    <hr style="border: none; height: 3px; background: linear-gradient(to right, #8B4513, #A0522D, #8B4513); margin: 30px 0; border-radius: 2px;">
                    
                    <div class="row">
                        <div class="boxed">
                            <div class="col-sm-12" id="project-content">
                                <!-- Dynamic project content will be loaded here -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Orange HR after project content -->
                    <hr style="border: none; height: 3px; background: linear-gradient(to right, #8B4513, #A0522D, #8B4513); margin: 30px 0; border-radius: 2px;">
                </div>

                <!-- Projects Overview Section -->
                <div id="projects-overview">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="title-box">
                                <p>Explore our</p>
                                <h2 class="title mt0">Latest Projects</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="boxed">
                            <div class="col-sm-12">
                                <p class="inner-p">
                                    Discover our innovative projects and cutting-edge solutions in game development and technology.
                                    Each project represents our commitment to excellence and innovation.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Current Projects -->
                <div class="service">
                    <div class="row">
                        <div class="boxed">
                            <div class="col-sm-6" style="margin-bottom: 30px;">
                                <div class="project-card" onclick="loadProject('neverwards', 'Neverwards', 'Current Project')" style="background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 20px; height: 200px; transition: all 0.3s ease; cursor: pointer; position: relative;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                        <h3 style="color: #8B4513; margin: 0; font-size: 18px;">Neverwards</h3>
                                        <img src="assets/images/neverwards-icon.png" alt="Neverwards" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;" onerror="this.style.display='none'">
                                    </div>
                                    <p style="color: #8B7355; line-height: 1.6; margin: 0; font-size: 14px;">
                                        An immersive multiplayer open-world adventure game featuring dynamic storytelling, cooperative gameplay, and endless exploration in a beautifully crafted fantasy universe.
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6" style="margin-bottom: 30px;">
                                <div class="project-card" onclick="loadProject('gameservers-world', 'Gameservers.world', 'Current Project')" style="background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 20px; height: 200px; transition: all 0.3s ease; cursor: pointer; position: relative;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                        <h3 style="color: #8B4513; margin: 0; font-size: 18px;">Gameservers.world</h3>
                                        <img src="assets/images/gameservers-icon.png" alt="Gameservers.world" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;" onerror="this.style.display='none'">
                                    </div>
                                    <p style="color: #8B7355; line-height: 1.6; margin: 0; font-size: 14px;">
                                        Global game server hosting platform providing the most affordable and reliable hosting solutions across multiple world locations with 24/7 uptime monitoring.
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6" style="margin-bottom: 30px;">
                                <div class="project-card" onclick="loadProject('worlddomination-dev', 'Worlddomination.dev', 'Current Project')" style="background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 20px; height: 200px; transition: all 0.3s ease; cursor: pointer; position: relative;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                        <h3 style="color: #8B4513; margin: 0; font-size: 18px;">Worlddomination.dev</h3>
                                        <img src="assets/images/worlddomination-icon.png" alt="Worlddomination.dev" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;" onerror="this.style.display='none'">
                                    </div>
                                    <p style="color: #8B7355; line-height: 1.6; margin: 0; font-size: 14px;">
                                        Our corporate development platform showcasing enterprise-grade web applications, custom business solutions, and professional consulting services for Fortune 500 companies.
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6" style="margin-bottom: 30px;">
                                <div class="project-card" onclick="loadProject('pureops', 'PureOps', 'Current Project')" style="background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 20px; height: 200px; transition: all 0.3s ease; cursor: pointer; position: relative;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                        <h3 style="color: #8B4513; margin: 0; font-size: 18px;">PureOps</h3>
                                        <img src="assets/images/pureops-icon.png" alt="PureOps" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;" onerror="this.style.display='none'">
                                    </div>
                                    <p style="color: #8B7355; line-height: 1.6; margin: 0; font-size: 14px;">
                                        Advanced DevOps automation platform streamlining deployment pipelines, infrastructure management, and continuous integration for scalable software operations.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Legacy Projects Section -->
            <div class="row" style="margin-top: 60px;">
                <div class="col-sm-12">
                    <div class="title-box">
                        <p>Our legacy</p>
                        <h2 class="title mt0">Legacy Projects</h2>
                    </div>
                </div>
            </div>
            <div class="service">
                <div class="row">
                    <div class="boxed">
                        <div class="col-sm-6" style="margin-bottom: 30px;">
                            <div class="project-card" onclick="loadProject('roadkill', 'Roadkill', 'Legacy Project')" style="background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 20px; height: 200px; transition: all 0.3s ease; cursor: pointer; position: relative;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                    <h3 style="color: #8B4513; margin: 0; font-size: 18px;">Roadkill</h3>
                                    <img src="assets/images/roadkill-icon.png" alt="Roadkill" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;" onerror="this.style.display='none'">
                                </div>
                                <p style="color: #8B7355; line-height: 1.6; margin: 0; font-size: 14px;">
                                    Classic vehicular combat game that defined an era. Fast-paced multiplayer racing with destructible environments, weapon pickups, and intense PvP battles across diverse arenas.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Projects Section -->
            <div class="row" style="margin-top: 60px;">
                <div class="col-sm-12">
                    <div class="title-box">
                        <p>Coming soon</p>
                        <h2 class="title mt0">Upcoming Projects</h2>
                    </div>
                </div>
            </div>
            <div class="service">
                <div class="row">
                    <div class="boxed">
                        <div class="col-sm-6" style="margin-bottom: 30px;">
                            <div class="project-card upcoming" onclick="loadProject('space-4x', 'Space-4X', 'Upcoming Project')" style="background: #1a1a1a; border: 1px solid #444; border-radius: 8px; padding: 20px; height: 200px; transition: all 0.3s ease; cursor: pointer; position: relative; opacity: 0.9;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                    <h3 style="color: #8B4513; margin: 0; font-size: 18px;">Space-4X</h3>
                                    <img src="assets/images/space4x-icon.png" alt="Space-4X" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;" onerror="this.style.display='none'">
                                </div>
                                <p style="color: #8B7355; line-height: 1.6; margin: 0; font-size: 14px;">
                                    Strategic space empire game combining the depth of VGA Planets with the economic complexity of TradeWars 2002. Build fleets, colonize worlds, and dominate the galaxy.
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-6" style="margin-bottom: 30px;">
                            <div class="project-card upcoming" onclick="loadProject('alien-apocalypse', 'Alien Apocalypse', 'Upcoming Project')" style="background: #1a1a1a; border: 1px solid #444; border-radius: 8px; padding: 20px; height: 200px; transition: all 0.3s ease; cursor: pointer; position: relative; opacity: 0.9;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                    <h3 style="color: #8B4513; margin: 0; font-size: 18px;">Alien Apocalypse</h3>
                                    <img src="assets/images/alien-apocalypse-icon.png" alt="Alien Apocalypse" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;" onerror="this.style.display='none'">
                                </div>
                                <p style="color: #8B7355; line-height: 1.6; margin: 0; font-size: 14px;">
                                    Intense survival shooter where humanity's last stand meets crafting and base-building. Fight alien hordes, scavenge resources, and build fortified settlements to survive.
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-6" style="margin-bottom: 30px;">
                            <div class="project-card upcoming" onclick="loadProject('bbs-revival', 'BBS Revival', 'Upcoming Project')" style="background: #1a1a1a; border: 1px solid #444; border-radius: 8px; padding: 20px; height: 200px; transition: all 0.3s ease; cursor: pointer; position: relative; opacity: 0.9;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                    <h3 style="color: #8B4513; margin: 0; font-size: 18px;">BBS Revival</h3>
                                    <img src="assets/images/bbs-icon.png" alt="BBS Revival" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;" onerror="this.style.display='none'">
                                </div>
                                <p style="color: #8B7355; line-height: 1.6; margin: 0; font-size: 14px;">
                                    Nostalgic tribute to classic bulletin board systems. ASCII art, door games, message boards, and file trading in a modern multiplayer environment that captures the 80s/90s spirit.
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-6" style="margin-bottom: 30px;">
                            <div class="project-card upcoming" onclick="loadProject('roadkill-v2', 'Roadkill v2', 'Upcoming Project')" style="background: #1a1a1a; border: 1px solid #444; border-radius: 8px; padding: 20px; height: 200px; transition: all 0.3s ease; cursor: pointer; position: relative; opacity: 0.9;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                    <h3 style="color: #8B4513; margin: 0; font-size: 18px;">Roadkill v2</h3>
                                    <img src="assets/images/roadkill-v2-icon.png" alt="Roadkill v2" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;" onerror="this.style.display='none'">
                                </div>
                                <p style="color: #8B7355; line-height: 1.6; margin: 0; font-size: 14px;">
                                    Complete remake of the classic with modern graphics, enhanced physics, expanded arenas, and new game modes. Everything you loved about the original, evolved for today's players.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <!-- Project Card Styles -->
                <style>
                    .project-card:hover {
                        background: #2a2a2a !important;
                        border-color: #8B4513 !important;
                        transform: translateY(-2px);
                        box-shadow: 0 8px 25px rgba(0, 200, 81, 0.15);
                    }
                    .project-card.upcoming:hover {
                        border-color: #8B4513 !important;
                        opacity: 1 !important;
                    }
                </style>

            <!-- Machine Man -->
                <div class="machine-man">
                    <div class="row">
                        <div class="boxed">
                            <div class="col-sm-8">
                                <h4 class="m-s-title">CES 2015 Control Product of the Year</h4>
                                <h1 class="m-p-title">Convert your smart device into the ultimate remote control</h1>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                    tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                                    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                    consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                                    cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                                    proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- CTA -->
                <div class="cta">
                    <div class="row">
                        <div class="boxed">
                            <div class="col-sm-9">
                                <p>
                                    If you love creating professional, beautiful and simple Robot or modify robot skills to help quickly and efficiently, then we can't wait to welcome you onto our team.
                                </p>
                            </div>
                            <div class="col-sm-3">
                                <a class="btn btn-robot btn-block" href="#">Send us your resume</a>
                            </div>
                        </div>
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

    <!-- Project Loading JavaScript -->
    <script>
        function loadProject(slug, title, category) {
            // Show the project detail container
            document.getElementById('project-detail').style.display = 'block';
            
            // Update the dynamic title and category
            document.getElementById('project-title').textContent = title;
            document.getElementById('project-category').textContent = category;
            
            // Hide the projects overview
            document.getElementById('projects-overview').style.display = 'none';
            
            // Show loading message
            document.getElementById('project-content').innerHTML = '<div style="text-align: center; padding: 40px; color: #8B7355;">Loading project details...</div>';
            
            // Load the project content via AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'projects/' + slug + '.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        // Since project files will now only contain content, directly insert the response
                        document.getElementById('project-content').innerHTML = xhr.responseText;
                    } else {
                        document.getElementById('project-content').innerHTML = '<div style="text-align: center; padding: 40px;"><p style="color: #8B7355;">Error loading project content. Please try again.</p></div>';
                    }
                    
                    // Scroll to the top of the project detail
                    document.getElementById('project-detail').scrollIntoView({ behavior: 'smooth' });
                }
            };
            xhr.send();
        }
        
        function hideProject() {
            // Hide the project detail container
            document.getElementById('project-detail').style.display = 'none';
            
            // Show the projects overview
            document.getElementById('projects-overview').style.display = 'block';
            
            // Clear the project content to prevent any lingering styles
            document.getElementById('project-content').innerHTML = '';
            
            // Scroll back to projects overview
            document.getElementById('projects-overview').scrollIntoView({ behavior: 'smooth' });
        }
    </script>

    </body>
</html>
