<?php
$current_page = 'search';
$page_subtitle = 'Search Results';
$page_description = '';
$page_title = 'Search';
$page_title_thin = 'Results';

if (!isset($_SESSION)) {
    session_start();
}

define('WDS_SYSTEM', true);
require_once 'includes/db-config.php';

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$search_results = [];
$total_results = 0;

function searchWebsiteContent($query) {
    $results = [];

    $static_pages = [
        'index.php' => ['title' => 'Home - Core Loop Development', 'description' => 'Main homepage featuring our company overview, services, and development philosophy.', 'keywords' => 'home, main, company, overview, development, software, core loop'],
        'projects.php' => ['title' => 'Projects - Development Portfolio', 'description' => 'Our complete portfolio of development projects including games and business applications.', 'keywords' => 'projects, portfolio, development, games, applications, work'],
        'joinus.php' => ['title' => 'Join Us - Career Opportunities', 'description' => 'Information about joining our development team and co-op opportunities.', 'keywords' => 'jobs, careers, employment, join, team, co-op, opportunities'],
        'contact.php' => ['title' => 'Contact - Get In Touch', 'description' => 'Contact information and ways to reach our development team.', 'keywords' => 'contact, email, phone, reach, communication, support'],
        'coop-journey.php' => ['title' => 'Co-op Journey - Development Experience', 'description' => 'Information about our cooperative development program and student opportunities.', 'keywords' => 'co-op, cooperative, student, experience, journey, program']
    ];

    $project_pages = [
        'projects/alien-apocalypse.php' => ['title' => 'Alien Apocalypse - Strategic Gaming', 'description' => 'Strategic gaming project featuring alien invasion scenarios and tactical gameplay.', 'keywords' => 'alien, apocalypse, strategy, gaming, tactical, invasion'],
        'projects/bbs-revival.php' => ['title' => 'BBS Revival - Bulletin Board System', 'description' => 'Modern revival of classic bulletin board systems with contemporary features.', 'keywords' => 'bbs, bulletin, board, system, revival, classic, modern'],
        'projects/gameservers-world.php' => ['title' => 'GameServers World - Gaming Infrastructure', 'description' => 'Gaming server infrastructure and hosting solutions for multiplayer games.', 'keywords' => 'gameservers, gaming, infrastructure, hosting, multiplayer, servers'],
        'projects/neverwards.php' => ['title' => 'Neverwards - Adventure Gaming', 'description' => 'Adventure gaming project with immersive storytelling and exploration.', 'keywords' => 'neverwards, adventure, gaming, story, exploration, immersive'],
        'projects/pureops.php' => ['title' => 'PureOps - Operations Management', 'description' => 'Business operations management system for streamlined workflows.', 'keywords' => 'pureops, operations, management, business, workflow, system'],
        'projects/roadkill-v2.php' => ['title' => 'Roadkill V2 - Enhanced Racing', 'description' => 'Enhanced version of our racing game with improved graphics and gameplay.', 'keywords' => 'roadkill, racing, enhanced, graphics, gameplay, version'],
        'projects/roadkill.php' => ['title' => 'Roadkill - Original Racing Game', 'description' => 'Original racing game project featuring high-speed action and competitive gameplay.', 'keywords' => 'roadkill, racing, original, action, competitive, speed'],
        'projects/space-4x.php' => ['title' => 'Space 4X - Strategy Gaming', 'description' => 'Space-based 4X strategy game with exploration, expansion, exploitation, and extermination.', 'keywords' => 'space, 4x, strategy, exploration, expansion, exploitation, extermination'],
        'projects/worlddomination-dev.php' => ['title' => 'World Domination Dev - Development Tools', 'description' => 'Internal development tools and utilities for project management and collaboration.', 'keywords' => 'development, tools, utilities, management, collaboration, internal']
    ];

    $all_pages = array_merge($static_pages, $project_pages);

    foreach ($all_pages as $file => $page_info) {
        $file_path = __DIR__ . '/' . $file;
        $relevance_score = 0;
        $snippet = '';

        if (file_exists($file_path)) {
            $content = file_get_contents($file_path);
            $clean_content = preg_replace('/\s+/', ' ', strip_tags($content));

            if (stripos($page_info['title'], $query) !== false) {
                $relevance_score += 10;
            }
            if (stripos($page_info['description'], $query) !== false) {
                $relevance_score += 5;
            }
            if (stripos($page_info['keywords'], $query) !== false) {
                $relevance_score += 3;
            }

            $content_matches = substr_count(strtolower($clean_content), strtolower($query));
            if ($content_matches > 0) {
                $relevance_score += $content_matches;
                $snippet_start = stripos($clean_content, $query);
                $start = max(0, $snippet_start - 100);
                $snippet = substr($clean_content, $start, 300);
                $snippet = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark>$1</mark>', $snippet);
                if ($start > 0) {
                    $snippet = '...' . $snippet;
                }
                if (strlen($clean_content) > $start + 300) {
                    $snippet .= '...';
                }
            } else {
                $snippet = $page_info['description'];
            }

            if ($relevance_score > 0) {
                $results[] = [
                    'title' => $page_info['title'],
                    'url' => $file,
                    'snippet' => $snippet,
                    'description' => $page_info['description'],
                    'relevance' => $relevance_score
                ];
            }
        }
    }

    usort($results, function($a, $b) {
        return $b['relevance'] - $a['relevance'];
    });

    return $results;
}

if (!empty($search_query)) {
    $search_results = searchWebsiteContent($search_query);
    $total_results = count($search_results);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/assets/images/icon.png">

    <title>Search Results - Core Loop Development</title>

    <link href="assets/css/coreloop.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/navigation.php'; ?>

    <section class="search-results">
        <div class="container page-bgc">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="search-form-page wds-card">
                        <form method="GET" action="search.php">
                            <div class="input-group">
                                <input type="text" name="q" class="form-control" placeholder="Search Core Loop Development..." value="<?php echo htmlspecialchars($search_query); ?>">
                                <span class="input-group-btn">
                                    <button class="btn btn-wds" type="submit"><i class="ion-search" aria-hidden="true"></i> Search</button>
                                </span>
                            </div>
                        </form>
                    </div>

                    <?php if (!empty($search_query)): ?>
                        <div class="search-stats">Found <?php echo $total_results; ?> result<?php echo $total_results !== 1 ? 's' : ''; ?> for "<?php echo htmlspecialchars($search_query); ?>"</div>

                        <?php if ($total_results > 0): ?>
                            <?php foreach ($search_results as $result): ?>
                                <div class="search-result-item wds-card">
                                    <div class="search-result-title"><a href="<?php echo htmlspecialchars($result['url']); ?>"><?php echo htmlspecialchars($result['title']); ?></a></div>
                                    <div class="search-result-snippet"><?php echo $result['snippet']; ?></div>
                                    <div class="search-result-url"><?php echo htmlspecialchars($result['url']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-results">
                                <h3>No results found</h3>
                                <p>Try different keywords or check your spelling.</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="no-results">
                            <h3>Search Core Loop Development</h3>
                            <p>Enter your search terms above to find content across our website.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
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
