<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gaming Industry 2025 - GameServer Panel Statistics</title>
    
    <!-- CSS -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/main.css" rel="stylesheet">
    <link href="../../assets/css/readability-improvements.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #D4CFC0;
            color: #D2B48C;
        }
        
        .stats-hero {
            background: linear-gradient(135deg, #0f1419 0%, #1C1C1C 50%, #2a2a2a 100%);
            padding: 80px 0;
            text-align: center;
            border-bottom: 2px solid #8B4513;
        }
        
        .stats-container {
            background-color: #D4CFC0;
            padding: 60px 0;
        }
        
        .stats-section {
            background-color: #2a2a2a;
            border: 1px solid #8B4513;
            border-radius: 12px;
            padding: 40px;
            margin: 40px 0;
        }
        
        .market-size-display {
            background: linear-gradient(135deg, #8B4513 0%, #D2B48C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 72px;
            font-weight: bold;
            margin: 30px 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .chart-container {
            position: relative;
            width: 100%;
            height: 400px;
            margin: 30px 0;
            background-color: #1a1a1a;
            border: 1px solid #8B4513;
            border-radius: 8px;
            padding: 20px;
        }
        
        .chart-title {
            color: #8B4513;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .chart-description {
            color: #4a4a4a;
            margin-bottom: 25px;
            text-align: center;
            font-size: 16px;
        }
        
        .trends-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .trend-card {
            background-color: #1a1a1a;
            border: 1px solid #8B4513;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .trend-card:hover {
            background-color: #2a2a2a;
            border-color: #D2B48C;
            transform: translateY(-5px);
        }
        
        .trend-icon {
            font-size: 48px;
            color: #8B4513;
            margin-bottom: 20px;
        }
        
        .trend-title {
            color: #8B4513;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .trend-description {
            color: #4a4a4a;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .stats-highlight {
            background: rgba(139, 69, 19, 0.2);
            border-left: 4px solid #8B4513;
            padding: 20px;
            margin: 30px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .demographic-split {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 30px;
        }
        
        .back-link {
            background: #8B4513;
            color: #D2B48C;
            padding: 15px 30px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            margin: 20px 0;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            background: #D2B48C;
            color: #1C1C1C;
            text-decoration: none;
        }
        
        @media (max-width: 768px) {
            .market-size-display {
                font-size: 48px;
            }
            
            .demographic-split {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .chart-container {
                height: 300px;
            }
        }
    </style>
</head>
<body>
    <?php 
    // Page-specific variables
    $current_page = 'projects';
    $page_subtitle = 'Gaming Industry 2025';
    $page_description = 'Market statistics and trends for game server hosting';
    $page_title = 'Industry';
    $page_title_thin = 'Stats';
    ?>

    <!-- Include Site Header -->
    <?php include '../../includes/header.html'; ?>
    
    <!-- Include Navigation Header -->
    <?php include '../../includes/navigation.php'; ?>

    <section class="stats-hero">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <h1 style="color: #8B4513; font-size: 48px; margin-bottom: 20px;">
                        <i class="fas fa-chart-line" style="margin-right: 15px;"></i>
                        The State of Gaming Industry 2025
                    </h1>
                    <p style="font-size: 20px; color: #4a4a4a; margin-bottom: 0;">
                        Market insights driving the future of game server hosting
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="stats-container">
        <div class="container">
            <a href="index.php" class="back-link">
                <i class="fas fa-arrow-left" style="margin-right: 8px;"></i>
                Back to GameServer Panel
            </a>

            <!-- Market Size Section -->
            <section class="stats-section text-center">
                <h2 style="color: #8B4513; font-size: 32px; margin-bottom: 20px;">Global Gaming Market Revenue</h2>
                <p style="color: #4a4a4a; font-size: 18px; margin-bottom: 30px;">
                    The gaming industry continues its explosive growth trajectory, establishing itself as the dominant entertainment sector worldwide.
                </p>
                <div class="market-size-display">$268 Billion</div>
                <p style="color: #4a4a4a; max-width: 700px; margin: 0 auto;">
                    Projected 2025 revenue driven by innovation in mobile gaming, cloud platforms, 
                    and immersive technologies, creating unprecedented opportunities for hosting providers.
                </p>
            </section>

            <!-- Platform Distribution -->
            <section class="stats-section">
                <div class="chart-title">Gaming Platform Market Share</div>
                <div class="chart-description">
                    Mobile gaming dominates the market, but console and PC platforms maintain dedicated, 
                    high-spending user bases that drive game server hosting demand.
                </div>
                <div class="chart-container">
                    <canvas id="platformChart"></canvas>
                </div>
            </section>

            <!-- Genre Popularity -->
            <section class="stats-section">
                <div class="chart-title">Most Popular Game Genres</div>
                <div class="chart-description">
                    Understanding genre popularity helps hosting providers optimize their server 
                    configurations and resource allocation for maximum customer satisfaction.
                </div>
                <div class="chart-container">
                    <canvas id="genreChart"></canvas>
                </div>
            </section>

            <!-- Demographics -->
            <section class="stats-section">
                <div class="chart-title">Gaming Demographics: Who's Playing?</div>
                <div class="chart-description">
                    The modern gaming audience is diverse, dispelling outdated stereotypes and 
                    revealing opportunities across all demographic segments.
                </div>
                
                <div class="demographic-split">
                    <div>
                        <h4 style="color: #8B4513; text-align: center; margin-bottom: 20px;">Gender Distribution</h4>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                    <div>
                        <h4 style="color: #8B4513; text-align: center; margin-bottom: 20px;">Age Distribution</h4>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="ageChart"></canvas>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Market Growth -->
            <section class="stats-section">
                <div class="chart-title">Gaming Market Growth Trajectory</div>
                <div class="chart-description">
                    Consistent year-over-year growth demonstrates the industry's resilience and 
                    the expanding market opportunity for game server hosting services.
                </div>
                <div class="chart-container">
                    <canvas id="growthChart"></canvas>
                </div>
                
                <div class="stats-highlight">
                    <h4 style="color: #8B4513; margin-bottom: 15px;">
                        <i class="fas fa-server" style="margin-right: 10px;"></i>
                        Hosting Market Implications
                    </h4>
                    <p style="color: #4a4a4a; margin-bottom: 0;">
                        This growth translates directly to increased demand for reliable game server infrastructure. 
                        GameServer Panel's commercial features position hosting providers to capitalize on this 
                        expanding market with professional-grade management tools and integrated billing systems.
                    </p>
                </div>
            </section>

            <!-- Future Trends -->
            <section class="stats-section">
                <h2 style="color: #8B4513; font-size: 32px; text-align: center; margin-bottom: 40px;">
                    Future Trends Shaping Game Hosting
                </h2>
                
                <div class="trends-grid">
                    <div class="trend-card">
                        <div class="trend-icon">
                            <i class="fas fa-cloud"></i>
                        </div>
                        <div class="trend-title">Cloud Gaming Revolution</div>
                        <div class="trend-description">
                            Services like Xbox Cloud Gaming and GeForce NOW are making high-end gaming 
                            accessible on any device, driving demand for scalable server infrastructure 
                            and edge computing solutions.
                        </div>
                    </div>
                    
                    <div class="trend-card">
                        <div class="trend-icon">
                            <i class="fas fa-rocket" aria-hidden="true"></i>
                        </div>
                        <div class="trend-title">AI Integration</div>
                        <div class="trend-description">
                            AI is revolutionizing game development with more realistic NPCs, dynamic worlds, 
                            and personalized experiences. This increases computational requirements and 
                            creates new hosting opportunities.
                        </div>
                    </div>
                    
                    <div class="trend-card">
                        <div class="trend-icon">
                            <i class="fas fa-vr-cardboard"></i>
                        </div>
                        <div class="trend-title">VR & AR Growth</div>
                        <div class="trend-description">
                            As VR/AR hardware becomes more affordable and compelling applications emerge, 
                            the demand for low-latency, high-performance server infrastructure will 
                            grow exponentially.
                        </div>
                    </div>
                    
                    <div class="trend-card">
                        <div class="trend-icon">
                            <i class="fas fa-globe-americas"></i>
                        </div>
                        <div class="trend-title">Global Expansion</div>
                        <div class="trend-description">
                            Gaming markets in developing regions are experiencing rapid growth, 
                            requiring hosting providers to expand their geographic presence and 
                            optimize for diverse network conditions.
                        </div>
                    </div>
                    
                    <div class="trend-card">
                        <div class="trend-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="trend-title">Mobile Dominance</div>
                        <div class="trend-description">
                            Mobile gaming's 55% market share continues to grow, driving demand for 
                            mobile-optimized server configurations and cross-platform compatibility 
                            in hosting solutions.
                        </div>
                    </div>
                    
                    <div class="trend-card">
                        <div class="trend-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="trend-title">Monetization Evolution</div>
                        <div class="trend-description">
                            Free-to-play models, battle passes, and live services require robust, 
                            scalable server infrastructure with integrated billing and analytics 
                            capabilities for successful operation.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Call to Action -->
            <section class="stats-section text-center">
                <h2 style="color: #8B4513; margin-bottom: 30px;">Ready to Capitalize on Gaming Growth?</h2>
                <p style="color: #4a4a4a; font-size: 18px; margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto;">
                    GameServer Panel provides the tools you need to build a successful game hosting business 
                    in this expanding market. Professional features, commercial billing, and enterprise support included.
                </p>
                <a href="admin-guide.php" class="back-link" style="margin-right: 20px;">
                    <i class="fas fa-book" style="margin-right: 8px;"></i>
                    View Admin Guide
                </a>
                <a href="../../contact.php" class="back-link">
                    <i class="fas fa-envelope" style="margin-right: 8px;"></i>
                    Contact Us
                </a>
            </section>
        </div>
    </div>

    <!-- Quick Navigation Section -->
    <section style="background-color: #2a2a2a; padding: 40px 0; border-top: 1px solid #444;">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h3 style="color: #8B4513; margin-bottom: 25px;">Explore GameServer Panel</h3>
                    <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                        <a href="index.php" class="btn-wds">
                            <i class="fas fa-home" style="margin-right: 8px;"></i>
                            Project Overview
                        </a>
                        <a href="admin-guide.php" class="btn-wds">
                            <i class="fas fa-book" style="margin-right: 8px;"></i>
                            Admin Guide
                        </a>
                        <a href="../../contact.php" class="btn-wds">
                            <i class="fas fa-envelope" style="margin-right: 8px;"></i>
                            Contact Us
                        </a>
                        <a href="../../joinus.php" class="btn-wds">
                            <i class="fas fa-users" style="margin-right: 8px;"></i>
                            Join Team
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Include Footer -->
    <?php include '../../includes/footer.php'; ?>

    <script>
        const wdsTheme = {
            primary: '#8B4513',
            secondary: '#D2B48C',
            accent: '#8B7355',
            background: '#1C1C1C',
            surface: '#2a2a2a',
            text: '#D2B48C'
        };

        Chart.defaults.color = wdsTheme.text;
        Chart.defaults.borderColor = wdsTheme.accent;
        Chart.defaults.backgroundColor = wdsTheme.surface;

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: wdsTheme.text,
                        font: {
                            size: 14
                        }
                    }
                }
            },
            scales: {
                x: {
                    ticks: { color: wdsTheme.text },
                    grid: { color: wdsTheme.accent + '40' }
                },
                y: {
                    ticks: { color: wdsTheme.text },
                    grid: { color: wdsTheme.accent + '40' }
                }
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            // Platform Share Chart
            new Chart(document.getElementById('platformChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Mobile Gaming', 'Console Gaming', 'PC Gaming'],
                    datasets: [{
                        data: [55, 28, 17],
                        backgroundColor: [wdsTheme.primary, wdsTheme.secondary, wdsTheme.accent],
                        borderColor: wdsTheme.background,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: wdsTheme.text, padding: 20 }
                        }
                    }
                }
            });

            // Genre Popularity Chart
            new Chart(document.getElementById('genreChart'), {
                type: 'bar',
                data: {
                    labels: ['Action', 'RPG', 'Strategy', 'Adventure', 'Shooter', 'Puzzle'],
                    datasets: [{
                        label: 'Popularity (%)',
                        data: [28, 22, 15, 13, 12, 10],
                        backgroundColor: wdsTheme.primary,
                        borderColor: wdsTheme.secondary,
                        borderWidth: 1
                    }]
                },
                options: {
                    ...chartOptions,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Gender Distribution Chart
            new Chart(document.getElementById('genderChart'), {
                type: 'pie',
                data: {
                    labels: ['Male', 'Female'],
                    datasets: [{
                        data: [52, 48],
                        backgroundColor: [wdsTheme.primary, wdsTheme.secondary],
                        borderColor: wdsTheme.background,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: wdsTheme.text }
                        }
                    }
                }
            });

            // Age Distribution Chart
            new Chart(document.getElementById('ageChart'), {
                type: 'bar',
                data: {
                    labels: ['Under 18', '18-24', '25-34', '35-44', '45+'],
                    datasets: [{
                        label: 'Percentage',
                        data: [18, 25, 30, 17, 10],
                        backgroundColor: wdsTheme.secondary,
                        borderColor: wdsTheme.primary,
                        borderWidth: 1
                    }]
                },
                options: {
                    ...chartOptions,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Market Growth Chart
            new Chart(document.getElementById('growthChart'), {
                type: 'line',
                data: {
                    labels: ['2021', '2022', '2023', '2024', '2025 (Proj.)'],
                    datasets: [{
                        label: 'Global Revenue (Billions USD)',
                        data: [180, 196, 221, 245, 268],
                        fill: true,
                        backgroundColor: wdsTheme.primary + '40',
                        borderColor: wdsTheme.primary,
                        tension: 0.4,
                        pointBackgroundColor: wdsTheme.secondary,
                        pointBorderColor: wdsTheme.primary,
                        pointRadius: 6
                    }]
                },
                options: {
                    ...chartOptions,
                    plugins: {
                        legend: {
                            labels: { color: wdsTheme.text }
                        }
                    },
                    scales: {
                        ...chartOptions.scales,
                        y: {
                            ...chartOptions.scales.y,
                            ticks: {
                                color: wdsTheme.text,
                                callback: function(value) {
                                    return '$' + value + 'B';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
