<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Session;
use App\Core\View;

class SystemController
{
    public function index(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $connection = Database::connection();
        $systems = $connection->query(
            "SELECT systems.*,
                    COUNT(DISTINCT branches.id) AS branch_count,
                    COUNT(DISTINCT rooms.id) AS room_count
             FROM systems
             LEFT JOIN branches ON branches.system_id = systems.id
             LEFT JOIN rooms ON rooms.branch_id = branches.id
             GROUP BY systems.id
             ORDER BY systems.name ASC"
        )->fetchAll();

        $branches = $connection->query(
            "SELECT branches.*, systems.name AS system_name, wards.name AS ward_name, districts.name AS district_name
             FROM branches
             INNER JOIN systems ON systems.id = branches.system_id
             LEFT JOIN wards ON wards.id = branches.ward_id
             INNER JOIN districts ON districts.id = branches.district_id
             ORDER BY systems.name ASC, branches.name ASC"
        )->fetchAll();

        $districts = $connection->query('SELECT * FROM districts ORDER BY name ASC')->fetchAll();
        $wards = $connection->query('SELECT id, name FROM wards ORDER BY name ASC')->fetchAll();

        $branchesBySystem = [];
        foreach ($branches as $branch) {
            $branchesBySystem[$branch['system_id']][] = $branch;
        }

        $editSystemId = (int) query('edit_system', 0);
        $editBranchId = (int) query('edit_branch', 0);

        $editSystem = null;
        foreach ($systems as $system) {
            if ((int) $system['id'] === $editSystemId) {
                $editSystem = $system;
                break;
            }
        }

        $editBranch = null;
        foreach ($branches as $branch) {
            if ((int) $branch['id'] === $editBranchId) {
                $editBranch = $branch;
                break;
            }
        }

        View::render('systems.index', [
            'pageTitle' => 'Hệ thống',
            'systems' => $systems,
            'branchesBySystem' => $branchesBySystem,
            'wards' => $wards,
            'districts' => $districts,
            'editSystem' => $editSystem,
            'editBranch' => $editBranch,
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($name === '') {
            Session::flash('error', 'Tên hệ thống không được để trống.');
            redirect('/systems');
        }

        $statement = Database::connection()->prepare(
            'INSERT INTO systems (name, description, is_active) VALUES (:name, :description, :is_active)'
        );

        try {
            $statement->execute([
                'name' => $name,
                'description' => $description !== '' ? $description : null,
                'is_active' => $isActive,
            ]);
        } catch (\PDOException) {
            Session::flash('error', 'Tên hệ thống đã tồn tại. Vui lòng dùng tên khác.');
            redirect('/systems');
        }

        $user = Auth::user();
        activity_log((int) $user['id'], 'create', 'systems', 'Tạo hệ thống: ' . $name);
        Session::flash('success', 'Đã tạo hệ thống mới thành công.');
        redirect('/systems');
    }

    public function update(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($id <= 0 || $name === '') {
            Session::flash('error', 'Thông tin hệ thống không hợp lệ.');
            redirect('/systems');
        }

        try {
            Database::connection()->prepare(
                'UPDATE systems SET name = :name, description = :description, is_active = :is_active WHERE id = :id'
            )->execute([
                'id' => $id,
                'name' => $name,
                'description' => $description !== '' ? $description : null,
                'is_active' => $isActive,
            ]);
        } catch (\PDOException) {
            Session::flash('error', 'Cập nhật thất bại. Có thể tên hệ thống đã bị trùng.');
            redirect('/systems?edit_system=' . $id);
        }

        $user = Auth::user();
        activity_log((int) $user['id'], 'update', 'systems', 'Cập nhật hệ thống #' . $id . ': ' . $name);
        Session::flash('success', 'Đã cập nhật hệ thống thành công.');
        redirect('/systems');
    }

    public function delete(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            Session::flash('error', 'Hệ thống không hợp lệ.');
            redirect('/systems');
        }

        $connection = Database::connection();
        $system = $connection->prepare('SELECT * FROM systems WHERE id = :id LIMIT 1');
        $system->execute(['id' => $id]);
        $systemData = $system->fetch();

        if (! $systemData) {
            Session::flash('error', 'Hệ thống không tồn tại.');
            redirect('/systems');
        }

        $checkStatement = $connection->prepare('SELECT COUNT(*) FROM branches WHERE system_id = :id');
        $checkStatement->execute(['id' => $id]);
        if ((int) $checkStatement->fetchColumn() > 0) {
            Session::flash('error', 'Không thể xóa hệ thống khi vẫn còn chi nhánh bên trong.');
            redirect('/systems');
        }

        $connection->prepare('DELETE FROM systems WHERE id = :id')->execute(['id' => $id]);

        $user = Auth::user();
        activity_log((int) $user['id'], 'delete', 'systems', 'Xóa hệ thống #' . $id . ': ' . $systemData['name']);
        Session::flash('success', 'Đã xóa hệ thống thành công.');
        redirect('/systems');
    }

    public function bulkDelete(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $ids = array_values(array_unique(array_filter(array_map('intval', $_POST['ids'] ?? []))));
        if ($ids === []) {
            Session::flash('error', 'Vui lòng chọn ít nhất một hệ thống để xóa.');
            redirect('/systems');
        }

        $connection = Database::connection();
        $deleted = 0;
        $skipped = 0;
        $user = Auth::user();
        $checkStatement = $connection->prepare('SELECT COUNT(*) FROM branches WHERE system_id = :id');
        $deleteStatement = $connection->prepare('DELETE FROM systems WHERE id = :id');

        foreach ($ids as $id) {
            $checkStatement->execute(['id' => $id]);
            if ((int) $checkStatement->fetchColumn() > 0) {
                $skipped++;
                continue;
            }

            $deleteStatement->execute(['id' => $id]);
            if ($deleteStatement->rowCount() > 0) {
                $deleted++;
                activity_log((int) $user['id'], 'bulk_delete', 'systems', 'Xóa hàng loạt hệ thống #' . $id);
            }
        }

        Session::flash($deleted > 0 ? 'success' : 'error', 'Đã xóa ' . $deleted . ' hệ thống. Bỏ qua ' . $skipped . ' hệ thống đang có chi nhánh.');
        redirect('/systems');
    }
}
