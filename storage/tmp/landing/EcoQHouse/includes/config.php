<?php

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

function site_base_path(): string
{
    static $basePath = null;

    if ($basePath !== null) {
        return $basePath;
    }

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $directory = str_replace('\\', '/', dirname($scriptName));
    $basePath = rtrim($directory, '/.');

    return $basePath === '' ? '' : $basePath;
}

function base_url(string $path = ''): string
{
    $basePath = site_base_path();
    $normalizedPath = ltrim($path, '/');

    if ($normalizedPath === '') {
        return $basePath === '' ? '/' : $basePath . '/';
    }

    return ($basePath === '' ? '' : $basePath) . '/' . $normalizedPath;
}

function current_year(): string
{
    return date('Y');
}

function format_vn_date(string $date): string
{
    return date('d/m/Y', strtotime($date));
}
