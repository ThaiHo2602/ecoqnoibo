<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;

class ActivityLogController
{
    public function index(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director']);

        $connection = Database::connection();
        $filters = [
            'user_id' => (int) query('user_id', 0),
            'module' => trim((string) query('module', '')),
            'action' => trim((string) query('action', '')),
            'keyword' => trim((string) query('keyword', '')),
        ];

        $users = $connection->query(
            "SELECT id, full_name, username
             FROM users
             ORDER BY full_name ASC"
        )->fetchAll();

        $modules = $connection->query(
            "SELECT DISTINCT module
             FROM activity_logs
             ORDER BY module ASC"
        )->fetchAll();

        $actions = $connection->query(
            "SELECT DISTINCT action
             FROM activity_logs
             ORDER BY action ASC"
        )->fetchAll();

        $sql = "SELECT activity_logs.*,
                       users.full_name,
                       users.username,
                       roles.display_name AS role_display_name
                FROM activity_logs
                LEFT JOIN users ON users.id = activity_logs.user_id
                LEFT JOIN roles ON roles.id = users.role_id
                WHERE 1 = 1";
        $params = [];

        if ($filters['user_id'] > 0) {
            $sql .= ' AND activity_logs.user_id = :user_id';
            $params['user_id'] = $filters['user_id'];
        }

        if ($filters['module'] !== '') {
            $sql .= ' AND activity_logs.module = :module';
            $params['module'] = $filters['module'];
        }

        if ($filters['action'] !== '') {
            $sql .= ' AND activity_logs.action = :action';
            $params['action'] = $filters['action'];
        }

        if ($filters['keyword'] !== '') {
            $sql .= ' AND (
                activity_logs.description LIKE :keyword
                OR activity_logs.ip_address LIKE :keyword
                OR users.full_name LIKE :keyword
                OR users.username LIKE :keyword
            )';
            $params['keyword'] = '%' . $filters['keyword'] . '%';
        }

        $sql .= ' ORDER BY activity_logs.created_at DESC, activity_logs.id DESC LIMIT 300';

        $statement = $connection->prepare($sql);
        $statement->execute($params);
        $logs = $statement->fetchAll();

        View::render('activity-logs.index', [
            'pageTitle' => 'Nhật ký hoạt động',
            'logs' => $logs,
            'filters' => $filters,
            'users' => $users,
            'modules' => $modules,
            'actions' => $actions,
        ]);
    }
}
