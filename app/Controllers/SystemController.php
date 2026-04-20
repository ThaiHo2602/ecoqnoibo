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
            "SELECT branches.*, systems.name AS system_name, districts.name AS district_name
             FROM branches
             INNER JOIN systems ON systems.id = branches.system_id
             INNER JOIN districts ON districts.id = branches.district_id
             ORDER BY systems.name ASC, branches.name ASC"
        )->fetchAll();

        $districts = $connection->query('SELECT * FROM districts ORDER BY name ASC')->fetchAll();

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
            'pageTitle' => 'He thong',
            'systems' => $systems,
            'branchesBySystem' => $branchesBySystem,
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
            Session::flash('error', 'Ten he thong khong duoc de trong.');
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
            Session::flash('error', 'Ten he thong da ton tai. Vui long dung ten khac.');
            redirect('/systems');
        }

        $user = Auth::user();
        activity_log((int) $user['id'], 'create', 'systems', 'Tao he thong: ' . $name);
        Session::flash('success', 'Da tao he thong moi thanh cong.');
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
            Session::flash('error', 'Thong tin he thong khong hop le.');
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
            Session::flash('error', 'Cap nhat that bai. Co the ten he thong da bi trung.');
            redirect('/systems?edit_system=' . $id);
        }

        $user = Auth::user();
        activity_log((int) $user['id'], 'update', 'systems', 'Cap nhat he thong #' . $id . ': ' . $name);
        Session::flash('success', 'Da cap nhat he thong thanh cong.');
        redirect('/systems');
    }

    public function delete(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            Session::flash('error', 'He thong khong hop le.');
            redirect('/systems');
        }

        $connection = Database::connection();
        $system = $connection->prepare('SELECT * FROM systems WHERE id = :id LIMIT 1');
        $system->execute(['id' => $id]);
        $systemData = $system->fetch();

        if (! $systemData) {
            Session::flash('error', 'He thong khong ton tai.');
            redirect('/systems');
        }

        $checkStatement = $connection->prepare('SELECT COUNT(*) FROM branches WHERE system_id = :id');
        $checkStatement->execute(['id' => $id]);
        if ((int) $checkStatement->fetchColumn() > 0) {
            Session::flash('error', 'Khong the xoa he thong khi van con chi nhanh ben trong.');
            redirect('/systems');
        }

        $connection->prepare('DELETE FROM systems WHERE id = :id')->execute(['id' => $id]);

        $user = Auth::user();
        activity_log((int) $user['id'], 'delete', 'systems', 'Xoa he thong #' . $id . ': ' . $systemData['name']);
        Session::flash('success', 'Da xoa he thong thanh cong.');
        redirect('/systems');
    }
}
