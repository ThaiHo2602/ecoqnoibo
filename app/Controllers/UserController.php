<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Session;
use App\Core\View;

class UserController
{
    public function index(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director']);

        $connection = Database::connection();
        $filters = [
            'role' => trim((string) query('role', '')),
            'status' => trim((string) query('status', '')),
            'keyword' => trim((string) query('keyword', '')),
        ];

        $roles = $connection->query('SELECT * FROM roles ORDER BY id ASC')->fetchAll();

        $sql = "SELECT users.*, roles.display_name AS role_display_name, roles.name AS role_name
                FROM users
                INNER JOIN roles ON roles.id = users.role_id
                WHERE 1 = 1";
        $params = [];

        if ($filters['role'] !== '') {
            $sql .= ' AND roles.name = :role';
            $params['role'] = $filters['role'];
        }
        if ($filters['status'] !== '') {
            $sql .= ' AND users.account_status = :status';
            $params['status'] = $filters['status'];
        }
        if ($filters['keyword'] !== '') {
            $sql .= ' AND (users.full_name LIKE :keyword OR users.username LIKE :keyword OR users.phone LIKE :keyword OR users.email LIKE :keyword)';
            $params['keyword'] = '%' . $filters['keyword'] . '%';
        }

        $sql .= ' ORDER BY users.created_at DESC';
        $statement = $connection->prepare($sql);
        $statement->execute($params);
        $users = $statement->fetchAll();

        $editUser = null;
        $editId = (int) query('edit', 0);
        if ($editId > 0) {
            $find = $connection->prepare(
                "SELECT users.*, roles.display_name AS role_display_name, roles.name AS role_name
                 FROM users
                 INNER JOIN roles ON roles.id = users.role_id
                 WHERE users.id = :id
                 LIMIT 1"
            );
            $find->execute(['id' => $editId]);
            $editUser = $find->fetch() ?: null;
        }

        View::render('users.index', [
            'pageTitle' => 'Nguoi dung',
            'roles' => $roles,
            'users' => $users,
            'filters' => $filters,
            'editUser' => $editUser,
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director']);

        $payload = $this->validatedPayload(requirePassword: true);
        if ($payload === null) {
            redirect('/users');
        }

        try {
            Database::connection()->prepare(
                'INSERT INTO users (role_id, full_name, job_title, phone, email, username, password, account_status, last_login_at)
                 VALUES (:role_id, :full_name, :job_title, :phone, :email, :username, :password, :account_status, NULL)'
            )->execute($payload);
        } catch (\PDOException) {
            Session::flash('error', 'Tao nguoi dung that bai. Username co the da ton tai.');
            redirect('/users');
        }

        $currentUser = Auth::user();
        activity_log((int) $currentUser['id'], 'create', 'users', 'Tao nguoi dung: ' . $payload['username']);
        Session::flash('success', 'Da tao nguoi dung moi thanh cong.');
        redirect('/users');
    }

    public function update(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director']);

        $userId = (int) ($_POST['id'] ?? 0);
        if ($userId <= 0) {
            Session::flash('error', 'Nguoi dung khong hop le.');
            redirect('/users');
        }

        $payload = $this->validatedPayload(requirePassword: false);
        if ($payload === null) {
            redirect('/users?edit=' . $userId);
        }

        $fields = [
            'role_id = :role_id',
            'full_name = :full_name',
            'job_title = :job_title',
            'phone = :phone',
            'email = :email',
            'username = :username',
            'account_status = :account_status',
        ];

        if ($payload['password'] !== null) {
            $fields[] = 'password = :password';
        } else {
            unset($payload['password']);
        }

        try {
            Database::connection()->prepare(
                'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id'
            )->execute($payload + ['id' => $userId]);
        } catch (\PDOException) {
            Session::flash('error', 'Cap nhat nguoi dung that bai. Username co the da ton tai.');
            redirect('/users?edit=' . $userId);
        }

        $currentUser = Auth::user();
        activity_log((int) $currentUser['id'], 'update', 'users', 'Cap nhat nguoi dung #' . $userId);
        Session::flash('success', 'Da cap nhat nguoi dung thanh cong.');
        redirect('/users');
    }

    public function delete(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director']);

        $userId = (int) ($_POST['id'] ?? 0);
        $currentUser = Auth::user();

        if ($userId <= 0) {
            Session::flash('error', 'Nguoi dung khong hop le.');
            redirect('/users');
        }

        if ($userId === (int) $currentUser['id']) {
            Session::flash('error', 'Ban khong the xoa chinh tai khoan dang dang nhap.');
            redirect('/users');
        }

        $statement = Database::connection()->prepare('SELECT username FROM users WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $userId]);
        $user = $statement->fetch();

        if (! $user) {
            Session::flash('error', 'Nguoi dung khong ton tai.');
            redirect('/users');
        }

        Database::connection()->prepare('DELETE FROM users WHERE id = :id')->execute(['id' => $userId]);

        activity_log((int) $currentUser['id'], 'delete', 'users', 'Xoa nguoi dung #' . $userId . ' - ' . $user['username']);
        Session::flash('success', 'Da xoa nguoi dung thanh cong.');
        redirect('/users');
    }

    private function validatedPayload(bool $requirePassword): ?array
    {
        $roleId = (int) ($_POST['role_id'] ?? 0);
        $fullName = trim($_POST['full_name'] ?? '');
        $jobTitle = trim($_POST['job_title'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $accountStatus = trim($_POST['account_status'] ?? 'active');

        if ($roleId <= 0 || $fullName === '' || $jobTitle === '' || $username === '') {
            Session::flash('error', 'Vui long nhap day du cac truong bat buoc cua nguoi dung.');
            return null;
        }

        if (! in_array($accountStatus, ['active', 'locked'], true)) {
            Session::flash('error', 'Trang thai tai khoan khong hop le.');
            return null;
        }

        if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Email khong hop le.');
            return null;
        }

        if ($requirePassword && strlen($password) < 6) {
            Session::flash('error', 'Mat khau moi phai tu 6 ky tu tro len.');
            return null;
        }

        $passwordHash = null;
        if ($password !== '') {
            if (strlen($password) < 6) {
                Session::flash('error', 'Mat khau phai tu 6 ky tu tro len.');
                return null;
            }
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        }

        return [
            'role_id' => $roleId,
            'full_name' => $fullName,
            'job_title' => $jobTitle,
            'phone' => $phone !== '' ? $phone : null,
            'email' => $email !== '' ? $email : null,
            'username' => $username,
            'password' => $passwordHash,
            'account_status' => $accountStatus,
        ];
    }
}
