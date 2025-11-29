<?php
session_start();
define('WDS_SYSTEM', true);

$current_page = 'projects';
$header_class = 'projects-header inner-header';
$page_subtitle = 'OGP Admin Guide';
$page_description = 'Comprehensive GameServer Panel administration guide';
$page_title = 'Admin';
$page_title_thin = 'Guide';

$isEmbedded = (
    (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
    (isset($_SERVER['HTTP_SEC_FETCH_MODE']) && $_SERVER['HTTP_SEC_FETCH_MODE'] !== 'navigate') ||
    (isset($_GET['partial']) && $_GET['partial'] === '1')
);
?>
<?php if (!$isEmbedded): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameServer Panel - OGP Admin Guide</title>
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/navigation.php'; ?>
<?php endif; ?>

<div class="project-doc project-admin-guide">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Roboto:400,300,500,700&display=swap');
        @import url('https://fonts.googleapis.com/css?family=Oswald:400,300,700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #D4CFC0;
            color: #E6D3B7;
            margin: 0;
            padding: 0;
        }

        .guide-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background-color: #2C2C2C;
            color: #D9D9D9;
            padding: 30px 20px;
            position: relative;
            min-height: 100vh;
            border-right: 2px solid #4B4B4B;
        }

        .sidebar h1 {
            color: #8B4513;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
            border-bottom: 2px solid #4B4B4B;
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
            color: #B0B0B0;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background-color: #4B4B4B;
            color: #F5F5F5;
            border-color: #7A7A7A;
            text-decoration: none;
        }

        .sidebar-link i {
            margin-right: 12px;
            width: 20px;
        }

        .main-content {
            flex: 1 1 auto;
            padding: 40px;
            background-color: #2C2C2C;
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

        .content-section h3,
        .content-section h4,
        .content-section h5 {
            color: #8B4513;
        }

        .content-section p,
        .content-section li {
            font-size: 16px;
            line-height: 1.7;
            color: #E6D3B7;
        }

        .card {
            background-color: #2a2a2a;
            border: 1px solid #8B4513;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 20px;
        }

        .home-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
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
            max-width: 220px;
        }

        .flow-item .icon {
            font-size: 48px;
            color: #8B4513;
            margin-bottom: 15px;
        }

        .flow-arrow {
            font-size: 32px;
            color: #8B4513;
            margin: 0 20px;
        }

        .code-block {
            background-color: #101010;
            border: 1px solid #555555;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
            font-family: 'Fira Code', monospace;
            font-size: 15px;
            line-height: 1.8;
            overflow-x: auto;
            color: #F5F1E6;
        }

        .code-block code {
            background: transparent;
            color: inherit;
            white-space: pre;
            display: block;
        }

        .quick-install-callout {
            background-color: #8B4513;
            border-radius: 6px;
            padding: 20px;
            margin: 30px 0;
            border-left: 4px solid #C08040;
            color: #FFFFFF;
            font-family: 'Fira Code', monospace;
        }

        .quick-install-meta {
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }

        .quick-install-note {
            margin-top: 15px;
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #F9E4C6;
        }

        .quick-install-links {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .xml-explorer {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }

        .xml-tag {
            cursor: pointer;
            color: #B8621B;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .xml-tag:hover {
            color: #D2691E;
            text-decoration: underline;
        }

        .xml-explanation {
            background-color: #2a2a2a;
            border: 1px solid #555555;
            border-radius: 8px;
            padding: 25px;
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
            max-height: 1200px;
            padding: 20px;
        }

        .accordion-arrow {
            transition: transform 0.3s ease;
        }

        .accordion-button.active .accordion-arrow {
            transform: rotate(180deg);
        }

        .footer-info {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #555555;
            font-size: 12px;
            color: #B0B0B0;
            text-align: center;
        }

        @media (max-width: 1024px) {
            .xml-explorer {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .guide-container {
                flex-direction: column;
            }

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
        }
    </style>

    <div class="guide-container">
        <aside class="sidebar">
            <h1><i class="fas fa-server"></i> OGP Admin Guide</h1>
            <nav>
                <ul class="sidebar-nav">
                    <li><a href="#guide-home" class="sidebar-link active" data-target="guide-home"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="#guide-core-concepts" class="sidebar-link" data-target="guide-core-concepts"><i class="fas fa-cogs"></i> Core Concepts</a></li>
                    <li><a href="#guide-agent-management" class="sidebar-link" data-target="guide-agent-management"><i class="fas fa-rocket"></i> Agent Management</a></li>
                    <li><a href="#guide-xml-deep-dive" class="sidebar-link" data-target="guide-xml-deep-dive"><i class="fas fa-code"></i> Game XML Deep Dive</a></li>
                    <li><a href="#guide-add-new-game" class="sidebar-link" data-target="guide-add-new-game"><i class="fas fa-plus-circle"></i> Add a New Game</a></li>
                    <li><a href="#agent-startup" class="sidebar-link" data-target="agent-startup"><i class="fas fa-plug"></i> Agent Start Workflow</a></li>
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
            <section id="guide-home" class="content-section">
                <h2>Welcome to the GameServer Panel Interactive Guide</h2>
                <p>This guide distills the practices we follow while maintaining the GameServer Panel fork of Open Game Panel. Use the navigation on the left to jump to architecture notes, agent installation tips, detailed XML references, and a complete walkthrough for adding new games.</p>

                <div class="home-grid">
                    <div class="card">
                        <h3>What is GameServer Panel?</h3>
                        <p>GameServer Panel is our enhanced fork of OGP. It layers commercial billing, automated provisioning, and professional support tooling on top of the battle-tested panel and agent model.</p>
                    </div>
                    <div class="card">
                        <h3>Key Architecture</h3>
                        <p>A central PHP web panel issues signed RPC calls to lightweight Agents. Those agents run on every machine that actually hosts games, handle SteamCMD installs, and report monitoring data back to the panel.</p>
                    </div>
                    <div class="card">
                        <h3>Commercial Features</h3>
                        <p>Integrated PayPal billing, coupon support, customer portal enhancements, and inventory reporting make the fork ready for production hosting providers.</p>
                    </div>
                    <div class="card">
                        <h3>Multi-Location Support</h3>
                        <p>Register as many agents as you need. The panel keeps firewall reservations, reserved ports, and monitoring information grouped per location.</p>
                    </div>
                </div>

                <div class="quick-install-callout">
                    <div class="quick-install-meta"># Deploy the panel straight from GitHub</div>
                    <div class="code-block" style="background: transparent; border: none; padding: 0; margin: 0; color: #FFFFFF;">
                        <code>curl -fsSL https://raw.githubusercontent.com/GameServerPanel/GSP/main/deploy_gsp.sh -o /tmp/deploy_gsp.sh
bash /tmp/deploy_gsp.sh</code>
                    </div>
                    <p class="quick-install-note">Always review <code>deploy_gsp.sh</code> before running it. The script clones <code>GameServerPanel/GSP</code>, syncs it to <code>/var/www/html/panel</code> (configurable), and preserves sensitive files such as <code>includes/config.inc.php</code>.</p>
                </div>

                <div class="quick-install-links">
                    <a href="https://github.com/GameServerPanel/GSP" class="btn-wds" target="_blank"><i class="fab fa-github" style="margin-right: 8px;"></i>Panel Source</a>
                    <a href="https://github.com/GameServerPanel/GSP/releases/latest" class="btn-wds" target="_blank"><i class="fas fa-download" style="margin-right: 8px;"></i>Panel Release</a>
                    <a href="https://github.com/GameServerPanel/GSP_Agent_Linux/releases/latest" class="btn-wds" target="_blank"><i class="fab fa-linux" style="margin-right: 8px;"></i>Linux Agent Release</a>
                    <a href="https://github.com/GameServerPanel/GSP-Agent-Windows/releases/latest" class="btn-wds" target="_blank"><i class="fab fa-windows" style="margin-right: 8px;"></i>Windows Agent Release</a>
                </div>
            </section>

            <section id="guide-core-concepts" class="content-section">
                <h2>Core Concepts: Panel &amp; Agent Architecture</h2>
                <p>The entire platform hinges on the relationship between the PHP panel and the Perl-based agents. Understanding the request flow helps when you debug installations or add new features.</p>

                <div class="card">
                    <div class="architecture-flow">
                        <div class="flow-item">
                            <div class="icon"><i class="fas fa-desktop"></i></div>
                            <h3>Web Panel</h3>
                            <p>Hosts the UI, billing, provisioning logic, and XML-driven server definitions. It calls the agent RPC endpoint that you configure under Administration → Game Servers.</p>
                        </div>
                        <div class="flow-arrow"><i class="fas fa-arrow-right"></i></div>
                        <div class="flow-item">
                            <div class="icon"><i class="fas fa-rocket"></i></div>
                            <h3>Agent</h3>
                            <p>Runs <code>ogp_agent.pl</code> on every machine that actually launches games. It validates the shared key from <code>Cfg/Config.pm</code> before executing commands.</p>
                        </div>
                        <div class="flow-arrow"><i class="fas fa-arrow-right"></i></div>
                        <div class="flow-item">
                            <div class="icon"><i class="fas fa-gamepad"></i></div>
                            <h3>Game Server</h3>
                            <p>Any binary defined in <code>modules/config_games/server_configs</code>. The agent starts it inside a screen session, captures the PID, and streams console output back to the panel.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="guide-agent-management" class="content-section">
                <h2>Agent Management</h2>
                <p>The Linux and Windows agents share the same configuration expectations: they must call home on TCP 12679 (default), use a matching encryption key, and know where your panel API lives. Below are the supported installation paths pulled directly from the active repositories.</p>

                <div class="card">
                    <h3>Linux Agent Installation</h3>
                    <p>The Linux agent lives in the <code>GameServerPanel/GSP_Agent_Linux</code> repository. Use the included <code>install.sh</code> and <code>agent_conf.sh</code> scripts, then keep <code>/home/ogp_agent/Cfg/Config.pm</code> in sync with your panel.</p>
                    <div class="code-block">
                        <code># Install the Linux agent
sudo apt-get install git curl rsync -y
cd /opt && sudo git clone https://github.com/GameServerPanel/GSP_Agent_Linux.git
cd GSP_Agent_Linux
sudo bash install.sh
# agent_conf.sh still expects the -s flag on Linux
sudo bash agent_conf.sh -s "yourRootPassword" -u ogp_agent

# Cfg/Config.pm controls the handshake
%Cfg::Config = (
    listen_ip    => '0.0.0.0',
    listen_port  => '12679',
    key          => 'shared-secret-from-panel',
    web_api_url  => 'https://panel.example.com/ogp_api.php',
    web_admin_api_key => '{optional_api_key}'
);</code>
                    </div>
                    <p>Re-run <code>agent_conf.sh</code> or edit <code>Cfg/Config.pm</code> any time you rotate the key inside the panel. The file is documented in <code>GSP/modules/config_games/schema_server_config.xml</code> and matches what our live agents run today.</p>
                </div>

                <div class="card">
                    <h3>Windows Agent (Cygwin)</h3>
                    <p>The Windows files are in <code>GameServerPanel/GSP-Agent-Windows</code>. Launch <code>Install\onceinstall_agent.bat</code> as Administrator to install Cygwin, create the <code>gameserver</code> service user, and copy the agent files.</p>
                    <div class="code-block">
                        <code># After the installer finishes, open the bundled Cygwin terminal
cd /OGP
bash agent_conf.sh -p "gameserverPassword"

# Configuration lives here
C:\OGP\Cfg\Config.pm</code>
                    </div>
                    <p><code>agent_conf.sh</code> writes the same structure shown above. Keep <code>key</code>, <code>listen_port</code>, and <code>web_api_url</code> aligned with the panel or the RPC handshake will fail with an “Unauthorized request” error.</p>
                </div>
            </section>

            <section id="guide-xml-deep-dive" class="content-section">
                <h2>Game XML Deep Dive</h2>
                <p>Every game definition shipped with the panel lives under <code>modules/config_games/server_configs</code>. They all conform to <code>modules/config_games/schema_server_config.xml</code> and the notes maintained in <code>OGP-Website.wiki/XML-Notes.md</code>. Click a tag to see what the schema expects.</p>

                <div class="xml-explorer">
                    <div class="xml-code">
                        <h3>Interactive XML Explorer</h3>
                        <pre class="code-block"><code><span class="xml-tag" data-tag="game_config">&lt;game_config&gt;</span>
    <span class="xml-tag" data-tag="game_key">&lt;game_key&gt;</span>valheim_linux64<span class="xml-tag" data-tag="game_key">&lt;/game_key&gt;</span>
    <span class="xml-tag" data-tag="protocol">&lt;protocol&gt;</span>lgsl<span class="xml-tag" data-tag="protocol">&lt;/protocol&gt;</span>
    <span class="xml-tag" data-tag="lgsl_query_name">&lt;lgsl_query_name&gt;</span>valheim<span class="xml-tag" data-tag="lgsl_query_name">&lt;/lgsl_query_name&gt;</span>
    <span class="xml-tag" data-tag="installer">&lt;installer&gt;</span>steamcmd<span class="xml-tag" data-tag="installer">&lt;/installer&gt;</span>
    <span class="xml-tag" data-tag="game_name">&lt;game_name&gt;</span>Valheim<span class="xml-tag" data-tag="game_name">&lt;/game_name&gt;</span>
    <span class="xml-tag" data-tag="server_exec_name">&lt;server_exec_name&gt;</span>start_server.sh<span class="xml-tag" data-tag="server_exec_name">&lt;/server_exec_name&gt;</span>
    <span class="xml-tag" data-tag="query_port">&lt;query_port type="add"&gt;</span>1<span class="xml-tag" data-tag="query_port">&lt;/query_port&gt;</span>
    <span class="xml-tag" data-tag="cli_template">&lt;cli_template&gt;</span>%HOME_PATH%/start_server.sh -name "%HOSTNAME%" -port %PORT% -world %MAP% %VAR_ALL%<span class="xml-tag" data-tag="cli_template">&lt;/cli_template&gt;</span>
    <span class="xml-tag" data-tag="cli_params">&lt;cli_params&gt;</span>
        <span class="xml-tag" data-tag="cli_param">&lt;cli_param id="HOSTNAME" cli_string="-name=" options="q" /&gt;</span>
        <span class="xml-tag" data-tag="cli_param">&lt;cli_param id="PORT" cli_string="-port=" options="sq" /&gt;</span>
        <span class="xml-tag" data-tag="cli_param">&lt;cli_param id="MAP" cli_string="-world=" options="q" /&gt;</span>
    <span class="xml-tag" data-tag="cli_params">&lt;/cli_params&gt;</span>
    <span class="xml-tag" data-tag="reserve_ports">&lt;reserve_ports&gt;</span>
        <span class="xml-tag" data-tag="port">&lt;port type="add" id="QUERY_PORT"&gt;</span>1<span class="xml-tag" data-tag="port">&lt;/port&gt;</span>
        <span class="xml-tag" data-tag="port">&lt;port type="add" id="RCON_PORT" cli_string="+rcon.port" options="sq"&gt;</span>10<span class="xml-tag" data-tag="port">&lt;/port&gt;</span>
    <span class="xml-tag" data-tag="reserve_ports">&lt;/reserve_ports&gt;</span>
    <span class="xml-tag" data-tag="cli_allow_chars">&lt;cli_allow_chars&gt;</span>;-_/\<span class="xml-tag" data-tag="cli_allow_chars">&lt;/cli_allow_chars&gt;</span>
    <span class="xml-tag" data-tag="maps_location">&lt;maps_location&gt;</span>saves/worlds<span class="xml-tag" data-tag="maps_location">&lt;/maps_location&gt;</span>
    <span class="xml-tag" data-tag="max_user_amount">&lt;max_user_amount&gt;</span>10<span class="xml-tag" data-tag="max_user_amount">&lt;/max_user_amount&gt;</span>
    <span class="xml-tag" data-tag="control_protocol">&lt;control_protocol&gt;</span>rcon2<span class="xml-tag" data-tag="control_protocol">&lt;/control_protocol&gt;</span>
    <span class="xml-tag" data-tag="mods">&lt;mods&gt;</span>
        <span class="xml-tag" data-tag="mod">&lt;mod key="default"&gt;</span>
            <span class="xml-tag" data-tag="name">&lt;name&gt;</span>Dedicated<span class="xml-tag" data-tag="name">&lt;/name&gt;</span>
            <span class="xml-tag" data-tag="installer_name">&lt;installer_name&gt;</span>896660<span class="xml-tag" data-tag="installer_name">&lt;/installer_name&gt;</span>
        <span class="xml-tag" data-tag="mod">&lt;/mod&gt;</span>
    <span class="xml-tag" data-tag="mods">&lt;/mods&gt;</span>
<span class="xml-tag" data-tag="game_config">&lt;/game_config&gt;</span></code></pre>
                    </div>
                    <div id="xml-explanation" class="xml-explanation">
                        <h3>Tag Explanation</h3>
                        <p>Select a tag to pull the description straight from <code>XML-Notes.md</code> and the current <code>schema_server_config.xml</code>.</p>
                    </div>
                </div>

                <div style="margin-top: 50px;">
                    <h3 style="color: #8B4513; font-size: 28px; margin-bottom: 30px;">
                        <i class="fas fa-book-open" style="margin-right: 15px;"></i>
                        Complete XML Structure Guide
                    </h3>

                    <div class="card">
                        <h4><i class="fas fa-sitemap" style="margin-right: 10px;"></i>Reference Files</h4>
                        <p>Cross-check every change against two canonical sources:</p>
                        <ul>
                            <li><code>modules/config_games/schema_server_config.xml</code> &mdash; validates element order, attributes, and enumerations.</li>
                            <li><code>../OGP-Website.wiki/XML-Notes.md</code> &mdash; human-readable explanations maintained by the OGP/GSP teams.</li>
                            <li><code>modules/config_games/server_configs/</code> &mdash; real game definitions to mimic.</li>
                        </ul>
                    </div>

                    <div class="card">
                        <h4><i class="fas fa-terminal" style="margin-right: 10px;"></i>CLI Template &amp; Parameters</h4>
                        <p><code>&lt;cli_template&gt;</code> must reference variables that have either built-in meaning (see the schema enumeration) or custom entries defined under <code>&lt;cli_params&gt;</code>. Formatting flags follow the same rules explained in <code>XML-Notes.md</code>.</p>
                        <div class="code-block">
                            <code>&lt;cli_param id="MAP" cli_string="-map" options="s" /&gt;
&lt;!-- Result: -map de_dust2 --&gt;

&lt;cli_param id="CONFIG" cli_string="-config" options="q" /&gt;
&lt;!-- Result: -config "server.cfg" --&gt;

&lt;cli_param id="NAME" cli_string="-hostname" options="sq" /&gt;
&lt;!-- Result: -hostname "My Server" --&gt;</code>
                        </div>
                    </div>

                    <div class="card">
                        <h4><i class="fas fa-network-wired" style="margin-right: 10px;"></i>Reserve Ports &amp; Allowed Characters</h4>
                        <p>Use <code>&lt;reserve_ports&gt;</code> to define offsets relative to %PORT%. <code>&lt;cli_allow_chars&gt;</code> loosens the command-line sanitizer when a game needs characters like semicolons.</p>
                        <div class="code-block">
                            <code>&lt;reserve_ports&gt;
    &lt;port type="add" id="WEB_ADMIN_PORT" cli_string="-webadminport=" options="sq"&gt;5&lt;/port&gt;
    &lt;port type="add" id="STEAM_PORT"&gt;19238&lt;/port&gt;
&lt;/reserve_ports&gt;
&lt;cli_allow_chars&gt;:;-_\&lt;/cli_allow_chars&gt;</code>
                        </div>
                    </div>

                    <div class="card">
                        <h4><i class="fas fa-sliders-h" style="margin-right: 10px;"></i>Server Parameters &amp; Config Replacement</h4>
                        <p><code>&lt;server_params&gt;</code> drives the UI form fields in the panel. Each <code>&lt;param&gt;</code> or <code>&lt;group&gt;</code> entry maps to CLI options or configuration replacements. Combine them with <code>&lt;replace_texts&gt;</code> to inject variables into config files.</p>
                        <div class="code-block">
                            <code>&lt;server_params&gt;
    &lt;param key="+server.identity" type="text" id="IDENTITY"&gt;
        &lt;default&gt;my_server_identity&lt;/default&gt;
        &lt;desc&gt;Sets the Rust identity folder.&lt;/desc&gt;
    &lt;/param&gt;
    &lt;group key="network" name="Networking"&gt;
        &lt;param key="-ip" id="IP" type="text"&gt;
            &lt;caption&gt;Bind Address&lt;/caption&gt;
            &lt;default&gt;0.0.0.0&lt;/default&gt;
        &lt;/param&gt;
    &lt;/group&gt;
&lt;/server_params&gt;</code>
                        </div>
                        <div class="code-block">
                            <code>&lt;replace_texts&gt;
    &lt;text key="server.cfg"&gt;
        &lt;filepath&gt;cfg/server.cfg&lt;/filepath&gt;
        &lt;var&gt;{IP}&lt;/var&gt;
        &lt;options&gt;replace_first&lt;/options&gt;
    &lt;/text&gt;
&lt;/replace_texts&gt;</code>
                        </div>
                    </div>
                </div>
            </section>

            <section id="guide-add-new-game" class="content-section">
                <h2>Adding a New Game: Step-by-Step</h2>
                <p>The process always follows the schema: gather command-line knowledge, create the XML, validate it, and test on a staging agent. These steps mirror the workflow we use before committing new files under <code>modules/config_games/server_configs</code>.</p>

                <div class="accordion-item">
                    <button class="accordion-button">
                        <span>Step 1: Gather Game Server Information</span>
                        <span class="accordion-arrow"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <div class="accordion-content">
                        <p>Identify the executable, required flags, and supported parameters. Capture the exact command line you would run manually along with any config files that need templating.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button">
                        <span>Step 2: Create the Basic XML File</span>
                        <span class="accordion-arrow"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <div class="accordion-content">
                        <p>Place the file inside <code>modules/config_games/server_configs</code>. Stick to the order enforced by <code>schema_server_config.xml</code>.</p>
                        <div class="code-block">
                            <code>&lt;game_config&gt;
  &lt;game_key&gt;mygame_linux64&lt;/game_key&gt;
  &lt;protocol&gt;lgsl&lt;/protocol&gt;
  &lt;installer&gt;steamcmd&lt;/installer&gt;
  &lt;game_name&gt;My Game&lt;/game_name&gt;
  &lt;server_exec_name&gt;run_mygame.sh&lt;/server_exec_name&gt;
  &lt;cli_template&gt;%HOME_PATH%/run_mygame.sh %VAR_ALL%&lt;/cli_template&gt;
  &lt;cli_params&gt;
      &lt;cli_param id="PORT" cli_string="-port=" options="sq" /&gt;
  &lt;/cli_params&gt;
&lt;/game_config&gt;</code>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button">
                        <span>Step 3: Define Variables (&lt;param&gt;)</span>
                        <span class="accordion-arrow"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <div class="accordion-content">
                        <p>Each <code>&lt;param&gt;</code> becomes a form field in the panel. Use <code>type="select"</code> and <code>&lt;option&gt;</code> elements when you want to restrict input to a controlled list.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button">
                        <span>Step 4: Define Commands (&lt;command&gt;)</span>
                        <span class="accordion-arrow"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <div class="accordion-content">
                        <p>The <code>&lt;command&gt;</code> block wraps the executable call. The agent already runs it under screen, so you only provide the actual binary and parameters.</p>
                        <div class="code-block">
                            <code>&lt;command&gt;
  &lt;name&gt;Start&lt;/name&gt;
  &lt;execute&gt;./MyGameServer -port {PORT} -ip {IP} {VAR_ALL}&lt;/execute&gt;
&lt;/command&gt;</code>
                        </div>
                        <p><code>{VAR_ALL}</code> expands to every <code>&lt;param&gt;</code> value you defined in <code>&lt;server_params&gt;</code>. Use specific placeholders like <code>{PORT}</code>, <code>{IP}</code>, and <code>{MAP}</code> when you want to control ordering.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button">
                        <span>Step 5: Test and Deploy</span>
                        <span class="accordion-arrow"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <div class="accordion-content">
                        <ul>
                            <li>Run <code>xmllint --schema schema_server_config.xml mygame.xml --noout</code> to catch ordering mistakes.</li>
                            <li>Upload to a staging panel, click “Update Games List”, and provision a test server.</li>
                            <li>Validate firewall reservations and reserved ports inside the agent logs.</li>
                            <li>Commit only after the server starts/stops cleanly and all variables appear in the UI.</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section id="agent-startup" class="content-section">
                <h2>Agent Start Workflow</h2>
                <p>There is no extra <em>panelStart</em> wrapper in this project. Instead, <code>ogp_agent_run</code> builds the final command, writes PID files, and launches it inside a dedicated screen session. Your XML only needs to describe the real command-line.</p>

                <div class="card">
                    <h3>What happens when you click “Start”?</h3>
                    <ul>
                        <li>The panel validates the agent key and sends the <code>&lt;command&gt;</code> block via RPC.</li>
                        <li><code>ogp_agent_run</code> sets up environment variables (see <code>&lt;environment_variables&gt;</code>), spawns a screen session, and logs STDOUT/STDERR to <code>console.log</code>.</li>
                        <li>PID tracking happens automatically; the agent writes <code>ogp_agent.pid</code> and <code>ogp_agent_run.pid</code> files for later stop/restart calls.</li>
                    </ul>
                </div>

                <div class="card">
                    <h3>Sample Execute Block</h3>
                    <div class="code-block">
                        <code>&lt;command&gt;
  &lt;name&gt;Start&lt;/name&gt;
  &lt;execute&gt;./RustDedicated -batchmode +server.port {PORT} +server.ip {IP} {VAR_ALL}&lt;/execute&gt;
&lt;/command&gt;</code>
                    </div>
                    <p>Keep commands shell-safe and rely on <code>&lt;cli_allow_chars&gt;</code> or <code>options</code> flags if a game needs unusual punctuation. The agent will prepend <code>cd %HOME_PATH%</code> and launch the binary from the correct directory.</p>
                </div>
            </section>
        </main>
    </div>
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
            desc: 'Root element for every file. schema_server_config.xml enforces a single instance per XML.'
        },
        game_key: {
            title: '&lt;game_key&gt;',
            desc: 'Unique identifier plus OS suffix (e.g., `_linux64`, `_win32`). Referenced when the panel filters games per agent platform.'
        },
        protocol: {
            title: '&lt;protocol&gt;',
            desc: 'Query protocol used for live stats. XML-Notes lists supported values: lgsl, gameq, rcon, rcon2, lcon.'
        },
        lgsl_query_name: {
            title: '&lt;lgsl_query_name&gt;',
            desc: 'Key inside LGSL protocol files. Required when protocol=lgsl so the panel knows which status template to load.'
        },
        gameq_query_name: {
            title: '&lt;gameq_query_name&gt;',
            desc: 'Alternate query library selector. Only include when protocol=gameq.'
        },
        installer: {
            title: '&lt;installer&gt;',
            desc: 'Defines how the agent fetches files: steamcmd, manual, rsync, or custom. See XML-Notes for valid keywords.'
        },
        game_name: {
            title: '&lt;game_name&gt;',
            desc: 'Friendly name shown in the panel when customers pick a game.'
        },
        server_exec_name: {
            title: '&lt;server_exec_name&gt;',
            desc: 'Binary or script launched by the agent. Used for process detection on Linux and Windows.'
        },
        query_port: {
            title: '&lt;query_port&gt;',
            desc: 'Adjusts %QUERY_PORT% relative to %PORT%. Attribute `type` supports add/subtract per schema.'
        },
        cli_template: {
            title: '&lt;cli_template&gt;',
            desc: 'Base command appended to the executable. Supports built-in variables plus any custom values defined under &lt;cli_params&gt;.'
        },
        cli_params: {
            title: '&lt;cli_params&gt;',
            desc: 'Collection of &lt;cli_param&gt; entries describing how to format each variable. Options: s (space), q (quote), sq (space+quote), n (omit if empty).'
        },
        cli_param: {
            title: '&lt;cli_param&gt;',
            desc: 'Single variable formatter. Attributes: id (must match schema enumeration), cli_string (prefix), options (format flags).'
        },
        reserve_ports: {
            title: '&lt;reserve_ports&gt;',
            desc: 'Optional list of derived ports (Steam, query, web admin). Offsets help the agent open firewall rules automatically.'
        },
        port: {
            title: '&lt;port&gt;',
            desc: 'Child element used inside &lt;reserve_ports&gt;. Attributes map offsets to a new variable id.'
        },
        cli_allow_chars: {
            title: '&lt;cli_allow_chars&gt;',
            desc: 'Adds extra characters to the whitelist so the sanitizer permits symbols like `;` or `$`.'
        },
        maps_location: {
            title: '&lt;maps_location&gt;',
            desc: 'Directory scanned for maps so the panel can build a dropdown list.'
        },
        map_list: {
            title: '&lt;map_list&gt;',
            desc: 'Alternate approach when maps_location does not work. Points to a text file that lists maps line-by-line.'
        },
        max_user_amount: {
            title: '&lt;max_user_amount&gt;',
            desc: 'Upper limit enforced when customers request player slots.'
        },
        control_protocol: {
            title: '&lt;control_protocol&gt;',
            desc: 'Sets the RCON/LGSL control implementation. Allowed values per schema: rcon, rcon2, lcon, armabe.'
        },
        mods: {
            title: '&lt;mods&gt;',
            desc: 'Wraps one or more &lt;mod&gt; entries so customers can pick variants (Steam app IDs, DLC, etc.).'
        },
        mod: {
            title: '&lt;mod&gt;',
            desc: 'Each mod contains a key plus child elements like &lt;name&gt; and &lt;installer_name&gt; to override download targets.'
        },
        name: {
            title: '&lt;name&gt;',
            desc: 'Human-friendly label used inside mods and params.'
        },
        installer_name: {
            title: '&lt;installer_name&gt;',
            desc: 'Steam AppID or package identifier consumed by the installer.'
        }
    };

    function showSection(targetId, updateHash = true) {
        contentSections.forEach(section => {
            section.classList.toggle('active', section.id === targetId);
        });
        sidebarLinks.forEach(link => {
            link.classList.toggle('active', link.dataset.target === targetId);
        });
        const target = document.getElementById(targetId);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        if (updateHash && window.history && window.history.replaceState) {
            window.history.replaceState(null, '', '#' + targetId);
        }
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
                    <p style="color: #E6D3B7;">${doc.desc}</p>
                `;
            }
        });
    });

    accordionButtons.forEach(button => {
        button.addEventListener('click', () => {
            const content = button.nextElementSibling;
            const isActive = button.classList.contains('active');
            accordionButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.nextElementSibling.classList.remove('active');
            });
            if (!isActive) {
                button.classList.add('active');
                content.classList.add('active');
            }
        });
    });

    const initialHash = window.location.hash.replace('#', '');
    if (initialHash && document.getElementById(initialHash)) {
        showSection(initialHash, false);
    } else {
        showSection('guide-home', false);
    }
</script>

<?php if (!$isEmbedded): ?>
</body>
</html>
<?php endif; ?>
