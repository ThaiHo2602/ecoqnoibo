<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Session;
use App\Core\View;

class ProfileController
{
    public function password(): void
    {
        Auth::requireLogin();

        View::render('profile.password', [
            'pageTitle' => 'Đổi mật khẩu',
            'user' => Auth::user(),
        ]);
    }

    public function updatePassword(): void
    {
        Auth::requireLogin();

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $user = Auth::user();

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            Session::flash('error', 'Vui lòng nhập đầy đủ thông tin đổi mật khẩu.');
            redirect('/profile/password');
        }

        if (strlen($newPassword) < 6) {
            Session::flash('error', 'Mật khẩu mới phải từ 6 ký tự trở lên.');
            redirect('/profile/password');
        }

        if ($newPassword !== $confirmPassword) {
            Session::flash('error', 'Xác nhận mật khẩu mới không khớp.');
            redirect('/profile/password');
        }

        $statement = Database::connection()->prepare('SELECT password FROM users WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $user['id']]);
        $dbUser = $statement->fetch();

        if (! $dbUser || ! password_verify($currentPassword, $dbUser['password'])) {
            Session::flash('error', 'Mật khẩu hiện tại không đúng.');
            redirect('/profile/password');
        }

        Database::connection()->prepare(
            'UPDATE users SET password = :password WHERE id = :id'
        )->execute([
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'id' => $user['id'],
        ]);

        activity_log((int) $user['id'], 'change_password', 'profile', 'Đổi mật khẩu tài khoản.');
        Session::flash('success', 'Đã đổi mật khẩu thành công.');
        redirect('/profile/password');
    }
}
