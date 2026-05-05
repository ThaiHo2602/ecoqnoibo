<?php
$articleSlug = trim((string) ($_GET['slug'] ?? ''));

if ($articleSlug === '') {
    require_once __DIR__ . '/includes/config.php';
    header('Location: ' . base_url('tin-tuc'), true, 302);
    exit;
}

require_once __DIR__ . '/data/news-content.php';
require_once __DIR__ . '/includes/news-article-template.php';
