<?php
if (!defined('WDS_SYSTEM')) {
    define('WDS_SYSTEM', true);
}

$allowedDocs = [
    'overview' => '../../content/docs/mystical-islands/reference/overview.md',
    'getting-started' => '../../content/docs/mystical-islands/reference/getting-started/getting-started.md',
    'setting-up-atavism' => '../../content/docs/mystical-islands/reference/setting-up-atavism/setting-up-atavism.md',
    'creating-item-templates' => '../../content/docs/mystical-islands/reference/creating-item-templates/creating-item-templates.md',
    'advanced-editing' => '../../content/docs/mystical-islands/reference/advanced-editing/advanced-editing.md',
    'atavism-client-unity-installation' => '../../content/docs/mystical-islands/reference/atavism-client-unity-installation/atavism-client-unity-installation.md',
    'atavism-windows-manager' => '../../content/docs/mystical-islands/reference/atavism-windows-manager/atavism-windows-manager.md',
    'external-packages' => '../../content/docs/mystical-islands/reference/external-packages/external-packages.md',
    'game-settings-plugin' => '../../content/docs/mystical-islands/reference/game-settings-plugin/game-settings-plugin.md',
    'troubleshooting' => '../../content/docs/mystical-islands/reference/troubleshooting/troubleshooting.md',
];

$docKey = isset($_GET['doc']) ? $_GET['doc'] : 'overview';

if (!array_key_exists($docKey, $allowedDocs)) {
    http_response_code(404);
    echo '<div class="boxed"><div class="alert alert-danger" role="alert">Unknown document.</div></div>';
    exit;
}

$docPath = __DIR__ . '/' . $allowedDocs[$docKey];

if (!file_exists($docPath)) {
    http_response_code(500);
    echo '<div class="boxed"><div class="alert alert-danger" role="alert">Document missing on server.</div></div>';
    exit;
}

$markdown = file_get_contents($docPath);

function mi_strip_front_matter($markdown)
{
    if (strpos($markdown, "---\n") === 0) {
        $end = strpos($markdown, "\n---", 3);
        if ($end !== false) {
            $markdown = substr($markdown, $end + 4);
        }
    }
    return ltrim($markdown);
}

function mi_format_inline($text)
{
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

    $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $text);
    $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
    $text = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2" target="_blank" rel="noopener">$1</a>', $text);

    return $text;
}

function mi_render_table($rows)
{
    if (count($rows) < 2) {
        return '';
    }

    $header = array_shift($rows);
    $html = '<table class="table table-sm" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">';
    $html .= '<thead><tr>';
    foreach ($header as $cell) {
        $html .= '<th style="border: 1px solid #333; padding: 6px; background: rgba(139,69,19,0.15);">' . mi_format_inline($cell) . '</th>';
    }
    $html .= '</tr></thead><tbody>';
    foreach ($rows as $row) {
        if (empty($row)) {
            continue;
        }
        $html .= '<tr>';
        foreach ($row as $cell) {
            $html .= '<td style="border: 1px solid #333; padding: 6px;">' . mi_format_inline($cell) . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';

    return $html;
}

function mi_convert_markdown($markdown)
{
    $markdown = mi_strip_front_matter($markdown);
    $markdown = str_replace(["\r\n", "\r"], "\n", $markdown);
    $lines = explode("\n", $markdown);

    $html = '';
    $inUl = false;
    $inOl = false;
    $inBlockquote = false;
    $inCode = false;
    $codeBuffer = '';

    $closeLists = function () use (&$html, &$inUl, &$inOl) {
        if ($inUl) {
            $html .= '</ul>';
            $inUl = false;
        }
        if ($inOl) {
            $html .= '</ol>';
            $inOl = false;
        }
    };

    $closeBlockquote = function () use (&$html, &$inBlockquote) {
        if ($inBlockquote) {
            $html .= '</blockquote>';
            $inBlockquote = false;
        }
    };

    $lineCount = count($lines);
    for ($i = 0; $i < $lineCount; $i++) {
        $line = rtrim($lines[$i]);

        if (preg_match('/^```/', $line)) {
            if ($inCode) {
                $html .= '<pre style="background: #272822; color: #f8f8f2; padding: 12px; border-radius: 6px; overflow-x: auto;"><code>' . htmlspecialchars($codeBuffer, ENT_QUOTES, 'UTF-8') . '</code></pre>';
                $codeBuffer = '';
                $inCode = false;
            } else {
                $closeLists();
                $closeBlockquote();
                $inCode = true;
            }
            continue;
        }

        if ($inCode) {
            $codeBuffer .= ($codeBuffer === '' ? '' : "\n") . $lines[$i];
            continue;
        }

        if ($line === '') {
            $closeLists();
            $closeBlockquote();
            continue;
        }

        $nextLine = ($i + 1 < $lineCount) ? rtrim($lines[$i + 1]) : '';
        if (strpos($line, '|') !== false && strpos($nextLine, '|') !== false && preg_match('/^\s*\|?\s*:?[-| ]+:?\s*\|/', $nextLine)) {
            $closeLists();
            $closeBlockquote();

            $tableRows = [];
            $tableRows[] = array_map('trim', explode('|', trim($line, '| ')));
            $i++; // skip separator line
            while ($i + 1 < $lineCount) {
                $candidate = rtrim($lines[$i + 1]);
                if ($candidate === '' || strpos($candidate, '|') === false) {
                    break;
                }
                $tableRows[] = array_map('trim', explode('|', trim($candidate, '| ')));
                $i++;
            }
            $html .= mi_render_table($tableRows);
            continue;
        }

        if (preg_match('/^\s*[0-9]+\.[ ]+(.*)$/', $line, $matches)) {
            $closeBlockquote();
            if (!$inOl) {
                $closeLists();
                $html .= '<ol style="margin-left: 20px;">';
                $inOl = true;
            }
            $html .= '<li>' . mi_format_inline($matches[1]) . '</li>';
            continue;
        }

        if (preg_match('/^\s*[-*][ ]+(.*)$/', $line, $matches)) {
            $closeBlockquote();
            if (!$inUl) {
                $closeLists();
                $html .= '<ul style="margin-left: 20px;">';
                $inUl = true;
            }
            $content = $matches[1];
            if (preg_match('/^\[( |x)\]\s+(.*)$/i', $content, $checkboxMatches)) {
                $checked = strtolower($checkboxMatches[1]) === 'x';
                $content = '<input type="checkbox" disabled' . ($checked ? ' checked' : '') . '> ' . mi_format_inline($checkboxMatches[2]);
            } else {
                $content = mi_format_inline($content);
            }
            $html .= '<li>' . $content . '</li>';
            continue;
        }

        if (preg_match('/^>\s?(.*)$/', $line, $matches)) {
            if (!$inBlockquote) {
                $closeLists();
                $html .= '<blockquote style="border-left: 4px solid #8B4513; padding-left: 16px; margin-left: 0; color: #4a4a4a;">';
                $inBlockquote = true;
            }
            $html .= '<p>' . mi_format_inline($matches[1]) . '</p>';
            continue;
        }

        $closeLists();
        $closeBlockquote();

        if (preg_match('/^(#{1,6})[ ]+(.*)$/', $line, $matches)) {
            $level = strlen($matches[1]);
            $html .= '<h' . $level . '>' . mi_format_inline($matches[2]) . '</h' . $level . '>';
            continue;
        }

        $html .= '<p>' . mi_format_inline($line) . '</p>';
    }

    $closeLists();
    $closeBlockquote();

    return $html;
}

$html = mi_convert_markdown($markdown);

?><div class="boxed">
    <div style="background: #D4CFC0; border: 1px solid #333; border-radius: 8px; padding: 30px; color: #1a1a1a;">
        <?php echo $html; ?>
    </div>
</div>
