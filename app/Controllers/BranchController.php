<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Session;
use App\Core\View;

class BranchController
{
    public function index(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $connection = Database::connection();
        $filters = [
            'system_id' => (int) query('system_id', 0),
            'ward_id' => (int) query('ward_id', 0),
            'district_id' => (int) query('district_id', 0),
            'keyword' => trim((string) query('keyword', '')),
        ];

        $systems = $connection->query('SELECT id, name FROM systems ORDER BY name ASC')->fetchAll();
        $wards = $connection->query('SELECT id, name FROM wards ORDER BY name ASC')->fetchAll();
        $districts = $connection->query('SELECT id, name FROM districts ORDER BY name ASC')->fetchAll();

        $sql = "SELECT branches.*,
                       systems.name AS system_name,
                       wards.name AS ward_name,
                       districts.name AS district_name,
                       COUNT(rooms.id) AS room_count
                FROM branches
                INNER JOIN systems ON systems.id = branches.system_id
                LEFT JOIN wards ON wards.id = branches.ward_id
                INNER JOIN districts ON districts.id = branches.district_id
                LEFT JOIN rooms ON rooms.branch_id = branches.id
                WHERE 1 = 1";

        $params = [];
        if ($filters['system_id'] > 0) {
            $sql .= ' AND branches.system_id = :system_id';
            $params['system_id'] = $filters['system_id'];
        }
        if ($filters['ward_id'] > 0) {
            $sql .= ' AND branches.ward_id = :ward_id';
            $params['ward_id'] = $filters['ward_id'];
        }
        if ($filters['district_id'] > 0) {
            $sql .= ' AND branches.district_id = :district_id';
            $params['district_id'] = $filters['district_id'];
        }
        if ($filters['keyword'] !== '') {
            $sql .= ' AND (branches.name LIKE :keyword OR branches.manager_phone LIKE :keyword)';
            $params['keyword'] = '%' . $filters['keyword'] . '%';
        }

        $sql .= ' GROUP BY branches.id ORDER BY branches.created_at DESC';

        $statement = $connection->prepare($sql);
        $statement->execute($params);
        $branches = $statement->fetchAll();

        $editId = (int) query('edit', 0);
        $editBranch = null;
        foreach ($branches as $branch) {
            if ((int) $branch['id'] === $editId) {
                $editBranch = $branch;
                break;
            }
        }

        if ($editBranch === null && $editId > 0) {
            $statement = $connection->prepare(
                "SELECT branches.*, systems.name AS system_name, wards.name AS ward_name, districts.name AS district_name
                 FROM branches
                 INNER JOIN systems ON systems.id = branches.system_id
                 LEFT JOIN wards ON wards.id = branches.ward_id
                 INNER JOIN districts ON districts.id = branches.district_id
                 WHERE branches.id = :id
                 LIMIT 1"
            );
            $statement->execute(['id' => $editId]);
            $editBranch = $statement->fetch() ?: null;
        }

        View::render('branches.index', [
            'pageTitle' => 'Chi nhánh',
            'systems' => $systems,
            'wards' => $wards,
            'districts' => $districts,
            'branches' => $branches,
            'filters' => $filters,
            'editBranch' => $editBranch,
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $wardId = (int) ($_POST['ward_id'] ?? 0);
        $districtId = (int) ($_POST['district_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $managerPhone = trim($_POST['manager_phone'] ?? '');
        $systemId = (int) ($_POST['system_id'] ?? 0);
        $fees = $this->feePayload();
        $ward = $this->findWard($wardId);

        if ($systemId <= 0 || ! $ward || $wardId <= 0 || $districtId <= 0 || $name === '') {
            Session::flash('error', 'Vui lòng nhập đầy đủ thông tin chi nhánh.');
            redirect($this->redirectTarget());
        }

        if ($this->branchNameExistsInSystem($systemId, $name)) {
            Session::flash('error', 'Tên chi nhánh này đã tồn tại trong hệ thống đã chọn.');
            redirect($this->redirectTarget());
        }

        try {
            Database::connection()->prepare(
                'INSERT INTO branches (system_id, ward_id, district_id, name, manager_phone, electricity_price, water_price, parking_price, service_price)
                 VALUES (:system_id, :ward_id, :district_id, :name, :manager_phone, :electricity_price, :water_price, :parking_price, :service_price)'
            )->execute([
                'system_id' => $systemId,
                'ward_id' => $wardId,
                'district_id' => $districtId,
                'name' => $name,
                'manager_phone' => $managerPhone !== '' ? $managerPhone : null,
            ] + $fees);
        } catch (\PDOException) {
            Session::flash('error', 'Thêm chi nhánh thất bại. Tên chi nhánh có thể đã bị trùng trong hệ thống.');
            redirect($this->redirectTarget());
        }

        $user = Auth::user();
        activity_log((int) $user['id'], 'create', 'branches', 'Tạo chi nhánh: ' . $name);
        Session::flash('success', 'Đã thêm chi nhánh thành công.');
        redirect($this->redirectTarget());
    }

    public function update(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $id = (int) ($_POST['id'] ?? 0);
        $wardId = (int) ($_POST['ward_id'] ?? 0);
        $districtId = (int) ($_POST['district_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $managerPhone = trim($_POST['manager_phone'] ?? '');
        $systemId = (int) ($_POST['system_id'] ?? 0);
        $fees = $this->feePayload();
        $ward = $this->findWard($wardId);

        if ($id <= 0 || $systemId <= 0 || ! $ward || $wardId <= 0 || $districtId <= 0 || $name === '') {
            Session::flash('error', 'Thông tin cập nhật chi nhánh không hợp lệ.');
            redirect($this->redirectTarget());
        }

        if ($this->branchNameExistsInSystem($systemId, $name, $id)) {
            Session::flash('error', 'Tên chi nhánh này đã tồn tại trong hệ thống đã chọn.');
            redirect($this->redirectTarget());
        }

        try {
            Database::connection()->prepare(
                'UPDATE branches
                 SET system_id = :system_id,
                     ward_id = :ward_id,
                     district_id = :district_id,
                     name = :name,
                     manager_phone = :manager_phone,
                     electricity_price = :electricity_price,
                     water_price = :water_price,
                     parking_price = :parking_price,
                     service_price = :service_price
                 WHERE id = :id'
            )->execute([
                'id' => $id,
                'system_id' => $systemId,
                'ward_id' => $wardId,
                'district_id' => $districtId,
                'name' => $name,
                'manager_phone' => $managerPhone !== '' ? $managerPhone : null,
            ] + $fees);
        } catch (\PDOException) {
            Session::flash('error', 'Cập nhật chi nhánh thất bại. Vui lòng kiểm tra dữ liệu bị trùng.');
            redirect($this->redirectTarget());
        }

        $user = Auth::user();
        activity_log((int) $user['id'], 'update', 'branches', 'Cập nhật chi nhánh #' . $id . ': ' . $name);
        Session::flash('success', 'Đã cập nhật chi nhánh thành công.');
        redirect($this->redirectTarget());
    }

    public function delete(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            Session::flash('error', 'Chi nhánh không hợp lệ.');
            redirect($this->redirectTarget());
        }

        $connection = Database::connection();
        $statement = $connection->prepare('SELECT * FROM branches WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $branch = $statement->fetch();

        if (! $branch) {
            Session::flash('error', 'Chi nhánh không tồn tại.');
            redirect($this->redirectTarget());
        }

        $roomCountStatement = $connection->prepare('SELECT COUNT(*) FROM rooms WHERE branch_id = :id');
        $roomCountStatement->execute(['id' => $id]);
        if ((int) $roomCountStatement->fetchColumn() > 0) {
            Session::flash('error', 'Không thể xóa chi nhánh khi vẫn còn phòng bên trong.');
            redirect($this->redirectTarget());
        }

        $connection->prepare('DELETE FROM branches WHERE id = :id')->execute(['id' => $id]);

        $user = Auth::user();
        activity_log((int) $user['id'], 'delete', 'branches', 'Xóa chi nhánh #' . $id . ': ' . $branch['name']);
        Session::flash('success', 'Đã xóa chi nhánh thành công.');
        redirect($this->redirectTarget());
    }

    public function bulkDelete(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $ids = array_values(array_unique(array_filter(array_map('intval', $_POST['ids'] ?? []))));
        if ($ids === []) {
            Session::flash('error', 'Vui lòng chọn ít nhất một chi nhánh để xóa.');
            redirect($this->redirectTarget());
        }

        $connection = Database::connection();
        $deleted = 0;
        $skipped = 0;
        $user = Auth::user();
        $checkStatement = $connection->prepare('SELECT COUNT(*) FROM rooms WHERE branch_id = :id');
        $deleteStatement = $connection->prepare('DELETE FROM branches WHERE id = :id');

        foreach ($ids as $id) {
            $checkStatement->execute(['id' => $id]);
            if ((int) $checkStatement->fetchColumn() > 0) {
                $skipped++;
                continue;
            }

            $deleteStatement->execute(['id' => $id]);
            if ($deleteStatement->rowCount() > 0) {
                $deleted++;
                activity_log((int) $user['id'], 'bulk_delete', 'branches', 'Xóa hàng loạt chi nhánh #' . $id);
            }
        }

        Session::flash($deleted > 0 ? 'success' : 'error', 'Đã xóa ' . $deleted . ' chi nhánh. Bỏ qua ' . $skipped . ' chi nhánh đang có phòng.');
        redirect($this->redirectTarget());
    }

    private function findWard(int $wardId): ?array
    {
        if ($wardId <= 0) {
            return null;
        }

        $statement = Database::connection()->prepare('SELECT * FROM wards WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $wardId]);

        return $statement->fetch() ?: null;
    }

    private function branchNameExistsInSystem(int $systemId, string $name, int $ignoreBranchId = 0): bool
    {
        $statement = Database::connection()->prepare(
            'SELECT id
             FROM branches
             WHERE system_id = :system_id
               AND name = :name
               AND (:ignore_branch_id = 0 OR id <> :ignore_branch_id)
             LIMIT 1'
        );
        $statement->execute([
            'system_id' => $systemId,
            'name' => $name,
            'ignore_branch_id' => $ignoreBranchId,
        ]);

        return (bool) $statement->fetch();
    }

    private function feePayload(): array
    {
        return [
            'electricity_price' => max(0, (float) ($_POST['electricity_price'] ?? 0)),
            'water_price' => max(0, (float) ($_POST['water_price'] ?? 0)),
            'parking_price' => max(0, (float) ($_POST['parking_price'] ?? 0)),
            'service_price' => max(0, (float) ($_POST['service_price'] ?? 0)),
        ];
    }

    private function redirectTarget(): string
    {
        $target = trim((string) ($_POST['redirect_to'] ?? '/branches'));

        if ($target === '' || ! str_starts_with($target, '/')) {
            return '/branches';
        }

        return $target;
    }
}
