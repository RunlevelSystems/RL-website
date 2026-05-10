<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Loop Development — Join Our Team</title>
    
    <!-- CSS -->
    <link href="assets/css/coreloop.css" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { 
            font-family: 'Exo 2', 'Inter', sans-serif; 
            background-color: #0a0a0f; 
            color: #94a3b8; 
            line-height: 1.6;
        }
        
        /* Dark theme adaptations */
        .nav-link { 
            transition: color 0.3s, border-color 0.3s; 
            color: #94a3b8;
            text-decoration: none;
        }
        .nav-link.active { color: #00d4ff; border-bottom-color: #00d4ff; }
        .nav-link:hover { color: #00d4ff; }
        
        .skill-card { 
            transition: transform 0.3s, box-shadow 0.3s; 
            background: rgba(255,255,255,0.08);
            border: 2px solid rgba(54,243,255,0.2);
            color: #94a3b8;
        }
        .skill-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 25px rgba(54,243,255,0.2);
            border-color: #00d4ff;
            background: rgba(255,255,255,0.12);
        }
        
        .chart-container { position: relative; width: 100%; max-width: 450px; margin: auto; height: 350px; }
        .bar-chart-container { position: relative; width: 100%; max-width: 800px; margin: auto; height: 400px; }
        
        @media (max-width: 768px) {
            .bar-chart-container { height: 350px; }
            .chart-container { height: 300px; }
        }
        
        /* Header styling */
        .sticky-header {
            background: rgba(8,8,8,0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(54,243,255,0.2);
        }
        
        /* Button styling */
        .btn-primary {
            background: #00d4ff;
            color: #080808;
            font-weight: 700;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: #0ea5e9;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(54,243,255,0.3);
            color: #080808;
            text-decoration: none;
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: #94a3b8;
            font-weight: 700;
            border: 2px solid rgba(54,243,255,0.3);
            padding: 10px 22px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: rgba(54,243,255,0.1);
            border-color: #00d4ff;
            color: #94a3b8;
            text-decoration: none;
        }
        
        /* Card backgrounds */
        .card-dark {
            background: rgba(255,255,255,0.08);
            border: 2px solid rgba(54,243,255,0.2);
            border-radius: 16px;
            padding: 32px;
        }
        .card-dark:hover {
            border-color: #00d4ff;
            background: rgba(255,255,255,0.12);
        }
        
        /* Text colors */
        .text-primary { color: #00d4ff !important; }
        .text-muted { color: #94a3b8 !important; }
        .text-light { color: #94a3b8 !important; }
        
        /* Gradient backgrounds */
        .gradient-bg {
            background: linear-gradient(135deg, #00d4ff 0%, #0ea5e9 50%, #0d1b2e 100%);
        }
        
        /* Process flow styling */
        .process-step {
            background: rgba(255,255,255,0.08);
            border: 2px solid rgba(54,243,255,0.2);
            border-radius: 12px;
            padding: 20px;
            margin: 8px 0;
            transition: all 0.3s ease;
        }
        .process-step:hover {
            background: rgba(255,255,255,0.12);
            border-color: #00d4ff;
        }
        
        /* Footer styling */
        .footer-dark {
            background: #0f1419;
            color: #7894b9;
            border-top: 1px solid rgba(54,243,255,0.2);
        }
        
        /* Container spacing */
        .container-custom {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Animation for slider */
        .profit-slider {
            -webkit-appearance: none;
            width: 100%;
            height: 8px;
            border-radius: 5px;
            background: rgba(255,255,255,0.2);
            outline: none;
        }
        .profit-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #00d4ff;
            cursor: pointer;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(54,243,255,0.3);
        }
        .profit-slider::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #00d4ff;
            cursor: pointer;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(54,243,255,0.3);
        }
        
        /* Progress bars */
        .progress-bar-bg {
            width: 100%;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            height: 12px;
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #00d4ff, #0ea5e9);
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        .progress-bar-fill-alt {
            height: 100%;
            background: linear-gradient(90deg, #48CAE4, #0096c7);
            border-radius: 10px;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>

    <header class="sticky-header sticky top-0 z-50 shadow-lg">
        <nav class="container-custom py-4 flex justify-between items-center" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center;">
                <a href="index.php" style="text-decoration: none;">
                    <span style="font-size: 24px; font-weight: 800; color: #00d4ff;">LEVEL X</span>
                    <span style="color: #94a3b8; font-size: 16px; margin-left: 8px;">Team Journey</span>
                </a>
            </div>
            <div style="display: none; gap: 32px;" class="nav-desktop">
                <a href="#hero" class="nav-link" style="padding-bottom: 8px; border-bottom: 2px solid transparent;">The Opportunity</a>
                <a href="#skills" class="nav-link" style="padding-bottom: 8px; border-bottom: 2px solid transparent;">Your Growth</a>
                <a href="#earnings" class="nav-link" style="padding-bottom: 8px; border-bottom: 2px solid transparent;">The Earnings</a>
                <a href="#model" class="nav-link" style="padding-bottom: 8px; border-bottom: 2px solid transparent;">Our Model</a>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="joinus.php" class="btn-primary">Join Us</a>
                <a href="index.php" class="btn-secondary">← Back to Main Site</a>
            </div>
        </nav>
    </header>

    <main>
        <section id="hero" style="padding: 80px 0 120px 0;">
            <div class="container-custom" style="text-align: center;">
                <h1 style="font-size: clamp(36px, 5vw, 64px); font-weight: 900; color: #94a3b8; line-height: 1.1; margin-bottom: 24px;">
                    Build Projects <br><span style="color: #00d4ff;">That Matter</span>
                </h1>
                <p style="margin-top: 24px; font-size: clamp(18px, 2.5vw, 24px); max-width: 900px; margin-left: auto; margin-right: auto; color: #7894b9; line-height: 1.5;">
                    Our developer co-op offers real-world experience, collaborative projects, and a share of the profits. Work on meaningful projects while building your skills and portfolio.
                </p>
                <div style="margin-top: 40px; display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
                    <a href="#earnings" class="btn-primary" style="font-size: 18px; padding: 16px 32px;">View Earnings Model</a>
                    <a href="#skills" class="btn-secondary" style="font-size: 18px; padding: 14px 30px;">See Skills Development</a>
                </div>
            </div>
        </section>

        <section id="skills" style="padding: 80px 0; background: rgba(255,255,255,0.02);">
            <div class="container-custom">
                <div style="text-align: center; margin-bottom: 48px;">
                    <h2 style="font-size: clamp(32px, 4vw, 48px); font-weight: 900; color: #94a3b8; margin-bottom: 16px;">Skills You'll Develop</h2>
                    <p style="font-size: 20px; max-width: 800px; margin: 0 auto; color: #7894b9;">Gain hands-on experience with skills that are valued in the industry. Click each card to see how it can appear on your resume.</p>
                </div>
                <div id="skill-cards-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 32px;">
                </div>
            </div>
        </section>

        <section id="earnings" style="padding: 80px 0;">
            <div class="container-custom">
                <div style="text-align: center; margin-bottom: 48px;">
                    <h2 style="font-size: clamp(32px, 4vw, 48px); font-weight: 900; color: #94a3b8; margin-bottom: 16px;">Earnings Calculator</h2>
                    <p style="font-size: 20px; max-width: 1000px; margin: 0 auto; color: #7894b9;">Our profit-sharing model is designed to be fair and transparent. As the co-op succeeds, contributor shares increase. Use the slider below to see how earnings scale at different profit levels.</p>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr; gap: 32px; align-items: center;">
                    <!-- Desktop: side by side, Mobile: stacked -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 32px; align-items: center;">
                        <div class="card-dark" style="order: 2;">
                            <h3 style="font-size: 24px; font-weight: 700; text-align: center; margin-bottom: 16px; color: #00d4ff;">Quarterly Profit</h3>
                            <input id="profit-slider" type="range" min="1000" max="300000" value="60000" step="1000" class="profit-slider">
                            <div style="text-align: center; margin-top: 16px;">
                                <span style="font-size: 36px; font-weight: 900; color: #00d4ff;" id="profit-label">$60,000</span>
                            </div>
                            <div style="margin-top: 32px; display: flex; flex-direction: column; gap: 24px;">
                                <div>
                                    <div style="display: flex; justify-content: space-between; font-weight: 600; margin-bottom: 8px;">
                                        <span>Manager Share</span>
                                        <span id="manager-share-percent" style="color: #48CAE4;"></span>
                                    </div>
                                    <div class="progress-bar-bg">
                                        <div id="manager-share-bar" class="progress-bar-fill-alt" style="width: 48%"></div>
                                    </div>
                                    <div style="text-align: right; font-size: 14px; color: #7894b9; margin-top: 4px;" id="manager-share-value"></div>
                                </div>
                                <div>
                                    <div style="display: flex; justify-content: space-between; font-weight: 600; margin-bottom: 8px;">
                                        <span>Contributor Pool</span>
                                        <span id="contributor-share-percent" style="color: #00d4ff;"></span>
                                    </div>
                                    <div class="progress-bar-bg">
                                        <div id="contributor-share-bar" class="progress-bar-fill" style="width: 52%"></div>
                                    </div>
                                    <div style="text-align: right; font-size: 14px; color: #7894b9; margin-top: 4px;" id="contributor-share-value"></div>
                                </div>
                            </div>
                        </div>
                        <div style="order: 1;">
                            <div class="chart-container">
                                <canvas id="profitDonutChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="model" style="padding: 80px 0; background: rgba(255,255,255,0.02);">
            <div class="container-custom">
                <div style="text-align: center; margin-bottom: 48px;">
                    <h2 style="font-size: clamp(32px, 4vw, 48px); font-weight: 900; color: #94a3b8; margin-bottom: 16px;">Our Revenue Model</h2>
                    <p style="font-size: 20px; max-width: 1000px; margin: 0 auto; color: #7894b9;">We generate income through two streams: steady revenue from services that fund operations, and growth potential from our own products.</p>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 48px;">
                    <div style="text-align: center;">
                        <h3 style="font-size: 28px; font-weight: 700; margin-bottom: 24px; color: #00d4ff;">🎮 Stream 1: Service Revenue</h3>
                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            <div class="process-step">Host & Manage Game Servers</div>
                            <div style="font-weight: 700; font-size: 24px; color: #475569;">↓</div>
                            <div class="process-step">Rent to Player Communities</div>
                            <div style="font-weight: 700; font-size: 24px; color: #475569;">↓</div>
                            <div class="process-step">Generate Monthly Recurring Revenue</div>
                            <div style="font-weight: 700; font-size: 24px; color: #475569;">↓</div>
                            <div style="background: rgba(54,243,255,0.2); color: #00d4ff; font-weight: 600; padding: 20px; border-radius: 12px; border: 2px solid #00d4ff;">Shared Quarterly Profits</div>
                        </div>
                    </div>
                    <div style="text-align: center;">
                        <h3 style="font-size: 28px; font-weight: 700; margin-bottom: 24px; color: #00d4ff;">🚀 Stream 2: Product Development</h3>
                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            <div class="process-step">Collaborate on Indie Game Dev</div>
                            <div style="font-weight: 700; font-size: 24px; color: #475569;">↓</div>
                            <div class="process-step">Publish on Steam, Itch.io, etc.</div>
                            <div style="font-weight: 700; font-size: 24px; color: #475569;">↓</div>
                            <div class="process-step">Game Sales & Revenue</div>
                            <div style="font-weight: 700; font-size: 24px; color: #475569;">↓</div>
                            <div style="background: rgba(54,243,255,0.2); color: #00d4ff; font-weight: 600; padding: 20px; border-radius: 12px; border: 2px solid #00d4ff;">Additional Shared Profits</div>
                        </div>
                    </div>
                </div>
                <div style="text-align: center; margin-top: 64px;">
                    <h3 style="font-size: 24px; font-weight: 700; color: #94a3b8; margin-bottom: 16px;">Tiered Profit Distribution</h3>
                    <p style="color: #7894b9; max-width: 800px; margin: 0 auto 32px; font-size: 16px;">The manager's share is higher on initial profits to reinvest in infrastructure, while the contributors' share increases significantly at higher profit levels.</p>
                    <div class="bar-chart-container">
                         <canvas id="tierBarChart"></canvas>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer-dark" style="margin-top: 80px;">
        <div class="container-custom" style="padding: 32px 20px; text-align: center;">
            <p style="margin: 0; color: #7894b9;">&copy; 2025 Core Loop Development. Building careers, together.</p>
            <p style="margin: 8px 0 0; font-size: 14px; color: #64748b;">
                <a href="index.php" style="color: #00d4ff; text-decoration: none;">← Back to Main Site</a> | 
                <a href="joinus.php" style="color: #00d4ff; text-decoration: none;">Apply Now</a>
            </p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const skillsData = [
                { title: 'Linux & Windows Server Admin', icon: '💻', resume: 'Managed and secured a Debian-based server environment for hosting live, public-facing game applications, ensuring 99.9% uptime.' },
                { title: 'Network Management', icon: '🌐', resume: 'Configured firewall rules (UFW), port forwarding, and DDoS mitigation for a high-traffic game server network.' },
                { title: 'DevOps & CI/CD', icon: '⚙️', resume: 'Implemented automated build and deployment pipelines using GitHub Actions for a collaborative development project.' },
                { title: 'Project Management', icon: '📋', resume: 'Collaborated within an agile team using project boards to manage tasks, review code, and contribute to a shared codebase.' }
            ];

            const skillCardsContainer = document.getElementById('skill-cards-container');
            skillsData.forEach(skill => {
                const card = document.createElement('div');
                card.className = 'skill-card';
                card.style.cssText = 'border-radius: 16px; padding: 32px; cursor: pointer; text-align: center; transition: all 0.3s ease;';
                card.innerHTML = `
                    <div style="font-size: 48px; margin-bottom: 16px;">${skill.icon}</div>
                    <h3 style="font-size: 20px; font-weight: 700; color: #94a3b8; margin-bottom: 8px;">${skill.title}</h3>
                    <p class="original-text" style="color: #7894b9;">Click to see a resume example</p>
                    <p class="resume-text" style="color: #00d4ff; font-weight: 600; display: none;"><strong>Resume:</strong> ${skill.resume}</p>
                `;
                skillCardsContainer.appendChild(card);
                
                card.addEventListener('click', () => {
                    const originalText = card.querySelector('.original-text');
                    const resumeText = card.querySelector('.resume-text');
                    
                    if (originalText.style.display === 'none') {
                        originalText.style.display = 'block';
                        resumeText.style.display = 'none';
                    } else {
                        originalText.style.display = 'none';
                        resumeText.style.display = 'block';
                    }
                });
            });

            // Smooth scrolling for navigation
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const target = document.querySelector(link.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });

            // Update active navigation link on scroll
            function updateActiveLink() {
                const sections = document.querySelectorAll('main > section');
                let current = '';
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    if (pageYOffset >= sectionTop - 80) {
                        current = section.getAttribute('id');
                    }
                });

                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${current}`) {
                        link.classList.add('active');
                    }
                });
            }
            window.addEventListener('scroll', updateActiveLink);
            updateActiveLink();

            // Profit simulator
            const profitSlider = document.getElementById('profit-slider');
            const profitLabel = document.getElementById('profit-label');
            const managerSharePercentLabel = document.getElementById('manager-share-percent');
            const contributorSharePercentLabel = document.getElementById('contributor-share-percent');
            const managerShareValueLabel = document.getElementById('manager-share-value');
            const contributorShareValueLabel = document.getElementById('contributor-share-value');
            const managerShareBar = document.getElementById('manager-share-bar');
            const contributorShareBar = document.getElementById('contributor-share-bar');

            // Donut chart
            const profitDonutCtx = document.getElementById('profitDonutChart').getContext('2d');
            const profitDonutChart = new Chart(profitDonutCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Contributor Pool', 'Manager Share'],
                    datasets: [{
                        data: [52, 48],
                        backgroundColor: ['#00d4ff', '#48CAE4'],
                        borderColor: '#080808',
                        borderWidth: 4,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: { 
                            display: true,
                            position: 'bottom',
                            labels: {
                                color: '#94a3b8',
                                font: {
                                    size: 16,
                                    weight: 'bold'
                                },
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#00d4ff',
                            borderWidth: 2,
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += context.parsed.toFixed(1) + '%';
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // Profit calculation function (matches joinus.php logic)
            function calculateShares(profit) {
                let managerTake = 0;
                let contributorTake = 0;
                let remainingProfit = profit;

                if (remainingProfit > 0) {
                    const firstTierProfit = Math.min(remainingProfit, 5000);
                    managerTake += firstTierProfit * 0.75;
                    contributorTake += firstTierProfit * 0.25;
                    remainingProfit -= firstTierProfit;
                }
                if (remainingProfit > 0) {
                    const secondTierProfit = Math.min(remainingProfit, 45000);
                    managerTake += secondTierProfit * 0.40;
                    contributorTake += secondTierProfit * 0.60;
                    remainingProfit -= secondTierProfit;
                }
                if (remainingProfit > 0) {
                    const thirdTierProfit = Math.min(remainingProfit, 50000);
                    managerTake += thirdTierProfit * 0.25;
                    contributorTake += thirdTierProfit * 0.75;
                    remainingProfit -= thirdTierProfit;
                }
                if (remainingProfit > 0) {
                    managerTake += remainingProfit * 0.15;
                    contributorTake += remainingProfit * 0.85;
                }

                const totalProfit = managerTake + contributorTake;
                const managerPercent = totalProfit > 0 ? (managerTake / totalProfit) * 100 : 0;
                const contributorPercent = totalProfit > 0 ? (contributorTake / totalProfit) * 100 : 0;

                return {
                    manager: { value: managerTake, percent: managerPercent },
                    contributors: { value: contributorTake, percent: contributorPercent }
                };
            }

            function updateSimulator(profit) {
                profitLabel.textContent = `$${parseInt(profit).toLocaleString()}`;
                const shares = calculateShares(profit);

                managerSharePercentLabel.textContent = `${shares.manager.percent.toFixed(1)}%`;
                contributorSharePercentLabel.textContent = `${shares.contributors.percent.toFixed(1)}%`;

                managerShareValueLabel.textContent = `$${shares.manager.value.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                contributorShareValueLabel.textContent = `$${shares.contributors.value.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

                managerShareBar.style.width = `${shares.manager.percent}%`;
                contributorShareBar.style.width = `${shares.contributors.percent}%`;
                
                profitDonutChart.data.datasets[0].data = [shares.contributors.percent, shares.manager.percent];
                profitDonutChart.update();
            }

            profitSlider.addEventListener('input', (e) => {
                updateSimulator(e.target.value);
            });
            updateSimulator(profitSlider.value);
            
            // Tier bar chart
            const tierBarCtx = document.getElementById('tierBarChart').getContext('2d');
            new Chart(tierBarCtx, {
                type: 'bar',
                data: {
                    labels: ['First $5K', 'Next $45K', 'Next $50K', 'Over $100K'],
                    datasets: [{
                        label: 'Manager Share',
                        data: [75, 40, 25, 15],
                        backgroundColor: '#48CAE4',
                    }, {
                        label: 'Contributor Pool',
                        data: [25, 60, 75, 85],
                        backgroundColor: '#00d4ff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: { 
                            stacked: true, 
                            ticks: { 
                                callback: value => `${value}%`,
                                color: '#94a3b8',
                                font: { weight: 'bold' }
                            },
                            grid: {
                                color: 'rgba(241,245,249,0.1)'
                            }
                        },
                        y: { 
                            stacked: true,
                            ticks: {
                                color: '#94a3b8',
                                font: { weight: 'bold' }
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: '#94a3b8',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#00d4ff',
                            borderWidth: 2,
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw}%`;
                                },
                                title: function(tooltipItems) {
                                    const item = tooltipItems[0];
                                    let label = item.chart.data.labels[item.dataIndex];
                                    if (Array.isArray(label)) { return label.join(' '); }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // Show desktop nav on larger screens
            function updateNavVisibility() {
                const navDesktop = document.querySelector('.nav-desktop');
                if (window.innerWidth >= 768) {
                    navDesktop.style.display = 'flex';
                } else {
                    navDesktop.style.display = 'none';
                }
            }
            window.addEventListener('resize', updateNavVisibility);
            updateNavVisibility();
        });
    </script>

    <!-- Include main site scripts for consistency -->
    <script src="assets/js/jquery-1.12.3.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>


