// Developed by World Domination Software LLC
<?php
// Project navigation order - matches the order on projects.php page
$project_order = [
    // Current Projects
    'neverwards' => ['title' => 'Neverwards', 'type' => 'current'],
    'gameservers-world' => ['title' => 'Gameservers.world', 'type' => 'current'],
    'worlddomination-dev' => ['title' => 'Worlddomination.dev', 'type' => 'current'], 
    'pureops' => ['title' => 'PureOps', 'type' => 'current'],
    // Legacy Projects
    'roadkill' => ['title' => 'Roadkill', 'type' => 'legacy'],
    // Upcoming Projects
    'space-4x' => ['title' => 'Space-4X', 'type' => 'upcoming'],
    'alien-apocalypse' => ['title' => 'Alien Apocalypse', 'type' => 'upcoming'],
    'bbs-revival' => ['title' => 'BBS Revival', 'type' => 'upcoming'],
    'roadkill-v2' => ['title' => 'Roadkill v2', 'type' => 'upcoming']
];

// Get current project from filename
$current_project = isset($current_project_slug) ? $current_project_slug : '';

// Find current project index
$project_keys = array_keys($project_order);
$current_index = array_search($current_project, $project_keys);

// Get previous and next projects
$prev_project = ($current_index > 0) ? $project_keys[$current_index - 1] : null;
$next_project = ($current_index < count($project_keys) - 1) ? $project_keys[$current_index + 1] : null;
?>

<?php
// Base path for project navigation links so they work from any install location
$proj_nav_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$proj_nav_base = '';

if (strpos($proj_nav_url, '/projects/') !== false) {
    // Determine if we're one or two levels deep under /projects/
    $afterProjects = substr($proj_nav_url, strpos($proj_nav_url, '/projects/') + strlen('/projects/'));
    if (strpos($afterProjects, '/') !== false) {
        // e.g. /projects/roadkill/roadkill-v2.php or /projects/foo/index.php
        $proj_nav_base = '../../';
    } else {
        // e.g. /projects/foo.php (not used today, but safe)
        $proj_nav_base = '../';
    }
}
?>
<!-- Project Navigation -->
<section style="background: #1C1C1C; padding: 40px 0; border-top: 1px solid #333;">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                    
                    <!-- Previous Project -->
                    <div style="flex: 1; min-width: 200px;">
                        <?php if ($prev_project): ?>
                            <a href="<?php echo $proj_nav_base; ?>projects/<?php echo $prev_project; ?>" style="text-decoration: none; color: #8B7355; display: block; padding: 15px; background: #1a1a1a; border: 1px solid #333; border-radius: 8px; transition: all 0.3s ease;">
                                <div style="font-size: 12px; color: #8B4513; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">← Previous Project</div>
                                <div style="font-size: 16px; font-weight: bold;"><?php echo $project_order[$prev_project]['title']; ?></div>
                                <div style="font-size: 12px; color: #64748b; text-transform: capitalize;"><?php echo $project_order[$prev_project]['type']; ?> Project</div>
                            </a>
                        <?php else: ?>
                            <div style="padding: 15px; color: #64748b; text-align: center; font-style: italic;">
                                First Project
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Back to Projects -->
                    <div style="text-align: center;">
                        <a href="<?php echo $proj_nav_base; ?>projects.php" style="text-decoration: none; color: #8B4513; display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: rgba(0, 200, 81, 0.1); border: 1px solid #8B4513; border-radius: 6px; font-weight: bold; transition: all 0.3s ease;">
                            <span class="ion-ios-grid-view"></span>
                            All Projects
                        </a>
                    </div>

                    <!-- Next Project -->
                    <div style="flex: 1; min-width: 200px; text-align: right;">
                        <?php if ($next_project): ?>
                            <a href="<?php echo $proj_nav_base; ?>projects/<?php echo $next_project; ?>" style="text-decoration: none; color: #8B7355; display: block; padding: 15px; background: #1a1a1a; border: 1px solid #333; border-radius: 8px; transition: all 0.3s ease;">
                                <div style="font-size: 12px; color: #8B4513; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; text-align: right;">Next Project →</div>
                                <div style="font-size: 16px; font-weight: bold; text-align: right;"><?php echo $project_order[$next_project]['title']; ?></div>
                                <div style="font-size: 12px; color: #64748b; text-transform: capitalize; text-align: right;"><?php echo $project_order[$next_project]['type']; ?> Project</div>
                            </a>
                        <?php else: ?>
                            <div style="padding: 15px; color: #64748b; text-align: center; font-style: italic;">
                                Last Project
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Project navigation hover effects */
section a[href*="/projects/"]:hover {
    background: #2a2a2a !important;
    border-color: #8B4513 !important;
    transform: translateY(-2px);
}

section a[href="/projects.php"]:hover {
    background: rgba(0, 200, 81, 0.2) !important;
    transform: translateY(-1px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    section div[style*="justify-content: space-between"] {
        flex-direction: column !important;
    }
    
    section div[style*="text-align: right"] {
        text-align: left !important;
    }
    
    section div[style*="text-align: right"] > a {
        text-align: left !important;
    }
    
    section div[style*="text-align: right"] div {
        text-align: left !important;
    }
}
</style>
