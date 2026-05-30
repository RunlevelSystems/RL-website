<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">

        <title>Projects | Runlevel Systems</title>
        <meta name="description" content="Portfolio of software, infrastructure, games, and open source projects built by Runlevel Systems.">
        <meta name="keywords" content="Runlevel Systems projects, software projects, infrastructure platforms, game projects, open source">
        <link href="assets/css/coreloop.css" rel="stylesheet">
    </head>
    <body>
        <?php
        $current_page = 'projects';
        $header_class = 'projects-header inner-header';
        $page_subtitle = 'Software • Infrastructure • Games • Open Source';

        $projectCategories = [
            'Software' => [
                [
                    'title' => 'Game Server Panel',
                    'description' => 'A platform for deploying, monitoring, and managing multiplayer game servers across providers and locations.',
                    'status' => 'Production Platform',
                    'link' => 'https://github.com/GameServerPanel/GSP',
                ],
                [
                    'title' => 'GameServers.World',
                    'description' => 'Managed game server hosting offering deployment workflows, server operations support, and customer management.',
                    'status' => 'Live Service',
                    'link' => 'https://gameservers.world',
                ],
                [
                    'title' => 'Runlevel Tools',
                    'description' => 'Shared project collaboration platform for planning, repositories, builds, documentation, testing, and support workflows.',
                    'status' => 'Planning',
                    'link' => '/products.php',
                ],
            ],
            'Infrastructure' => [
                [
                    'title' => 'Game Infrastructure Services',
                    'description' => 'Infrastructure work for hosting, deployment, update delivery, and uptime support for game services.',
                    'status' => 'Active',
                    'link' => 'https://gameservers.world',
                ],
                [
                    'title' => 'Project Infrastructure Services',
                    'description' => 'Technical infrastructure support for websites, apps, APIs, and cloud-based software workflows.',
                    'status' => 'Active',
                    'link' => '/contact.php',
                ],
            ],
            'Games & Interactive' => [
                [
                    'title' => 'Neverwards',
                    'description' => 'A multiplayer game project focused on persistent progression and cooperative gameplay systems.',
                    'status' => 'Active Development',
                    'link' => 'https://store.steampowered.com/app/2096070/Neverwards/',
                ],
                [
                    'title' => 'Roadkill',
                    'description' => 'A competitive multiplayer title built around vehicular combat and online match experiences.',
                    'status' => 'Released / Ongoing Support',
                    'link' => 'https://store.steampowered.com/app/1376150/Roadkill/',
                ],
                [
                    'title' => 'Current Game Projects',
                    'description' => 'A portfolio stream of current and upcoming game concepts under active iteration.',
                    'status' => 'In Progress',
                    'link' => 'https://github.com/World-Domination-Software/Projects/discussions',
                ],
            ],
            'Open Source Projects' => [
                [
                    'title' => 'GameServerPanel / GSP',
                    'description' => 'Open source game server management codebase used as the foundation for deployment and hosting operations.',
                    'status' => 'Open Source',
                    'link' => 'https://github.com/GameServerPanel/GSP',
                ],
                [
                    'title' => 'Runlevel Systems GitHub Projects',
                    'description' => 'Public repositories, experiments, and tools that support our software and infrastructure ecosystem.',
                    'status' => 'Ongoing',
                    'link' => 'https://github.com/World-Domination-Software',
                ],
            ],
        ];
        ?>

        <?php include 'includes/header.php'; ?>
        <?php include 'includes/navigation.php'; ?>

        <main class="service-site" aria-label="Runlevel Systems project portfolio">
            <section class="service-hero compact">
                <div class="container">
                    <p class="service-kicker">Portfolio</p>
                    <h1>Projects</h1>
                    <p class="service-lead">Completed work, active builds, platforms, and technologies created by Runlevel Systems.</p>
                    <p class="hero-tagline">DESIGN • DEBUG • DEPLOY</p>
                </div>
            </section>

            <section class="service-section">
                <div class="container">
                    <p class="section-intro">This page highlights the broader Runlevel Systems portfolio.</p>
                    <p class="section-intro">It includes production platforms, active development work, infrastructure systems, games, and open source projects.</p>
                    <p class="section-intro">Games are included here as part of our development experience, not as the primary company focus.</p>

                    <?php foreach ($projectCategories as $category => $projects): ?>
                        <section class="portfolio-category">
                            <h2><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></h2>
                            <div class="service-card-grid two-columns">
                                <?php foreach ($projects as $project): ?>
                                    <?php $isExternal = strpos($project['link'], 'http') === 0; ?>
                                    <article class="service-card-item project-card-item">
                                        <div class="project-shot">Screenshot Placeholder</div>
                                        <h3><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                        <p><?php echo htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                                        <p class="project-status"><strong>Status:</strong> <?php echo htmlspecialchars($project['status'], ENT_QUOTES, 'UTF-8'); ?></p>
                                        <a class="core-action tertiary" href="<?php echo htmlspecialchars($project['link'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $isExternal ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>External Link</a>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>

        <script src="assets/js/jquery-1.12.3.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.magnific-popup.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
        <script src="assets/js/script.js"></script>
    </body>
</html>
