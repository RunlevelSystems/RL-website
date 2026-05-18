 
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
            $page_subtitle = 'Infrastructure • Multiplayer • Operations';
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
        'gameservers-world' => 'A managed multiplayer hosting and infrastructure platform operated by RunLevel Systems. Gameservers World provides multiplayer server hosting, scalable deployment infrastructure, centralized operational tooling, Linux-based hosting environments, automated provisioning, monitoring and lifecycle management, and multi-location deployment support for customer hosting services powered by our internal platforms.',
        'gameserver-panel' => 'A commercial-grade game server management and hosting automation platform developed and operated by RunLevel Systems. GameServer Panel delivers centralized server lifecycle management, automated provisioning and deployment, remote node orchestration, multi-region infrastructure coordination, Linux-first infrastructure tooling, monitoring and control systems, customer management integration, deployment automation, update orchestration, and extensible architecture for scalable hosting operations.',
        'neverwards' => 'Multiplayer technology stack focused on synchronized gameplay systems, persistent world architecture, and service-backed progression systems for large-scale online environments.',
        'roadkill' => 'Cross-platform multiplayer systems initiative built around synchronized simulation, distributed gameplay infrastructure, and real-time session orchestration.',
        'castle-walls' => 'Multiplayer simulation platform centered on persistent systems architecture, synchronized combat state, and high-throughput online session performance.',
        'mystical-islands' => 'Persistent world architecture initiative for distributed multiplayer gameplay, service-backed progression, and long-lived online ecosystem operations.',
        'retro-space-blaster' => 'Real-time multiplayer technology platform optimized for high-frequency interaction loops, synchronized systems, and cross-platform online consistency.',
        'pureops' => 'Infrastructure automation and operational governance platform for deployment orchestration, environment standardization, provisioning workflows, and scalable backend operations.',
        'alien-apocalypse' => 'Internal R&D initiative exploring large-scale autonomous optimization models across distributed simulation systems.',
        'space5x' => 'Internal R&D systems research focused on advanced simulation strategy, distributed state control, and long-horizon platform design.',
        'bbs-revival' => 'A modernized communication and community platform inspired by legacy distributed systems architecture.',
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
            'title' => 'Hosting & Infrastructure Platforms',
            'kicker' => 'Primary Commercial Offerings',
            'description' => 'RunLevel Systems operates production infrastructure platforms for multiplayer hosting services and enterprise-grade orchestration.',
            'slugs' => ['gameservers-world', 'gameserver-panel'],
        ],
        [
            'title' => 'Multiplayer Game Technologies',
            'kicker' => 'Multiplayer Ecosystems',
            'description' => 'Our multiplayer initiatives are developed as technology platforms with service-backed systems, synchronization architecture, and scalable online operations.',
            'slugs' => ['neverwards', 'roadkill', 'castle-walls', 'mystical-islands', 'retro-space-blaster'],
        ],
        [
            'title' => 'Infrastructure & Automation',
            'kicker' => 'Operations Tooling',
            'description' => 'Internal platform engineering and automation systems that support reliable deployment, governance, and service operations.',
            'slugs' => ['pureops'],
        ],
        [
            'title' => 'Research & Development',
            'kicker' => 'Internal R&D',
            'description' => 'These are internal R&D initiatives and experimental systems research programs.',
            'slugs' => ['alien-apocalypse', 'space5x'],
        ],
        [
            'title' => 'Community & Communication Systems',
            'kicker' => 'Community Platforms',
            'description' => 'Communication and community platform technologies that support distributed engagement and long-term ecosystem growth.',
            'slugs' => ['bbs-revival'],
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
        $defaultDisc = 'https://github.com/World-Domination-Software/Projects/discussions';
        $defaultIssue = 'https://github.com/World-Domination-Software/Projects/issues';

        // Preserve older githubUrl/wikiUrl as a fallback for documentation links
        $readUrl = !empty($project['readMoreUrl'])
            ? $project['readMoreUrl']
            : (!empty($project['githubUrl']) ? $project['githubUrl'] : (!empty($project['wikiUrl']) ? $project['wikiUrl'] : $defaultRead));
        $discUrl = !empty($project['discussionUrl']) ? $project['discussionUrl'] : $defaultDisc;
        $platformUrl = !empty($project['platformUrl']) ? $project['platformUrl'] : $discUrl;
        $notesUrl = !empty($project['technicalNotesUrl']) ? $project['technicalNotesUrl'] : $discUrl;
        $issueUrl = !empty($project['issueUrl']) ? $project['issueUrl'] : $defaultIssue;

        echo '<li class="project-row" data-slug="' . $slug . '">';
        echo '  <div class="project-main">';
        echo '      <div class="project-title-text">' . $title . '</div>';
        echo '      <div class="project-short">' . $short . '</div>';
        echo '      <div class="project-actions">';
        echo '          <a class="project-action" href="' . h($readUrl) . '" target="_blank" rel="noopener noreferrer">Documentation</a>';
        echo '          <a class="project-action" href="' . h($platformUrl) . '" target="_blank" rel="noopener noreferrer">Platform Details</a>';
        echo '          <a class="project-action" href="' . h($notesUrl) . '" target="_blank" rel="noopener noreferrer">Technical Notes</a>';
        echo '          <a class="project-action" href="' . h($issueUrl) . '" target="_blank" rel="noopener noreferrer">Support</a>';
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
                                    RunLevel Systems develops and operates commercial infrastructure platforms, multiplayer technologies, and hosting systems.
                                </p>
                                <p class="inner-p" style="margin-top:15px;">
                                    Our customer-facing business is led by Gameservers World and GameServer Panel, supported by internal automation platforms,
                                    multiplayer ecosystem engineering, and focused systems research.
                                </p>
                                <p class="inner-p" style="margin-top:10px;">
                                    Core resources:
                                    <br>
                                    • <a href="https://github.com/World-Domination-Software/Projects/wiki" target="_blank" rel="noopener noreferrer" style="color:var(--core-cyan); text-decoration:underline;">Documentation</a><br>
                                    • <a href="https://github.com/World-Domination-Software/Projects/discussions" target="_blank" rel="noopener noreferrer" style="color:var(--core-cyan); text-decoration:underline;">Platform Details &amp; Technical Notes</a><br>
                                    • <a href="https://github.com/World-Domination-Software/Projects/issues" target="_blank" rel="noopener noreferrer" style="color:var(--core-cyan); text-decoration:underline;">Support</a>
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
                        max-width: 900px;
                        line-height: 1.7;
                    }
                    .project-row {
                        display: flex;
                        align-items: flex-start;
                        padding: 20px 22px;
                        margin-bottom: 18px;
                        background: linear-gradient(180deg, #0d1a33, #0b1830);
                        border-radius: 12px;
                        border: 1px solid rgba(54,243,255,0.28);
                        box-shadow: 0 0 0 1px rgba(54,243,255,0.08), 0 10px 24px rgba(0,0,0,0.22);
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
                        letter-spacing: 0.01em;
                        margin-right: 10px;
                        margin-bottom: 6px;
                    }
                    .project-short {
                        color: #a8bedc;
                        font-size: 14px;
                        line-height: 1.7;
                        margin-top: 2px;
                    }
                    .project-actions {
                        margin-top: 14px;
                        display: flex;
                        flex-wrap: wrap;
                        gap: 8px;
                    }
                    .project-action {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        border: 1px solid rgba(54,243,255,0.45);
                        border-radius: 6px;
                        padding: 6px 12px;
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
                        }
                        .project-actions {
                            gap: 7px;
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
