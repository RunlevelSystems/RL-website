<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/icon.png">

        <title>Projects | Core Loop Development</title>

        <link href="assets/css/coreloop.css" rel="stylesheet">
    </head>
    <body>
        <?php
        $current_page = 'projects';
        $header_class = 'projects-header inner-header';
        $page_subtitle = 'Engineer • Ship • Scale';

        include 'includes/header.php';
        include 'includes/navigation.php';

        require_once __DIR__ . '/includes/projects-data.php';
        $allProjects = loadAllProjects();

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

        $groupedProjects = [];
        foreach ($allProjects as $project) {
            if (!isset($project['slug'])) {
                continue;
            }
            $category = isset($project['category']) && $project['category'] !== '' ? $project['category'] : 'Current Projects';
            if (!isset($groupedProjects[$category])) {
                $groupedProjects[$category] = [];
            }
            $groupedProjects[$category][] = $project;
        }

        foreach ($groupedProjects as $cat => &$projectsInCat) {
            usort($projectsInCat, function ($a, $b) {
                $ta = isset($a['title']) ? $a['title'] : (isset($a['name']) ? $a['name'] : '');
                $tb = isset($b['title']) ? $b['title'] : (isset($b['name']) ? $b['name'] : '');
                return strcasecmp($ta, $tb);
            });
        }
        unset($projectsInCat);

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

        function h($value) {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }

        function renderProjectRow($project) {
            $slug = h($project['slug']);
            $title = h($project['title'] ?? $project['name'] ?? $slug);
            $short = h($project['shortDescription'] ?? $project['description'] ?? '');

            $defaultRead = 'https://github.com/World-Domination-Software/Projects/wiki';
            $defaultDisc = 'https://github.com/World-Domination-Software/Projects/discussions';
            $defaultIssue = 'https://github.com/World-Domination-Software/Projects/issues';

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

        <section class="about">
            <div class="container page-bgc">
                <div id="project-detail" class="hidden">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="title-box">
                                <a href="#" onclick="hideProject(); return false;" id="back-to-projects">← Back to All Projects</a>
                                <p id="project-category">Current Project</p>
                                <h2 class="title mt0" id="project-title">Project Title</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="boxed">
                            <div class="col-sm-12" id="project-content"></div>
                        </div>
                    </div>
                </div>

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
                                <p class="inner-p">Discover our active and planned projects in game development and technology.</p>
                                <p class="inner-p">For deeper details, every project on this page links to our public GitHub project hub, where we track design documents, roadmaps, ideas, bug reports, and community discussions.</p>
                                <p class="inner-p">
                                    • <a href="https://github.com/World-Domination-Software/Projects/wiki" target="_blank" rel="noopener noreferrer">Project descriptions &amp; design documents (Wiki)</a><br>
                                    • <a href="https://github.com/World-Domination-Software/Projects/discussions/categories/ideas" target="_blank" rel="noopener noreferrer">Future project ideas &amp; feature discussions (Ideas)</a><br>
                                    • <a href="https://github.com/World-Domination-Software/Projects/issues" target="_blank" rel="noopener noreferrer">Bug reports &amp; support issues (Issues)</a><br>
                                    • <a href="https://github.com/World-Domination-Software/Projects/discussions" target="_blank" rel="noopener noreferrer">General project discussions</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                foreach ($groupedProjects as $categoryName => $projectsInCategory) {
                    echo '<div class="row">';
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
            </div>
        </section>

        <?php include 'includes/footer.php'; ?>

        <script src="assets/js/jquery-1.12.3.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.magnific-popup.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
        <script src="assets/js/script.js"></script>
    </body>
</html>
