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
        <!-- Font Awesome for GameServer Panel icons -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

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

    <?php
    // Scan projects directory and load project metadata
    $projectsDir = __DIR__ . '/projects';
    $projects = [];
    
    if (is_dir($projectsDir)) {
        $directories = array_diff(scandir($projectsDir), ['.', '..']);
        
        foreach ($directories as $dir) {
            $dirPath = $projectsDir . '/' . $dir;
            if (is_dir($dirPath)) {
                $metadataFile = $dirPath . '/project.json';
                if (file_exists($metadataFile)) {
                    $metadata = json_decode(file_get_contents($metadataFile), true);
                    if ($metadata && !isset($metadata['hidden'])) {
                        $metadata['slug'] = $dir;
                        $projects[] = $metadata;
                    }
                }
            }
        }
    }
    
    // Group projects by category
    $categorizedProjects = [
        'Current Project' => [],
        'Legacy Project' => [],
        'Upcoming Project' => []
    ];
    
    foreach ($projects as $project) {
        $category = $project['category'] ?? 'Current Project';
        if (isset($categorizedProjects[$category])) {
            $categorizedProjects[$category][] = $project;
        }
    }
    ?>

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

            <?php
            // Function to render project card
            function renderProjectCard($project, $isUpcoming = false) {
                $slug = htmlspecialchars($project['slug']);
                $title = htmlspecialchars($project['title']);
                $category = htmlspecialchars($project['category']);
                $description = htmlspecialchars($project['description']);
                
                $cardClass = $isUpcoming ? 'project-card upcoming' : 'project-card';
                $borderColor = $isUpcoming ? '#444' : '#333';
                $opacity = $isUpcoming ? 'opacity: 0.9;' : '';
                
                echo '<div class="col-sm-6" style="margin-bottom: 30px;">';
                echo '<div class="' . $cardClass . '" onclick="loadProject(\'' . $slug . '\', \'' . $title . '\', \'' . $category . '\')" style="background: #1a1a1a; border: 1px solid ' . $borderColor . '; border-radius: 8px; padding: 20px; height: 200px; transition: all 0.3s ease; cursor: pointer; position: relative; ' . $opacity . '">';
                echo '<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">';
                echo '<h3 style="color: #8B4513; margin: 0; font-size: 18px;">' . $title . '</h3>';
                
                // Handle icon rendering
                if (isset($project['iconType']) && $project['iconType'] === 'fontawesome') {
                    echo '<div style="width: 40px; height: 40px; background: #8B4513; border-radius: 4px; display: flex; align-items: center; justify-content: center;">';
                    echo '<i class="fas ' . htmlspecialchars($project['icon']) . '" style="color: #D2B48C; font-size: 20px;"></i>';
                    echo '</div>';
                } else {
                    $icon = htmlspecialchars($project['icon'] ?? '');
                    echo '<img src="' . $icon . '" alt="' . $title . '" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;" onerror="this.style.display=\'none\'">';
                }
                
                echo '</div>';
                echo '<p style="color: #8B7355; line-height: 1.6; margin: 0; font-size: 14px;">';
                echo $description;
                echo '</p>';
                echo '</div>';
                echo '</div>';
            }
            
            // Render Current Projects
            if (!empty($categorizedProjects['Current Project'])) {
                echo '<!-- Current Projects -->';
                echo '<div class="service">';
                echo '<div class="row">';
                echo '<div class="boxed">';
                foreach ($categorizedProjects['Current Project'] as $project) {
                    renderProjectCard($project, false);
                }
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            
            // Render Legacy Projects
            if (!empty($categorizedProjects['Legacy Project'])) {
                echo '<!-- Legacy Projects Section -->';
                echo '<div class="row" style="margin-top: 60px;">';
                echo '<div class="col-sm-12">';
                echo '<div class="title-box">';
                echo '<p>Our legacy</p>';
                echo '<h2 class="title mt0">Legacy Projects</h2>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="service">';
                echo '<div class="row">';
                echo '<div class="boxed">';
                foreach ($categorizedProjects['Legacy Project'] as $project) {
                    renderProjectCard($project, false);
                }
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            
            // Render Upcoming Projects
            if (!empty($categorizedProjects['Upcoming Project'])) {
                echo '<!-- Upcoming Projects Section -->';
                echo '<div class="row" style="margin-top: 60px;">';
                echo '<div class="col-sm-12">';
                echo '<div class="title-box">';
                echo '<p>Coming soon</p>';
                echo '<h2 class="title mt0">Upcoming Projects</h2>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="service">';
                echo '<div class="row">';
                echo '<div class="boxed">';
                foreach ($categorizedProjects['Upcoming Project'] as $project) {
                    renderProjectCard($project, true);
                }
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>

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
            // Special handling for GameServer Panel - redirect to full page
            if (slug === 'gameserver-panel') {
                window.location.href = 'projects/gameserver-panel/index.php';
                return;
            }
            
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
            xhr.open('GET', 'projects/' + slug + '/index.php', true);
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
