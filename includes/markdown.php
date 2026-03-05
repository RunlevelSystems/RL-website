// Developed by World Domination Software LLC
<?php
// Simple Markdown renderer tailored for staff content
if (!defined('WDS_SYSTEM')) {
    die('Access denied');
}

function render_markdown($text) {
    $text = str_replace(["\r\n", "\r"], "\n", $text);
    $lines = explode("\n", $text);
    $html = '';
    $inList = false;
    $inCode = false;

    foreach ($lines as $line) {
        if (preg_match('/^```/', $line)) {
            if ($inCode) {
                $html .= "</code></pre>";
                $inCode = false;
            } else {
                $html .= "<pre class=\"md-code\"><code>";
                $inCode = true;
            }
            continue;
        }

        if ($inCode) {
            $html .= htmlspecialchars($line) . "\n";
            continue;
        }

        if (preg_match('/^\s*[-*]\s+(.+)/', $line, $matches)) {
            if (!$inList) {
                $html .= '<ul class="md-list">';
                $inList = true;
            }
            $html .= '<li>' . render_inline_markdown($matches[1]) . '</li>';
            continue;
        } else {
            if ($inList) {
                $html .= '</ul>';
                $inList = false;
            }
        }

        if (trim($line) === '') {
            $html .= "";
            continue;
        }

        if (preg_match('/^(#{1,6})\s+(.*)$/', $line, $matches)) {
            $level = strlen($matches[1]);
            $content = render_inline_markdown($matches[2]);
            $html .= "<h{$level}>{$content}</h{$level}>";
            continue;
        }

        if (preg_match('/^>\s?(.*)$/', $line, $matches)) {
            $html .= '<blockquote>' . render_inline_markdown($matches[1]) . '</blockquote>';
            continue;
        }

        $html .= '<p>' . render_inline_markdown($line) . '</p>';
    }

    if ($inList) {
        $html .= '</ul>';
    }
    if ($inCode) {
        $html .= '</code></pre>';
    }

    return $html;
}

function render_inline_markdown($text) {
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $text);
    $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
    $text = preg_replace_callback('/\[(.+?)\]\((.+?)\)/', function ($m) {
        $label = $m[1];
        $url = $m[2];
        return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener">' . $label . '</a>';
    }, $text);

    return nl2br($text);
}
