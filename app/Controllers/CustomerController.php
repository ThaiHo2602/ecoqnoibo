<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Session;
use App\Core\View;
use App\Services\Mailer;

class CustomerController
{
    public function index(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'staff', 'collaborator']);

        $currentUser = Auth::user();
        $connection = Database::connection();
        $showAllCustomers = query('view', '') === 'all';
        $filters = [
            'status' => trim((string) query('status', '')),
            'customer_name' => trim((string) query('customer_name', '')),
            'phone' => trim((string) query('phone', '')),
            'appointment_date' => trim((string) query('appointment_date', '')),
            'appointment_month' => trim((string) query('appointment_month', '')),
            'assigned_to' => (int) query('assigned_to', 0),
            'created_by' => (int) query('created_by', 0),
        ];

        $sql = "SELECT customer_leads.*,
                       creator.full_name AS creator_name,
                       creator.username AS creator_username,
                       assignee.full_name AS assignee_name,
                       assignee.username AS assignee_username,
                       assigner.full_name AS assigner_name,
                       rooms.room_number,
                       branches.name AS branch_name
                FROM customer_leads
                INNER JOIN users AS creator ON creator.id = customer_leads.created_by
                LEFT JOIN users AS assignee ON assignee.id = customer_leads.assigned_to
                LEFT JOIN users AS assigner ON assigner.id = customer_leads.assigned_by
                LEFT JOIN rooms ON rooms.id = customer_leads.selected_room_id
                LEFT JOIN branches ON branches.id = rooms.branch_id
                WHERE 1 = 1";
        $params = [];

        if ($currentUser['role_name'] === 'collaborator') {
            $sql .= ' AND customer_leads.created_by = :created_by_scope';
            $params['created_by_scope'] = $currentUser['id'];
        }

        if ($currentUser['role_name'] === 'staff') {
            $sql .= ' AND customer_leads.assigned_to = :assigned_scope';
            $params['assigned_scope'] = $currentUser['id'];

            if (! $showAllCustomers && $filters['status'] === '') {
                $sql .= " AND customer_leads.status <> 'completed'";
            }
        }

        if ($currentUser['role_name'] === 'director' && ! $showAllCustomers && $filters['status'] === '') {
            $sql .= " AND (customer_leads.assignment_status = 'pending' OR customer_leads.status = 'new')";
        }

        if ($filters['status'] !== '') {
            $sql .= ' AND customer_leads.status = :status';
            $params['status'] = $filters['status'];
        }

        if ($filters['customer_name'] !== '') {
            $sql .= ' AND customer_leads.customer_name LIKE :customer_name';
            $params['customer_name'] = '%' . $filters['customer_name'] . '%';
        }

        if ($filters['phone'] !== '') {
            $sql .= ' AND customer_leads.phone LIKE :phone';
            $params['phone'] = '%' . $filters['phone'] . '%';
        }

        if ($filters['appointment_date'] !== '') {
            $sql .= ' AND DATE(customer_leads.appointment_at) = :appointment_date';
            $params['appointment_date'] = $filters['appointment_date'];
        }

        if ($filters['appointment_month'] !== '') {
            $sql .= " AND DATE_FORMAT(customer_leads.appointment_at, '%Y-%m') = :appointment_month";
            $params['appointment_month'] = $filters['appointment_month'];
        }

        if ($filters['assigned_to'] > 0 && $currentUser['role_name'] === 'director') {
            $sql .= ' AND customer_leads.assigned_to = :assigned_to';
            $params['assigned_to'] = $filters['assigned_to'];
        }

        if ($filters['created_by'] > 0 && $currentUser['role_name'] === 'director') {
            $sql .= ' AND customer_leads.created_by = :created_by';
            $params['created_by'] = $filters['created_by'];
        }

        $sql .= " ORDER BY
                    CASE
                        WHEN customer_leads.assigned_to IS NULL THEN 0
                        WHEN customer_leads.assignment_status = 'pending' THEN 1
                        WHEN customer_leads.assignment_status = 'rejected' THEN 2
                        ELSE 3
                    END ASC,
                    customer_leads.appointment_at ASC,
                    customer_leads.id DESC";

        $statement = $connection->prepare($sql);
        $statement->execute($params);
        $customers = $statement->fetchAll();

        $staffUsers = $connection->query(
            "SELECT users.id, users.full_name, users.username
             FROM users
             INNER JOIN roles ON roles.id = users.role_id
             WHERE roles.name = 'staff' AND users.account_status = 'active'
             ORDER BY users.full_name ASC"
        )->fetchAll();

        $collaboratorUsers = $connection->query(
            "SELECT users.id, users.full_name, users.username
             FROM users
             INNER JOIN roles ON roles.id = users.role_id
             WHERE roles.name = 'collaborator' AND users.account_status = 'active'
             ORDER BY users.full_name ASC"
        )->fetchAll();

        $availableRooms = $connection->query(
            "SELECT rooms.id,
                    rooms.room_number,
                    branches.name AS branch_name,
                    systems.name AS system_name
             FROM rooms
             INNER JOIN branches ON branches.id = rooms.branch_id
             INNER JOIN systems ON systems.id = branches.system_id
             WHERE rooms.status = 'chua_lock'
             ORDER BY systems.name ASC, branches.name ASC, rooms.room_number ASC"
        )->fetchAll();

        View::render('customers.index', [
            'pageTitle' => 'Khách hàng',
            'customers' => $customers,
            'filters' => $filters,
            'staffUsers' => $staffUsers,
            'collaboratorUsers' => $collaboratorUsers,
            'availableRooms' => $availableRooms,
            'currentUser' => $currentUser,
            'showAllCustomers' => $showAllCustomers,
        ]);
    }

    public function store(): void
    {
        $this->persistLead($_POST);
    }

    public function storeFromQuery(): void
    {
        $this->persistLead($_GET);
    }

    public function update(): void
    {
        Auth::requireLogin();
        Auth::authorize(['collaborator', 'director']);

        $lead = $this->findLead((int) ($_POST['id'] ?? 0));
        if (! $lead) {
            Session::flash('error', 'Khách hàng không tồn tại.');
            redirect('/customers');
        }

        $currentUser = Auth::user();
        if ($currentUser['role_name'] === 'collaborator' && (int) $lead['created_by'] !== (int) $currentUser['id']) {
            abort(403, 'Bạn không có quyền chỉnh sửa khách hàng này.');
        }

        $payload = $this->validatedCreatePayload();
        if ($payload === null) {
            redirect('/customers');
        }

        Database::connection()->prepare(
            'UPDATE customer_leads
             SET customer_name = :customer_name,
                 phone = :phone,
                 note = :note,
                 planning_scope = :planning_scope,
                 appointment_at = :appointment_at
             WHERE id = :id'
        )->execute([
            'id' => $lead['id'],
            'customer_name' => $payload['customer_name'],
            'phone' => $payload['phone'],
            'note' => $payload['note'],
            'planning_scope' => $payload['planning_scope'],
            'appointment_at' => $payload['appointment_at'],
        ]);

        activity_log((int) $currentUser['id'], 'update', 'customer_leads', 'Cập nhật khách hàng #' . $lead['id']);
        Session::flash('success', 'Đã cập nhật khách hàng thành công.');
        redirect('/customers');
    }

    public function delete(): void
    {
        Auth::requireLogin();
        Auth::authorize(['collaborator', 'director']);

        $lead = $this->findLead((int) ($_POST['id'] ?? 0));
        if (! $lead) {
            Session::flash('error', 'Khách hàng không tồn tại.');
            redirect('/customers');
        }

        $currentUser = Auth::user();
        if ($currentUser['role_name'] === 'collaborator' && (int) $lead['created_by'] !== (int) $currentUser['id']) {
            abort(403, 'Bạn không có quyền xóa khách hàng này.');
        }

        if ($currentUser['role_name'] === 'collaborator' && (int) ($lead['assigned_to'] ?? 0) > 0) {
            Session::flash('error', 'Khách hàng đã được giám đốc phân cho nhân viên, cộng tác viên không thể xóa.');
            redirect('/customers');
        }

        Database::connection()->prepare('DELETE FROM customer_leads WHERE id = :id')->execute(['id' => $lead['id']]);

        activity_log((int) $currentUser['id'], 'delete', 'customer_leads', 'Xóa khách hàng #' . $lead['id'] . ' - ' . $lead['customer_name']);
        Session::flash('success', 'Đã xóa khách hàng khỏi lịch.');
        redirect('/customers');
    }

    public function assign(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director']);

        $lead = $this->findLead((int) ($_POST['id'] ?? 0));
        $staffId = (int) ($_POST['assigned_to'] ?? 0);
        $wantsJson = $this->wantsJson();

        if (! $lead || $staffId <= 0) {
            $message = 'Thông tin phân khách không hợp lệ.';
            if ($wantsJson) {
                $this->jsonResponse(['ok' => false, 'message' => $message], 422);
            }

            Session::flash('error', $message);
            redirect('/customers');
        }

        if (($lead['assignment_status'] ?? '') === 'accepted') {
            $message = 'Khách hàng này đã được nhân viên xác nhận. Không thể phân lại.';
            if ($wantsJson) {
                $this->jsonResponse(['ok' => false, 'message' => $message], 422);
            }

            Session::flash('error', $message);
            redirect('/customers');
        }

        $staffStatement = Database::connection()->prepare(
            "SELECT users.id, users.full_name, users.username, users.email
             FROM users
             INNER JOIN roles ON roles.id = users.role_id
             WHERE users.id = :id AND roles.name = 'staff'
             LIMIT 1"
        );
        $staffStatement->execute(['id' => $staffId]);
        $staff = $staffStatement->fetch();

        if (! $staff) {
            $message = 'Nhân viên được chọn không hợp lệ.';
            if ($wantsJson) {
                $this->jsonResponse(['ok' => false, 'message' => $message], 422);
            }

            Session::flash('error', $message);
            redirect('/customers');
        }

        $currentUser = Auth::user();
        Database::connection()->prepare(
            "UPDATE customer_leads
             SET assigned_to = :assigned_to,
                 assigned_by = :assigned_by,
                 status = 'assigned',
                 assignment_status = 'pending',
                 assignment_response_note = NULL,
                 assignment_responded_at = NULL
             WHERE id = :id"
        )->execute([
            'assigned_to' => $staffId,
            'assigned_by' => $currentUser['id'],
            'id' => $lead['id'],
        ]);

        $mailResult = [
            'sent' => false,
            'reason' => $wantsJson ? 'ajax_fast_assign' : 'not_sent',
        ];

        if (! $wantsJson) {
            try {
                $mailResult = Mailer::sendAssignmentNotification($staff, $lead, $currentUser);
            } catch (\Throwable $exception) {
                $mailResult = [
                    'sent' => false,
                    'reason' => 'mailer_exception',
                    'error' => $exception->getMessage(),
                ];
            }
        }

        activity_log((int) $currentUser['id'], 'assign', 'customer_leads', 'Phân khách hàng #' . $lead['id'] . ' cho nhân viên #' . $staffId);

        $message = 'Đã phân khách hàng cho nhân viên.';
        if ($mailResult['sent']) {
            $message .= ' Email thông báo đã được gửi.';
        } elseif ($mailResult['reason'] === 'ajax_fast_assign') {
            $message .= ' Chế độ phân nhanh không chờ gửi email.';
        } elseif (in_array($mailResult['reason'], ['missing_recipient_email', 'invalid_recipient_email'], true)) {
            $message .= ' Tuy nhiên chưa gửi email vì tài khoản nhân viên chưa có email hợp lệ.';
        } else {
            $message .= ' Tuy nhiên email chưa được gửi vì SMTP chưa cấu hình xong.';
        }

        if ($wantsJson) {
            $this->jsonResponse([
                'ok' => true,
                'message' => $message,
                'customer' => [
                    'id' => (int) $lead['id'],
                    'status' => 'assigned',
                    'status_label' => 'Đã phân',
                    'assignment_status' => 'pending',
                    'assignment_label' => 'Chờ xác nhận',
                    'assignee_name' => $staff['full_name'],
                    'assignee_username' => $staff['username'] ?? '',
                    'assigner_name' => $currentUser['full_name'] ?? '',
                ],
            ]);
        }

        Session::flash('success', $message);
        redirect('/customers');
    }

    public function confirmAssignment(): void
    {
        Auth::requireLogin();
        Auth::authorize(['staff']);

        $lead = $this->findLead((int) ($_POST['id'] ?? 0));
        $currentUser = Auth::user();

        if (! $lead || (int) $lead['assigned_to'] !== (int) $currentUser['id']) {
            Session::flash('error', 'Bạn không có quyền xác nhận khách hàng này.');
            redirect('/customers');
        }

        if (($lead['assignment_status'] ?? '') !== 'pending') {
            Session::flash('error', 'Khách hàng này không ở trạng thái chờ xác nhận.');
            redirect('/customers');
        }

        Database::connection()->prepare(
            "UPDATE customer_leads
             SET assignment_status = 'accepted',
                 assignment_response_note = NULL,
                 assignment_responded_at = NOW()
             WHERE id = :id"
        )->execute(['id' => $lead['id']]);

        activity_log((int) $currentUser['id'], 'accept_assignment', 'customer_leads', 'Xác nhận nhận khách hàng #' . $lead['id']);
        Session::flash('success', 'Đã xác nhận nhận khách hàng.');
        redirect('/customers');
    }

    public function rejectAssignment(): void
    {
        Auth::requireLogin();
        Auth::authorize(['staff']);

        $lead = $this->findLead((int) ($_POST['id'] ?? 0));
        $currentUser = Auth::user();
        $reason = trim((string) ($_POST['assignment_rejection_reason'] ?? ''));

        if (! $lead || (int) $lead['assigned_to'] !== (int) $currentUser['id']) {
            Session::flash('error', 'Bạn không có quyền từ chối khách hàng này.');
            redirect('/customers');
        }

        if (($lead['assignment_status'] ?? '') !== 'pending') {
            Session::flash('error', 'Khách hàng này không ở trạng thái chờ xác nhận.');
            redirect('/customers');
        }

        if ($reason === '') {
            Session::flash('error', 'Vui lòng nhập lý do từ chối.');
            redirect('/customers');
        }

        Database::connection()->prepare(
            "UPDATE customer_leads
             SET assigned_to = NULL,
                 status = 'new',
                 assignment_status = 'rejected',
                 assignment_response_note = :assignment_response_note,
                 assignment_responded_at = NOW()
             WHERE id = :id"
        )->execute([
            'id' => $lead['id'],
            'assignment_response_note' => $reason,
        ]);

        activity_log((int) $currentUser['id'], 'reject_assignment', 'customer_leads', 'Từ chối nhận khách hàng #' . $lead['id']);
        Session::flash('success', 'Đã từ chối nhận khách hàng và gửi lại cho giám đốc.');
        redirect('/customers');
    }

    public function progress(): void
    {
        Auth::requireLogin();
        Auth::authorize(['staff']);

        $lead = $this->findLead((int) ($_POST['id'] ?? 0));
        $action = trim((string) ($_POST['progress_action'] ?? ''));
        $note = trim((string) ($_POST['progress_note'] ?? ''));
        $currentUser = Auth::user();

        if (! $lead || (int) $lead['assigned_to'] !== (int) $currentUser['id']) {
            Session::flash('error', 'Bạn không có quyền xử lý khách hàng này.');
            redirect('/customers');
        }

        if (($lead['assignment_status'] ?? '') !== 'accepted') {
            Session::flash('error', 'Bạn cần xác nhận nhận khách trước khi xử lý.');
            redirect('/customers');
        }

        $connection = Database::connection();

        if ($action === 'completed') {
            $connection->prepare(
                "UPDATE customer_leads
                 SET status = 'completed',
                     note = :note,
                     completed_at = NOW()
                 WHERE id = :id"
            )->execute([
                'id' => $lead['id'],
                'note' => $this->mergeNote($lead['note'], $note),
            ]);

            activity_log((int) $currentUser['id'], 'complete', 'customer_leads', 'Đánh dấu hoàn thành khách hàng #' . $lead['id']);
            Session::flash('success', 'Đã đánh dấu khách hàng là hoàn thành.');
            redirect('/customers');
        }

        if ($action === 'canceled') {
            $connection->prepare(
                "UPDATE customer_leads
                 SET status = 'canceled',
                     note = :note
                 WHERE id = :id"
            )->execute([
                'id' => $lead['id'],
                'note' => $this->mergeNote($lead['note'], $note),
            ]);

            activity_log((int) $currentUser['id'], 'cancel', 'customer_leads', 'Đánh dấu hủy khách hàng #' . $lead['id']);
            Session::flash('success', 'Đã đánh dấu khách hàng là đã hủy.');
            redirect('/customers');
        }

        if ($action === 'rescheduled') {
            $newAppointment = trim((string) ($_POST['new_appointment_at'] ?? ''));
            if (! $this->isValidDatetime($newAppointment)) {
                Session::flash('error', 'Ngày hẹn mới không hợp lệ.');
                redirect('/customers');
            }

            $connection->prepare(
                "UPDATE customer_leads
                 SET status = 'rescheduled',
                     appointment_at = :appointment_at,
                     note = :note
                 WHERE id = :id"
            )->execute([
                'id' => $lead['id'],
                'appointment_at' => $newAppointment,
                'note' => $this->mergeNote($lead['note'], $note),
            ]);

            activity_log((int) $currentUser['id'], 'reschedule', 'customer_leads', 'Dời lịch khách hàng #' . $lead['id']);
            Session::flash('success', 'Đã dời lịch cho khách hàng.');
            redirect('/customers');
        }

        if ($action === 'deposited') {
            $roomId = (int) ($_POST['room_id'] ?? 0);
            if ($roomId <= 0) {
                Session::flash('error', 'Vui lòng chọn phòng khách đã cọc.');
                redirect('/customers');
            }

            $roomStatement = $connection->prepare('SELECT * FROM rooms WHERE id = :id LIMIT 1');
            $roomStatement->execute(['id' => $roomId]);
            $room = $roomStatement->fetch();

            if (! $room || $room['status'] !== 'chua_lock') {
                Session::flash('error', 'Phòng được chọn không hợp lệ hoặc không còn ở trạng thái Chưa lock.');
                redirect('/customers');
            }

            $pendingStatement = $connection->prepare(
                "SELECT COUNT(*) FROM lock_requests
                 WHERE room_id = :room_id AND request_status = 'pending'"
            );
            $pendingStatement->execute(['room_id' => $roomId]);
            if ((int) $pendingStatement->fetchColumn() > 0) {
                Session::flash('error', 'Phòng này đã có yêu cầu lock đang chờ duyệt.');
                redirect('/customers');
            }

            $connection->beginTransaction();

            try {
                $connection->prepare(
                    "INSERT INTO lock_requests (room_id, requested_by, request_status, request_note, requested_at)
                     VALUES (:room_id, :requested_by, 'pending', :request_note, NOW())"
                )->execute([
                    'room_id' => $roomId,
                    'requested_by' => $currentUser['id'],
                    'request_note' => 'Khách ' . $lead['customer_name'] . ' đã cọc phòng. ' . ($note !== '' ? $note : 'Đề nghị quản lý duyệt lock.'),
                ]);

                $connection->prepare("UPDATE rooms SET status = 'dang_giu' WHERE id = :id")
                    ->execute(['id' => $roomId]);

                $connection->prepare(
                    "UPDATE customer_leads
                     SET status = 'deposited',
                         selected_room_id = :selected_room_id,
                         note = :note
                     WHERE id = :id"
                )->execute([
                    'id' => $lead['id'],
                    'selected_room_id' => $roomId,
                    'note' => $this->mergeNote($lead['note'], $note),
                ]);

                $connection->commit();
            } catch (\Throwable) {
                $connection->rollBack();
                Session::flash('error', 'Không thể tạo yêu cầu lock cho khách cọc căn hộ dịch vụ.');
                redirect('/customers');
            }

            activity_log((int) $currentUser['id'], 'deposit', 'customer_leads', 'Khách hàng #' . $lead['id'] . ' đã cọc căn hộ dịch vụ, gửi lock phòng #' . $roomId);
            Session::flash('success', 'Đã ghi nhận khách cọc căn hộ dịch vụ và gửi yêu cầu lock phòng.');
            redirect('/customers');
        }

        Session::flash('error', 'Thao tác xử lý khách hàng không hợp lệ.');
        redirect('/customers');
    }

    private function persistLead(array $source): void
    {
        Auth::requireLogin();
        Auth::authorize(['collaborator', 'director']);

        $payload = $this->validatedCreatePayload($source);
        if ($payload === null) {
            redirect('/customers');
        }

        $currentUser = Auth::user();
        Database::connection()->prepare(
            'INSERT INTO customer_leads (created_by, customer_name, phone, note, planning_scope, appointment_at, status)
             VALUES (:created_by, :customer_name, :phone, :note, :planning_scope, :appointment_at, :status)'
        )->execute([
            'created_by' => $currentUser['id'],
            'customer_name' => $payload['customer_name'],
            'phone' => $payload['phone'],
            'note' => $payload['note'],
            'planning_scope' => $payload['planning_scope'],
            'appointment_at' => $payload['appointment_at'],
            'status' => 'new',
        ]);

        $leadId = (int) Database::connection()->lastInsertId();
        activity_log((int) $currentUser['id'], 'create', 'customer_leads', 'Tạo khách hàng #' . $leadId . ' - ' . $payload['customer_name']);
        Session::flash('success', 'Đã thêm khách hàng mới vào lịch.');
        redirect('/customers');
    }

    private function validatedCreatePayload(array $source): ?array
    {
        $customerName = trim((string) ($source['customer_name'] ?? ''));
        $phone = trim((string) ($source['phone'] ?? ''));
        $note = trim((string) ($source['note'] ?? ''));
        $planningScope = trim((string) ($source['planning_scope'] ?? 'week'));
        $appointmentAt = trim((string) ($source['appointment_at'] ?? ''));

        if ($customerName === '' || $phone === '' || ! in_array($planningScope, ['week', 'month'], true) || ! $this->isValidDatetime($appointmentAt)) {
            Session::flash('error', 'Vui lòng nhập đầy đủ và đúng thông tin khách hàng.');
            return null;
        }

        return [
            'customer_name' => $customerName,
            'phone' => $phone,
            'note' => $note !== '' ? $note : null,
            'planning_scope' => $planningScope,
            'appointment_at' => $appointmentAt,
        ];
    }

    private function isValidDatetime(string $value): bool
    {
        return strtotime($value) !== false;
    }

    private function findLead(int $leadId): ?array
    {
        if ($leadId <= 0) {
            return null;
        }

        $statement = Database::connection()->prepare('SELECT * FROM customer_leads WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $leadId]);

        return $statement->fetch() ?: null;
    }

    private function wantsJson(): bool
    {
        $accept = strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? ''));
        $requestedWith = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''));

        return strpos($accept, 'application/json') !== false || $requestedWith === 'xmlhttprequest';
    }

    private function jsonResponse(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function mergeNote(?string $original, string $appended): ?string
    {
        $original = trim((string) $original);
        $appended = trim($appended);

        if ($appended === '') {
            return $original !== '' ? $original : null;
        }

        if ($original === '') {
            return $appended;
        }

        return $original . PHP_EOL . '[' . date('Y-m-d H:i') . '] ' . $appended;
    }
}
