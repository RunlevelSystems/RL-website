<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>World Domination Software | Join Our Co-op</title>

        <!-- CSS -->

        <!-- google fonts -->
        <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>

        <!-- files -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/css/magnific-popup.css" rel="stylesheet">
        <link href="assets/css/owl.carousel.css" rel="stylesheet">
        <link href="assets/css/owl.carousel.theme.min.css" rel="stylesheet">
        <link href="assets/css/ionicons.css" rel="stylesheet">
        <link href="<!-- main.css removed -->" rel="stylesheet">
        <link href="assets/css/wds-unified.css" rel="stylesheet">

        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <?php 
        // Page-specific variables
        $current_page = 'joinus';
        $header_class = 'joinus-header inner-header';
        $show_breadcrumb = true;
        $page_breadcrumb = 'Join Us';
        ?>

    <!-- Include Site Header -->
    <?php include 'includes/header.html'; ?>
        
    <!-- Include Navigation Header -->
    <?php include 'includes/navigation.php'; ?>

    <!-- Join Us -->
    <section class="join-us-coop">
        <div class="container page-bgc">
            <div class="row">
                <div class="col-sm-12">
                    <div class="title-box">
                        <p>Join the</p>
                        <h2 class="title mt0">Co-op Team</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="boxed">
                    <div class="col-sm-12">
                        <div class="why-work-for-us" style="color: #D2B48C; max-width: 900px; margin: 0 auto; padding: 20px;">
                            <h2 style="color: #8B4513; margin-bottom: 20px;">About Our Co-op</h2>
                            <p style="font-size: 18px; line-height: 1.6; margin-bottom: 30px;">
                                We're a co-op. That means instead of just collecting a paycheck, 
                                you directly share in the <strong>profits</strong> of everything we build together. 
                                Contributors share in net profits based on their work using a tiered system that favors growth, 
                                while the founder covers all upfront costs for servers, tools, and AI.
                            </p>

                            <h3 style="color: #8B4513; margin-bottom: 15px;">What We Do</h3>
                            <ul style="font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
                                <li>Commercial <strong>game server hosting</strong> for classic and modern titles</li>
                                <li><strong>Game testing</strong>, <strong>customer support</strong>, and <strong>mod development</strong></li>
                                <li><strong>Code & website updates</strong> (Linux and Windows administration)</li>
                                <li><strong>Game development</strong> in Unity and Unreal</li>
                                <li><strong>Business applications</strong> for real-world clients</li>
                            </ul>

                            <h3 style="color: #8B4513; margin-bottom: 15px;">Why It's Worth It</h3>
                            <ul style="font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
                                <li>It's genuinely <strong>fun to build games</strong> with a passionate team</li>
                                <li>Hands-on Linux & Windows sysadmin experience</li>
                                <li>Resume-ready projects in hosting, coding, and game development</li>
                                <li>Opportunities to learn Unity, Unreal, PHP, AI tools, and more</li>
                                <li><strong>Share in profits</strong> with increasing percentages as we grow</li>
                                <li><strong>Use our tools for your own side projects</strong> — we'll even help where we can</li>
                            </ul>

                            <h3 style="color: #8B4513; margin-bottom: 15px;">Who We're Looking For</h3>
                            <p style="font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                                Gamers, coders, modders, and developers interested in real-world experience and 
                                collaborative projects. If you're interested in game development, 
                                server management, or working on projects with professional tools, 
                                we'd like to hear from you.
                            </p>

                            <p style="font-size: 20px; font-weight: bold; text-align: center; color: #8B4513;"><strong>Collaborate. Build. Grow Together.</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- New Career-Focused Infographic Section -->
    <section class="career-focused-infographic" style="background: #1C1C1C; padding: 60px 0;">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
            .career-infographic-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
                background: rgba(0,0,0,0.3);
                border-radius: 20px;
                backdrop-filter: blur(10px);
            }
            
            /* Hero Section */
            .career-hero {
                text-align: center;
                padding: 60px 40px;
                background: linear-gradient(135deg, #8B4513 0%, #654321 50%, #2F2F2F 100%);
                border-radius: 20px;
                margin-bottom: 40px;
                position: relative;
                overflow: hidden;
            }
            .career-hero::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: repeating-linear-gradient(
                    45deg,
                    transparent,
                    transparent 10px,
                    rgba(255,255,255,0.05) 10px,
                    rgba(255,255,255,0.05) 20px
                );
                animation: shimmer 8s linear infinite;
            }
            @keyframes shimmer {
                0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
                100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
            }
            .career-hero h2 {
                color: #FFFFFF;
                font-size: 48px;
                font-weight: 900;
                margin-bottom: 20px;
                text-shadow: 3px 3px 6px rgba(0,0,0,0.6);
                position: relative;
                z-index: 1;
            }
            .career-hero p {
                color: #F5F5F5;
                font-size: 24px;
                font-weight: 600;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
                position: relative;
                z-index: 1;
                max-width: 800px;
                margin: 0 auto;
                line-height: 1.4;
            }
            
            /* Timeline Section */
            .timeline-section {
                margin: 50px 0;
            }
            .timeline-section h3 {
                color: #8B4513;
                font-size: 36px;
                font-weight: bold;
                text-align: center;
                margin-bottom: 40px;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            }
            .career-timeline {
                position: relative;
                max-width: 900px;
                margin: 0 auto;
            }
            .career-timeline::before {
                content: '';
                position: absolute;
                left: 50%;
                top: 0;
                bottom: 0;
                width: 4px;
                background: linear-gradient(to bottom, #8B4513, #654321);
                transform: translateX(-50%);
                border-radius: 2px;
            }
            .timeline-item {
                position: relative;
                width: 50%;
                padding: 20px 40px;
                margin: 40px 0;
            }
            .timeline-item:nth-child(odd) {
                left: 0;
                text-align: right;
            }
            .timeline-item:nth-child(even) {
                left: 50%;
                text-align: left;
            }
            .timeline-item::before {
                content: '';
                position: absolute;
                width: 20px;
                height: 20px;
                background: #8B4513;
                border: 4px solid #fff;
                border-radius: 50%;
                top: 30px;
                box-shadow: 0 0 0 4px #8B4513;
            }
            .timeline-item:nth-child(odd)::before {
                right: -50px;
            }
            .timeline-item:nth-child(even)::before {
                left: -50px;
            }
            .timeline-content {
                background: rgba(255,255,255,0.1);
                border: 2px solid rgba(0,200,81,0.3);
                border-radius: 15px;
                padding: 30px;
                backdrop-filter: blur(10px);
                transition: all 0.3s ease;
            }
            .timeline-content:hover {
                background: rgba(255,255,255,0.15);
                border-color: #8B4513;
                transform: translateY(-5px);
                box-shadow: 0 10px 25px rgba(0,200,81,0.2);
            }
            .timeline-icon {
                font-size: 36px;
                margin-bottom: 15px;
                display: block;
                color: #8B4513;
            }
            .timeline-title {
                color: #8B4513;
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 10px;
                text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
            }
            .timeline-desc {
                color: #D2B48C;
                font-size: 16px;
                line-height: 1.6;
                font-weight: 500;
            }
            
            /* Mission Cards */
            .mission-cards {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
                gap: 30px;
                margin: 50px 0;
            }
            .mission-card {
                background: linear-gradient(135deg, rgba(0,200,81,0.15), rgba(0,160,67,0.15));
                border: 3px solid rgba(0,200,81,0.3);
                border-radius: 20px;
                padding: 40px;
                text-align: center;
                transition: all 0.4s ease;
                position: relative;
                overflow: hidden;
            }
            .mission-card::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: conic-gradient(from 0deg, transparent, rgba(0,200,81,0.1), transparent);
                animation: rotate 6s linear infinite;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            .mission-card:hover::before {
                opacity: 1;
            }
            .mission-card:hover {
                transform: translateY(-10px) scale(1.02);
                background: linear-gradient(135deg, rgba(0,200,81,0.25), rgba(0,160,67,0.25));
                border-color: #8B4513;
                box-shadow: 0 20px 40px rgba(0,200,81,0.3);
            }
            @keyframes rotate {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            .mission-icon {
                font-size: 64px;
                margin-bottom: 20px;
                display: block;
                color: #8B4513;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
                position: relative;
                z-index: 1;
            }
            .mission-title {
                color: #8B4513;
                font-size: 28px;
                font-weight: bold;
                margin-bottom: 15px;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
                position: relative;
                z-index: 1;
            }
            .mission-desc {
                color: #D2B48C;
                font-size: 18px;
                line-height: 1.7;
                font-weight: 500;
                position: relative;
                z-index: 1;
            }
            
            /* Skills Chart */
            .skills-chart-section {
                background: rgba(255,255,255,0.08);
                border: 2px solid rgba(0,200,81,0.2);
                border-radius: 20px;
                padding: 50px;
                margin: 50px 0;
                text-align: center;
            }
            .skills-chart-section h3 {
                color: #8B4513;
                font-size: 36px;
                font-weight: bold;
                margin-bottom: 20px;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            }
            .skills-chart-section p {
                color: #D2B48C;
                font-size: 20px;
                margin-bottom: 40px;
                font-weight: 500;
                max-width: 700px;
                margin-left: auto;
                margin-right: auto;
                line-height: 1.6;
            }
            .chart-container {
                position: relative;
                width: 100%;
                max-width: 700px;
                margin: 0 auto;
                height: 400px;
            }
            
            /* CTA Section */
            .career-cta {
                background: linear-gradient(135deg, #8B4513, #654321);
                border-radius: 20px;
                padding: 60px 40px;
                text-align: center;
                margin-top: 50px;
                position: relative;
                overflow: hidden;
            }
            .career-cta::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
                animation: slide 3s ease-in-out infinite;
            }
            @keyframes slide {
                0% { left: -100%; }
                50% { left: 100%; }
                100% { left: 100%; }
            }
            .career-cta h3 {
                color: #D2B48C;
                font-size: 42px;
                font-weight: 900;
                margin-bottom: 20px;
                text-shadow: 3px 3px 6px rgba(0,0,0,0.4);
                position: relative;
                z-index: 1;
            }
            .career-cta p {
                color: #D2B48C;
                font-size: 22px;
                margin-bottom: 40px;
                font-weight: 600;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
                position: relative;
                z-index: 1;
                max-width: 600px;
                margin-left: auto;
                margin-right: auto;
                line-height: 1.5;
            }
            .cta-button {
                display: inline-block;
                background: #fff;
                color: #8B4513;
                font-size: 24px;
                font-weight: 900;
                text-decoration: none;
                padding: 20px 50px;
                border-radius: 50px;
                border: 4px solid #fff;
                transition: all 0.3s ease;
                text-transform: uppercase;
                letter-spacing: 1px;
                position: relative;
                z-index: 1;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            }
            .cta-button:hover {
                background: transparent;
                color: #D2B48C;
                transform: translateY(-3px);
                box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            }
            
            /* Responsive Design */
            @media (max-width: 768px) {
                .career-timeline::before {
                    left: 20px;
                }
                .timeline-item {
                    width: 100%;
                    left: 0 !important;
                    text-align: left !important;
                    padding-left: 60px;
                    padding-right: 20px;
                }
                .timeline-item::before {
                    left: 10px !important;
                }
                .career-hero h2 {
                    font-size: 36px;
                }
                .career-hero p {
                    font-size: 20px;
                }
                .mission-cards {
                    grid-template-columns: 1fr;
                }
                .chart-container {
                    height: 300px;
                }
            }
        </style>

        <div class="career-infographic-container">
            
            <!-- Hero Section -->
            <div class="career-hero">
                <h2>🚀 Grow Your Skills & Career</h2>
                <p>A co-op where your skills develop, your ideas have value, and you share in the success of what we build together.</p>
            </div>
            
            <!-- Career Timeline -->
            <div class="timeline-section">
                <h3>Your Journey With Us</h3>
                <div class="career-timeline">
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <span class="timeline-icon">🎯</span>
                            <div class="timeline-title">Month 1-3: Foundation</div>
                            <div class="timeline-desc">Learn our stack and contribute to real projects while earning as you develop new skills.</div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <span class="timeline-icon">💡</span>
                            <div class="timeline-title">Month 4-6: Innovation</div>
                            <div class="timeline-desc">Take on feature development, suggest improvements, and develop your expertise in areas that interest you.</div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <span class="timeline-icon">🏆</span>
                            <div class="timeline-title">Month 7-12: Leadership</div>
                            <div class="timeline-desc">Help guide new members, contribute to project decisions, and influence the technical direction.</div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <span class="timeline-icon">🌟</span>
                            <div class="timeline-title">Year 2+: Mastery</div>
                            <div class="timeline-desc">Build deep expertise, work on your own projects, and benefit from increasing profit shares as the co-op grows.</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mission Cards -->
            <div class="mission-cards">
                <div class="mission-card">
                    <span class="mission-icon">🛠️</span>
                    <div class="mission-title">Develop Practical Skills</div>
                    <div class="mission-desc">Work on production systems with modern technologies. Each project adds valuable experience to your portfolio.</div>
                </div>
                <div class="mission-card">
                    <span class="mission-icon">💰</span>
                    <div class="mission-title">Share in Profits</div>
                    <div class="mission-desc">Receive compensation for your contributions, with profit shares that grow as you develop expertise and the co-op succeeds.</div>
                </div>
                <div class="mission-card">
                    <span class="mission-icon">🤝</span>
                    <div class="mission-title">Own Your Future</div>
                    <div class="mission-desc">As a co-op member, you have a real voice in decisions and a direct stake in our collective success.</div>
                </div>
            </div>
            
            <!-- Skills Growth Chart -->
            <div class="skills-chart-section">
                <h3>📈 Skills & Earnings Growth</h3>
                <p>Technical abilities and earning potential can develop together as you gain experience with the co-op.</p>
                <div class="chart-container">
                    <canvas id="skillsGrowthChart"></canvas>
                </div>
            </div>
            

            </div>
        </div>

        <script>
            // Dystopian 1984 color palette for charts
            const careerColors = {
                primary: '#8B4513',
                secondary: '#A0522D',
                accent: '#CD853F',
                success: '#556B2F',
                warning: '#8B7355',
                danger: '#8B0000',
                light: '#D2B48C'
            };

            // Skills Growth Chart
            const skillsCtx = document.getElementById('skillsGrowthChart').getContext('2d');
            new Chart(skillsCtx, {
                type: 'line',
                data: {
                    labels: ['Month 1', 'Month 6', 'Year 1', 'Year 2', 'Year 3', 'Year 5'],
                    datasets: [{
                        label: 'Technical Skills',
                        data: [30, 50, 70, 85, 92, 98],
                        borderColor: careerColors.primary,
                        backgroundColor: careerColors.primary + '20',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 8,
                        pointBackgroundColor: careerColors.primary,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 3
                    }, {
                        label: 'Earning Potential',
                        data: [20, 35, 55, 75, 88, 95],
                        borderColor: careerColors.accent,
                        backgroundColor: careerColors.accent + '20',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 8,
                        pointBackgroundColor: careerColors.accent,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 3
                    }, {
                        label: 'Leadership Impact',
                        data: [10, 25, 45, 65, 80, 90],
                        borderColor: careerColors.warning,
                        backgroundColor: careerColors.warning + '20',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 8,
                        pointBackgroundColor: careerColors.warning,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 16,
                                    family: 'Roboto',
                                    weight: 'bold'
                                },
                                color: '#000000',
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: careerColors.primary,
                            borderWidth: 2,
                            cornerRadius: 10,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                font: {
                                    size: 14,
                                    family: 'Roboto',
                                    weight: 'bold'
                                },
                                color: '#000000'
                            },
                            grid: {
                                color: 'rgba(241,245,249,0.1)',
                                lineWidth: 1
                            }
                        },
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                font: {
                                    size: 14,
                                    family: 'Roboto',
                                    weight: 'bold'
                                },
                                color: '#000000',
                                callback: function(value) {
                                    return value + '%';
                                }
                            },
                            grid: {
                                color: 'rgba(241,245,249,0.1)',
                                lineWidth: 1
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        </script>
    </section>

    <!-- Audio Break Section -->
    <section style="background: #1C1C1C; padding: 60px 0; border-top: 1px solid rgba(0,200,81,0.2);">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <div style="background: rgba(255,255,255,0.08); padding: 40px; border-radius: 20px; text-align: center; backdrop-filter: blur(10px);">
                        <div style="margin-bottom: 30px;">
                            <i class="fas fa-headphones" style="font-size: 48px; color: #8B4513; margin-bottom: 20px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);"></i>
                            <h3 style="color: #8B4513; font-size: 32px; font-weight: bold; margin-bottom: 15px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                                🎧 Learn More (Audio)
                            </h3>
                            <p style="color: #D2B48C; font-size: 18px; font-weight: 500; line-height: 1.6; max-width: 600px; margin: 0 auto 25px;">
                                An audio overview of our exit strategy and how the co-op structure benefits all members.
                            </p>
                        </div>
                        
                        <div style="background: rgba(0,200,81,0.1); padding: 30px; border-radius: 15px; margin-bottom: 20px;">
                            <audio controls preload="metadata" style="width: 100%; max-width: 600px; height: 60px; border-radius: 10px;">
                                <source src="assets/The Accelerated Exit Strategy.mp3" type="audio/mpeg">
                                <p style="color: #D2B48C;">Your browser doesn't support audio playback. 
                                   <a href="assets/The Accelerated Exit Strategy.mp3" download style="color: #8B4513;">Download the audio file</a>
                                </p>
                            </audio>
                        </div>
                        
                        <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; margin-top: 20px;">
                            <div style="display: flex; align-items: center; gap: 8px; color: #8B7355; font-size: 14px;">
                                <i class="fas fa-clock" style="color: #8B4513;"></i>
                                <span>Audio duration varies</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px; color: #8B7355; font-size: 14px;">
                                <i class="fas fa-volume-up" style="color: #8B4513;"></i>
                                <span>Headphones recommended</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px; color: #8B7355; font-size: 14px;">
                                <i class="fas fa-download" style="color: #8B4513;"></i>
                                <a href="assets/The Accelerated Exit Strategy.mp3" download style="color: #8B4513; text-decoration: none;">
                                    Download MP3
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Application Form Section -->
    <section id="apply-form" class="application-form">
        <div class="container page-bgc">
            <div class="row">
                <div class="col-sm-12">
                    <div class="title-box">
                        <p>Interested in joining?</p>
                        <h2 class="title mt0" style="color: #8B4513;">Application Form</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="boxed">
                    <div class="col-sm-8 col-sm-offset-2">
                        <div class="application-form-container" style="background: rgba(255,255,255,0.15); padding: 40px; border-radius: 12px;">
                            <?php
                            $form_submitted = false;
                            $error_message = '';
                            
                            if ($_POST && isset($_POST['application_form'])) {
                                $name = $_POST['name'] ?? '';
                                $email = $_POST['email'] ?? '';
                                $discord = $_POST['discord'] ?? '';
                                $experience = $_POST['experience'] ?? '';
                                $interests = $_POST['interests'] ?? '';
                                $portfolio = $_POST['portfolio'] ?? '';
                                $motivation = $_POST['motivation'] ?? '';
                                
                                if (!empty($name) && !empty($email) && !empty($motivation)) {
                                    // Here you would normally process/email the application
                                    $form_submitted = true;
                                } else {
                                    $error_message = 'Please fill in all required fields (Name, Email, and Motivation).';
                                }
                            }
                            ?>
                            
                            <?php if ($form_submitted): ?>
                                <div style="background-color: #8B4513; color: #0f1419; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                                    <h3 style="margin: 0 0 10px; color: #0f1419;">Application Received!</h3>
                                    <p style="margin: 0; color: #0f1419;">Thanks for applying! We'll review your application and get back to you soon via email or Discord.</p>
                                </div>
                            <?php elseif ($error_message): ?>
                                <div style="background-color: #f87171; color: #D2B48C; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                                    <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
                                </div>
                            <?php endif; ?>
                            
                            <form action="joinus.php#apply-form" method="post" class="coop-application-form">
                                <input type="hidden" name="application_form" value="1">
                                
                                <div style="margin-bottom: 25px;">
                                    <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold;">Name *</label>
                                    <input type="text" name="name" required 
                                           style="width: 100%; padding: 12px; border-radius: 6px; background: rgba(255,255,255,0.1); color: #D2B48C; font-size: 16px;"
                                           placeholder="Your full name">
                                </div>
                                
                                <div style="margin-bottom: 25px;">
                                    <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold;">Email *</label>
                                    <input type="email" name="email" required 
                                           style="width: 100%; padding: 12px; border-radius: 6px; background: rgba(255,255,255,0.1); color: #D2B48C; font-size: 16px;"
                                           placeholder="your@email.com">
                                </div>
                                
                                <div style="margin-bottom: 25px;">
                                    <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold;">Discord Username</label>
                                    <input type="text" name="discord" 
                                           style="width: 100%; padding: 12px; border-radius: 6px; background: rgba(255,255,255,0.1); color: #D2B48C; font-size: 16px;"
                                           placeholder="username#1234 or @username">
                                </div>
                                
                                <div style="margin-bottom: 25px;">
                                    <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold;">Experience & Skills</label>
                                    <textarea name="experience" rows="4" 
                                              style="width: 100%; padding: 12px; border-radius: 6px; background: rgba(255,255,255,0.1); color: #D2B48C; font-size: 16px; resize: vertical;"
                                              placeholder="Tell us about your experience with coding, gaming, server admin, Unity/Unreal, or any relevant skills..."></textarea>
                                </div>
                                
                                <div style="margin-bottom: 25px;">
                                    <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold;">Areas of Interest</label>
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; color: #8B7355;">
                                        <label style="display: flex; align-items: center; margin-bottom: 0;">
                                            <input type="checkbox" name="interests[]" value="game-hosting" style="margin-right: 8px;">
                                            Game Server Hosting
                                        </label>
                                        <label style="display: flex; align-items: center; margin-bottom: 0;">
                                            <input type="checkbox" name="interests[]" value="game-dev" style="margin-right: 8px;">
                                            Game Development
                                        </label>
                                        <label style="display: flex; align-items: center; margin-bottom: 0;">
                                            <input type="checkbox" name="interests[]" value="testing-qa" style="margin-right: 8px;">
                                            Testing & QA
                                        </label>
                                        <label style="display: flex; align-items: center; margin-bottom: 0;">
                                            <input type="checkbox" name="interests[]" value="customer-support" style="margin-right: 8px;">
                                            Customer Support
                                        </label>
                                        <label style="display: flex; align-items: center; margin-bottom: 0;">
                                            <input type="checkbox" name="interests[]" value="web-dev" style="margin-right: 8px;">
                                            Web Development
                                        </label>
                                        <label style="display: flex; align-items: center; margin-bottom: 0;">
                                            <input type="checkbox" name="interests[]" value="sysadmin" style="margin-right: 8px;">
                                            System Administration
                                        </label>
                                    </div>
                                </div>
                                
                                <div style="margin-bottom: 25px;">
                                    <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold;">Portfolio / GitHub / Projects</label>
                                    <input type="url" name="portfolio" 
                                           style="width: 100%; padding: 12px; border-radius: 6px; background: rgba(255,255,255,0.1); color: #D2B48C; font-size: 16px;"
                                           placeholder="https://github.com/yourname or link to your work">
                                </div>
                                
                                <div style="margin-bottom: 30px;">
                                    <label style="display: block; color: #8B4513; margin-bottom: 8px; font-weight: bold;">Why do you want to join our co-op? *</label>
                                    <textarea name="motivation" rows="5" required 
                                              style="width: 100%; padding: 12px; border-radius: 6px; background: rgba(255,255,255,0.1); color: #D2B48C; font-size: 16px; resize: vertical;"
                                              placeholder="Tell us what excites you about this opportunity, what you hope to learn/build, and why profit-sharing appeals to you..."></textarea>
                                </div>
                                
                                <div style="text-align: center;">
                                    <button type="submit" 
                                            style="background: #8B4513; color: #0f1419; border: none; padding: 15px 30px; font-size: 18px; font-weight: bold; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                        Submit Application
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contract Section -->
    <section class="contract-section" style="background: #1C1C1C; padding: 60px 0;">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="title-box" style="margin-bottom: 40px;">
                        <p style="color: #8B7355;">Review the</p>
                        <h2 class="title mt0" style="color: #8B4513;">Co-op Agreement</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-10 col-sm-offset-1">
                    <div class="contract-paper" style="
                        background: #ffffff; 
                        color: #000000; 
                        padding: 60px; 
                        margin: 20px 0; 
                        border-radius: 8px; 
                        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                        font-family: 'Times New Roman', serif;
                        line-height: 1.6;
                        max-width: 800px;
                        margin-left: auto;
                        margin-right: auto;
                    ">
                        <div style="text-align: center; margin-bottom: 40px; border-bottom: 2px solid #000; padding-bottom: 20px;">
                            <h1 style="margin: 0; font-size: 24px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px;">
                                CO-OP CONTRIBUTOR AGREEMENT
                            </h1>
                        </div>

                        <p style="margin-bottom: 20px;">
                            This Agreement is between World Domination Software / GameServers World ("The Co-Op"), 
                            managed by __________________ ("Manager"), and __________________ ("Contributor").
                        </p>

                        <h3 style="font-weight: bold; margin: 30px 0 15px 0; font-size: 18px;">1. NATURE OF RELATIONSHIP</h3>
                        <ul style="margin-left: 20px; margin-bottom: 25px;">
                            <li>This is not an employer/employee relationship.</li>
                            <li>Contributor is a co-op partner working voluntarily in exchange for a share of profits.</li>
                            <li>The Manager is the final authority for all decisions.</li>
                        </ul>

                        <h3 style="font-weight: bold; margin: 30px 0 15px 0; font-size: 18px;">2. RESPONSIBILITIES</h3>
                        <ul style="margin-left: 20px; margin-bottom: 25px;">
                            <li>Contributor agrees to assist with tasks such as game testing, customer support, 
                                Linux/Windows administration, mod development, coding, website updates, and/or game development.</li>
                            <li>Contributor will log hours and tasks completed for tracking purposes.</li>
                            <li>Contributor may also work on personal side projects using Co-Op tools and infrastructure, 
                                provided they do not conflict with official co-op work. Profits from such side projects 
                                are 100% the Contributor's to keep.</li>
                        </ul>

                        <h3 style="font-weight: bold; margin: 30px 0 15px 0; font-size: 18px;">3. PROFIT SHARING</h3>
                        <ul style="margin-left: 20px; margin-bottom: 25px;">
                            <li>Profits are allocated quarterly after all expenses are deducted.</li>
                            <li>Manager allocation (for reinvestment and operations):</li>
                            <ul style="margin-left: 20px; margin-top: 10px;">
                                <li>a) First $5,000 profit → Manager gets 75% ($3,750), Contributors share 25% ($1,250)</li>
                                <li>b) Next $45,000 profit → Manager gets 40%, Contributors share 60%</li>
                                <li>c) Next $50,000 profit → Manager gets 25%, Contributors share 75%</li>
                                <li>d) Over $100,000 profit → Manager gets 15%, Contributors share 85%</li>
                            </ul>
                            <li style="margin-top: 15px;">Additionally, the Manager receives 1 equal share from the contributors' pool as a developer.</li>
                            <li style="margin-top: 15px;">Advances: Contributors may receive advances for work performed. Advances are deducted 
                                from any future profit share the Contributor earns.</li>
                        </ul>

                        <h3 style="font-weight: bold; margin: 30px 0 15px 0; font-size: 18px;">4. EXAMPLES</h3>
                        <ul style="margin-left: 20px; margin-bottom: 25px;">
                            <li><strong>$20,000 quarterly profit:</strong></li>
                            <ul style="margin-left: 20px; margin-top: 5px; margin-bottom: 15px;">
                                <li>First $5K: Manager gets $3,750, Contributors get $1,250</li>
                                <li>Next $15K: Manager gets $6,000 (40%), Contributors get $9,000 (60%)</li>
                                <li>Manager also gets 1 dev share from contributor pool</li>
                                <li><strong>Result:</strong> Manager receives ~$11,781 (59%), Contributors share ~$8,219 (41%)</li>
                            </ul>
                            <li><strong>$60,000 quarterly profit:</strong></li>
                            <ul style="margin-left: 20px; margin-top: 5px; margin-bottom: 15px;">
                                <li>First $5K: Manager $3,750, Contributors $1,250</li>
                                <li>Next $45K: Manager $18,000 (40%), Contributors $27,000 (60%)</li>
                                <li>Next $10K: Manager $2,500 (25%), Contributors $7,500 (75%)</li>
                                <li>Manager also gets 1 dev share from contributor pool</li>
                                <li><strong>Result:</strong> Manager receives ~$28,636 (48%), Contributors share ~$31,364 (52%)</li>
                            </ul>
                            <li><strong>$120,000 quarterly profit:</strong></li>
                            <ul style="margin-left: 20px; margin-top: 5px; margin-bottom: 15px;">
                                <li>First $5K: Manager $3,750, Contributors $1,250</li>
                                <li>Next $45K: Manager $18,000, Contributors $27,000</li>
                                <li>Next $50K: Manager $12,500, Contributors $37,500</li>
                                <li>Next $20K: Manager $3,000, Contributors $17,000</li>
                                <li>Manager also gets 1 dev share from contributor pool</li>
                                <li><strong>Result:</strong> Manager receives ~$41,818 (35%), Contributors share ~$78,182 (65%)</li>
                            </ul>
                            <li><strong>$300,000 quarterly profit:</strong></li>
                            <ul style="margin-left: 20px; margin-top: 5px;">
                                <li>First $5K: Manager $3,750, Contributors $1,250</li>
                                <li>Next $45K: Manager $18,000, Contributors $27,000</li>
                                <li>Next $50K: Manager $12,500, Contributors $37,500</li>
                                <li>Next $200K: Manager $30,000 (15%), Contributors $170,000 (85%)</li>
                                <li>Manager also gets 1 dev share from contributor pool</li>
                                <li><strong>Result:</strong> Manager receives ~$75,294 (25%), Contributors share ~$224,706 (75%)</li>
                            </ul>
                        </ul>

                        <h3 style="font-weight: bold; margin: 30px 0 15px 0; font-size: 18px;">5. AMENDMENTS</h3>
                        <ul style="margin-left: 20px; margin-bottom: 25px;">
                            <li>Terms of this agreement, including profit-sharing percentages, may be updated if 
                                approved by a majority of active contributors. The Manager retains final decision authority.</li>
                        </ul>

                        <h3 style="font-weight: bold; margin: 30px 0 15px 0; font-size: 18px;">6. TERMINATION</h3>
                        <ul style="margin-left: 20px; margin-bottom: 40px;">
                            <li>Either party may end participation at any time with written notice.</li>
                            <li>Contributor retains no ownership rights but does keep credit for work performed 
                                and profits already earned.</li>
                        </ul>

                        <div style="margin-top: 50px; border-top: 1px solid #8B7355; padding-top: 30px;">
                            <p style="margin-bottom: 40px;">
                                <strong>Signed:</strong> ______________________ &nbsp;&nbsp;&nbsp;&nbsp; <strong>Date:</strong> __________
                            </p>
                        </div>
                    </div>

                    <!-- Download Section -->
                    <div style="text-align: center; margin: 30px 0;">
                        <div style="background: rgba(255,255,255,0.1); padding: 25px; border-radius: 12px;">
                            <h3 style="color: #8B4513; margin-bottom: 15px;">Download Contract Package</h3>
                            <p style="color: #8B7355; margin-bottom: 20px;">
                                Get the complete contract documents including fillable forms and legal templates.
                            </p>
                            <a href="assets/contract.zip" download 
                               style="
                                   display: inline-flex; 
                                   align-items: center; 
                                   background: #8B4513; 
                                   color: #0f1419; 
                                   padding: 12px 24px; 
                                   text-decoration: none; 
                                   font-weight: bold; 
                                   border-radius: 8px; 
                                   font-size: 16px;
                                   transition: all 0.3s ease;
                               ">
                                <i class="ion-android-download" style="margin-right: 8px; font-size: 18px;"></i>
                                Download contract.zip
                            </a>
                        </div>
                    </div>
                </div>
            </div>
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


