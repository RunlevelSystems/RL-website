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

<div class="project-doc gsp-admin-guide">
    <style>
        :root {
            --gsp-rust: #8B4513;
            --gsp-rust-light: #C5803A;
            --gsp-charcoal: #2C2C2C;
            --gsp-ash: #3A3A3A;
            --gsp-cream: #F7E3C5;
            --gsp-text: #E6D3B7;
        }

        .gsp-admin-guide {
            font-family: 'Roboto', sans-serif;
            color: var(--gsp-text);
            background-color: var(--gsp-charcoal);
            padding: 40px 30px;
        }

        .guide-shell {
            max-width: 1100px;
            margin: 0 auto;
        }

        .guide-hero {
            background: linear-gradient(135deg, #1f1f1f, #2f2f2f);
            border: 1px solid var(--gsp-rust);
            border-radius: 8px;
            padding: 35px;
            margin-bottom: 30px;
        }

        .guide-hero h1 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 36px;
            color: var(--gsp-cream);
        }

        .guide-hero p {
            margin: 0 0 15px 0;
            max-width: 720px;
            color: var(--gsp-text);
            line-height: 1.6;
        }

        .guide-pill-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .guide-pill {
            background-color: rgba(139, 69, 19, 0.25);
            border: 1px solid rgba(197, 128, 58, 0.5);
            color: var(--gsp-cream);
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 13px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .guide-toc {
            background-color: var(--gsp-ash);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 35px;
        }

        .guide-toc h2 {
            margin: 0 0 10px 0;
            font-size: 18px;
            color: var(--gsp-rust-light);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .guide-toc ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .guide-toc a {
            color: var(--gsp-cream);
            text-decoration: none;
            padding-bottom: 2px;
            border-bottom: 1px solid transparent;
        }

        .guide-toc a:hover {
            border-bottom-color: var(--gsp-rust-light);
        }

        .guide-section {
            margin-bottom: 45px;
            padding-bottom: 35px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .guide-section:last-of-type {
            border-bottom: none;
            padding-bottom: 0;
        }

        .guide-section h2 {
            color: var(--gsp-cream);
            margin-top: 0;
            margin-bottom: 12px;
            font-size: 28px;
        }

        .guide-section p,
        .guide-section li {
            line-height: 1.7;
            color: var(--gsp-text);
        }

        .guide-section li {
            margin-bottom: 8px;
        }

        .code-block {
            background-color: #101010;
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-left: 4px solid var(--gsp-rust);
            border-radius: 6px;
            margin: 18px 0;
            padding: 18px;
            font-family: 'Fira Code', monospace;
            font-size: 15px;
            color: #F5F1E6;
            overflow-x: auto;
        }

        .callout {
            background-color: rgba(139, 69, 19, 0.15);
            border: 1px solid rgba(139, 69, 19, 0.5);
            border-radius: 6px;
            padding: 16px 18px;
            margin: 15px 0;
        }

        .callout strong {
            color: var(--gsp-cream);
        }

        .guide-tag-list {
            background-color: #1f1f1f;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 8px;
            padding: 25px;
            margin-top: 25px;
        }

        .guide-tag-list h3 {
            margin-top: 0;
            color: var(--gsp-rust-light);
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .guide-tag-list dl {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px 25px;
            margin: 0;
        }

        .guide-tag-list dt {
            font-family: 'Fira Code', monospace;
            color: var(--gsp-cream);
            font-size: 14px;
        }

        .guide-tag-list dd {
            margin: 4px 0 0 0;
            font-size: 14px;
            line-height: 1.5;
            color: var(--gsp-text);
        }

        .two-column {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        @media (max-width: 700px) {
            .gsp-admin-guide {
                padding: 25px 15px;
            }
        }
    </style>

    <div class="guide-shell">
        <header class="guide-hero" id="gsp-guide">
            <p class="guide-pill">GameServer Panel</p>
            <h1>Admin Guide</h1>
            <p>Everything we reference while maintaining the World Domination Software fork of Open Game Panel. This page keeps the language simple, focuses on real scripts/configs that exist inside the repository, and walks you through architecture, deployment, agent management, and XML authoring in a straight top-down flow.</p>
            <div class="guide-pill-group">
                <span class="guide-pill">OGP Fork</span>
                <span class="guide-pill">Commercial Billing</span>
                <span class="guide-pill">Linux + Windows Agents</span>
            </div>
        </header>

        <nav class="guide-toc">
            <h2>Sections</h2>
            <ul>
                <li><a href="#gsp-overview">Overview</a></li>
                <li><a href="#gsp-install">Install</a></li>
                <li><a href="#gsp-architecture">Architecture</a></li>
                <li><a href="#gsp-agents">Agent Ops</a></li>
                <li><a href="#gsp-xml">XML Deep Dive</a></li>
                <li><a href="#gsp-new-game">Add New Game</a></li>
                <li><a href="#gsp-operations">Ops Notes</a></li>
            </ul>
        </nav>

        <section class="guide-section" id="gsp-overview">
            <h2>Project Overview</h2>
            <p>GameServer Panel is a commercial-ready fork of OGP. We retain upstream flexibility (agents, XML-driven games) while layering on our billing, coupon, and enterprise additions. Keep three directories close when working on the project:</p>
            <ul>
                <li><code>GSP/</code> &mdash; the PHP panel itself, including <code>modules/config_games</code>.</li>
                <li><code>GSP_Agent_Linux/</code> &mdash; Perl/Linux agent with <code>install.sh</code> and <code>agent_conf.sh</code>.</li>
                <li><code>GSP-Agent-Windows/</code> &mdash; packaged Cygwin environment for Windows hosts.</li>
            </ul>
            <p>Documentation snippets also live in <code>../OGP-Website.wiki</code>. The <code>XML-Notes.md</code> file there mirrors the schema bundled with the panel.</p>
        </section>

        <section class="guide-section" id="gsp-install">
            <h2>Quick Install &amp; Upcoming Release</h2>
            <p>We ship an automated deployment script, <code>deploy_gsp.sh</code>, alongside the panel. A signed release bundle will be published on GitHub shortly; until that tag exists, grab the script straight from the repository, read it end-to-end, and then run it.</p>
            <div class="code-block">
<code>curl -fsSL https://raw.githubusercontent.com/GameServerPanel/GSP/main/deploy_gsp.sh \ 
    -o /tmp/deploy_gsp.sh
bash /tmp/deploy_gsp.sh</code>
            </div>
            <div class="callout">
                <strong>Heads-up:</strong> the release package will eventually pin the script checksum and default variables. For now, the script expects Git + rsync, syncs everything to <code>/var/www/html/panel</code> (overridable), and preserves files such as <code>includes/config.inc.php</code> and <code>modules/billing/includes/config.inc.php</code>.
            </div>
            <p>Each repository also has traditional install notes (<code>README.md</code> in GSP_Agent_Linux, the <code>Install/</code> folder in GSP-Agent-Windows). Follow those whenever you need to bootstrap a new staging machine.</p>
        </section>

        <section class="guide-section" id="gsp-architecture">
            <h2>Panel &amp; Agent Architecture</h2>
            <p>The fork still leans on the classic triad: web panel, agents, and actual game servers. Keep the following relationships in mind when troubleshooting:</p>
            <ul>
                <li><strong>Web Panel</strong> &mdash; Issues signed RPC calls, handles provisioning, billing, and UI. Runs entirely in PHP.</li>
                <li><strong>Agents</strong> &mdash; <code>ogp_agent.pl</code> daemons on each host. Default port is 12679/TCP. They validate the shared key stored in <code>Cfg/Config.pm</code>.</li>
                <li><strong>Game Servers</strong> &mdash; Defined via the XML files under <code>modules/config_games/server_configs</code>. Agents launch them inside GNU screen sessions, track PIDs, and stream console output back.</li>
            </ul>
            <p>All agent commands originate from XML definitions, so accessor names and command templates need to stay faithful to the schema.</p>
        </section>

        <section class="guide-section" id="gsp-agents">
            <h2>Agent Management</h2>
            <div class="two-column">
                <div>
                    <h3>Linux Agent</h3>
                    <p>The Linux agent lives in <code>GameServerPanel/GSP_Agent_Linux</code>. Install it with the supplied scripts, then edit <code>Cfg/Config.pm</code> whenever you rotate keys.</p>
                    <div class="code-block">
<code>sudo apt install git curl rsync -y
cd /opt && sudo git clone https://github.com/GameServerPanel/GSP_Agent_Linux.git
cd GSP_Agent_Linux
sudo bash install.sh
sudo bash agent_conf.sh -s "yourRootPassword" -u ogp_agent</code>
                    </div>
                    <p><strong>Key file:</strong> <code>/home/ogp_agent/Cfg/Config.pm</code>. It stores <code>listen_ip</code>, <code>listen_port</code>, <code>key</code>, <code>web_api_url</code>, and optional stats database credentials. These entries are documented in <code>modules/config_games/schema_server_config.xml</code> and match upstream OGP.</p>
                </div>
                <div>
                    <h3>Windows Agent</h3>
                    <p>For Windows hosts, use <code>GameServerPanel/GSP-Agent-Windows</code>. Launch <code>Install\onceinstall_agent.bat</code> as an Administrator; it bootstraps Cygwin, creates the <code>gameserver</code> service user, and copies the agent files.</p>
                    <div class="code-block">
<code># Inside the bundled Cygwin terminal
cd /OGP
bash agent_conf.sh -p "gameserverPassword"
# Config lives at
C:\\OGP\\Cfg\\Config.pm</code>
                    </div>
                    <p>Use the same shared key and API URL as you configured on the panel. When the Windows service refuses to start, inspect <code>C:\\OGP\\ogp_agent.log</code> for TLS, permission, or firewall errors.</p>
                </div>
            </div>
            <p><strong>Firewall reminder:</strong> the panel talks to agents over the configured TCP port, and agents talk back to the panel API over HTTPS. Make sure both directions are whitelisted.</p>
        </section>

        <section class="guide-section" id="gsp-xml">
            <h2>Game XML Deep Dive</h2>
            <p>Every definition under <code>modules/config_games/server_configs</code> must follow the structure enforced in <code>schema_server_config.xml</code> and described in <code>../OGP-Website.wiki/XML-Notes.md</code>. Keep tags on their own lines so diffs stay readable and so the schema validator can spot mistakes quickly.</p>
            <div class="code-block">
<code>&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;game_config&gt;
    &lt;game_key&gt;valheim_linux64&lt;/game_key&gt;
    &lt;protocol&gt;lgsl&lt;/protocol&gt;
    &lt;lgsl_query_name&gt;valheim&lt;/lgsl_query_name&gt;
    &lt;installer&gt;steamcmd&lt;/installer&gt;
    &lt;game_name&gt;Valheim&lt;/game_name&gt;
    &lt;server_exec_name&gt;start_server.sh&lt;/server_exec_name&gt;
    &lt;query_port type="add"&gt;1&lt;/query_port&gt;
    &lt;cli_template&gt;%HOME_PATH%/start_server.sh -name "%HOSTNAME%" -port %PORT% -world %MAP% %VAR_ALL%&lt;/cli_template&gt;
    &lt;cli_params&gt;
        &lt;cli_param id="HOSTNAME" cli_string="-name=" options="q" /&gt;
        &lt;cli_param id="PORT" cli_string="-port=" options="sq" /&gt;
        &lt;cli_param id="MAP" cli_string="-world=" options="q" /&gt;
    &lt;/cli_params&gt;
    &lt;reserve_ports&gt;
        &lt;port type="add" id="QUERY_PORT"&gt;1&lt;/port&gt;
        &lt;port type="add" id="RCON_PORT" cli_string="+rcon.port" options="sq"&gt;10&lt;/port&gt;
    &lt;/reserve_ports&gt;
    &lt;cli_allow_chars&gt;:-_\&lt;/cli_allow_chars&gt;
    &lt;maps_location&gt;saves/worlds&lt;/maps_location&gt;
    &lt;max_user_amount&gt;10&lt;/max_user_amount&gt;
    &lt;control_protocol&gt;rcon2&lt;/control_protocol&gt;
    &lt;mods&gt;
        &lt;mod key="default"&gt;
            &lt;name&gt;Dedicated&lt;/name&gt;
            &lt;installer_name&gt;896660&lt;/installer_name&gt;
        &lt;/mod&gt;
    &lt;/mods&gt;
    &lt;server_params&gt;
        &lt;param key="+server.identity" type="text" id="IDENTITY"&gt;
            &lt;default&gt;my_server_identity&lt;/default&gt;
            &lt;desc&gt;Sets the Rust identity folder.&lt;/desc&gt;
        &lt;/param&gt;
    &lt;/server_params&gt;
    &lt;commands&gt;
        &lt;command&gt;
            &lt;name&gt;Start&lt;/name&gt;
            &lt;execute&gt;./RustDedicated -port {PORT} -ip {IP} {VAR_ALL}&lt;/execute&gt;
        &lt;/command&gt;
    &lt;/commands&gt;
    &lt;environment_variables&gt;
        export LD_LIBRARY_PATH=$LD_LIBRARY_PATH:{OGP_HOME_DIR}/RustDedicated_Data/Plugins/x86_64
    &lt;/environment_variables&gt;
&lt;/game_config&gt;</code>
            </div>

            <div class="guide-tag-list">
                <h3>Schema + Wiki Tag Reference</h3>
                <dl>
                    <dt>&lt;game_config&gt;</dt>
                    <dd>Root container; exactly one per file.</dd>
                    <dt>&lt;game_key&gt;</dt>
                    <dd>Unique slug with OS suffix (_linux64, _win32, etc.).</dd>
                    <dt>&lt;protocol&gt;</dt>
                    <dd>Live query protocol (lgsl, gameq, rcon, rcon2, lcon).</dd>
                    <dt>&lt;lgsl_query_name&gt;</dt>
                    <dd>Key lookup for LGSL definitions.</dd>
                    <dt>&lt;gameq_query_name&gt;</dt>
                    <dd>Key lookup for GameQ entries.</dd>
                    <dt>&lt;installer&gt;</dt>
                    <dd>Installer backend: steamcmd, rsync, manual, custom.</dd>
                    <dt>&lt;game_name&gt;</dt>
                    <dd>Display name customers see in the panel.</dd>
                    <dt>&lt;server_exec_name&gt;</dt>
                    <dd>Actual binary or script name launched by the agent.</dd>
                    <dt>&lt;query_port&gt;</dt>
                    <dd>Offset between %PORT% and %QUERY_PORT%. Attribute <code>type</code> is add/subtract.</dd>
                    <dt>&lt;cli_template&gt;</dt>
                    <dd>Base command string appended after the executable.</dd>
                    <dt>&lt;cli_params&gt;</dt>
                    <dd>Wrapper for <code>&lt;cli_param&gt;</code> entries.</dd>
                    <dt>&lt;cli_param&gt;</dt>
                    <dd>Defines how each variable renders (id, cli_string, options).</dd>
                    <dt>&lt;reserve_ports&gt;</dt>
                    <dd>Lists derived ports so agents can reserve firewall slots.</dd>
                    <dt>&lt;port&gt;</dt>
                    <dd>Child element inside reserve_ports; includes offset value and optional CLI string.</dd>
                    <dt>&lt;cli_allow_chars&gt;</dt>
                    <dd>Expands the command-line whitelist with custom characters.</dd>
                    <dt>&lt;maps_location&gt;</dt>
                    <dd>Directory scanned for map names.</dd>
                    <dt>&lt;map_list&gt;</dt>
                    <dd>Fallback text file containing map entries.</dd>
                    <dt>&lt;max_user_amount&gt;</dt>
                    <dd>Upper slot limit exposed to customers.</dd>
                    <dt>&lt;control_protocol&gt;</dt>
                    <dd>Console protocol (rcon, rcon2, lcon, armabe).</dd>
                    <dt>&lt;control_protocol_type&gt;</dt>
                    <dd>Hints for legacy vs new Half-Life style control (old/new).</dd>
                    <dt>&lt;mods&gt;</dt>
                    <dd>Container for multiple build variants.</dd>
                    <dt>&lt;mod&gt;</dt>
                    <dd>Single variant with <code>key</code> plus metadata.</dd>
                    <dt>&lt;name&gt;</dt>
                    <dd>Display label for mods or params.</dd>
                    <dt>&lt;installer_name&gt;</dt>
                    <dd>Steam AppID or identifier used during install.</dd>
                    <dt>&lt;installer_login&gt;</dt>
                    <dd>Optional Steam credentials for private branches.</dd>
                    <dt>&lt;betaname&gt;</dt>
                    <dd>Steam beta branch name when needed.</dd>
                    <dt>&lt;betapwd&gt;</dt>
                    <dd>Password for protected beta branches.</dd>
                    <dt>&lt;steam_bitness&gt;</dt>
                    <dd>Forces SteamCMD bitness when non-default.</dd>
                    <dt>&lt;server_params&gt;</dt>
                    <dd>Defines the form inputs shown in the panel.</dd>
                    <dt>&lt;group&gt;</dt>
                    <dd>Logical grouping of parameters (with name/key attributes).</dd>
                    <dt>&lt;param&gt;</dt>
                    <dd>Single UI control; supports <code>type</code>, <code>id</code>, <code>key</code>.</dd>
                    <dt>&lt;option&gt;</dt>
                    <dd>Used within select/radio params to list choices.</dd>
                    <dt>&lt;attribute&gt;</dt>
                    <dd>Key/value pair inserted into <code>{VAR_ALL}</code>.</dd>
                    <dt>&lt;default&gt;</dt>
                    <dd>Default value shown in the UI or config replacements.</dd>
                    <dt>&lt;caption&gt;</dt>
                    <dd>Human-readable label above a param.</dd>
                    <dt>&lt;desc&gt;</dt>
                    <dd>Tooltip/help text for the parameter.</dd>
                    <dt>&lt;options&gt;</dt>
                    <dd>Extra formatting hints (e.g., <code>options="s"</code> adds space).</dd>
                    <dt>&lt;access&gt;</dt>
                    <dd>Restricts parameters to admin-level users.</dd>
                    <dt>&lt;replace_texts&gt;</dt>
                    <dd>Holds file edit instructions for config templating.</dd>
                    <dt>&lt;alltext&gt;</dt>
                    <dd>Grouped replacement instructions with a shared <code>key</code>.</dd>
                    <dt>&lt;text&gt;</dt>
                    <dd>Single replacement block (with <code>key</code> attribute).</dd>
                    <dt>&lt;filepath&gt;</dt>
                    <dd>Path to the file being edited.</dd>
                    <dt>&lt;var&gt;</dt>
                    <dd>Variable placeholder swapped into the file.</dd>
                    <dt>&lt;commands&gt;</dt>
                    <dd>Collection of executable definitions: Start, Stop, etc.</dd>
                    <dt>&lt;command&gt;</dt>
                    <dd>Wraps <code>&lt;name&gt;</code> and <code>&lt;execute&gt;</code> blocks.</dd>
                    <dt>&lt;execute&gt;</dt>
                    <dd>The literal command run by the agent; supports {PORT}, {IP}, {VAR_ALL}, etc.</dd>
                    <dt>&lt;environment_variables&gt;</dt>
                    <dd>Shell exports evaluated before launching the command.</dd>
                </dl>
            </div>
        </section>

        <section class="guide-section" id="gsp-new-game">
            <h2>Adding a New Game Definition</h2>
            <ol>
                <li><strong>Gather data.</strong> Identify the executable, command-line flags, config files, ports, and query protocol.</li>
                <li><strong>Create XML.</strong> Copy an existing file in <code>modules/config_games/server_configs</code>, update the tags, and validate it with <code>xmllint --schema modules/config_games/schema_server_config.xml newgame.xml --noout</code>.</li>
                <li><strong>Define parameters.</strong> Use <code>&lt;server_params&gt;</code> for every knob you want in the UI and <code>&lt;reserve_ports&gt;</code> for additional ports.</li>
                <li><strong>Test commands.</strong> Upload the XML, click “Update Games List,” and start a server through the panel. Watch <code>ogp_agent.log</code> for failures.</li>
                <li><strong>Review diffs.</strong> Keep one tag per line, document any quirks in <code>XML-Notes.md</code>, and commit.</li>
            </ol>
        </section>

        <section class="guide-section" id="gsp-operations">
            <h2>Operational Notes</h2>
            <p>Unlike some earlier documentation, we do not rely on a custom <em>panelStart</em> wrapper. The agent itself (&lt;command&gt; entries) handles PID tracking, screen sessions, stdout logging, and stop/restart signals. Keep command strings clean and rely on schema-provided tags rather than inventing new wrappers.</p>
            <ul>
                <li><strong>Logging:</strong> Agents write <code>ogp_agent.log</code>, <code>ogp_agent_run.pid</code>, and per-server <code>console.log</code> files automatically.</li>
                <li><strong>Stats:</strong> The optional stats database credentials inside <code>Cfg/Config.pm</code> are used by the resource tracking cron. Rotate them whenever you rotate the panel API key.</li>
                <li><strong>Backups:</strong> Many XML files (Rust, Minecraft, etc.) expose backup toggles. These map to agent-side scripts; keep those entries consistent when editing <code>&lt;server_params&gt;</code>.</li>
            </ul>
        </section>
    </div>
</div>

<?php if (!$isEmbedded): ?>
</body>
</html>
<?php endif; ?>
