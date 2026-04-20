<?php

namespace App\Core;

class Auth
{
    public static function user(): ?array
    {
        return Session::get('auth_user');
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function attempt(string $username, string $password): bool
    {
        $sql = 'SELECT users.*, roles.name AS role_name, roles.display_name AS role_display_name
                FROM users
                INNER JOIN roles ON roles.id = users.role_id
                WHERE users.username = :username
                LIMIT 1';

        $statement = Database::connection()->prepare($sql);
        $statement->execute(['username' => $username]);
        $user = $statement->fetch();

        if (! $user) {
            return false;
        }

        if ($user['account_status'] !== 'active') {
            Session::flash('error', 'Tài khoản của bạn đang bị khóa.');
            return false;
        }

        $inactiveDays = (int) config('security.inactive_lock_days', 7);
        if (! empty($user['last_login_at'])) {
            $lastLogin = strtotime($user['last_login_at']);
            if ($lastLogin !== false && $lastLogin < strtotime("-{$inactiveDays} days")) {
                Database::connection()
                    ->prepare('UPDATE users SET account_status = :status WHERE id = :id')
                    ->execute([
                        'status' => 'locked',
                        'id' => $user['id'],
                    ]);

                Session::flash('error', 'Tài khoản đã bị khóa do không đăng nhập trong 1 tuần.');
                return false;
            }
        }

        if (! password_verify($password, $user['password'])) {
            return false;
        }

        Database::connection()
            ->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id')
            ->execute(['id' => $user['id']]);

        unset($user['password']);
        $user['last_login_at'] = date('Y-m-d H:i:s');
        Session::put('auth_user', $user);

        activity_log((int) $user['id'], 'login', 'auth', 'Dang nhap he thong.');

        return true;
    }

    public static function logout(): void
    {
        $user = self::user();
        if ($user) {
            activity_log((int) $user['id'], 'logout', 'auth', 'Dang xuat he thong.');
        }

        Session::forget('auth_user');
    }

    public static function requireLogin(): void
    {
        if (! self::check()) {
            redirect('/login');
        }
    }

    public static function can(array|string $roles): bool
    {
        $allowedRoles = (array) $roles;
        $user = self::user();

        return $user !== null && in_array($user['role_name'], $allowedRoles, true);
    }

    public static function authorize(array|string $roles): void
    {
        if (! self::can($roles)) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }
    }
}
