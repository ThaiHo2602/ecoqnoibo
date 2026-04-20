<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Session;
use App\Core\View;

class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('/');
        }

        View::render('auth.login', [
            'pageTitle' => 'Đăng nhập',
        ], 'auth');
    }

    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            Session::flash('error', 'Vui lòng nhập đầy đủ tài khoản và mật khẩu.');
            redirect('/login');
        }

        if (! Auth::attempt($username, $password)) {
            if (! Session::hasFlash('error')) {
                Session::flash('error', 'Thông tin đăng nhập không chính xác.');
            }
            redirect('/login');
        }

        Session::flash('success', 'Đăng nhập thành công.');
        redirect('/');
    }

    public function logout(): void
    {
        Auth::logout();
        Session::destroy();
        redirect('/login');
    }
}
