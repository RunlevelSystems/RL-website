 
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
        'gameservers-world' => 'Gameservers World is the live production hosting environment operated by RunLevel Systems using our internally developed GameServer Panel platform. It provides multiplayer game server hosting, customer environment management, automated deployment and management workflows, Linux-based hosting operations, and scalable support for online gaming communities.',
        'gameserver-panel' => 'A commercial-grade game server management and hosting automation platform developed by RunLevel Systems. GameServer Panel provides centralized server management, automated deployment, remote node management, monitoring and lifecycle control, multi-location infrastructure support, customer hosting integration, and Linux-first operational tooling.',
        'pureops' => 'PureOps is a business operations and management simulation platform focused on organizational decision making, operational strategy, workflow optimization, and systems-based training environments.',
        'neverwards' => 'A multiplayer online game world focused on cooperative play, character progression, and persistent shared environments.',
        'roadkill' => 'A fast-paced multiplayer vehicular combat game built for competitive online sessions and team-based action.',
        'castle-walls' => 'A multiplayer strategy and combat game centered on base defense, coordinated battles, and persistent online progression.',
        'mystical-islands' => 'A multiplayer fantasy adventure game featuring cooperative exploration, progression systems, and persistent online islands.',
        'space5x' => 'A multiplayer sci-fi strategy and simulation game with persistent progression, economic planning, and cooperative long-term play.',
        'retro-space-blaster' => 'An arcade-inspired, fast-paced mobile action game designed for cross-platform gameplay and quick-session combat.',
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
            'kicker' => 'Primary Business Category',
            'description' => 'Our core business is commercial multiplayer hosting infrastructure operated in live production environments.',
            'slugs' => ['gameservers-world', 'gameserver-panel'],
        ],
        [
            'title' => 'Business Simulation & Training',
            'kicker' => 'Operations Learning Tools',
            'description' => 'Simulation software for management training, systems thinking, and operational decision making.',
            'slugs' => ['pureops'],
        ],
        [
            'title' => 'Multiplayer Games',
            'kicker' => 'Active Game Development',
            'description' => 'Online multiplayer games and persistent worlds developed for cooperative, competitive, and strategy-focused gameplay.',
            'slugs' => ['neverwards', 'roadkill', 'castle-walls', 'mystical-islands', 'space5x'],
        ],
        [
            'title' => 'Mobile Games',
            'kicker' => 'Action-Focused Mobile Play',
            'description' => 'Arcade-inspired game experiences designed for fast, responsive cross-platform mobile gameplay.',
            'slugs' => ['retro-space-blaster'],
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
                                    RunLevel Systems develops and operates commercial hosting software, business simulation tools, and online games.
                                </p>
                                <p class="inner-p" style="margin-top:15px;">
                                    Our primary commercial offerings are Gameservers World and GameServer Panel. We also build PureOps for business simulation
                                    and training, alongside active multiplayer and mobile game projects.
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
