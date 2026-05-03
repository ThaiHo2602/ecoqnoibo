<?php

namespace App\Core;

class Auth
{
    private const REMEMBER_COOKIE = 'ecoq_remember';

    public static function user(): ?array
    {
        $user = Session::get('auth_user');

        if (is_array($user)) {
            if (! isset($user['role_name']) && isset($user['id'])) {
                $freshUser = self::findUserWithRole((int) $user['id']);
                if ($freshUser) {
                    unset($freshUser['password']);
                    Session::put('auth_user', $freshUser);
                    return $freshUser;
                }
            }

            return $user;
        }

        return self::restoreRememberedUser();
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function attempt(string $username, string $password): bool
    {
        self::lockInactiveUsers();

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

        if (! password_verify($password, $user['password'])) {
            return false;
        }

        Database::connection()
            ->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id')
            ->execute(['id' => $user['id']]);

        unset($user['password']);
        $user['last_login_at'] = date('Y-m-d H:i:s');
        Session::put('auth_user', $user);
        self::issueRememberCookie((int) $user['id']);

        activity_log((int) $user['id'], 'login', 'auth', 'Đăng nhập hệ thống.');

        return true;
    }

    public static function logout(): void
    {
        $user = self::user();
        if ($user) {
            activity_log((int) $user['id'], 'logout', 'auth', 'Đăng xuất hệ thống.');
            Database::connection()
                ->prepare('UPDATE users SET remember_token_hash = NULL, remember_token_expires_at = NULL WHERE id = :id')
                ->execute(['id' => $user['id']]);
        }

        Session::forget('auth_user');
        self::forgetRememberCookie();
    }

    public static function requireLogin(): void
    {
        self::lockInactiveUsers();
        self::ensureCurrentSessionUserIsActive();

        if (! self::check()) {
            redirect('/login');
        }
    }

    public static function can(array|string $roles): bool
    {
        $allowedRoles = (array) $roles;
        $user = self::user();

        if ($user === null) {
            return false;
        }

        $resolvedRole = self::resolveRoleName($user);

        return $resolvedRole !== null && in_array($resolvedRole, $allowedRoles, true);
    }

    public static function authorize(array|string $roles): void
    {
        if (! self::can($roles)) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }
    }

    public static function lockInactiveUsers(): int
    {
        $inactiveDays = max(1, (int) config('security.inactive_lock_days', 7));
        $inactiveBefore = date('Y-m-d H:i:s', strtotime("-{$inactiveDays} days"));

        $statement = Database::connection()->prepare(
            "UPDATE users
             SET account_status = 'locked',
                 remember_token_hash = NULL,
                 remember_token_expires_at = NULL
             WHERE account_status = 'active'
               AND COALESCE(last_login_at, created_at) < :inactive_before"
        );
        $statement->execute(['inactive_before' => $inactiveBefore]);

        return $statement->rowCount();
    }

    private static function ensureCurrentSessionUserIsActive(): void
    {
        $sessionUser = Session::get('auth_user');
        if (! is_array($sessionUser) || empty($sessionUser['id'])) {
            return;
        }

        $freshUser = self::findUserWithRole((int) $sessionUser['id']);
        if ($freshUser) {
            unset($freshUser['password']);
            Session::put('auth_user', $freshUser);
            return;
        }

        Session::forget('auth_user');
        self::forgetRememberCookie();
        Session::flash('error', 'Tài khoản của bạn đã bị khóa do không đăng nhập trong 1 tuần.');
    }

    private static function restoreRememberedUser(): ?array
    {
        $cookieValue = $_COOKIE[self::REMEMBER_COOKIE] ?? '';
        if (! is_string($cookieValue) || $cookieValue === '' || ! str_contains($cookieValue, '|')) {
            return null;
        }

        [$userId, $token] = explode('|', $cookieValue, 2);
        $userId = (int) $userId;

        if ($userId <= 0 || $token === '') {
            self::forgetRememberCookie();
            return null;
        }

        $user = self::findUserWithRole($userId, true);
        if (! $user || ! password_verify($token, $user['remember_token_hash'])) {
            self::forgetRememberCookie();
            return null;
        }

        unset($user['password']);
        Session::put('auth_user', $user);
        self::issueRememberCookie((int) $user['id']);

        activity_log((int) $user['id'], 'auto_login', 'auth', 'Khôi phục đăng nhập từ cookie ghi nhớ.');

        return $user;
    }

    private static function issueRememberCookie(int $userId): void
    {
        $token = bin2hex(random_bytes(32));
        $rememberDays = (int) config('security.remember_days', 30);
        $expiresAt = time() + ($rememberDays * 86400);
        $expiresAtSql = date('Y-m-d H:i:s', $expiresAt);

        Database::connection()
            ->prepare('UPDATE users SET remember_token_hash = :hash, remember_token_expires_at = :expires_at WHERE id = :id')
            ->execute([
                'hash' => password_hash($token, PASSWORD_DEFAULT),
                'expires_at' => $expiresAtSql,
                'id' => $userId,
            ]);

        setcookie(self::REMEMBER_COOKIE, $userId . '|' . $token, [
            'expires' => $expiresAt,
            'path' => '/',
            'secure' => self::isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        $_COOKIE[self::REMEMBER_COOKIE] = $userId . '|' . $token;
    }

    private static function forgetRememberCookie(): void
    {
        setcookie(self::REMEMBER_COOKIE, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => self::isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        unset($_COOKIE[self::REMEMBER_COOKIE]);
    }

    private static function isHttps(): bool
    {
        return (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    }

    private static function findUserWithRole(int $userId, bool $requireRememberToken = false): ?array
    {
        $sql = 'SELECT users.*, roles.name AS role_name, roles.display_name AS role_display_name
                FROM users
                INNER JOIN roles ON roles.id = users.role_id
                WHERE users.id = :id
                  AND users.account_status = :status';

        if ($requireRememberToken) {
            $sql .= '
                  AND users.remember_token_hash IS NOT NULL
                  AND users.remember_token_expires_at IS NOT NULL
                  AND users.remember_token_expires_at >= NOW()';
        }

        $sql .= ' LIMIT 1';

        $statement = Database::connection()->prepare($sql);
        $statement->execute([
            'id' => $userId,
            'status' => 'active',
        ]);

        return $statement->fetch() ?: null;
    }

    public static function resolveRoleName(?array $user): ?string
    {
        if (! is_array($user)) {
            return null;
        }

        $roleName = trim((string) ($user['role_name'] ?? ''));
        if ($roleName !== '') {
            return strtolower($roleName);
        }

        $roleId = (int) ($user['role_id'] ?? 0);
        if ($roleId > 0) {
            $roleFromId = match ($roleId) {
                1 => 'director',
                2 => 'manager',
                3 => 'staff',
                4 => 'collaborator',
                default => null,
            };

            if ($roleFromId !== null) {
                return $roleFromId;
            }
        }

        $displayName = strtolower(trim((string) ($user['role_display_name'] ?? '')));
        $normalizedDisplayName = str_replace(
            ['á', 'à', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ',
             'é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ',
             'í', 'ì', 'ỉ', 'ĩ', 'ị',
             'ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ',
             'ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự',
             'ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'đ'],
            ['a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
             'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
             'i', 'i', 'i', 'i', 'i',
             'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
             'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
             'y', 'y', 'y', 'y', 'y', 'd'],
            $displayName
        );

        $normalizedDisplayName = preg_replace('/\s+/', ' ', $normalizedDisplayName ?? '');

        return match ($normalizedDisplayName) {
            'giam doc' => 'director',
            'quan ly' => 'manager',
            'nhan vien' => 'staff',
            'cong tac vien' => 'collaborator',
            default => null,
        };
    }
}
