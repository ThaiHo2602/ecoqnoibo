<?php

namespace App\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            self::prepareSavePath();
            @session_start();
        }
    }

    private static function prepareSavePath(): void
    {
        $configuredPath = (string) ini_get('session.save_path');
        $path = $configuredPath;

        if (str_contains($path, ';')) {
            $parts = explode(';', $path);
            $path = (string) end($parts);
        }

        if ($path !== '' && is_dir($path) && is_writable($path)) {
            return;
        }

        $fallbackPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'sessions';

        if (! is_dir($fallbackPath)) {
            @mkdir($fallbackPath, 0775, true);
        }

        if (is_dir($fallbackPath) && is_writable($fallbackPath)) {
            ini_set('session.save_path', $fallbackPath);
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);

        return $value;
    }

    public static function destroy(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }
}
