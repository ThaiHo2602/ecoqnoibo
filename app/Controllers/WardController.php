<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Session;
use App\Core\View;

class WardController
{
    public function index(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $connection = Database::connection();
        $filters = [
            'keyword' => trim((string) query('keyword', '')),
        ];

        $sql = "SELECT wards.*,
                       COUNT(branches.id) AS branch_count
                FROM wards
                LEFT JOIN branches ON branches.ward_id = wards.id
                WHERE 1 = 1";
        $params = [];

        if ($filters['keyword'] !== '') {
            $sql .= ' AND (wards.name LIKE :keyword OR wards.description LIKE :keyword)';
            $params['keyword'] = '%' . $filters['keyword'] . '%';
        }

        $sql .= ' GROUP BY wards.id ORDER BY wards.name ASC';

        $statement = $connection->prepare($sql);
        $statement->execute($params);
        $wards = $statement->fetchAll();

        $editId = (int) query('edit', 0);
        $editWard = null;
        foreach ($wards as $ward) {
            if ((int) $ward['id'] === $editId) {
                $editWard = $ward;
                break;
            }
        }

        if ($editWard === null && $editId > 0) {
            $statement = $connection->prepare('SELECT * FROM wards WHERE id = :id LIMIT 1');
            $statement->execute(['id' => $editId]);
            $editWard = $statement->fetch() ?: null;
        }

        View::render('wards.index', [
            'pageTitle' => 'Phường',
            'wards' => $wards,
            'filters' => $filters,
            'editWard' => $editWard,
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $name = trim((string) ($_POST['name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));

        if ($name === '') {
            Session::flash('error', 'Vui lòng nhập tên phường.');
            redirect('/wards');
        }

        try {
            Database::connection()->prepare(
                'INSERT INTO wards (name, description) VALUES (:name, :description)'
            )->execute([
                'name' => $name,
                'description' => $description !== '' ? $description : null,
            ]);
        } catch (\PDOException) {
            Session::flash('error', 'Thêm phường thất bại. Tên phường có thể đã bị trùng.');
            redirect('/wards');
        }

        $user = Auth::user();
        activity_log((int) $user['id'], 'create', 'wards', 'Tạo phường: ' . $name);
        Session::flash('success', 'Đã thêm phường thành công.');
        redirect('/wards');
    }

    public function update(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $id = (int) ($_POST['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));

        if ($id <= 0 || $name === '') {
            Session::flash('error', 'Thông tin cập nhật phường không hợp lệ.');
            redirect('/wards');
        }

        try {
            Database::connection()->prepare(
                'UPDATE wards SET name = :name, description = :description WHERE id = :id'
            )->execute([
                'id' => $id,
                'name' => $name,
                'description' => $description !== '' ? $description : null,
            ]);
        } catch (\PDOException) {
            Session::flash('error', 'Cập nhật phường thất bại. Vui lòng kiểm tra dữ liệu bị trùng.');
            redirect('/wards?edit=' . $id);
        }

        $user = Auth::user();
        activity_log((int) $user['id'], 'update', 'wards', 'Cập nhật phường #' . $id . ': ' . $name);
        Session::flash('success', 'Đã cập nhật phường thành công.');
        redirect('/wards');
    }

    public function delete(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            Session::flash('error', 'Phường không hợp lệ.');
            redirect('/wards');
        }

        $connection = Database::connection();
        $statement = $connection->prepare('SELECT * FROM wards WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $ward = $statement->fetch();

        if (! $ward) {
            Session::flash('error', 'Phường không tồn tại.');
            redirect('/wards');
        }

        $branchCountStatement = $connection->prepare('SELECT COUNT(*) FROM branches WHERE ward_id = :id');
        $branchCountStatement->execute(['id' => $id]);
        if ((int) $branchCountStatement->fetchColumn() > 0) {
            Session::flash('error', 'Không thể xóa phường khi vẫn còn chi nhánh bên trong.');
            redirect('/wards');
        }

        $connection->prepare('DELETE FROM wards WHERE id = :id')->execute(['id' => $id]);

        $user = Auth::user();
        activity_log((int) $user['id'], 'delete', 'wards', 'Xóa phường #' . $id . ': ' . $ward['name']);
        Session::flash('success', 'Đã xóa phường thành công.');
        redirect('/wards');
    }

    public function bulkDelete(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $ids = array_values(array_unique(array_filter(array_map('intval', $_POST['ids'] ?? []))));
        if ($ids === []) {
            Session::flash('error', 'Vui lòng chọn ít nhất một phường để xóa.');
            redirect('/wards');
        }

        $connection = Database::connection();
        $deleted = 0;
        $skipped = 0;
        $user = Auth::user();
        $checkStatement = $connection->prepare('SELECT COUNT(*) FROM branches WHERE ward_id = :id');
        $deleteStatement = $connection->prepare('DELETE FROM wards WHERE id = :id');

        foreach ($ids as $id) {
            $checkStatement->execute(['id' => $id]);
            if ((int) $checkStatement->fetchColumn() > 0) {
                $skipped++;
                continue;
            }

            $deleteStatement->execute(['id' => $id]);
            if ($deleteStatement->rowCount() > 0) {
                $deleted++;
                activity_log((int) $user['id'], 'bulk_delete', 'wards', 'Xóa hàng loạt phường #' . $id);
            }
        }

        Session::flash($deleted > 0 ? 'success' : 'error', 'Đã xóa ' . $deleted . ' phường. Bỏ qua ' . $skipped . ' phường đang có chi nhánh.');
        redirect('/wards');
    }
}
