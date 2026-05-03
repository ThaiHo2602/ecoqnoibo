<?php

use App\Core\Database;
use App\Core\Session;

date_default_timezone_set('Asia/Ho_Chi_Minh');

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDirectory = __DIR__ . '/app/';

    if (! str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDirectory . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

Session::start();

function app_config(): array
{
    static $config;

    if ($config === null) {
        $config = [];

        foreach (glob(__DIR__ . '/config/*.php') ?: [] as $file) {
            $key = pathinfo($file, PATHINFO_FILENAME);
            $loaded = require $file;
            $config[$key] = $loaded;

            if ($key === 'app' && is_array($loaded)) {
                $config = array_merge($loaded, $config);
            }
        }
    }

    return $config;
}

function config(string $key = null, mixed $default = null): mixed
{
    $config = app_config();

    if ($key === null) {
        return $config;
    }

    $segments = explode('.', $key);
    $value = $config;

    foreach ($segments as $segment) {
        if (! is_array($value) || ! array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }

    return $value;
}

function base_path(string $path = ''): string
{
    $base = __DIR__;
    return $path === '' ? $base : $base . DIRECTORY_SEPARATOR . ltrim($path, '\\/');
}

function public_path(string $path = ''): string
{
    return base_path('public' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '\\/') : ''));
}

function app_base_url(): string
{
    $configuredBaseUrl = rtrim((string) config('base_url', ''), '/');

    if (PHP_SAPI === 'cli') {
        return $configuredBaseUrl;
    }

    $configuredHost = strtolower((string) (parse_url($configuredBaseUrl, PHP_URL_HOST) ?? ''));
    $configuredPath = trim((string) (parse_url($configuredBaseUrl, PHP_URL_PATH) ?? ''), '/');

    $forwardedHost = trim((string) ($_SERVER['HTTP_X_FORWARDED_HOST'] ?? ''));
    $requestHost = $forwardedHost !== ''
        ? trim(explode(',', $forwardedHost)[0])
        : trim((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $requestHost = preg_replace('/:\d+$/', '', $requestHost ?? '') ?? '';

    if ($requestHost === '') {
        return $configuredBaseUrl;
    }

    $httpsIndicators = [
        strtolower((string) ($_SERVER['HTTPS'] ?? '')),
        strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')),
        strtolower((string) ($_SERVER['REQUEST_SCHEME'] ?? '')),
    ];

    $scheme = in_array('https', $httpsIndicators, true) || in_array('on', $httpsIndicators, true)
        ? 'https'
        : 'http';

    $configuredIsLocal = in_array($configuredHost, ['', 'localhost', '127.0.0.1'], true);
    $requestMatchesConfigured = $configuredHost !== '' && strcasecmp($configuredHost, $requestHost) === 0;

    if (! $configuredIsLocal && $requestMatchesConfigured) {
        return $configuredBaseUrl;
    }

    $requestPath = trim((string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? ''), '/');
    $resolvedBasePath = '';

    if ($configuredPath !== '' && ($requestPath === $configuredPath || str_starts_with($requestPath, $configuredPath . '/'))) {
        $resolvedBasePath = $configuredPath;
    }

    return $scheme . '://' . $requestHost . ($resolvedBasePath !== '' ? '/' . $resolvedBasePath : '');
}

function asset(string $path): string
{
    return rtrim(app_base_url(), '/') . '/' . ltrim($path, '/');
}

function url(string $path = ''): string
{
    return rtrim(app_base_url(), '/') . '/' . ltrim($path, '/');
}

function media_url(string $path): string
{
    $normalizedPath = str_replace('\\', '/', ltrim($path, '\\/'));
    return url($normalizedPath);
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function old(string $key, mixed $default = ''): mixed
{
    return $_POST[$key] ?? $default;
}

function query(string $key, mixed $default = null): mixed
{
    return $_GET[$key] ?? $default;
}

function e(string|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function is_current_path(string $path): bool
{
    $requestPath = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/', '/');
    $basePath = trim(parse_url(app_base_url(), PHP_URL_PATH) ?? '', '/');

    if ($basePath !== '' && str_starts_with($requestPath, $basePath)) {
        $requestPath = trim(substr($requestPath, strlen($basePath)), '/');
    }

    return '/' . $requestPath === rtrim($path, '/') || ($requestPath === '' && $path === '/');
}

function current_path(): string
{
    $requestPath = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/', '/');
    $basePath = trim(parse_url(app_base_url(), PHP_URL_PATH) ?? '', '/');

    if ($basePath !== '' && str_starts_with($requestPath, $basePath)) {
        $requestPath = trim(substr($requestPath, strlen($basePath)), '/');
    }

    return '/' . $requestPath;
}

function is_path_prefix(string $prefix): bool
{
    return str_starts_with(current_path(), rtrim($prefix, '/'));
}

function request_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function activity_log(?int $userId, string $action, string $module, string $description): void
{
    try {
        Database::connection()->prepare(
            'INSERT INTO activity_logs (user_id, action, module, description, ip_address)
             VALUES (:user_id, :action, :module, :description, :ip_address)'
        )->execute([
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ]);
    } catch (Throwable) {
        // Keep the UI responsive even when logging fails.
    }
}

function abort(int $statusCode, string $message = ''): never
{
    http_response_code($statusCode);
    $pageTitle = match ($statusCode) {
        403 => 'Không có quyền truy cập',
        404 => 'Không tìm thấy trang',
        default => 'Lỗi hệ thống',
    };

    $view = match ($statusCode) {
        403 => base_path('resources/views/errors/403.php'),
        404 => base_path('resources/views/errors/404.php'),
        default => base_path('resources/views/errors/500.php'),
    };

    require $view;
    exit;
}
