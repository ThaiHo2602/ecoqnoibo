<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;

class DashboardController
{
    public function index(): void
    {
        Auth::requireLogin();

        $stats = [
            'systems' => (int) Database::connection()->query('SELECT COUNT(*) FROM systems')->fetchColumn(),
            'branches' => (int) Database::connection()->query('SELECT COUNT(*) FROM branches')->fetchColumn(),
            'rooms' => (int) Database::connection()->query('SELECT COUNT(*) FROM rooms')->fetchColumn(),
            'pendingLocks' => (int) Database::connection()->query("SELECT COUNT(*) FROM lock_requests WHERE request_status = 'pending'")->fetchColumn(),
        ];

        $recentRooms = Database::connection()->query(
            "SELECT rooms.id, rooms.room_number, rooms.price, rooms.status, branches.name AS branch_name, districts.name AS district_name
             FROM rooms
             INNER JOIN branches ON branches.id = rooms.branch_id
             INNER JOIN districts ON districts.id = branches.district_id
             ORDER BY rooms.updated_at DESC
             LIMIT 8"
        )->fetchAll();

        $recentLockRequests = Database::connection()->query(
            "SELECT lock_requests.id, lock_requests.request_status, lock_requests.requested_at,
                    rooms.room_number, users.full_name
             FROM lock_requests
             INNER JOIN rooms ON rooms.id = lock_requests.room_id
             INNER JOIN users ON users.id = lock_requests.requested_by
             ORDER BY lock_requests.requested_at DESC
             LIMIT 8"
        )->fetchAll();

        View::render('dashboard.index', [
            'pageTitle' => 'Bảng điều khiển',
            'stats' => $stats,
            'recentRooms' => $recentRooms,
            'recentLockRequests' => $recentLockRequests,
            'user' => Auth::user(),
        ]);
    }
}
