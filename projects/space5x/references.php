<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Space5X - Technical References | World Domination Software</title>

        <!-- CSS -->
        <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>
        <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="../../assets/css/magnific-popup.css" rel="stylesheet">
        <link href="../../assets/css/owl.carousel.css" rel="stylesheet">
        <link href="../../assets/css/owl.carousel.theme.min.css" rel="stylesheet">
        <link href="../../assets/css/ionicons.css" rel="stylesheet">
        <link href="../../assets/css/main.css" rel="stylesheet">
        <link href="../../assets/css/readability-improvements.css" rel="stylesheet">
    </head>
    <body>
        <?php 
        $current_page = 'projects';
        $header_class = 'projects-header inner-header';
        $page_subtitle = 'Design. Debug. Deploy.';
        $current_project_slug = 'space5x';
        ?>

    <?php include '../../includes/header.html'; ?>
    <?php include '../../includes/navigation.php'; ?>

    <section class="about">
        <div class="container page-bgc">
            <div class="row">
                <div class="col-sm-12">
                    <div class="title-box">
                        <a href="index.php" style="color: #8B4513; text-decoration: none; font-size: 14px; display: inline-block; margin-bottom: 10px;">
                            ← Back to Space5X
                        </a>
                        <p>Technical Documentation</p>
                        <h2 class="title mt0">Space5X: Technical References for Nerds</h2>
                    </div>
                </div>
            </div>
            
            <!-- MAIN CONTENT AREA -->
            <div class="row">
                <div class="boxed">
                    <div class="col-sm-12">
                        
                        <!-- Introduction -->
                        <div style="background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 40px; margin-bottom: 40px;">
                            <h3 style="color: #8B4513; margin-bottom: 20px;">Technical Deep Dive</h3>
                            <p style="color: #8B7355; font-size: 16px; line-height: 1.8;">
                                This page contains detailed technical information about Space5X's architecture, algorithms, and implementation details. 
                                If you're interested in the nitty-gritty of how we're building a massive-scale space empire simulation, you've come to the right place.
                            </p>
                        </div>

                        <!-- Architecture Overview -->
                        <div style="background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 40px; margin-bottom: 40px;">
                            <h3 style="color: #8B4513; margin-bottom: 30px;">System Architecture</h3>
                            
                            <h4 style="color: #D2B48C; margin-bottom: 15px;">Backend Infrastructure</h4>
                            <ul style="color: #8B7355; line-height: 2; margin-bottom: 30px;">
                                <li><strong style="color: #8B4513;">Game Engine:</strong> Custom C++ engine with multi-threaded processing</li>
                                <li><strong style="color: #8B4513;">Database:</strong> PostgreSQL with CitusDB for horizontal sharding</li>
                                <li><strong style="color: #8B4513;">Caching Layer:</strong> Redis cluster for session management and real-time data</li>
                                <li><strong style="color: #8B4513;">Message Queue:</strong> RabbitMQ for asynchronous task processing</li>
                                <li><strong style="color: #8B4513;">API Gateway:</strong> Node.js/Express with rate limiting and authentication</li>
                            </ul>

                            <h4 style="color: #D2B48C; margin-bottom: 15px;">Client Architecture</h4>
                            <ul style="color: #8B7355; line-height: 2;">
                                <li><strong style="color: #8B4513;">Frontend:</strong> WebGL-based rendering using Three.js</li>
                                <li><strong style="color: #8B4513;">State Management:</strong> Redux for predictable state containers</li>
                                <li><strong style="color: #8B4513;">Real-time Communication:</strong> WebSocket connections with fallback to long-polling</li>
                                <li><strong style="color: #8B4513;">Mobile:</strong> React Native for iOS/Android with shared business logic</li>
                            </ul>
                        </div>

                        <!-- Galaxy Generation -->
                        <div style="background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 40px; margin-bottom: 40px;">
                            <h3 style="color: #8B4513; margin-bottom: 20px;">Procedural Galaxy Generation</h3>
                            <p style="color: #8B7355; font-size: 16px; line-height: 1.8; margin-bottom: 20px;">
                                Space5X uses a deterministic procedural generation algorithm to create galaxies with thousands of star systems. 
                                The algorithm ensures reproducibility while allowing for rich diversity in planet types, resources, and anomalies.
                            </p>
                            
                            <div style="background: #0f1419; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                                <h4 style="color: #D2B48C; margin-bottom: 15px;">Generation Parameters</h4>
                                <ul style="color: #8B7355; line-height: 2; font-family: 'Courier New', monospace; font-size: 14px;">
                                    <li>Seed: 64-bit integer for reproducible generation</li>
                                    <li>Galaxy Size: 2,000 - 10,000 star systems</li>
                                    <li>Star Density: Variable using Perlin noise for spiral arms</li>
                                    <li>Planet Generation: 0-15 planets per system based on star type</li>
                                    <li>Resource Distribution: Weighted random with regional biasing</li>
                                </ul>
                            </div>

                            <h4 style="color: #D2B48C; margin-bottom: 15px;">Star Classification</h4>
                            <p style="color: #8B7355; font-size: 14px; line-height: 1.8;">
                                We implement a simplified stellar classification based on the Harvard spectral classification:
                            </p>
                            <ul style="color: #8B7355; line-height: 2; margin-bottom: 20px;">
                                <li><strong style="color: #8B4513;">O-Type:</strong> Blue giants, rare (0.003%), high-energy output</li>
                                <li><strong style="color: #8B4513;">B-Type:</strong> Blue-white, uncommon (0.13%), few habitable planets</li>
                                <li><strong style="color: #8B4513;">A-Type:</strong> White, moderate (0.6%), potential for exotic resources</li>
                                <li><strong style="color: #8B4513;">F-Type:</strong> Yellow-white, common (3%), good colonization targets</li>
                                <li><strong style="color: #8B4513;">G-Type:</strong> Yellow, most common (7.6%), optimal for life</li>
                                <li><strong style="color: #8B4513;">K-Type:</strong> Orange, abundant (12.1%), stable and long-lived</li>
                                <li><strong style="color: #8B4513;">M-Type:</strong> Red dwarfs, dominant (76.5%), numerous planets</li>
                            </ul>
                        </div>

                        <!-- Physics Engine -->
                        <div style="background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 40px; margin-bottom: 40px;">
                            <h3 style="color: #8B4513; margin-bottom: 20px;">Physics and Movement</h3>
                            
                            <h4 style="color: #D2B48C; margin-bottom: 15px;">Orbital Mechanics</h4>
                            <p style="color: #8B7355; font-size: 16px; line-height: 1.8; margin-bottom: 20px;">
                                Space5X implements simplified Keplerian orbital mechanics for predictable but realistic ship movement. 
                                We use a time-step simulation with velocity Verlet integration for numerical stability.
                            </p>
                            
                            <div style="background: #0f1419; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                                <p style="color: #D2B48C; font-family: 'Courier New', monospace; font-size: 13px; margin: 0;">
                                    // Simplified position update (velocity Verlet)<br>
                                    x(t+Δt) = x(t) + v(t)Δt + ½a(t)Δt²<br>
                                    v(t+Δt) = v(t) + ½[a(t) + a(t+Δt)]Δt<br>
                                    <br>
                                    // Gravitational acceleration<br>
                                    a = -GM/r² * r̂<br>
                                    <br>
                                    // Time compression for long journeys<br>
                                    Δt_effective = Δt_real × time_warp_factor
                                </p>
                            </div>

                            <h4 style="color: #D2B48C; margin-bottom: 15px;">FTL Travel</h4>
                            <p style="color: #8B7355; font-size: 14px; line-height: 1.8;">
                                FTL uses a point-to-point warp system rather than continuous superluminal travel. 
                                Travel time is calculated based on distance, ship engine rating, and navigational hazards:
                            </p>
                            <ul style="color: #8B7355; line-height: 2;">
                                <li>Base travel time: distance / (engine_rating × warp_factor)</li>
                                <li>Fuel consumption: proportional to mass and distance</li>
                                <li>Navigation checks: discrete probability of encountering anomalies</li>
                            </ul>
                        </div>

                        <!-- Economic System -->
                        <div style="background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 40px; margin-bottom: 40px;">
                            <h3 style="color: #8B4513; margin-bottom: 20px;">Economic Simulation</h3>
                            
                            <h4 style="color: #D2B48C; margin-bottom: 15px;">Supply & Demand Model</h4>
                            <p style="color: #8B7355; font-size: 16px; line-height: 1.8; margin-bottom: 20px;">
                                The economy uses a dynamic supply-demand model with regional markets. Prices fluctuate based on local availability, 
                                trade routes, and player activities. Each commodity has elasticity parameters that determine price responsiveness.
                            </p>
                            
                            <div style="background: #0f1419; padding: 20px; border-radius: 8px;">
                                <p style="color: #D2B48C; font-family: 'Courier New', monospace; font-size: 13px; margin: 0;">
                                    // Price calculation<br>
                                    price = base_price × (demand/supply)^elasticity<br>
                                    <br>
                                    // Elasticity values<br>
                                    - Essential goods: 0.3-0.5 (inelastic)<br>
                                    - Luxury goods: 1.2-2.0 (elastic)<br>
                                    - Strategic resources: 0.8-1.0 (unit elastic)<br>
                                    <br>
                                    // Market update frequency: every 5 game minutes
                                </p>
                            </div>
                        </div>

                        <!-- Combat Mechanics -->
                        <div style="background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 40px; margin-bottom: 40px;">
                            <h3 style="color: #8B4513; margin-bottom: 20px;">Combat System</h3>
                            
                            <h4 style="color: #D2B48C; margin-bottom: 15px;">Damage Calculation</h4>
                            <p style="color: #8B7355; font-size: 16px; line-height: 1.8; margin-bottom: 20px;">
                                Combat uses a deterministic system with random variance to ensure fair outcomes while maintaining excitement. 
                                Multiple factors influence combat effectiveness including ship design, crew experience, and tactical positioning.
                            </p>
                            
                            <ul style="color: #8B7355; line-height: 2; margin-bottom: 20px;">
                                <li><strong style="color: #8B4513;">Hit Probability:</strong> Based on attacker accuracy vs defender evasion</li>
                                <li><strong style="color: #8B4513;">Damage Mitigation:</strong> Shields absorb percentage, then armor reduces remainder</li>
                                <li><strong style="color: #8B4513;">Critical Hits:</strong> 5% chance for 2x damage, affected by crew skill</li>
                                <li><strong style="color: #8B4513;">Subsystem Targeting:</strong> Players can focus fire on engines, weapons, or life support</li>
                            </ul>

                            <h4 style="color: #D2B48C; margin-bottom: 15px;">Fleet Tactics</h4>
                            <p style="color: #8B7355; font-size: 14px; line-height: 1.8;">
                                Large-scale fleet combat uses formation bonuses and command hierarchy. Fleet commanders provide tactical bonuses 
                                based on their leadership skill, and coordinated attacks can overwhelm individual ships' defenses.
                            </p>
                        </div>

                        <!-- Network Protocol -->
                        <div style="background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 40px; margin-bottom: 40px;">
                            <h3 style="color: #8B4513; margin-bottom: 20px;">Network Architecture</h3>
                            
                            <h4 style="color: #D2B48C; margin-bottom: 15px;">Communication Protocol</h4>
                            <p style="color: #8B7355; font-size: 16px; line-height: 1.8; margin-bottom: 20px;">
                                Space5X uses a hybrid protocol combining WebSocket for real-time updates and REST API for state queries. 
                                All messages use MessagePack for efficient binary serialization.
                            </p>
                            
                            <ul style="color: #8B7355; line-height: 2; margin-bottom: 20px;">
                                <li><strong style="color: #8B4513;">Heartbeat:</strong> Client pings every 30 seconds</li>
                                <li><strong style="color: #8B4513;">State Sync:</strong> Delta updates sent only for changed game state</li>
                                <li><strong style="color: #8B4513;">Command Queue:</strong> Player actions queued and executed in order</li>
                                <li><strong style="color: #8B4513;">Lag Compensation:</strong> Server-authoritative with client prediction</li>
                            </ul>

                            <h4 style="color: #D2B48C; margin-bottom: 15px;">Scalability</h4>
                            <p style="color: #8B7355; font-size: 14px; line-height: 1.8;">
                                The game is designed to scale horizontally using Kubernetes for orchestration. Each star system can run on 
                                a separate game server instance, with player connections routed through a load balancer based on their 
                                current location in the galaxy.
                            </p>
                        </div>

                        <!-- Database Schema -->
                        <div style="background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 40px; margin-bottom: 40px;">
                            <h3 style="color: #8B4513; margin-bottom: 20px;">Data Storage</h3>
                            
                            <h4 style="color: #D2B48C; margin-bottom: 15px;">Core Tables</h4>
                            <div style="background: #0f1419; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                                <p style="color: #D2B48C; font-family: 'Courier New', monospace; font-size: 13px; margin: 0;">
                                    players (player_id, username, empire_id, created_at)<br>
                                    empires (empire_id, name, faction, credits, research_points)<br>
                                    star_systems (system_id, galaxy_id, x, y, z, star_type)<br>
                                    planets (planet_id, system_id, type, resources, population)<br>
                                    ships (ship_id, owner_id, design_id, location, status)<br>
                                    fleets (fleet_id, commander_id, ships[], destination)<br>
                                    trade_routes (route_id, start, end, cargo, frequency)<br>
                                    technologies (tech_id, empire_id, level, progress)
                                </p>
                            </div>

                            <h4 style="color: #D2B48C; margin-bottom: 15px;">Optimization Strategies</h4>
                            <ul style="color: #8B7355; line-height: 2;">
                                <li><strong style="color: #8B4513;">Partitioning:</strong> Data partitioned by galaxy region for faster queries</li>
                                <li><strong style="color: #8B4513;">Indexing:</strong> Spatial indexes for coordinate-based lookups</li>
                                <li><strong style="color: #8B4513;">Caching:</strong> Hot data kept in Redis with 5-minute TTL</li>
                                <li><strong style="color: #8B4513;">Archiving:</strong> Inactive empires moved to cold storage after 90 days</li>
                            </ul>
                        </div>

                        <!-- Performance Metrics -->
                        <div style="background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 40px; margin-bottom: 40px;">
                            <h3 style="color: #8B4513; margin-bottom: 20px;">Performance Targets</h3>
                            
                            <div class="row">
                                <div class="col-sm-4">
                                    <h4 style="color: #D2B48C; margin-bottom: 15px;">Response Time</h4>
                                    <ul style="color: #8B7355; line-height: 2; font-size: 14px;">
                                        <li>API calls: &lt;100ms p99</li>
                                        <li>State updates: &lt;50ms</li>
                                        <li>Combat resolution: &lt;200ms</li>
                                        <li>Galaxy render: &lt;16ms (60fps)</li>
                                    </ul>
                                </div>
                                <div class="col-sm-4">
                                    <h4 style="color: #D2B48C; margin-bottom: 15px;">Capacity</h4>
                                    <ul style="color: #8B7355; line-height: 2; font-size: 14px;">
                                        <li>100,000 concurrent players</li>
                                        <li>1,000,000 active empires</li>
                                        <li>10,000 star systems per galaxy</li>
                                        <li>1,000,000 ships in universe</li>
                                    </ul>
                                </div>
                                <div class="col-sm-4">
                                    <h4 style="color: #D2B48C; margin-bottom: 15px;">Reliability</h4>
                                    <ul style="color: #8B7355; line-height: 2; font-size: 14px;">
                                        <li>99.9% uptime SLA</li>
                                        <li>Zero data loss</li>
                                        <li>Auto-failover &lt;30s</li>
                                        <li>Daily backups</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Contributing -->
                        <div style="text-align: center; padding: 40px;">
                            <h3 style="color: #8B4513; margin-bottom: 20px;">Want to Contribute?</h3>
                            <p style="color: #8B7355; font-size: 16px; margin-bottom: 30px;">
                                We welcome technical contributions from experienced developers. Check out our GitHub repository 
                                or join our Discord server to discuss implementation details with the dev team.
                            </p>
                            <a href="/project-contact.php" class="space5x-btn" style="display: inline-block; background: transparent; color: #8B4513; border: 2px solid #8B4513; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; transition: all 0.3s ease; margin-right: 15px;">
                                Join Dev Team
                            </a>
                            <a href="index.php" class="space5x-btn" style="display: inline-block; background: transparent; color: #8B4513; border: 2px solid #8B4513; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; transition: all 0.3s ease;">
                                Back to Space5X
                            </a>
                        </div>
                        
                        <!-- Button Styles -->
                        <style>
                            .space5x-btn:hover {
                                background: #8B4513 !important;
                                color: #E8E4D8 !important;
                                text-decoration: none !important;
                                transform: translateY(-2px);
                                box-shadow: 0 4px 12px rgba(139, 69, 19, 0.4);
                            }
                        </style>
                        
                        <div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../../includes/footer.php'; ?>

    <!-- JavaScript -->
    <script src="../../assets/js/jquery-1.12.3.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script src="../../assets/js/jquery.magnific-popup.min.js"></script>
    <script src="../../assets/js/owl.carousel.min.js"></script>
    <script src="../../assets/js/script.js"></script>
</body>
</html>
