<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;

class ReportController
{
    public function index(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $connection = Database::connection();
        $rankingMode = trim((string) query('ranking_mode', 'highest'));
        $topLimit = (int) query('top_limit', 10);

        $allowedModes = ['highest', 'lowest'];
        $allowedLimits = [10, 50, 100];

        if (! in_array($rankingMode, $allowedModes, true)) {
            $rankingMode = 'highest';
        }

        if (! in_array($topLimit, $allowedLimits, true)) {
            $topLimit = 10;
        }

        $orderDirection = $rankingMode === 'lowest' ? 'ASC' : 'DESC';

        $summary = [
            'day' => $this->countApprovedLocks("DATE(lock_requests.decided_at) = CURDATE()"),
            'week' => $this->countApprovedLocks('YEARWEEK(lock_requests.decided_at, 1) = YEARWEEK(CURDATE(), 1)'),
            'month' => $this->countApprovedLocks('YEAR(lock_requests.decided_at) = YEAR(CURDATE()) AND MONTH(lock_requests.decided_at) = MONTH(CURDATE())'),
            'quarter' => $this->countApprovedLocks('YEAR(lock_requests.decided_at) = YEAR(CURDATE()) AND QUARTER(lock_requests.decided_at) = QUARTER(CURDATE())'),
            'pending' => (int) $connection->query("SELECT COUNT(*) FROM lock_requests WHERE request_status = 'pending'")->fetchColumn(),
            'totalApproved' => (int) $connection->query("SELECT COUNT(*) FROM lock_requests WHERE request_status = 'approved'")->fetchColumn(),
        ];

        $topStaff = $this->fetchRankedRows(
            "SELECT users.full_name,
                    users.username,
                    COUNT(lock_requests.id) AS approved_count
             FROM lock_requests
             INNER JOIN users ON users.id = lock_requests.requested_by
             WHERE lock_requests.request_status = 'approved'
             GROUP BY users.id, users.full_name, users.username",
            $orderDirection,
            $topLimit,
            'users.full_name ASC'
        );

        $topSystems = $this->fetchRankedRows(
            "SELECT systems.name,
                    COUNT(lock_requests.id) AS approved_count
             FROM lock_requests
             INNER JOIN rooms ON rooms.id = lock_requests.room_id
             INNER JOIN branches ON branches.id = rooms.branch_id
             INNER JOIN systems ON systems.id = branches.system_id
             WHERE lock_requests.request_status = 'approved'
             GROUP BY systems.id, systems.name",
            $orderDirection,
            $topLimit,
            'systems.name ASC'
        );

        $topBranches = $this->fetchRankedRows(
            "SELECT branches.name,
                    systems.name AS system_name,
                    COUNT(lock_requests.id) AS approved_count
             FROM lock_requests
             INNER JOIN rooms ON rooms.id = lock_requests.room_id
             INNER JOIN branches ON branches.id = rooms.branch_id
             INNER JOIN systems ON systems.id = branches.system_id
             WHERE lock_requests.request_status = 'approved'
             GROUP BY branches.id, branches.name, systems.name",
            $orderDirection,
            $topLimit,
            'branches.name ASC'
        );

        View::render('reports.index', [
            'pageTitle' => 'Báo cáo thống kê',
            'summary' => $summary,
            'topStaff' => $topStaff,
            'topSystems' => $topSystems,
            'topBranches' => $topBranches,
            'rankingMode' => $rankingMode,
            'topLimit' => $topLimit,
        ]);
    }

    private function countApprovedLocks(string $condition): int
    {
        $statement = Database::connection()->query(
            "SELECT COUNT(*)
             FROM lock_requests
             WHERE request_status = 'approved'
               AND decided_at IS NOT NULL
               AND {$condition}"
        );

        return (int) $statement->fetchColumn();
    }

    private function fetchRankedRows(string $baseSql, string $orderDirection, int $limit, string $secondaryOrder): array
    {
        $sql = $baseSql . " ORDER BY approved_count {$orderDirection}, {$secondaryOrder} LIMIT {$limit}";

        return Database::connection()->query($sql)->fetchAll();
    }
}
