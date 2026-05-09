 
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Projects | Level X Development</title>

        <!-- CSS -->

        <!-- Level X Development Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">


        <!-- files -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/css/magnific-popup.css" rel="stylesheet">
        <link href="assets/css/owl.carousel.css" rel="stylesheet">
        <link href="assets/css/owl.carousel.theme.min.css" rel="stylesheet">
        <link href="assets/css/ionicons.css" rel="stylesheet">
        <!-- WDS Unified CSS - Simplified & Clean -->
        <link href="assets/css/wds-unified.css" rel="stylesheet">
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
            $page_subtitle = 'Design • Debug • Deploy';
            ?>
    <!-- Include Site Header -->
    <?php include 'includes/header.php'; ?>
        
    <!-- Include Navigation Header -->
    <?php include 'includes/navigation.php'; ?>

    <?php
    // Central JSON-based projects data
    require_once __DIR__ . '/includes/projects-data.php';

    $allProjects = loadAllProjects();

    // One more safety pass to ensure Optimization Protocol rename if legacy title exists
    $renamePerformed = false;
    foreach ($allProjects as &$p) {
        if (isset($p['title']) && stripos($p['title'], 'Infestation Control') !== false) {
            $p['title'] = 'Optimization Protocol';
            $p['shortDescription'] = 'AI was given the task to optimize the world\'s automated systems and human civilization was the part that needed the most optimization which creates a dystopian, survival sandbox in a chaotic world.';
            $p['fullDescription'] = $p['shortDescription'];
            $renamePerformed = true;
        }
    }
    unset($p);
    if ($renamePerformed) {
        saveAllProjects($allProjects);
    }

    // Normalize and group projects by category for display
    $groupedProjects = [];
    foreach ($allProjects as $project) {
        if (!isset($project['slug'])) {
            continue;
        }
        $category = isset($project['category']) && $project['category'] !== ''
            ? $project['category']
            : 'Current Projects';
        if (!isset($groupedProjects[$category])) {
            $groupedProjects[$category] = [];
        }
        $groupedProjects[$category][] = $project;
    }

    // Sort projects within each category by title (alphabetical)
    foreach ($groupedProjects as $cat => &$projectsInCat) {
        usort($projectsInCat, function ($a, $b) {
            $ta = isset($a['title']) ? $a['title'] : (isset($a['name']) ? $a['name'] : '');
            $tb = isset($b['title']) ? $b['title'] : (isset($b['name']) ? $b['name'] : '');
            return strcasecmp($ta, $tb);
        });
    }
    unset($projectsInCat);

    // Sort categories so key ones appear first (configurable order)
    $preferredOrder = getCategoryOrder();
    uksort($groupedProjects, function ($a, $b) use ($preferredOrder) {
        $ia = array_search($a, $preferredOrder, true);
        $ib = array_search($b, $preferredOrder, true);
        if ($ia === false && $ib === false) {
            return strcasecmp($a, $b);
        }
        if ($ia === false) return 1;
        if ($ib === false) return -1;
        return $ia - $ib;
    });

    // Helper to safely output HTML attributes
    function h($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    // Render a single project row (list item)
    function renderProjectRow($project) {
        $slug = h($project['slug']);
        $title = h($project['title'] ?? $project['name'] ?? $slug);
        $short = h($project['shortDescription'] ?? $project['description'] ?? '');

        // Per-project links with sensible defaults
        $defaultRead = 'https://github.com/World-Domination-Software/Projects/wiki';
        $defaultDisc = 'https://github.com/World-Domination-Software/Projects/discussions';
        $defaultIssue = 'https://github.com/World-Domination-Software/Projects/issues';

        // Preserve older githubUrl/wikiUrl as a fallback for Read more
        $readUrl = !empty($project['readMoreUrl'])
            ? $project['readMoreUrl']
            : (!empty($project['githubUrl']) ? $project['githubUrl'] : (!empty($project['wikiUrl']) ? $project['wikiUrl'] : $defaultRead));
        $discUrl = !empty($project['discussionUrl']) ? $project['discussionUrl'] : $defaultDisc;
        $issueUrl = !empty($project['issueUrl']) ? $project['issueUrl'] : $defaultIssue;

        echo '<li class="project-row" data-slug="' . $slug . '">';
        echo '  <div class="project-main">';
        echo '      <div class="project-title-text">' . $title . '</div>';
        echo '      <div class="project-short">' . $short . '</div>';
        echo '      <div class="project-links-block"><a href="' . h($readUrl) . '" target="_blank" rel="noopener noreferrer">Read more</a></div>';
        echo '      <div class="project-links-block"><a href="' . h($discUrl) . '" target="_blank" rel="noopener noreferrer">Discussion</a></div>';
        echo '      <div class="project-links-block"><a href="' . h($issueUrl) . '" target="_blank" rel="noopener noreferrer">Report Issue</a></div>';
        echo '  </div>';
        echo '</li>';
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
                                    <p class="inner-p" style="margin-top:15px;">
                                        For deeper details, every project on this page links to our public GitHub project hub, where we track design documents, roadmaps,
                                        future project ideas, bug reports, and community discussions.
                                    </p>
                                    <p class="inner-p" style="margin-top:10px;">
                                        Key GitHub resources for our projects:
                                        <br>
                                        • <a href="https://github.com/World-Domination-Software/Projects/wiki" target="_blank" rel="noopener noreferrer" style="color:#8B4513; text-decoration:underline;">Project descriptions &amp; design documents (Wiki)</a><br>
                                        • <a href="https://github.com/World-Domination-Software/Projects/discussions/categories/ideas" target="_blank" rel="noopener noreferrer" style="color:#8B4513; text-decoration:underline;">Future project ideas &amp; feature discussions (Ideas)</a><br>
                                        • <a href="https://github.com/World-Domination-Software/Projects/issues" target="_blank" rel="noopener noreferrer" style="color:#8B4513; text-decoration:underline;">Bug reports &amp; support issues (Issues)</a><br>
                                        • <a href="https://github.com/World-Domination-Software/Projects/discussions" target="_blank" rel="noopener noreferrer" style="color:#8B4513; text-decoration:underline;">General project discussions</a>
                                    </p>
                            </div>
                        </div>
                    </div>
                </div>

            <?php
            // Render each category section as a list of projects
            foreach ($groupedProjects as $categoryName => $projectsInCategory) {
                echo '<div class="row" style="margin-top: 40px;">';
                echo '  <div class="col-sm-12">';
                echo '      <div class="title-box">';
                echo '          <p>' . h($categoryName) . '</p>';
                echo '          <h2 class="title mt0">' . h($categoryName) . '</h2>';
                echo '      </div>';
                echo '  </div>';
                echo '</div>';

                echo '<div class="service">';
                echo '  <div class="row">';
                echo '      <div class="boxed">';
                echo '          <ul class="project-list">';
                foreach ($projectsInCategory as $project) {
                    renderProjectRow($project);
                }
                echo '          </ul>';
                echo '      </div>';
                echo '  </div>';
                echo '</div>';
            }
            ?>

                <!-- Project List Styles -->
                <style>
                    .project-list {
                        list-style: none;
                        padding: 0;
                        margin: 0;
                    }
                    .project-row {
                        display: flex;
                        align-items: flex-start;
                        padding: 10px 15px;
                        margin-bottom: 5px;
                        background-color: #D4CFC0;
                        border-radius: 4px;
                        border: 1px solid #B8B19F;
                    }
                    .project-row:hover {
                        background-color: #C5BEAE;
                    }
                    .project-main {
                        display: flex;
                        flex-direction: column;
                        flex: 1;
                    }
                    .project-title-text {
                        color: #8B4513;
                        font-weight: 600;
                        margin-right: 10px;
                        margin-bottom: 2px;
                    }
                    .project-short {
                        color: #1a1a1a;
                        font-size: 14px;
                        line-height: 1.6;
                        margin-top: 2px;
                    }
                    .project-links-block {
                        margin-top: 2px;
                        font-size: 13px;
                    }
                    .project-links-block a {
                        color: #8B4513;
                        text-decoration: none;
                    }
                    .project-links-block a:hover {
                        color: #6B3410;
                        text-decoration: underline;
                    }
                    /* Old detail/admin styles removed now that details live on GitHub */
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

    </body>
</html>
