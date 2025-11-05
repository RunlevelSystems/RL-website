<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameServer Panel - OGP Admin Guide</title>
    
    <!-- CSS -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/main.css" rel="stylesheet">
    <link href="../../assets/css/readability-improvements.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500&display=swap' rel='stylesheet'>
    
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #1C1C1C;
            color: #D2B48C;
            margin: 0;
            padding: 0;
        }
        
        .guide-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 280px;
            background-color: #2C2C2C; /* Concrete background */
            color: #D9D9D9; /* Accessible light gray */
            padding: 30px 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            border-right: 2px solid #4B4B4B; /* Gritty Urban border */
        }
        
        .sidebar h1 {
            color: #8B4513; /* Keep rust for non-critical headings */
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
            border-bottom: 2px solid #4B4B4B; /* Gritty Urban border */
            padding-bottom: 15px;
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-link {
            display: block;
            padding: 15px;
            color: #B0B0B0; /* Shadowed Desolation for subtle navigation */
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        
        .sidebar-link:hover {
            background-color: #4B4B4B; /* Gritty Urban surface */
            color: #F5F5F5; /* Off-white on hover */
            border-color: #7A7A7A;
            text-decoration: none;
        }
        
        .sidebar-link.active {
            background-color: #4B4B4B; /* Gritty Urban surface */
            color: #F5F5F5; /* Off-white for active */
            font-weight: 500;
        }
        
        .sidebar-link i {
            margin-right: 12px;
            width: 20px;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 40px;
            background-color: #2C2C2C; /* Concrete background */
            min-height: 100vh;
            width: calc(100% - 280px);
        }
        
        .content-section {
            display: none;
            animation: fadeIn 0.3s ease-in;
        }
        
        .content-section.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .content-section h2 {
            color: #8B4513;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .content-section h3 {
            color: #8B4513;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .content-section p {
            font-size: 16px;
            line-height: 1.7;
            color: #8B7355;
            margin-bottom: 20px;
        }
        
        .card {
            background-color: #2a2a2a;
            border: 1px solid #8B4513;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 20px;
        }
        
        .card h3 {
            color: #8B4513;
            margin-bottom: 15px;
        }
        
        .architecture-flow {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            align-items: center;
            margin: 40px 0;
        }
        
        .flow-item {
            text-align: center;
            margin: 20px;
            max-width: 200px;
        }
        
        .flow-item .icon {
            font-size: 48px;
            color: #8B4513;
            margin-bottom: 15px;
        }
        
        .flow-arrow {
            font-size: 32px;
            color: #8B7355;
            margin: 0 20px;
        }
        
        .code-block {
            background: transparent; /* No background color */
            border: 1px solid #555555;
            border-radius: 6px;
            padding: 25px;
            margin: 20px 0;
            font-family: 'Fira Code', monospace;
            font-size: 15px;
            line-height: 1.9;
            overflow-x: auto;
        }
        
        .code-block code {
            color: #E6D3B7; /* Normal text color - brighter tan */
            background: transparent;
        }
        
        .xml-explorer {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }
        
        .xml-code {
            background: transparent; /* No background color */
            border: 1px solid #555555;
            border-radius: 8px;
            padding: 25px;
        }
        
        .xml-explanation {
            background-color: #2a2a2a;
            border: 1px solid #555555;
            border-radius: 8px;
            padding: 25px;
        }
        
        .xml-tag {
            cursor: pointer;
            color: #B8621B; /* Rust highlight color for tags */
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .xml-tag:hover {
            color: #D2691E; /* Brighter rust on hover */
            text-decoration: underline;
        }
        
        .accordion-item {
            margin-bottom: 15px;
        }
        
        .accordion-button {
            width: 100%;
            text-align: left;
            background-color: #4A4A4A;
            color: #D2B48C;
            border: 1px solid #777777;
            padding: 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .accordion-button:hover {
            background-color: #777777;
        }
        
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background-color: #3A3A3A;
            border: 1px solid #777777;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
        
        .accordion-content.active {
            max-height: 1000px;
            padding: 20px;
        }
        
        .accordion-arrow {
            transition: transform 0.3s ease;
        }
        
        .accordion-button.active .accordion-arrow {
            transform: rotate(180deg);
        }
        
        .home-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .footer-info {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #555555;
            font-size: 12px;
            color: #8B7355;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 20px;
            }
            
            .xml-explorer {
                grid-template-columns: 1fr;
            }
            
            .architecture-flow {
                flex-direction: column;
            }
            
            .flow-arrow {
                transform: rotate(90deg);
            }
        }
    </style>
</head>
<body>
    <?php 
    // Page-specific variables
    $current_page = 'projects';
    $page_subtitle = 'OGP Admin Guide';
    $page_description = 'Comprehensive GameServer Panel administration guide';
    $page_title = 'Admin';
    $page_title_thin = 'Guide';
    ?>

    <!-- Include Site Header -->
    <?php include '../includes/header.html'; ?>
    
    <!-- Include Navigation Header -->
    <?php include '../includes/navigation.php'; ?>

    <div class="guide-container">
        <aside class="sidebar">
            <h1><i class="fas fa-server"></i> OGP Admin Guide</h1>
            <nav>
                <ul class="sidebar-nav">
                    <li><a href="#" class="sidebar-link active" data-target="home">
                        <i class="fas fa-home"></i> Home
                    </a></li>
                    <li><a href="#" class="sidebar-link" data-target="core-concepts">
                        <i class="fas fa-cogs"></i> Core Concepts
                    </a></li>
                    <li><a href="#" class="sidebar-link" data-target="agent-management">
                        <i class="fas fa-robot"></i> Agent Management
                    </a></li>
                    <li><a href="#" class="sidebar-link" data-target="xml-deep-dive">
                        <i class="fas fa-code"></i> Game XML Deep Dive
                    </a></li>
                    <li><a href="#" class="sidebar-link" data-target="add-new-game">
                        <i class="fas fa-plus-circle"></i> Add a New Game
                    </a></li>
                    <li><a href="#" class="sidebar-link" data-target="panelstart">
                        <i class="fas fa-rocket"></i> The panelStart Script
                    </a></li>
                </ul>
            </nav>
            <div class="footer-info">
                <p>Interactive guide for GameServer Panel (OGP Fork)</p>
                <p>&copy; 2025 World Domination Software</p>
                
                <div class="quick-links" style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #444;">
                    <h4 style="color: #8B4513; margin-bottom: 15px; font-size: 14px;">Related Resources</h4>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <a href="index.php" class="btn-wds" style="font-size: 11px; padding: 6px 12px; text-decoration: none;">
                            <i class="fas fa-home" style="margin-right: 5px;"></i>
                            Project Overview
                        </a>
                        <a href="industry-stats.php" class="btn-wds" style="font-size: 11px; padding: 6px 12px; text-decoration: none;">
                            <i class="fas fa-chart-line" style="margin-right: 5px;"></i>
                            Industry Stats
                        </a>
                        <a href="../../contact.php" class="btn-wds" style="font-size: 11px; padding: 6px 12px; text-decoration: none;">
                            <i class="fas fa-envelope" style="margin-right: 5px;"></i>
                            Get Support
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <div id="home" class="content-section active">
                <h2>Welcome to the GameServer Panel Interactive Guide</h2>
                <p>This comprehensive guide covers everything you need to know about managing a GameServer Panel instance, from basic concepts to advanced XML configuration. Use the navigation on the left to explore different topics.</p>
                
                <div class="home-grid">
                    <div class="card">
                        <h3>What is GameServer Panel?</h3>
                        <p>GameServer Panel is our enhanced fork of Open Game Panel (OGP), a powerful open-source game server control panel. It allows you to manage game servers through a user-friendly web interface, with added commercial billing, support systems, and multi-location management.</p>
                    </div>
                    <div class="card">
                        <h3>Key Architecture</h3>
                        <p>GameServer Panel operates on a distributed model: a central <strong>Web Panel</strong> that sends commands to one or more <strong>Agents</strong> installed on your server machines. The Agents execute these commands to manage the actual <strong>Game Server</strong> processes.</p>
                    </div>
                    <div class="card">
                        <h3>Commercial Features</h3>
                        <p>Unlike standard OGP, our fork includes integrated billing systems, automated provisioning, professional support ticketing, and enterprise-grade security features designed for hosting providers.</p>
                    </div>
                    <div class="card">
                        <h3>Multi-Location Support</h3>
                        <p>Manage game servers across multiple data centers and regions from a single interface. Load balancing, failover capabilities, and centralized monitoring included.</p>
                    </div>
                </div>

                <div style="background-color: #2A2A2A; padding: 30px; border-radius: 8px; margin-top: 40px; border: 1px solid #555555;">
                    <h3 style="color: #8B4513; margin-bottom: 25px;">
                        <i class="fas fa-download" style="margin-right: 10px;"></i>
                        Quick Installation
                    </h3>
                    <p style="color: #8B7355; margin-bottom: 20px;">
                        Get started with GameServer Panel using our automated installation script:
                    </p>
                    <div style="background-color: #0f1419; padding: 20px; border-radius: 4px; border-left: 4px solid #555555; margin-bottom: 20px;">
                        <div style="color: #8B7355; margin-bottom: 8px; font-family: 'Courier New', monospace;"># One-line installer for Ubuntu/Debian/CentOS</div>
                        <div style="color: #D2B48C; font-family: 'Courier New', monospace; font-size: 14px;">curl -fsSL https://install.gameserver-panel.org | sudo bash</div>
                    </div>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <a href="https://github.com/World-Domination-Software/GameServer-Panel" class="btn-wds" target="_blank">
                            <i class="fab fa-github" style="margin-right: 8px;"></i>
                            View Source
                        </a>
                        <a href="https://github.com/World-Domination-Software/GameServer-Panel/releases/latest" class="btn-wds" target="_blank">
                            <i class="fas fa-download" style="margin-right: 8px;"></i>
                            Download Release
                        </a>
                        <a href="https://docs.gameserver-panel.org/installation" class="btn-wds" target="_blank">
                            <i class="fas fa-book" style="margin-right: 8px;"></i>
                            Full Install Guide
                        </a>
                    </div>
                </div>
            </div>

            <div id="core-concepts" class="content-section">
                <h2>Core Concepts: Panel & Agent Architecture</h2>
                <p>Understanding the relationship between the Web Panel and the Agent is crucial for managing and troubleshooting GameServer Panel. The entire system is built on this remote-command architecture.</p>
                
                <div class="card">
                    <div class="architecture-flow">
                        <div class="flow-item">
                            <div class="icon"><i class="fas fa-desktop"></i></div>
                            <h3>Web Panel</h3>
                            <p>The user interface where you and your customers manage servers, users, and games. Includes billing and support systems.</p>
                        </div>
                        <div class="flow-arrow"><i class="fas fa-arrow-right"></i></div>
                        <div class="flow-item">
                            <div class="icon"><i class="fas fa-robot"></i></div>
                            <h3>GameServer Agent</h3>
                            <p>A daemon running on your game server machine(s). It listens for encrypted commands from the Panel.</p>
                        </div>
                        <div class="flow-arrow"><i class="fas fa-arrow-right"></i></div>
                        <div class="flow-item">
                            <div class="icon"><i class="fas fa-gamepad"></i></div>
                            <h3>Game Server</h3>
                            <p>The actual game process (e.g., srcds_run, bedrock_server) managed by the Agent.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="agent-management" class="content-section">
                <h2>Agent Management</h2>
                <p>The Agent is the workhorse of GameServer Panel. It must be installed and running on every machine where you want to host game servers. Configuration differs between Linux and Windows.</p>
                
                <div class="card">
                    <h3>Linux Agent Installation</h3>
                    <p>The Linux agent is typically installed via our enhanced installer script. Key steps include downloading, making it executable, and running the installer with commercial features enabled.</p>
                    <div class="code-block">
                        <code># Download the GameServer Panel installer
wget -N https://github.com/World-Domination-Software/GameServer-Panel/raw/master/gsp-agent-latest.deb

# Install it
sudo dpkg -i gsp-agent-latest.deb

# Run the enhanced configurator
sudo /usr/share/gsp_agent/gsp_agent_config.sh --commercial</code>
                    </div>
                </div>
                
                <div class="card">
                    <h3>Windows Agent (via Cygwin)</h3>
                    <p>The Windows Agent uses Cygwin to provide a Unix-like environment. After installation, configure it by editing the gsp_agent.conf file with your panel details and encryption key.</p>
                    <div class="code-block">
                        <code># Example gsp_agent.conf
PANEL_URL="https://yourpanel.example.com"
AGENT_IP="0.0.0.0"
AGENT_PORT="12679"
ENCRYPTION_KEY="your_secret_key_from_panel"
COMMERCIAL_MODE="true"
BILLING_API="https://yourpanel.example.com/api/billing"</code>
                    </div>
                </div>
            </div>

            <div id="xml-deep-dive" class="content-section">
                <h2>Game XML Deep Dive</h2>
                <p>Game configurations are the heart of GameServer Panel's flexibility. They are defined in XML files located in the modules directory. Click on any colored XML tag below to see a detailed explanation.</p>

                <div class="xml-explorer">
                    <div class="xml-code">
                        <h3>Interactive XML Explorer</h3>
                        <div class="code-block">
                            <code><span class="xml-tag" data-tag="game_config">&lt;game_config&gt;</span>

    <span class="xml-tag" data-tag="game_key">&lt;game_key&gt;</span>my_custom_game_linux64<span class="xml-tag" data-tag="game_key">&lt;/game_key&gt;</span>

    <span class="xml-tag" data-tag="protocol">&lt;protocol&gt;</span>lgsl<span class="xml-tag" data-tag="protocol">&lt;/protocol&gt;</span>

    <span class="xml-tag" data-tag="lgsl_query_name">&lt;lgsl_query_name&gt;</span>unreal2<span class="xml-tag" data-tag="lgsl_query_name">&lt;/lgsl_query_name&gt;</span>

    <span class="xml-tag" data-tag="installer">&lt;installer&gt;</span>steamcmd<span class="xml-tag" data-tag="installer">&lt;/installer&gt;</span>

    <span class="xml-tag" data-tag="game_name">&lt;game_name&gt;</span>My Custom Game Server<span class="xml-tag" data-tag="game_name">&lt;/game_name&gt;</span>

    <span class="xml-tag" data-tag="server_exec_name">&lt;server_exec_name&gt;</span>MyGameServer<span class="xml-tag" data-tag="server_exec_name">&lt;/server_exec_name&gt;</span>

    <span class="xml-tag" data-tag="query_port" data-attributes='type="add"'>&lt;query_port type="add"&gt;</span>1<span class="xml-tag" data-tag="query_port">&lt;/query_port&gt;</span>


    <span class="xml-tag" data-tag="cli_template">&lt;cli_template&gt;</span>
        %IP% %PORT% %MAP% %PLAYERS%
    <span class="xml-tag" data-tag="cli_template">&lt;/cli_template&gt;</span>


    <span class="xml-tag" data-tag="cli_params">&lt;cli_params&gt;</span>

        <span class="xml-tag" data-tag="cli_param" data-attributes='id="MAP" cli_string="-map=" options="q"'>&lt;cli_param id="MAP" cli_string="-map=" options="q" /&gt;</span>

        <span class="xml-tag" data-tag="cli_param" data-attributes='id="IP" cli_string="-ip=" options="q"'>&lt;cli_param id="IP" cli_string="-ip=" options="q" /&gt;</span>

        <span class="xml-tag" data-tag="cli_param" data-attributes='id="PORT" cli_string="-port=" options="sq"'>&lt;cli_param id="PORT" cli_string="-port=" options="sq" /&gt;</span>

        <span class="xml-tag" data-tag="cli_param" data-attributes='id="PLAYERS" cli_string="-maxplayers=" options="s"'>&lt;cli_param id="PLAYERS" cli_string="-maxplayers=" options="s" /&gt;</span>

    <span class="xml-tag" data-tag="cli_params">&lt;/cli_params&gt;</span>


    <span class="xml-tag" data-tag="maps_location">&lt;maps_location&gt;</span>Maps<span class="xml-tag" data-tag="maps_location">&lt;/maps_location&gt;</span>

    <span class="xml-tag" data-tag="max_user_amount">&lt;max_user_amount&gt;</span>32<span class="xml-tag" data-tag="max_user_amount">&lt;/max_user_amount&gt;</span>

    <span class="xml-tag" data-tag="control_protocol">&lt;control_protocol&gt;</span>rcon2<span class="xml-tag" data-tag="control_protocol">&lt;/control_protocol&gt;</span>


    <span class="xml-tag" data-tag="mods">&lt;mods&gt;</span>

        <span class="xml-tag" data-tag="mod" data-attributes='key="base_game"'>&lt;mod key="base_game"&gt;</span>

            <span class="xml-tag" data-tag="name">&lt;name&gt;</span>Base Game<span class="xml-tag" data-tag="name">&lt;/name&gt;</span>

            <span class="xml-tag" data-tag="installer_name">&lt;installer_name&gt;</span>123456<span class="xml-tag" data-tag="installer_name">&lt;/installer_name&gt;</span>

        <span class="xml-tag" data-tag="mod">&lt;/mod&gt;</span>

    <span class="xml-tag" data-tag="mods">&lt;/mods&gt;</span>

<span class="xml-tag" data-tag="game_config">&lt;/game_config&gt;</span></code>
                        </div>
                    </div>

                    <div id="xml-explanation" class="xml-explanation">
                        <h3>Tag Explanation</h3>
                        <p>Select a tag from the XML example to learn more about its function and usage in GameServer Panel configurations.</p>
                    </div>
                </div>

                <!-- Comprehensive XML Structure Documentation -->
                <div style="margin-top: 50px;">
                    <h3 style="color: #8B4513; font-size: 28px; margin-bottom: 30px;">
                        <i class="fas fa-book-open" style="margin-right: 15px;"></i>
                        Complete XML Structure Guide
                    </h3>

                    <!-- Core Structure Section -->
                    <div class="card" style="margin-bottom: 30px;">
                        <h4 style="color: #8B4513; margin-bottom: 20px;">
                            <i class="fas fa-sitemap" style="margin-right: 10px;"></i>
                            Core XML Structure
                        </h4>
                        <p style="margin-bottom: 20px;">Every OGP game configuration follows this essential structure:</p>
                        
                        <div class="code-block" style="margin-bottom: 20px;">
                            <code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;game_config&gt;
    &lt;!-- Basic Game Information --&gt;
    &lt;game_key&gt;unique_game_identifier_os&lt;/game_key&gt;
    &lt;game_name&gt;Display Name&lt;/game_name&gt;
    &lt;server_exec_name&gt;executable_name&lt;/server_exec_name&gt;
    
    &lt;!-- Installation & Protocol --&gt;
    &lt;installer&gt;steamcmd&lt;/installer&gt;
    &lt;protocol&gt;lgsl&lt;/protocol&gt;
    
    &lt;!-- Command Line Configuration --&gt;
    &lt;cli_template&gt;%SERVER_EXEC_NAME% [parameters]&lt;/cli_template&gt;
    &lt;cli_params&gt;
        &lt;!-- Parameter definitions --&gt;
    &lt;/cli_params&gt;
    
    &lt;!-- Optional Elements --&gt;
    &lt;mods&gt;...&lt;/mods&gt;
&lt;/game_config&gt;</code>
                        </div>
                        
                        <div style="background: #2A2A2A; padding: 20px; border-radius: 8px; border-left: 4px solid #555555;">
                            <strong style="color: #C4A676;">Important Notes:</strong>
                            <ul style="margin: 10px 0 0 20px; color: #E6D3B7;">
                                <li>The <code>game_key</code> must include OS suffix (_linux64, _win32, _win64)</li>
                                <li>All elements are case-sensitive</li>
                                <li>Order of elements matters for proper parsing</li>
                                <li>Use proper XML encoding for special characters</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Required vs Optional Elements -->
                    <div class="card" style="margin-bottom: 30px;">
                        <h4 style="color: #8B4513; margin-bottom: 20px;">
                            <i class="fas fa-list-check" style="margin-right: 10px;"></i>
                            Required vs Optional Elements
                        </h4>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                            <div>
                                <h5 style="color: #C4A676; margin-bottom: 15px;">
                                    <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                                    Required Elements
                                </h5>
                                <ul style="color: #E6D3B7; line-height: 1.6;">
                                    <li><code>game_config</code> - Root container</li>
                                    <li><code>game_key</code> - Unique identifier</li>
                                    <li><code>game_name</code> - Display name</li>
                                    <li><code>server_exec_name</code> - Executable</li>
                                    <li><code>installer</code> - Installation method</li>
                                    <li><code>cli_template</code> - Start command</li>
                                </ul>
                            </div>
                            
                            <div>
                                <h5 style="color: #C4A676; margin-bottom: 15px;">
                                    <i class="fas fa-plus-circle" style="margin-right: 8px;"></i>
                                    Optional Elements
                                </h5>
                                <ul style="color: #E6D3B7; line-height: 1.6;">
                                    <li><code>protocol</code> - Query protocol</li>
                                    <li><code>query_port</code> - Port calculation</li>
                                    <li><code>cli_params</code> - Parameter formatting</li>
                                    <li><code>maps_location</code> - Maps directory</li>
                                    <li><code>max_user_amount</code> - Player limit</li>
                                    <li><code>control_protocol</code> - Management protocol</li>
                                    <li><code>mods</code> - Multiple versions</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- CLI Template Variables -->
                    <div class="card" style="margin-bottom: 30px;">
                        <h4 style="color: #8B4513; margin-bottom: 20px;">
                            <i class="fas fa-terminal" style="margin-right: 10px;"></i>
                            CLI Template Variables
                        </h4>
                        
                        <p style="margin-bottom: 20px;">Use these variables in your <code>cli_template</code> - they will be replaced with actual values:</p>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                            <div style="background: #2A2A2A; padding: 15px; border-radius: 6px; border: 1px solid #444;">
                                <h6 style="color: #C4A676; margin-bottom: 10px;">System Variables</h6>
                                <ul style="font-size: 13px; line-height: 1.5; color: #E6D3B7;">
                                    <li><code>%SERVER_EXEC_NAME%</code> - Executable name</li>
                                    <li><code>%IP%</code> - Server IP address</li>
                                    <li><code>%PORT%</code> - Main server port</li>
                                    <li><code>%HOME_PATH%</code> - Server directory</li>
                                </ul>
                            </div>
                            
                            <div style="background: #2A2A2A; padding: 15px; border-radius: 6px; border: 1px solid #444;">
                                <h6 style="color: #C4A676; margin-bottom: 10px;">Game Variables</h6>
                                <ul style="font-size: 13px; line-height: 1.5; color: #E6D3B7;">
                                    <li><code>%MAP%</code> - Starting map</li>
                                    <li><code>%PLAYERS%</code> - Max players</li>
                                    <li><code>%HOSTNAME%</code> - Server name</li>
                                    <li><code>%RCON_PASS%</code> - RCON password</li>
                                </ul>
                            </div>
                            
                            <div style="background: #2A2A2A; padding: 15px; border-radius: 6px; border: 1px solid #444;">
                                <h6 style="color: #C4A676; margin-bottom: 10px;">Custom Variables</h6>
                                <ul style="font-size: 13px; line-height: 1.5; color: #E6D3B7;">
                                    <li>Define with <code>cli_param</code> elements</li>
                                    <li>Reference as <code>%YOUR_VAR%</code></li>
                                    <li>Set formatting with <code>options</code></li>
                                    <li>Control spacing and quotes</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- CLI Parameter Options -->
                    <div class="card" style="margin-bottom: 30px;">
                        <h4 style="color: #8B4513; margin-bottom: 20px;">
                            <i class="fas fa-cog" style="margin-right: 10px;"></i>
                            CLI Parameter Formatting Options
                        </h4>
                        
                        <p style="margin-bottom: 20px;">The <code>options</code> attribute in <code>cli_param</code> controls how values are formatted:</p>
                        
                        <div class="code-block" style="margin-bottom: 20px;">
                            <code>&lt;!-- Examples of different formatting options --&gt;
&lt;cli_param id="MAP" cli_string="-map" options="s" /&gt;
&lt;!-- Result: -map de_dust2 --&gt;

&lt;cli_param id="CONFIG" cli_string="-config" options="q" /&gt;
&lt;!-- Result: -config"server.cfg" --&gt;

&lt;cli_param id="NAME" cli_string="-hostname" options="sq" /&gt;
&lt;!-- Result: -hostname "My Server" --&gt;</code>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                            <div style="background: #2A2A2A; padding: 15px; border-radius: 6px; border: 1px solid #444;">
                                <code style="color: #C4A676; font-weight: bold;">s</code>
                                <p style="margin: 8px 0 0 0; font-size: 13px; color: #E6D3B7;">Add space between flag and value</p>
                            </div>
                            <div style="background: #2A2A2A; padding: 15px; border-radius: 6px; border: 1px solid #444;">
                                <code style="color: #C4A676; font-weight: bold;">q</code>
                                <p style="margin: 8px 0 0 0; font-size: 13px; color: #E6D3B7;">Wrap value in quotes</p>
                            </div>
                            <div style="background: #2A2A2A; padding: 15px; border-radius: 6px; border: 1px solid #444;">
                                <code style="color: #C4A676; font-weight: bold;">sq</code>
                                <p style="margin: 8px 0 0 0; font-size: 13px; color: #E6D3B7;">Space + quotes combined</p>
                            </div>
                        </div>
                    </div>

                    <!-- Common Patterns -->
                    <div class="card">
                        <h4 style="color: #8B4513; margin-bottom: 20px;">
                            <i class="fas fa-puzzle-piece" style="margin-right: 10px;"></i>
                            Common Game Server Patterns
                        </h4>
                        
                        <div style="display: grid; grid-template-columns: 1fr; gap: 25px;">
                            <div>
                                <h5 style="color: #C4A676; margin-bottom: 15px;">Source Engine Games</h5>
                                <div class="code-block">
                                    <code>&lt;cli_template&gt;./srcds_run -game %GAME% +map %MAP% -port %PORT% +maxplayers %PLAYERS%&lt;/cli_template&gt;</code>
                                </div>
                            </div>
                            
                            <div>
                                <h5 style="color: #C4A676; margin-bottom: 15px;">Minecraft Servers</h5>
                                <div class="code-block">
                                    <code>&lt;cli_template&gt;java -Xmx%MEMORY%M -jar %SERVER_EXEC_NAME% --port %PORT% --world %WORLD%&lt;/cli_template&gt;</code>
                                </div>
                            </div>
                            
                            <div>
                                <h5 style="color: #C4A676; margin-bottom: 15px;">Unreal Engine Games</h5>
                                <div class="code-block">
                                    <code>&lt;cli_template&gt;%SERVER_EXEC_NAME% %MAP%?MaxPlayers=%PLAYERS%?Port=%PORT% -log&lt;/cli_template&gt;</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="add-new-game" class="content-section">
                <h2>Adding a New Game: Step-by-Step</h2>
                <p>Follow these steps to create a new game configuration XML. This process involves gathering information about the game server, creating the XML file, and testing it in the panel.</p>

                <div class="accordion-item">
                    <button class="accordion-button">
                        <span>Step 1: Gather Game Server Information</span>
                        <span class="accordion-arrow"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <div class="accordion-content">
                        <p>Before writing any XML, you need to know how to run the game server from the command line. Find out:</p>
                        <ul style="color: #8B7355; margin-left: 20px;">
                            <li>The name of the executable (e.g., srcds_run, arma3server_x64.exe)</li>
                            <li>The command-line arguments it accepts (e.g., -port, +map, -config)</li>
                            <li>Which arguments are required and which are optional</li>
                            <li>The server's working directory and file structure</li>
                        </ul>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <button class="accordion-button">
                        <span>Step 2: Create the Basic XML File</span>
                        <span class="accordion-arrow"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <div class="accordion-content">
                        <p>Create a new file, for example mygame.xml, inside the modules/ directory of your GameServer Panel installation. Start with the basic structure:</p>
                        <div class="code-block">
                            <code>&lt;game&gt;
  &lt;name&gt;My Awesome Game&lt;/name&gt;
  ... your variables and commands here ...
&lt;/game&gt;</code>
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <button class="accordion-button">
                        <span>Step 3: Define Variables (&lt;var&gt;)</span>
                        <span class="accordion-arrow"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <div class="accordion-content">
                        <p>For each command-line argument you want to control from the panel, create a var block. This defines the UI element that will appear in the panel.</p>
                        <ul style="color: #8B7355; margin-left: 20px;">
                            <li>&lt;name&gt;: The label shown in the panel (e.g., "Game Map")</li>
                            <li>&lt;option&gt;: The command-line flag (e.g., +map)</li>
                            <li>&lt;type&gt;: The input type (e.g., text, dropdown, checkbox)</li>
                            <li>&lt;default&gt;: The default value for this variable</li>
                        </ul>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <button class="accordion-button">
                        <span>Step 4: Define Commands (&lt;command&gt;)</span>
                        <span class="accordion-arrow"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <div class="accordion-content">
                        <p>The command block tells the agent how to start the server. The execute tag contains the actual command.</p>
                        <div class="code-block">
                            <code>&lt;command&gt;
  &lt;name&gt;Start&lt;/name&gt;
  &lt;execute&gt;./panelStart.sh ./game_binary -port {PORT} -ip {IP} {VAR_ALL}&lt;/execute&gt;
&lt;/command&gt;</code>
                        </div>
                        <p>Use GameServer Panel variables like {PORT}, {IP}, and {VAR_ALL}. {VAR_ALL} automatically includes all defined variables.</p>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <button class="accordion-button">
                        <span>Step 5: Test and Deploy</span>
                        <span class="accordion-arrow"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <div class="accordion-content">
                        <p>Upload your XML file and test it thoroughly:</p>
                        <ul style="color: #8B7355; margin-left: 20px;">
                            <li>Go to the "Games" section in your panel and click "Update Games List"</li>
                            <li>Create a test server instance with your new game</li>
                            <li>Check the agent logs for any startup errors</li>
                            <li>Verify the server responds on the configured ports</li>
                            <li>Test all variable configurations work correctly</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div id="panelstart" class="content-section">
                <h2>The panelStart Script</h2>
                <p>You will see panelStart.sh (Linux) or panelStart.bat (Windows) used in almost every game's start command. This script is essential for GameServer Panel to correctly track and manage game server processes.</p>
                
                <div class="card">
                    <h3>What does it do?</h3>
                    <p>The panelStart script acts as a wrapper for your game server executable. Its primary jobs are:</p>
                    <ul style="color: #8B7355; margin-left: 20px; margin-top: 15px;">
                        <li><strong>Process ID (PID) Tracking:</strong> Determines the Process ID of the launched game server and writes it to a .pid file. The Agent reads this file to manage the process.</li>
                        <li><strong>Environment Setup:</strong> Sets up necessary environment variables and changes directories before launching the main executable.</li>
                        <li><strong>Logging:</strong> Redirects the server's output streams to a log file (console.log) viewable from the web panel.</li>
                        <li><strong>Commercial Integration:</strong> In our fork, it also handles billing events and usage tracking.</li>
                    </ul>
                </div>
                
                <div class="card">
                    <h3>How to Use It</h3>
                    <p>Always prepend your actual game server command with panelStart. The script takes the command you want to run as its arguments.</p>
                    <div class="code-block">
                        <code># Correct Usage in XML execute tag
./panelStart.sh ./srcds_run -game cstrike +map de_dust2

# Incorrect Usage (GameServer Panel cannot track the process)
./srcds_run -game cstrike +map de_dust2</code>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        const contentSections = document.querySelectorAll('.content-section');
        const xmlTags = document.querySelectorAll('.xml-tag');
        const xmlExplanation = document.getElementById('xml-explanation');
        const accordionButtons = document.querySelectorAll('.accordion-button');

        const xmlDocs = {
            game_config: {
                title: '&lt;game_config&gt;',
                desc: 'The root element for the entire OGP game configuration. All other elements must be contained within this tag. There can only be one game_config element per XML file.'
            },
            game_key: {
                title: '&lt;game_key&gt;',
                desc: 'A unique identifier for this game server in OGP. Should contain only alphanumeric characters and underscores. Must include OS suffix like _linux64, _win32, _win64 to indicate compatibility.'
            },
            protocol: {
                title: '&lt;protocol&gt;',
                desc: 'Defines the query protocol used by OGP to communicate with the game server. Available protocols are: lgsl, gameq, rcon, rcon2, lcon.'
            },
            lgsl_query_name: {
                title: '&lt;lgsl_query_name&gt;',
                desc: 'The unique key referencing this game server in the LGSL protocol file. Used when protocol is set to "lgsl" for server querying and status monitoring.'
            },
            gameq_query_name: {
                title: '&lt;gameq_query_name&gt;',
                desc: 'The unique key referencing this game server in GameQ protocol files. Used when protocol is set to "gameq" for server querying and player information.'
            },
            installer: {
                title: '&lt;installer&gt;',
                desc: 'Defines the installation method for the game server. "steamcmd" is the most common value for Steam-based game servers. Other options include "rsync" for custom installations.'
            },
            game_name: {
                title: '&lt;game_name&gt;',
                desc: 'The display name for this game server that appears in the OGP interface when users are selecting which game to install. This should be user-friendly and descriptive.'
            },
            server_exec_name: {
                title: '&lt;server_exec_name&gt;',
                desc: 'The name of the server executable file that will be launched. This is the actual binary/script name (e.g., "srcds_run", "MyGameServer.exe") used in the start command.'
            },
            query_port: {
                title: '&lt;query_port&gt;',
                desc: 'Defines the relationship between server port and query port. Type="add" means query port = server port + value. Type="subtract" means query port = server port - value.'
            },
            cli_template: {
                title: '&lt;cli_template&gt;',
                desc: 'The command line template used to start the server. Variables like %PORT%, %IP%, %MAP%, %PLAYERS% will be replaced with actual values. Custom variables can also be defined.'
            },
            cli_params: {
                title: '&lt;cli_params&gt;',
                desc: 'Container for cli_param elements that define how variables in cli_template are formatted. Each cli_param specifies the command-line syntax for a variable.'
            },
            cli_param: {
                title: '&lt;cli_param&gt;',
                desc: 'Defines formatting for a variable used in cli_template. Attributes: id (variable name), cli_string (prefix), options (formatting: s=space, q=quotes, sq=space+quotes).'
            },
            maps_location: {
                title: '&lt;maps_location&gt;',
                desc: 'Path to the maps folder relative to server root. OGP will scan this directory to generate a selectable map list for server startup. Can contain map files or subdirectories.'
            },
            max_user_amount: {
                title: '&lt;max_user_amount&gt;',
                desc: 'Maximum number of players that can be set when creating this game server. This defines the upper limit for player slots in the OGP interface.'
            },
            control_protocol: {
                title: '&lt;control_protocol&gt;',
                desc: 'Protocol used for server control commands like player management. Options: rcon, rcon2, lcon. May require additional control_protocol_type for legacy compatibility.'
            },
            mods: {
                title: '&lt;mods&gt;',
                desc: 'Container for mod definitions. Each mod represents a different version or modification of the game server that can be installed and managed separately.'
            },
            mod: {
                title: '&lt;mod&gt;',
                desc: 'Defines a specific mod/version of the game server. Contains name for display and installer_name (usually Steam AppID) for installation via SteamCMD.'
            },
            name: {
                title: '&lt;name&gt;',
                desc: 'Display name used in various contexts - for mods, server parameters, or other named elements. This text appears in the OGP user interface.'
            },
            installer_name: {
                title: '&lt;installer_name&gt;',
                desc: 'The Steam AppID or identifier used by the installer to download/update the game server files. For SteamCMD, this is the numerical Steam application ID.'
            }
        };

        function showSection(targetId) {
            contentSections.forEach(section => {
                section.classList.toggle('active', section.id === targetId);
            });
            sidebarLinks.forEach(link => {
                link.classList.toggle('active', link.dataset.target === targetId);
            });
        }

        sidebarLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = e.currentTarget.dataset.target;
                showSection(targetId);
            });
        });

        xmlTags.forEach(tag => {
            tag.addEventListener('click', (e) => {
                const tagKey = e.currentTarget.dataset.tag;
                if (xmlDocs[tagKey]) {
                    const doc = xmlDocs[tagKey];
                    xmlExplanation.innerHTML = `
                        <h3 style="color: #8B4513; font-family: 'Fira Code', monospace;">${doc.title}</h3>
                        <p style="color: #8B7355;">${doc.desc}</p>
                    `;
                }
            });
        });
        
        accordionButtons.forEach(button => {
            button.addEventListener('click', () => {
                const content = button.nextElementSibling;
                const isActive = button.classList.contains('active');
                
                // Close all accordions
                accordionButtons.forEach(btn => {
                    btn.classList.remove('active');
                    btn.nextElementSibling.classList.remove('active');
                });
                
                // Open this accordion if it wasn't active
                if (!isActive) {
                    button.classList.add('active');
                    content.classList.add('active');
                }
            });
        });

        // Initialize
        showSection('home');
    </script>
</body>
</html>