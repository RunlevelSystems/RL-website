<?php
session_start();
define('WDS_SYSTEM', true);

require_once '../../includes/db-config.php';
require_once '../../includes/markdown.php';

$docs = [
    'overview' => [
        'title' => 'GameServer Panel Overview',
        'file'  => __DIR__ . '/../../content/docs/gsp/wiki-overview.md'
    ],
    'xml' => [
        'title' => 'XML Reference',
        'file'  => __DIR__ . '/../../content/docs/gsp/wiki-xml-guide.md'
    ],
    'windows' => [
        'title' => 'Windows Agent & WINE Notes',
        'file'  => __DIR__ . '/../../content/docs/gsp/wiki-windows-agent.md'
    ]
];

$requested = isset($_GET['doc']) ? strtolower(preg_replace('/[^a-z0-9_-]/', '', $_GET['doc'])) : 'overview';
if (!array_key_exists($requested, $docs)) {
    $requested = 'overview';
}

$docMeta = $docs[$requested];
$docContent = file_exists($docMeta['file']) ? file_get_contents($docMeta['file']) : '# Document missing';
$rendered = render_markdown($docContent);

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$current_page = 'projects';
$header_class = 'projects-header inner-header';
$page_subtitle = 'GameServer Panel Wiki';
$page_description = 'Internal knowledge base for the GSP fork.';
$page_title = 'GameServer Panel';
$page_title_thin = 'Wiki';
?>
<?php if (!$isAjax): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameServer Panel - Wiki</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/ionicons.css" rel="stylesheet">
    <link href="../../assets/css/main.css" rel="stylesheet">
    <link href="../../assets/css/wds-unified.css" rel="stylesheet">
    <link href="../../assets/css/readability-improvements.css" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/navigation.php'; ?>
<?php endif; ?>

<div class="project-doc project-admin-guide">
    <style>
        .gsp-wiki {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
            background: rgba(0,0,0,0.5);
            border-radius: 18px;
            padding: 30px;
            border: 1px solid rgba(255,255,255,0.08);
        }
        @media (max-width: 900px) {
            .gsp-wiki {
                grid-template-columns: 1fr;
            }
        }
        .gsp-wiki-nav {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .gsp-wiki-nav li {
            margin-bottom: 12px;
        }
        .gsp-wiki-nav a {
            display: block;
            padding: 10px 14px;
            border-radius: 10px;
            text-decoration: none;
            color: #fdf8e4 !important;
            border: 1px solid rgba(255,255,255,0.15);
        }
        .gsp-wiki-nav a.active,
        .gsp-wiki-nav a:hover {
            background: rgba(253,212,77,0.12);
            border-color: rgba(253,212,77,0.4);
        }
        .gsp-wiki-content {
            background: rgba(15,23,42,0.9);
            border-radius: 18px;
            padding: 25px 30px;
            border: 1px solid rgba(255,255,255,0.1);
            max-height: 70vh;
            overflow-y: auto;
        }
        .gsp-wiki-content h1,
        .gsp-wiki-content h2,
        .gsp-wiki-content h3 {
            color: #fcd34d !important;
        }
        .gsp-wiki-content pre {
            background: rgba(0,0,0,0.65);
            border-radius: 10px;
            padding: 15px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .gsp-wiki-content code {
            background: rgba(255,255,255,0.08);
            border-radius: 6px;
            padding: 2px 6px;
        }
    </style>

    <div class="gsp-wiki">
        <nav>
            <ul class="gsp-wiki-nav">
                <?php foreach ($docs as $slug => $meta): ?>
                    <li>
                        <a href="?doc=<?php echo urlencode($slug); ?>" data-doc="<?php echo htmlspecialchars($slug, ENT_QUOTES); ?>" class="<?php echo $slug === $requested ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($meta['title']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <article class="gsp-wiki-content">
            <?php echo $rendered; ?>
        </article>
    </div>
</div>
<script>
(function () {
    if (typeof loadProjectFile !== 'function') {
        return;
    }
    var links = document.querySelectorAll('.gsp-wiki-nav a[data-doc]');
    links.forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            var doc = this.getAttribute('data-doc');
            loadProjectFile('wiki.php?doc=' + encodeURIComponent(doc));
        });
    });
})();
</script>

<?php if (!$isAjax): ?>
<?php include '../../includes/footer.php'; ?>
<script src="../../assets/js/jquery-1.12.3.min.js"></script>
<script src="../../assets/js/bootstrap.min.js"></script>
</body>
</html>
<?php endif; ?>
