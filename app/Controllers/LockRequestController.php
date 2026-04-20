<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Session;
use App\Core\View;

class LockRequestController
{
    public function index(): void
    {
        Auth::requireLogin();

        $connection = Database::connection();
        $filters = [
            'status' => trim((string) query('status', '')),
            'staff_id' => (int) query('staff_id', 0),
            'system_id' => (int) query('system_id', 0),
            'branch_id' => (int) query('branch_id', 0),
        ];

        $isManagerView = Auth::can(['director', 'manager']);
        $currentUser = Auth::user();

        $systems = $connection->query('SELECT id, name FROM systems ORDER BY name ASC')->fetchAll();
        $staffUsers = $connection->query(
            "SELECT users.id, users.full_name
             FROM users
             INNER JOIN roles ON roles.id = users.role_id
             WHERE roles.name = 'staff'
             ORDER BY users.full_name ASC"
        )->fetchAll();
        $branches = $connection->query(
            "SELECT branches.id, branches.name, systems.name AS system_name
             FROM branches
             INNER JOIN systems ON systems.id = branches.system_id
             ORDER BY systems.name ASC, branches.name ASC"
        )->fetchAll();

        $sql = "SELECT lock_requests.*,
                       rooms.room_number,
                       rooms.status AS room_status,
                       rooms.price,
                       branches.id AS branch_id,
                       branches.name AS branch_name,
                       systems.id AS system_id,
                       systems.name AS system_name,
                       districts.name AS district_name,
                       requester.full_name AS requester_name,
                       approver.full_name AS approver_name
                FROM lock_requests
                INNER JOIN rooms ON rooms.id = lock_requests.room_id
                INNER JOIN branches ON branches.id = rooms.branch_id
                INNER JOIN systems ON systems.id = branches.system_id
                INNER JOIN districts ON districts.id = branches.district_id
                INNER JOIN users AS requester ON requester.id = lock_requests.requested_by
                LEFT JOIN users AS approver ON approver.id = lock_requests.approved_by
                WHERE 1 = 1";

        $params = [];

        if (! $isManagerView) {
            $sql .= ' AND lock_requests.requested_by = :current_user_id';
            $params['current_user_id'] = $currentUser['id'];
        }
        if ($filters['status'] !== '') {
            $sql .= ' AND lock_requests.request_status = :status';
            $params['status'] = $filters['status'];
        }
        if ($filters['staff_id'] > 0 && $isManagerView) {
            $sql .= ' AND lock_requests.requested_by = :staff_id';
            $params['staff_id'] = $filters['staff_id'];
        }
        if ($filters['system_id'] > 0) {
            $sql .= ' AND systems.id = :system_id';
            $params['system_id'] = $filters['system_id'];
        }
        if ($filters['branch_id'] > 0) {
            $sql .= ' AND branches.id = :branch_id';
            $params['branch_id'] = $filters['branch_id'];
        }

        $sql .= ' ORDER BY lock_requests.requested_at DESC';

        $statement = $connection->prepare($sql);
        $statement->execute($params);
        $requests = $statement->fetchAll();

        View::render('locks.index', [
            'pageTitle' => 'Duyệt lock',
            'requests' => $requests,
            'filters' => $filters,
            'systems' => $systems,
            'branches' => $branches,
            'staffUsers' => $staffUsers,
            'isManagerView' => $isManagerView,
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();
        Auth::authorize(['staff']);

        $roomId = (int) ($_POST['room_id'] ?? 0);
        $requestNote = trim($_POST['request_note'] ?? '');
        $returnTo = $this->resolveReturnTo();

        if ($roomId <= 0) {
            Session::flash('error', 'Phòng không hợp lệ để gửi yêu cầu lock.');
            redirect($returnTo);
        }

        $connection = Database::connection();
        $roomStatement = $connection->prepare('SELECT * FROM rooms WHERE id = :id LIMIT 1');
        $roomStatement->execute(['id' => $roomId]);
        $room = $roomStatement->fetch();

        if (! $room) {
            Session::flash('error', 'Phòng không tồn tại.');
            redirect($returnTo);
        }

        if ($room['status'] !== 'chua_lock') {
            Session::flash('error', 'Chỉ có thể gửi lock với phòng đang ở trạng thái Chưa lock.');
            redirect($returnTo);
        }

        $pendingStatement = $connection->prepare(
            "SELECT COUNT(*) FROM lock_requests
             WHERE room_id = :room_id AND request_status = 'pending'"
        );
        $pendingStatement->execute(['room_id' => $roomId]);
        if ((int) $pendingStatement->fetchColumn() > 0) {
            Session::flash('error', 'Phòng này đã có yêu cầu lock đang chờ duyệt.');
            redirect($returnTo);
        }

        $currentUser = Auth::user();
        $connection->beginTransaction();

        try {
            $connection->prepare(
                "INSERT INTO lock_requests (room_id, requested_by, request_status, request_note, requested_at)
                 VALUES (:room_id, :requested_by, 'pending', :request_note, NOW())"
            )->execute([
                'room_id' => $roomId,
                'requested_by' => $currentUser['id'],
                'request_note' => $requestNote !== '' ? $requestNote : null,
            ]);

            $connection->prepare("UPDATE rooms SET status = 'dang_giu' WHERE id = :id")
                ->execute(['id' => $roomId]);

            $connection->commit();

            activity_log((int) $currentUser['id'], 'request_lock', 'lock_requests', 'Gửi yêu cầu lock phòng #' . $roomId);
            Session::flash('success', 'Đã gửi yêu cầu lock phòng thành công. Phòng đã chuyển sang trạng thái Đang giữ.');
        } catch (\Throwable) {
            $connection->rollBack();
            Session::flash('error', 'Gửi yêu cầu lock thất bại. Vui lòng thử lại.');
        }

        redirect($returnTo);
    }

    public function approve(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);
        $this->decide('approved', 'da_lock', 'approve_lock');
    }

    public function reject(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);
        $this->decide('rejected', 'chua_lock', 'reject_lock');
    }

    private function decide(string $requestStatus, string $roomStatus, string $logAction): void
    {
        $requestId = (int) ($_POST['id'] ?? 0);
        $decisionNote = trim($_POST['decision_note'] ?? '');

        if ($requestId <= 0) {
            Session::flash('error', 'Yêu cầu lock không hợp lệ.');
            redirect('/lock-requests');
        }

        $connection = Database::connection();
        $statement = $connection->prepare(
            "SELECT lock_requests.*, rooms.room_number, rooms.id AS room_id
             FROM lock_requests
             INNER JOIN rooms ON rooms.id = lock_requests.room_id
             WHERE lock_requests.id = :id
             LIMIT 1"
        );
        $statement->execute(['id' => $requestId]);
        $request = $statement->fetch();

        if (! $request) {
            Session::flash('error', 'Không tìm thấy yêu cầu lock.');
            redirect('/lock-requests');
        }

        if ($request['request_status'] !== 'pending') {
            Session::flash('error', 'Yêu cầu này đã được xử lý trước đó.');
            redirect('/lock-requests');
        }

        $currentUser = Auth::user();
        $connection->beginTransaction();

        try {
            $connection->prepare(
                'UPDATE lock_requests
                 SET request_status = :request_status,
                     approved_by = :approved_by,
                     decision_note = :decision_note,
                     decided_at = NOW()
                 WHERE id = :id'
            )->execute([
                'request_status' => $requestStatus,
                'approved_by' => $currentUser['id'],
                'decision_note' => $decisionNote !== '' ? $decisionNote : null,
                'id' => $requestId,
            ]);

            $connection->prepare(
                'UPDATE rooms SET status = :room_status WHERE id = :room_id'
            )->execute([
                'room_status' => $roomStatus,
                'room_id' => $request['room_id'],
            ]);

            $connection->commit();

            activity_log((int) $currentUser['id'], $logAction, 'lock_requests', 'Xử lý yêu cầu #' . $requestId . ' => ' . $requestStatus);
            Session::flash('success', $requestStatus === 'approved'
                ? 'Đã duyệt lock phòng thành công.'
                : 'Đã từ chối yêu cầu lock. Phòng đã quay về trạng thái trống.');
        } catch (\Throwable) {
            $connection->rollBack();
            Session::flash('error', 'Xử lý yêu cầu lock thất bại. Vui lòng thử lại.');
        }

        redirect('/lock-requests');
    }

    private function resolveReturnTo(): string
    {
        $returnTo = trim((string) ($_POST['return_to'] ?? ''));

        if ($returnTo !== '' && str_starts_with($returnTo, '/')) {
            return $returnTo;
        }

        return '/';
    }
}
