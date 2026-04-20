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
            'pageTitle' => 'Doi mat khau',
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
            Session::flash('error', 'Vui long nhap day du thong tin doi mat khau.');
            redirect('/profile/password');
        }

        if (strlen($newPassword) < 6) {
            Session::flash('error', 'Mat khau moi phai tu 6 ky tu tro len.');
            redirect('/profile/password');
        }

        if ($newPassword !== $confirmPassword) {
            Session::flash('error', 'Xac nhan mat khau moi khong khop.');
            redirect('/profile/password');
        }

        $statement = Database::connection()->prepare('SELECT password FROM users WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $user['id']]);
        $dbUser = $statement->fetch();

        if (! $dbUser || ! password_verify($currentPassword, $dbUser['password'])) {
            Session::flash('error', 'Mat khau hien tai khong dung.');
            redirect('/profile/password');
        }

        Database::connection()->prepare(
            'UPDATE users SET password = :password WHERE id = :id'
        )->execute([
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'id' => $user['id'],
        ]);

        activity_log((int) $user['id'], 'change_password', 'profile', 'Doi mat khau tai khoan.');
        Session::flash('success', 'Da doi mat khau thanh cong.');
        redirect('/profile/password');
    }
}
