 
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">

    <title>Projects | Runlevel Systems</title>

        <!-- CSS -->
        <link href="assets/css/coreloop.css" rel="stylesheet">

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
            $page_subtitle = 'Hosting • Simulation • Games';
            ?>
    <!-- Include Site Header -->
    <?php include 'includes/header.php'; ?>
        
    <!-- Include Navigation Header -->
    <?php include 'includes/navigation.php'; ?>

    <?php
    // Central JSON-based projects data
    require_once __DIR__ . '/includes/projects-data.php';

    $allProjects = loadAllProjects();

    $projectsBySlug = [];
    foreach ($allProjects as $project) {
        if (!empty($project['slug'])) {
            $projectsBySlug[(string)$project['slug']] = $project;
        }
    }

    $projectContentOverrides = [
        'gameservers-world' => 'Gameservers World is our live game server hosting business. We run it ourselves using GameServer Panel — our own hosting management software. Customers rent multiplayer game servers that we deploy and manage on Linux-based infrastructure. Everything from provisioning to day-to-day management is handled through our own platform.',
        'gameserver-panel' => 'GameServer Panel is our commercial game server management platform. It gives hosting providers a single control panel to deploy servers, automate installs and updates, manage remote nodes, and monitor their entire infrastructure. It\'s Linux-first and built around the real workflows of running a game server hosting business.',
        'pureops' => 'PureOps is a business simulation and training platform. It puts players in charge of managing a virtual organization, making operational decisions, and dealing with realistic business challenges. It\'s designed for training environments where hands-on decision-making matters more than reading a manual.',
        'neverwards' => 'Neverwards is a multiplayer online game with cooperative play, character progression, and a persistent shared world. Players explore, build, and advance together in a world that keeps running between sessions.',
        'roadkill' => 'Roadkill is a fast-paced multiplayer vehicular combat game. Players compete in destructive online matches with team-based objectives and quick, action-heavy sessions.',
        'castle-walls' => 'Castle Walls is a multiplayer strategy game built around base defense and coordinated team combat. Players build up defenses and fight back against attackers in persistent online matches.',
        'mystical-islands' => 'Mystical Islands is a multiplayer fantasy adventure game. Players explore islands together, unlock progression systems, and share a persistent online world that grows over time.',
        'space5x' => 'Space5X is a multiplayer sci-fi strategy game with long-term economic planning, cooperative play, and persistent universe progression. Sessions can last weeks as players build empires and compete for control.',
        'retro-space-blaster' => 'Retro Space Blaster is an arcade-style mobile action game built for quick sessions and fast-paced gameplay. Designed for cross-platform mobile play with simple controls and satisfying combat.',
        'be-very-very-quiet' => 'Be Very Very Quiet is a fast-paced zombie survival mobile game. Your job is to rescue survivors and reach the evacuation helicopter — but every gunshot attracts every zombie in the area. Use weapons when you have to, then run. Swarms grow quickly, and keeping the survivors alive while making it out yourself is harder than it sounds.',
    ];

    foreach ($projectContentOverrides as $slug => $description) {
        if (!isset($projectsBySlug[$slug])) {
            continue;
        }
        $projectsBySlug[$slug]['shortDescription'] = $description;
        $projectsBySlug[$slug]['fullDescription'] = $description;
    }

    $sections = [
        [
            'title' => 'Hosting & Infrastructure',
            'kicker' => 'Our Core Business',
            'description' => 'We host multiplayer game servers commercially and build the management platform that powers it all.',
            'slugs' => ['gameservers-world', 'gameserver-panel'],
        ],
        [
            'title' => 'Business Simulation & Training',
            'kicker' => 'Operational Training Tools',
            'description' => 'Simulation software that puts people in realistic management scenarios to develop practical decision-making skills.',
            'slugs' => ['pureops'],
        ],
        [
            'title' => 'Multiplayer Games',
            'kicker' => 'Active Game Development',
            'description' => 'Online multiplayer games we are actively developing — covering cooperative, competitive, and strategy gameplay.',
            'slugs' => ['neverwards', 'roadkill', 'castle-walls', 'mystical-islands', 'space5x'],
        ],
        [
            'title' => 'Mobile Games',
            'kicker' => 'Mobile Game Development',
            'description' => 'Fast-paced arcade games built for mobile — quick to pick up, hard to put down.',
            'slugs' => ['retro-space-blaster', 'be-very-very-quiet'],
        ],
    ];

    // Helper to safely output HTML attributes
    function h($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    // Render a single platform row (list item)
    function renderProjectRow($project) {
        $slug = h($project['slug']);
        $title = h($project['title'] ?? $project['name'] ?? $slug);
        $short = h($project['shortDescription'] ?? $project['description'] ?? '');

        // Per-project links with sensible defaults
        $defaultRead = 'https://github.com/World-Domination-Software/Projects/wiki';
        $readUrl = !empty($project['readMoreUrl'])
            ? $project['readMoreUrl']
            : (!empty($project['githubUrl']) ? $project['githubUrl'] : (!empty($project['wikiUrl']) ? $project['wikiUrl'] : $defaultRead));

        echo '<li class="project-row" data-slug="' . $slug . '">';
        echo '  <div class="project-main">';
        echo '      <div class="project-title-text">' . $title . '</div>';
        echo '      <div class="project-short">' . $short . '</div>';
        echo '      <div class="project-actions">';
        echo '          <a class="project-action" href="' . h($readUrl) . '" target="_blank" rel="noopener noreferrer">Learn More</a>';
        echo '      </div>';
        echo '  </div>';
        echo '</li>';
    }
    ?>

    <!-- Projects -->
        <section class="about">
            <div class="container page-bgc">
                <div id="projects-overview">
                    <div class="row">
                        <div class="boxed">
                            <div class="col-sm-12">
                                <p class="inner-p">
                                    RunLevel Systems develops and operates hosting software, business simulation tools, and online games.
                                </p>
                                <p class="inner-p" style="margin-top:15px;">
                                    Our main commercial products are Gameservers World (our live hosting service) and GameServer Panel (the management platform behind it). We also build PureOps for business simulation and training, plus a range of multiplayer and mobile game projects.
                                </p>
                                <p class="inner-p" style="margin-top:10px;">
                                    Project resources:
                                    <br>
                                    • <a href="https://github.com/World-Domination-Software/Projects/wiki" target="_blank" rel="noopener noreferrer" style="color:var(--core-cyan); text-decoration:underline;">Project Documentation</a><br>
                                    • <a href="https://github.com/World-Domination-Software/Projects/discussions" target="_blank" rel="noopener noreferrer" style="color:var(--core-cyan); text-decoration:underline;">Ideas &amp; Discussions</a><br>
                                    • <a href="https://github.com/World-Domination-Software/Projects/issues" target="_blank" rel="noopener noreferrer" style="color:var(--core-cyan); text-decoration:underline;">Bug Reports &amp; Support</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php foreach ($sections as $section): ?>
                    <div class="portfolio-section">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="title-box">
                                    <p class="section-kicker"><?php echo h($section['kicker']); ?></p>
                                    <h2 class="title mt0"><?php echo h($section['title']); ?></h2>
                                    <p class="section-description"><?php echo h($section['description']); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="service">
                            <div class="row">
                                <div class="boxed">
                                    <ul class="project-list">
                                        <?php foreach ($section['slugs'] as $slug): ?>
                                            <?php if (isset($projectsBySlug[$slug])): ?>
                                                <?php renderProjectRow($projectsBySlug[$slug]); ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Project List Styles -->
                <style>
                    .project-list {
                        list-style: none;
                        padding: 0;
                        margin: 0;
                    }
                    .portfolio-section {
                        margin-top: 64px;
                    }
                    .section-kicker {
                        letter-spacing: 0.08em;
                        text-transform: uppercase;
                        font-size: 12px;
                    }
                    .section-description {
                        color: #a8bedc;
                        max-width: 880px;
                        line-height: 1.7;
                        margin-bottom: 0;
                    }
                    .project-row {
                        display: flex;
                        align-items: flex-start;
                        padding: 22px 24px;
                        margin-bottom: 18px;
                        background: linear-gradient(180deg, #0d1a33, #0b1830);
                        border-radius: 14px;
                        border: 1px solid rgba(54,243,255,0.28);
                        box-shadow: 0 0 0 1px rgba(54,243,255,0.08), 0 12px 28px rgba(0,0,0,0.22);
                        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
                    }
                    .project-row:hover {
                        border-color: rgba(54,243,255,0.54);
                        box-shadow: 0 0 0 1px rgba(54,243,255,0.14), 0 12px 28px rgba(0,0,0,0.28), 0 0 20px rgba(54,243,255,0.16);
                        transform: translateY(-2px);
                    }
                    .project-main {
                        display: flex;
                        flex-direction: column;
                        flex: 1;
                    }
                    .project-title-text {
                        color: var(--core-cyan);
                        font-weight: 700;
                        font-size: 1.08rem;
                        letter-spacing: 0.01em;
                        margin-right: 10px;
                        margin-bottom: 8px;
                    }
                    .project-short {
                        color: #a8bedc;
                        font-size: 15px;
                        line-height: 1.75;
                        margin-top: 2px;
                    }
                    .project-actions {
                        margin-top: 16px;
                        display: flex;
                        flex-wrap: wrap;
                        gap: 8px;
                    }
                    .project-action {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        border: 1px solid rgba(54,243,255,0.45);
                        border-radius: 7px;
                        padding: 7px 14px;
                        font-size: 12px;
                        font-weight: 600;
                        color: #dff9ff;
                        background: rgba(11, 24, 48, 0.92);
                        text-decoration: none;
                        transition: border-color 0.2s ease, color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
                    }
                    .project-action:hover,
                    .project-action:focus {
                        border-color: rgba(255, 198, 0, 0.76);
                        color: #ffcf66;
                        background: rgba(16, 31, 57, 0.98);
                        box-shadow: 0 0 14px rgba(54,243,255,0.18);
                        text-decoration: none;
                    }
                    @media (max-width: 768px) {
                        .portfolio-section {
                            margin-top: 48px;
                        }
                        .project-row {
                            padding: 18px 16px;
                            margin-bottom: 14px;
                        }
                        .project-actions {
                            gap: 7px;
                        }
                        .project-short {
                            font-size: 14px;
                            line-height: 1.7;
                        }
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
