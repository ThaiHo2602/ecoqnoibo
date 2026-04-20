<?php
$statusLabels = [
    'new' => 'Mới tạo',
    'assigned' => 'Đã phân',
    'completed' => 'Đã hoàn thành',
    'canceled' => 'Đã hủy',
    'rescheduled' => 'Đã dời ngày',
    'deposited' => 'Khách đã cọc',
];
$scopeLabels = [
    'week' => 'Lịch tuần',
    'month' => 'Lịch tháng',
];
$isDirector = $currentUser['role_name'] === 'director';
$isStaff = $currentUser['role_name'] === 'staff';
$isCollaborator = $currentUser['role_name'] === 'collaborator';
?>

<section class="content-grid two-column-balanced">
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Bộ lọc khách hàng</h3>
                <p class="panel-subtitle mb-0">Lọc theo trạng thái, loại lịch và từ khóa khách hàng.</p>
            </div>
        </div>

        <form method="GET" action="<?= e(url('/customers')) ?>" class="d-grid gap-3">
            <div class="grid-2">
                <div>
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <?php foreach ($statusLabels as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= $filters['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Loại lịch</label>
                    <select name="planning_scope" class="form-select">
                        <option value="">Tất cả</option>
                        <?php foreach ($scopeLabels as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= $filters['planning_scope'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label">Từ khóa</label>
                <input type="text" name="keyword" class="form-control" value="<?= e($filters['keyword']) ?>" placeholder="Tên khách, số điện thoại, ghi chú...">
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">Lọc khách hàng</button>
                <a href="<?= e(url('/customers')) ?>" class="btn btn-outline-secondary">Xóa lọc</a>
            </div>
        </form>
    </div>

    <div class="panel-card">
        <?php if ($isCollaborator): ?>
            <div class="panel-header">
                <div>
                    <h3>Thêm khách hàng vào lịch</h3>
                    <p class="panel-subtitle mb-0">Cho phép trùng thời gian để cộng tác viên nhập nhiều khách trong cùng khung giờ.</p>
                </div>
            </div>

            <form method="POST" action="<?= e(url('/customers/store')) ?>" class="d-grid gap-3">
                <div class="grid-2">
                    <div>
                        <label class="form-label">Tên khách hàng</label>
                        <input type="text" name="customer_name" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label class="form-label">Loại lịch</label>
                        <select name="planning_scope" class="form-select" required>
                            <option value="week">Lịch tuần</option>
                            <option value="month">Lịch tháng</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Ngày giờ hẹn</label>
                        <input type="datetime-local" name="appointment_at" class="form-control" required>
                    </div>
                </div>

                <div>
                    <label class="form-label">Ghi chú</label>
                    <textarea name="note" class="form-control" rows="4" placeholder="Ví dụ: khách thích phòng có gác, gần quận 7..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Thêm khách hàng</button>
            </form>
        <?php elseif ($isDirector): ?>
            <div class="panel-header">
                <div>
                    <h3>Phân khách cho nhân viên</h3>
                    <p class="panel-subtitle mb-0">Giám đốc xem toàn bộ lịch khách và phân công cho từng nhân viên phụ trách.</p>
                </div>
            </div>
            <div class="empty-state">Chọn một khách hàng ở bảng bên dưới để phân cho nhân viên phù hợp.</div>
        <?php else: ?>
            <div class="panel-header">
                <div>
                    <h3>Xử lý khách được phân</h3>
                    <p class="panel-subtitle mb-0">Nhân viên có thể hoàn thành, hủy, dời ngày hoặc ghi nhận khách đã cọc trọ.</p>
                </div>
            </div>
            <div class="empty-state">Khi khách đã cọc trọ, chọn đúng phòng để hệ thống tự gửi yêu cầu lock cho quản lý.</div>
        <?php endif; ?>
    </div>
</section>

<section class="panel-card mt-4">
    <div class="panel-header">
        <div>
            <h3>Danh sách khách hàng</h3>
            <p class="panel-subtitle mb-0">
                <?= $isCollaborator ? 'Đây là danh sách khách hàng do bạn tạo.' : ($isStaff ? 'Đây là danh sách khách hàng đã được phân cho bạn.' : 'Đây là toàn bộ lịch khách hàng trong hệ thống.') ?>
            </p>
        </div>
        <span class="badge text-bg-light"><?= e((string) count($customers)) ?> khách</span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Khách hàng</th>
                    <th>Lịch hẹn</th>
                    <th>Người tạo</th>
                    <th>Nhân viên phụ trách</th>
                    <th>Trạng thái</th>
                    <th>Ghi chú / phòng</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! $customers): ?>
                    <tr>
                        <td colspan="7"><div class="empty-state my-3">Chưa có khách hàng nào phù hợp với bộ lọc hiện tại.</div></td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($customer['customer_name']) ?></div>
                            <div class="text-muted small"><?= e($customer['phone']) ?></div>
                        </td>
                        <td>
                            <div><?= e($customer['appointment_at']) ?></div>
                            <div class="text-muted small"><?= e($scopeLabels[$customer['planning_scope']] ?? $customer['planning_scope']) ?></div>
                        </td>
                        <td>
                            <div><?= e($customer['creator_name']) ?></div>
                            <div class="text-muted small"><?= e($customer['creator_username']) ?></div>
                        </td>
                        <td>
                            <div><?= e($customer['assignee_name'] ?: 'Chưa phân') ?></div>
                            <?php if ($customer['assigner_name']): ?>
                                <div class="text-muted small">Phân bởi: <?= e($customer['assigner_name']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><span class="status-pill status-approved"><?= e($statusLabels[$customer['status']] ?? $customer['status']) ?></span></td>
                        <td>
                            <div><?= e($customer['note'] ?: '-') ?></div>
                            <?php if ($customer['room_number']): ?>
                                <div class="text-muted small mt-1">Phòng cọc: <?= e($customer['room_number'] . ' - ' . $customer['branch_name']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="d-grid gap-2 customer-action-stack">
                                <?php if ($isCollaborator): ?>
                                    <details class="customer-inline-card">
                                        <summary>Sửa khách</summary>
                                        <form method="POST" action="<?= e(url('/customers/update')) ?>" class="d-grid gap-2 mt-3">
                                            <input type="hidden" name="id" value="<?= e((string) $customer['id']) ?>">
                                            <input type="text" name="customer_name" class="form-control form-control-sm" value="<?= e($customer['customer_name']) ?>" required>
                                            <input type="text" name="phone" class="form-control form-control-sm" value="<?= e($customer['phone']) ?>" required>
                                            <select name="planning_scope" class="form-select form-select-sm">
                                                <option value="week" <?= $customer['planning_scope'] === 'week' ? 'selected' : '' ?>>Lịch tuần</option>
                                                <option value="month" <?= $customer['planning_scope'] === 'month' ? 'selected' : '' ?>>Lịch tháng</option>
                                            </select>
                                            <input type="datetime-local" name="appointment_at" class="form-control form-control-sm" value="<?= e(date('Y-m-d\TH:i', strtotime($customer['appointment_at']))) ?>" required>
                                            <textarea name="note" class="form-control form-control-sm" rows="3"><?= e($customer['note']) ?></textarea>
                                            <button type="submit" class="btn btn-sm btn-primary">Lưu chỉnh sửa</button>
                                        </form>
                                    </details>
                                    <form method="POST" action="<?= e(url('/customers/delete')) ?>" onsubmit="return confirm('Bạn chắc chắn muốn xóa khách hàng này?');">
                                        <input type="hidden" name="id" value="<?= e((string) $customer['id']) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                    </form>
                                <?php elseif ($isDirector): ?>
                                    <form method="POST" action="<?= e(url('/customers/assign')) ?>" class="d-grid gap-2">
                                        <input type="hidden" name="id" value="<?= e((string) $customer['id']) ?>">
                                        <select name="assigned_to" class="form-select form-select-sm" required>
                                            <option value="">Chọn nhân viên</option>
                                            <?php foreach ($staffUsers as $staff): ?>
                                                <option value="<?= e((string) $staff['id']) ?>" <?= (int) $customer['assigned_to'] === (int) $staff['id'] ? 'selected' : '' ?>>
                                                    <?= e($staff['full_name'] . ' (' . $staff['username'] . ')') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">Phân khách</button>
                                    </form>
                                <?php elseif ($isStaff): ?>
                                    <details class="customer-inline-card">
                                        <summary>Xử lý khách</summary>
                                        <form method="POST" action="<?= e(url('/customers/progress')) ?>" class="d-grid gap-2 mt-3">
                                            <input type="hidden" name="id" value="<?= e((string) $customer['id']) ?>">
                                            <select name="progress_action" class="form-select form-select-sm" required>
                                                <option value="completed">Đã hoàn thành</option>
                                                <option value="canceled">Đã hủy</option>
                                                <option value="rescheduled">Dời ngày</option>
                                                <option value="deposited">Khách cọc trọ</option>
                                            </select>
                                            <input type="datetime-local" name="new_appointment_at" class="form-control form-control-sm" value="<?= e(date('Y-m-d\TH:i', strtotime($customer['appointment_at']))) ?>">
                                            <select name="room_id" class="form-select form-select-sm">
                                                <option value="">Chọn phòng nếu khách cọc</option>
                                                <?php foreach ($availableRooms as $room): ?>
                                                    <option value="<?= e((string) $room['id']) ?>">
                                                        <?= e($room['system_name'] . ' - ' . $room['branch_name'] . ' - ' . $room['room_number']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <textarea name="progress_note" class="form-control form-control-sm" rows="3" placeholder="Ghi chú xử lý"></textarea>
                                            <button type="submit" class="btn btn-sm btn-primary">Cập nhật xử lý</button>
                                        </form>
                                    </details>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
