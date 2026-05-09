<?php
// Page-specific variables
$current_page = 'search';
$page_subtitle = 'Search Results';
$page_description = '';
$page_title = 'Search';
$page_title_thin = 'Results';

// Initialize session
if (!isset($_SESSION)) {
    session_start();
}

// Include database configuration
define('WDS_SYSTEM', true);
require_once 'includes/db-config.php';

// Get search query
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$search_results = [];
$total_results = 0;

/**
 * Enhanced search function that searches through all website content
 */
function searchWebsiteContent($query) {
    $results = [];
    
    // Define searchable pages with better descriptions
    $static_pages = [
        'index.php' => [
            'title' => 'Home - Level X Development',
            'description' => 'Main homepage featuring our company overview, services, and development philosophy.',
            'keywords' => 'home, main, company, overview, development, software, world domination'
        ],
        'projects.php' => [
            'title' => 'Projects - Development Portfolio', 
            'description' => 'Our complete portfolio of development projects including games and business applications.',
            'keywords' => 'projects, portfolio, development, games, applications, work'
        ],
        'joinus.php' => [
            'title' => 'Join Us - Career Opportunities',
            'description' => 'Information about joining our development team and co-op opportunities.',
            'keywords' => 'jobs, careers, employment, join, team, co-op, opportunities'
        ],
        'contact.php' => [
            'title' => 'Contact - Get In Touch',
            'description' => 'Contact information and ways to reach our development team.',
            'keywords' => 'contact, email, phone, reach, communication, support'
        ],
        'coop-journey.php' => [
            'title' => 'Co-op Journey - Development Experience',
            'description' => 'Information about our cooperative development program and student opportunities.',
            'keywords' => 'co-op, cooperative, student, experience, journey, program'
        ]
    ];
    
    // Define project pages with detailed information
    $project_pages = [
        'projects/alien-apocalypse.php' => [
            'title' => 'Alien Apocalypse - Strategic Gaming',
            'description' => 'Strategic gaming project featuring alien invasion scenarios and tactical gameplay.',
            'keywords' => 'alien, apocalypse, strategy, gaming, tactical, invasion'
        ],
        'projects/bbs-revival.php' => [
            'title' => 'BBS Revival - Bulletin Board System',
            'description' => 'Modern revival of classic bulletin board systems with contemporary features.',
            'keywords' => 'bbs, bulletin, board, system, revival, classic, modern'
        ],
        'projects/gameservers-world.php' => [
            'title' => 'GameServers World - Gaming Infrastructure',
            'description' => 'Gaming server infrastructure and hosting solutions for multiplayer games.',
            'keywords' => 'gameservers, gaming, infrastructure, hosting, multiplayer, servers'
        ],
        'projects/neverwards.php' => [
            'title' => 'Neverwards - Adventure Gaming',
            'description' => 'Adventure gaming project with immersive storytelling and exploration.',
            'keywords' => 'neverwards, adventure, gaming, story, exploration, immersive'
        ],
        'projects/pureops.php' => [
            'title' => 'PureOps - Operations Management',
            'description' => 'Business operations management system for streamlined workflows.',
            'keywords' => 'pureops, operations, management, business, workflow, system'
        ],
        'projects/roadkill-v2.php' => [
            'title' => 'Roadkill V2 - Enhanced Racing',
            'description' => 'Enhanced version of our racing game with improved graphics and gameplay.',
            'keywords' => 'roadkill, racing, enhanced, graphics, gameplay, version'
        ],
        'projects/roadkill.php' => [
            'title' => 'Roadkill - Original Racing Game',
            'description' => 'Original racing game project featuring high-speed action and competitive gameplay.',
            'keywords' => 'roadkill, racing, original, action, competitive, speed'
        ],
        'projects/space-4x.php' => [
            'title' => 'Space 4X - Strategy Gaming',
            'description' => 'Space-based 4X strategy game with exploration, expansion, exploitation, and extermination.',
            'keywords' => 'space, 4x, strategy, exploration, expansion, exploitation, extermination'
        ],
        'projects/worlddomination-dev.php' => [
            'title' => 'World Domination Dev - Development Tools',
            'description' => 'Internal development tools and utilities for project management and collaboration.',
            'keywords' => 'development, tools, utilities, management, collaboration, internal'
        ],
        'projects/gameserver-panel.php' => [
            'title' => 'GameServer Panel - Open Game Panel Fork',
            'description' => 'Enhanced fork of Open Game Panel (OGP) with commercial billing, multi-location management, and enterprise features for game hosting providers.',
            'keywords' => 'gameserver, panel, ogp, open, game, hosting, billing, commercial, enterprise, server, management'
        ],
        'projects/gameserver-panel-industry-stats.php' => [
            'title' => 'Gaming Industry Statistics 2025 - Market Analysis',
            'description' => '2025 gaming industry market analysis with interactive charts showing $268B market size, platform distribution, demographics, and hosting opportunities.',
            'keywords' => 'gaming, industry, statistics, market, analysis, charts, revenue, demographics, hosting, opportunities'
        ]
    ];
    
    $all_pages = array_merge($static_pages, $project_pages);
    
    foreach ($all_pages as $file => $page_info) {
        $file_path = __DIR__ . '/' . $file;
        $relevance_score = 0;
        $snippet = '';
        
        if (file_exists($file_path)) {
            $content = file_get_contents($file_path);
            $clean_content = strip_tags($content);
            $clean_content = preg_replace('/\s+/', ' ', $clean_content);
            
            // Check title match (highest priority)
            if (stripos($page_info['title'], $query) !== false) {
                $relevance_score += 10;
            }
            
            // Check description match (medium priority)
            if (stripos($page_info['description'], $query) !== false) {
                $relevance_score += 5;
            }
            
            // Check keywords match (medium priority)
            if (stripos($page_info['keywords'], $query) !== false) {
                $relevance_score += 3;
            }
            
            // Check content match (lower priority but important)
            $content_matches = substr_count(strtolower($clean_content), strtolower($query));
            if ($content_matches > 0) {
                $relevance_score += $content_matches;
                
                // Extract snippet around the first match
                $snippet_start = stripos($clean_content, $query);
                $start = max(0, $snippet_start - 100);
                $snippet = substr($clean_content, $start, 300);
                $snippet = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark>$1</mark>', $snippet);
                if ($start > 0) $snippet = '...' . $snippet;
                if (strlen($clean_content) > $start + 300) $snippet .= '...';
            } else {
                $snippet = $page_info['description'];
            }
            
            // If we have any relevance, add to results
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
    
    // Sort results by relevance score (highest first)
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

    <title>Search Results - Level X Development</title>

    <!-- CSS -->
    
    
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/magnific-popup.css" rel="stylesheet">
    <link href="assets/css/owl.carousel.css" rel="stylesheet">
    <link href="assets/css/owl.carousel.theme.min.css" rel="stylesheet">
    <link href="assets/css/ionicons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/wds-unified.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .search-results {
            background-color: #E8E4D8;
            color: #1a1a1a;
            padding: 40px 0;
            min-height: 500px;
        }
        
        .search-result-item {
            background-color: transparent;
            border: none;
            border-radius: 0;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .search-result-item:hover {
            transform: translateY(-2px);
        }
        
        .search-result-title {
            color: #8B4513;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .search-result-title a {
            color: #8B4513;
            text-decoration: none;
        }
        
        .search-result-title a:hover {
            color: #D2B48C;
            text-decoration: underline;
        }
        
        .search-result-snippet {
            color: #1a1a1a;
            line-height: 1.6;
            font-size: 14px;
        }
        
        .search-result-url {
            color: #8B7355;
            font-size: 12px;
            margin-top: 10px;
        }
        
        .search-stats {
            color: #6B3410;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .no-results {
            text-align: center;
            color: #4a4a4a;
            font-size: 16px;
            padding: 50px 0;
        }
        
        .search-form-page {
            background-color: transparent;
            padding: 20px;
            border-radius: 0;
            margin-bottom: 30px;
        }
        
        mark {
            background-color: #8B4513;
            color: white;
            padding: 2px 4px;
            border-radius: 2px;
        }
    </style>
</head>

<body>
    <!-- Include Site Header -->
    <?php include 'includes/header.php'; ?>
    
    <!-- Include Navigation Header -->
    <?php include 'includes/navigation.php'; ?>

    <section class="search-results">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <!-- Search Form -->
                    <div class="search-form-page wds-card">
                        <form method="GET" action="search.php">
                            <div class="input-group">
                                <input type="text" name="q" class="form-control" placeholder="Search Level X Development..." value="<?php echo htmlspecialchars($search_query); ?>">
                                <span class="input-group-btn">
                                    <button class="btn btn-default btn-wds" type="submit">
                                        <i class="ion-search" aria-hidden="true"></i> Search
                                    </button>
                                </span>
                            </div>
                        </form>
                    </div>

                    <?php if (!empty($search_query)): ?>
                        <!-- Search Statistics -->
                        <div class="search-stats">
                            Found <?php echo $total_results; ?> result<?php echo $total_results !== 1 ? 's' : ''; ?> for "<?php echo htmlspecialchars($search_query); ?>"
                        </div>

                        <?php if ($total_results > 0): ?>
                            <!-- Search Results -->
                            <?php foreach ($search_results as $result): ?>
                                <div class="search-result-item wds-card">
                                    <div class="search-result-title">
                                        <a href="<?php echo htmlspecialchars($result['url']); ?>">
                                            <?php echo htmlspecialchars($result['title']); ?>
                                        </a>
                                    </div>
                                    <div class="search-result-snippet">
                                        <?php echo $result['snippet']; ?>
                                    </div>
                                    <div class="search-result-url">
                                        <?php echo htmlspecialchars($result['url']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-results">
                                <h3>No results found</h3>
                                <p>Sorry, no pages were found matching your search criteria.</p>
                                <p>Try using different keywords or check your spelling.</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="no-results">
                            <h3>Search Level X Development</h3>
                            <p>Enter your search terms above to find content across our website.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Include Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/jquery-1.12.3.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
