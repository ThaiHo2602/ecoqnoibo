<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$site = [
    'name' => 'Eco-Q House',
    'tagline' => 'Định Hình Mới - Giá Trị Mới',
    'phone' => '0814 305 555',
    'email' => 'hello@ecoqhouse.vn',
    'address' => '123 Nguyễn Văn Linh, Quận 7, TP. Hồ Chí Minh',
    'working_hours' => 'Thứ 2 - Chủ nhật, 8:00 - 20:00',
    'logo' => 'assets/images/logo.png',
    'hero_image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1600&q=80',
    'socials' => [
        'facebook' => 'https://www.facebook.com/profile.php?id=61574255939629',
        'tiktok' => 'https://www.tiktok.com/@naruma262',
        'zalo' => 'https://zalo.me/0814305555',
        'phone_link' => 'tel:0814305555',
    ],
];

function landing_route_map(): array
{
    return [
        '' => '',
        'index.php' => '',
        'gioi-thieu.php' => 'gioi-thieu',
        'du-an.php' => 'du-an',
        'chi-tiet-du-an.php' => 'chi-tiet-du-an',
        'tin-tuc.php' => 'tin-tuc',
        'bai-viet.php' => 'bai-viet',
        'tuyen-dung.php' => 'tuyen-dung',
        'viec-lam.php' => 'viec-lam',
        'lien-he.php' => 'lien-he',
        'gioi-thieu' => 'gioi-thieu',
        'du-an' => 'du-an',
        'chi-tiet-du-an' => 'chi-tiet-du-an',
        'tin-tuc' => 'tin-tuc',
        'bai-viet' => 'bai-viet',
        'tuyen-dung' => 'tuyen-dung',
        'viec-lam' => 'viec-lam',
        'lien-he' => 'lien-he',
    ];
}

function site_base_path(): string
{
    static $basePath = null;

    if ($basePath !== null) {
        return $basePath;
    }

    $requestPath = str_replace('\\', '/', parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
    $requestPath = rtrim($requestPath, '/');
    $landingRootPages = [
        '',
        '/',
        '/index.php',
        '/gioi-thieu',
        '/gioi-thieu.php',
        '/du-an',
        '/du-an.php',
        '/chi-tiet-du-an',
        '/chi-tiet-du-an.php',
        '/tin-tuc',
        '/tin-tuc.php',
        '/bai-viet',
        '/bai-viet.php',
        '/tuyen-dung',
        '/tuyen-dung.php',
        '/viec-lam',
        '/viec-lam.php',
        '/lien-he',
        '/lien-he.php',
    ];

    if (in_array($requestPath, $landingRootPages, true)) {
        $basePath = '';
        return $basePath;
    }

    if ($requestPath === '/public/company' || str_starts_with($requestPath, '/public/company/')) {
        $basePath = '/public/company';
        return $basePath;
    }

    if ($requestPath === '/company' || str_starts_with($requestPath, '/company/')) {
        $basePath = '/company';
        return $basePath;
    }

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $directory = str_replace('\\', '/', dirname($scriptName));
    $basePath = rtrim($directory, '/.');
    $basePath = preg_replace('#/public(?=/|$)#', '', $basePath) ?: $basePath;

    return $basePath === '' ? '' : $basePath;
}

function site_asset_base_path(): string
{
    $requestPath = str_replace('\\', '/', parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
    $requestPath = rtrim($requestPath, '/');

    if ($requestPath === '/public/company' || str_starts_with($requestPath, '/public/company/')) {
        return '/public/company';
    }

    if ($requestPath === '/company' || str_starts_with($requestPath, '/company/')) {
        return '/company';
    }

    return '/company';
}

function site_origin(): string
{
    $isHttps = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    $scheme = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    return $scheme . '://' . $host;
}

function base_url(string $path = ''): string
{
    $basePath = site_base_path();
    $normalizedPath = ltrim($path, '/');
    $pathWithoutQuery = $normalizedPath;
    $queryString = '';

    if (str_contains($normalizedPath, '?')) {
        [$pathWithoutQuery, $queryString] = explode('?', $normalizedPath, 2);
        $queryString = '?' . $queryString;
    }

    if ($normalizedPath === '') {
        $targetPath = $basePath === '' ? '/' : $basePath . '/';
        return site_origin() . $targetPath;
    }

    if ($pathWithoutQuery === 'index.php') {
        $targetPath = $basePath === '' ? '/' : $basePath . '/';
        return site_origin() . $targetPath . $queryString;
    }

    if (str_starts_with($pathWithoutQuery, 'assets/')) {
        return site_origin() . rtrim(site_asset_base_path(), '/') . '/' . $normalizedPath;
    }

    $routeMap = landing_route_map();
    if (array_key_exists($pathWithoutQuery, $routeMap)) {
        $mappedPath = $routeMap[$pathWithoutQuery];
        $targetPath = $mappedPath === '' ? '/' : '/' . $mappedPath;

        return site_origin() . $targetPath . $queryString;
    }

    return site_origin() . ($basePath === '' ? '' : $basePath) . '/' . $normalizedPath;
}

function app_url(string $path = ''): string
{
    $basePath = site_base_path();
    $appBasePath = preg_replace('#/(public/)?company$#', '', $basePath) ?: '';
    $normalizedPath = ltrim($path, '/');

    if ($normalizedPath === '') {
        $targetPath = $appBasePath === '' ? '/' : $appBasePath . '/';
        return site_origin() . $targetPath;
    }

    return site_origin() . ($appBasePath === '' ? '' : $appBasePath) . '/' . $normalizedPath;
}

function company_auth_check(): bool
{
    return isset($_SESSION['auth_user']) && is_array($_SESSION['auth_user']);
}

function current_year(): string
{
    return date('Y');
}

function format_vn_date(string $date): string
{
    return date('d/m/Y', strtotime($date));
}
