<?php
$statusLabels = [
    'new' => 'Mới tạo',
    'assigned' => 'Đã phân',
    'completed' => 'Đã hoàn thành',
    'canceled' => 'Đã hủy',
    'rescheduled' => 'Đã dời ngày',
    'deposited' => 'Khách đã cọc',
];
$assignmentLabels = [
    'pending' => 'Chờ xác nhận',
    'accepted' => 'Đã nhận khách',
    'rejected' => 'Đã từ chối',
];
$isDirector = $currentUser['role_name'] === 'director';
$isStaff = $currentUser['role_name'] === 'staff';
$isCollaborator = $currentUser['role_name'] === 'collaborator';
$showAllCustomers = $showAllCustomers ?? false;
$canToggleAllCustomers = $isDirector || $isStaff;
?>

<section class="content-grid two-column-balanced">
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Bộ lọc khách hàng</h3>
                <p class="panel-subtitle mb-0">Ưu tiên hiển thị khách chưa phân lên đầu và hỗ trợ lọc nhanh theo thông tin chăm sóc thực tế.</p>
            </div>
        </div>

        <form method="GET" action="<?= e(url('/customers')) ?>" class="d-grid gap-3">
            <?php if ($showAllCustomers): ?>
                <input type="hidden" name="view" value="all">
            <?php endif; ?>

            <div class="grid-2">
                <div>
                    <label class="form-label">Tên khách hàng</label>
                    <input type="text" name="customer_name" class="form-control" value="<?= e($filters['customer_name']) ?>" placeholder="Nhập tên khách">
                </div>
                <div>
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" value="<?= e($filters['phone']) ?>" placeholder="Nhập số điện thoại">
                </div>
            </div>

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
                    <label class="form-label">Ngày hẹn</label>
                    <input type="date" name="appointment_date" class="form-control" value="<?= e($filters['appointment_date'] ?? '') ?>">
                </div>
            </div>

            <div class="grid-2">
                <div>
                    <label class="form-label">Tháng hẹn</label>
                    <input type="month" name="appointment_month" class="form-control" value="<?= e($filters['appointment_month'] ?? '') ?>">
                </div>
            </div>

            <?php if ($isDirector): ?>
                <div class="grid-2">
                    <div>
                        <label class="form-label">Nhân viên phụ trách</label>
                        <select name="assigned_to" class="form-select">
                            <option value="0">Tất cả nhân viên</option>
                            <?php foreach ($staffUsers as $staff): ?>
                                <option value="<?= e((string) $staff['id']) ?>" <?= (int) $filters['assigned_to'] === (int) $staff['id'] ? 'selected' : '' ?>>
                                    <?= e($staff['full_name'] . ' (' . $staff['username'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Cộng tác viên tạo khách</label>
                        <select name="created_by" class="form-select">
                            <option value="0">Tất cả cộng tác viên</option>
                            <?php foreach ($collaboratorUsers as $collaborator): ?>
                                <option value="<?= e((string) $collaborator['id']) ?>" <?= (int) $filters['created_by'] === (int) $collaborator['id'] ? 'selected' : '' ?>>
                                    <?= e($collaborator['full_name'] . ' (' . $collaborator['username'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            <?php endif; ?>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">Lọc khách hàng</button>
                <a href="<?= e(url('/customers')) ?>" class="btn btn-outline-secondary">Xóa lọc</a>
            </div>
        </form>
    </div>

    <div class="panel-card">
        <?php if ($isCollaborator || $isDirector): ?>
            <div class="panel-header">
                <div>
                    <h3><?= $isDirector ? 'Giám đốc thêm khách hàng' : 'Thêm khách hàng vào lịch' ?></h3>
                    <p class="panel-subtitle mb-0"><?= $isDirector ? 'Giám đốc cũng có thể chủ động tạo khách mới rồi phân tiếp cho nhân viên.' : 'Cho phép trùng thời gian để cộng tác viên nhập nhiều khách trong cùng khung giờ.' ?></p>
                </div>
            </div>

            <form method="POST" action="<?= e(url('/customers')) ?>" class="d-grid gap-3">
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

                <input type="hidden" name="planning_scope" value="week">

                <div>
                    <label class="form-label">Ngày giờ hẹn</label>
                    <input type="datetime-local" name="appointment_at" class="form-control" required>
                </div>

                <div>
                    <label class="form-label">Ghi chú</label>
                    <textarea name="note" class="form-control" rows="4" placeholder="Ví dụ: khách thích phòng có gác, gần quận 7..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Thêm khách hàng</button>
            </form>
        <?php elseif ($isStaff): ?>
            <div class="panel-header">
                <div>
                    <h3>Xử lý khách được phân</h3>
                    <p class="panel-subtitle mb-0">Nhân viên cần xác nhận nhận khách trước, sau đó mới xử lý hoàn thành, hủy, dời ngày hoặc ghi nhận khách cọc.</p>
                </div>
            </div>
            <div class="empty-state">Nếu chưa sẵn sàng nhận khách, bạn có thể từ chối và ghi rõ lý do để giám đốc phân lại ngay.</div>
        <?php endif; ?>
    </div>
</section>

<script>
(function () {
    const showToast = function (type, message) {
        if (typeof window.appShowToast === 'function') {
            window.appShowToast(type, message);
            return;
        }

        window.alert(message);
    };

    const makeDiv = function (className, text) {
        const element = document.createElement('div');
        if (className) {
            element.className = className;
        }
        element.textContent = text || '';
        return element;
    };

    const makeStatusPill = function (className, text) {
        const wrapper = document.createElement('div');
        wrapper.className = 'mt-2';

        const pill = document.createElement('span');
        pill.className = 'status-pill ' + className;
        pill.textContent = text || '';
        wrapper.appendChild(pill);

        return wrapper;
    };

    const refreshCustomerList = async function () {
        const currentBody = document.querySelector('[data-customer-list-body]');
        if (!currentBody) {
            return false;
        }

        const url = new URL(window.location.href);
        url.searchParams.set('_customer_list_refresh', String(Date.now()));

        const response = await fetch(url.toString(), {
            method: 'GET',
            credentials: 'same-origin',
            cache: 'no-store',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-App-Pjax': '1',
                'Cache-Control': 'no-cache',
            },
        });

        const html = await response.text();
        const nextDocument = new DOMParser().parseFromString(html, 'text/html');
        const nextBody = nextDocument.querySelector('[data-customer-list-body]');

        if (!nextBody) {
            return false;
        }

        currentBody.replaceWith(nextBody);

        const currentCount = document.querySelector('[data-customer-count-badge]');
        const nextCount = nextDocument.querySelector('[data-customer-count-badge]');
        if (currentCount && nextCount) {
            currentCount.replaceWith(nextCount);
        }

        return true;
    };

    const updateCustomerRow = function (form, customer) {
        if (!customer || !customer.id) {
            return null;
        }

        const row = form.closest('[data-customer-row]')
            || document.querySelector('[data-customer-row="' + String(customer.id) + '"]')
            || form.closest('tr');
        if (!row) {
            return null;
        }

        const assigneeCell = row.querySelector('[data-customer-assignee-cell]');
        if (assigneeCell) {
            assigneeCell.replaceChildren(
                makeDiv('', customer.assignee_name || 'Chưa phân'),
                makeDiv('text-muted small', 'Phân bởi: ' + (customer.assigner_name || 'Giám đốc')),
                makeStatusPill('status-pending', customer.assignment_label || 'Chờ xác nhận')
            );
        }

        const statusCell = row.querySelector('[data-customer-status-cell]');
        if (statusCell) {
            const pill = document.createElement('span');
            pill.className = 'status-pill status-pending';
            pill.textContent = customer.status_label || 'Đã phân';
            statusCell.replaceChildren(pill);
        }

        const selectedOption = form.querySelector('select[name="assigned_to"] option:checked');
        const summaryText = selectedOption ? selectedOption.textContent.trim() : (customer.assignee_name || '');
        const actionCell = row.querySelector('[data-customer-action-cell]');
        if (actionCell) {
            const stack = document.createElement('div');
            stack.className = 'd-grid gap-2 customer-action-stack';
            stack.appendChild(makeDiv('small text-muted', 'Đang chờ phản hồi'));
            stack.appendChild(makeDiv('fw-semibold', customer.assignee_name || summaryText || 'Nhân viên phụ trách'));

            form.classList.add('mt-2');
            stack.appendChild(form);
            stack.appendChild(makeDiv('text-muted small', 'Nhân viên chưa xác nhận, giám đốc vẫn có thể đổi người phụ trách.'));
            actionCell.replaceChildren(stack);
        }

        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.textContent = 'Cập nhật phân khách';
            submitButton.disabled = false;
        }

        return row;
    };

    if (window.customerAssignSubmitHandler) {
        document.removeEventListener('submit', window.customerAssignSubmitHandler);
    }

    window.customerAssignSubmitHandler = async function (event) {
        const form = event.target.closest('[data-customer-assign-form]');
        if (!form) {
            return;
        }

        event.preventDefault();

        const submitButton = event.submitter || form.querySelector('button[type="submit"]');
        const originalText = submitButton ? submitButton.textContent : '';

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Đang lưu...';
        }

        try {
            const formData = new FormData(form);
            if (window.appCsrfToken && !formData.has('_csrf_token')) {
                formData.append('_csrf_token', window.appCsrfToken);
            }

            const response = await fetch(form.action, {
                method: form.method || 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': window.appCsrfToken || '',
                },
            });

            const rawResponse = await response.text();
            let data = null;
            try {
                data = rawResponse ? JSON.parse(rawResponse) : null;
            } catch (parseError) {
                throw new Error('Máy chủ chưa trả đúng dữ liệu JSON. Vui lòng tải lại trang rồi thử lại.');
            }

            if (!data) {
                throw new Error('Máy chủ trả phản hồi rỗng. Vui lòng kiểm tra log PHP hoặc thử lại.');
            }

            if (!response.ok || !data.ok) {
                throw new Error(data.message || 'Phân khách thất bại.');
            }

            updateCustomerRow(form, data.customer);
            refreshCustomerList().catch(function () {});
            showToast('success', data.message || 'Đã phân khách hàng cho nhân viên.');
        } catch (error) {
            showToast('error', error.message || 'Phân khách thất bại.');
            if (submitButton) {
                submitButton.textContent = originalText;
            }
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                if (submitButton.textContent === 'Đang lưu...') {
                    submitButton.textContent = originalText || 'Cập nhật phân khách';
                }
            }
        }
    };

    document.addEventListener('submit', window.customerAssignSubmitHandler);
})();
</script>

<section class="panel-card mt-4">
    <div class="panel-header">
        <div>
            <h3>Danh sách khách hàng</h3>
            <p class="panel-subtitle mb-0">
                <?= $isCollaborator ? 'Đây là danh sách khách hàng do bạn tạo.' : ($isStaff ? 'Đây là danh sách khách hàng đã được phân cho bạn.' : 'Các khách chưa phân hoặc đang chờ xác nhận sẽ được ưu tiên hiển thị lên đầu.') ?>
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <?php if ($canToggleAllCustomers): ?>
                <?php if ($showAllCustomers): ?>
                    <a href="<?= e(url('/customers')) ?>" class="btn btn-sm btn-outline-secondary">
                        <?= $isDirector ? 'Chỉ xem chờ xác nhận' : 'Ẩn khách đã hoàn thành' ?>
                    </a>
                <?php else: ?>
                    <a href="<?= e(url('/customers?view=all')) ?>" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                <?php endif; ?>
            <?php endif; ?>
            <span class="badge text-bg-light" data-customer-count-badge><?= e((string) count($customers)) ?> khách</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0 customer-table">
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
            <tbody data-customer-list-body>
                <?php if (! $customers): ?>
                    <tr>
                        <td colspan="7"><div class="empty-state my-3">Chưa có khách hàng nào phù hợp với bộ lọc hiện tại.</div></td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($customers as $customer): ?>
                    <?php
                    $assignmentStatus = $customer['assignment_status'] ?? null;
                    $assignmentLabel = $assignmentLabels[$assignmentStatus] ?? null;
                    $statusClass = match ($customer['status']) {
                        'completed', 'deposited' => 'status-approved',
                        'canceled' => 'status-rejected',
                        default => 'status-pending',
                    };
                    $assignmentClass = match ($assignmentStatus) {
                        'accepted' => 'status-approved',
                        'rejected' => 'status-rejected',
                        'pending' => 'status-pending',
                        default => 'status-pending',
                    };
                    $canCollaboratorDelete = $isCollaborator && (int) ($customer['assigned_to'] ?? 0) === 0;
                    ?>
                    <tr data-customer-row="<?= e((string) $customer['id']) ?>">
                        <td data-label="Khách hàng">
                            <div class="fw-semibold"><?= e($customer['customer_name']) ?></div>
                            <div class="text-muted small"><?= e($customer['phone']) ?></div>
                        </td>
                        <td data-label="Lịch hẹn">
                            <div><?= e($customer['appointment_at']) ?></div>
                        </td>
                        <td data-label="Người tạo">
                            <div><?= e($customer['creator_name']) ?></div>
                            <div class="text-muted small"><?= e($customer['creator_username']) ?></div>
                        </td>
                        <td data-label="Nhân viên phụ trách" data-customer-assignee-cell>
                            <div><?= e($customer['assignee_name'] ?: 'Chưa phân') ?></div>
                            <?php if ($customer['assigner_name']): ?>
                                <div class="text-muted small">Phân bởi: <?= e($customer['assigner_name']) ?></div>
                            <?php endif; ?>
                            <?php if ($assignmentLabel): ?>
                                <div class="mt-2">
                                    <span class="status-pill <?= e($assignmentClass) ?>"><?= e($assignmentLabel) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (! empty($customer['assignment_response_note'])): ?>
                                <div class="text-muted small mt-2">Phản hồi: <?= e($customer['assignment_response_note']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td data-label="Trạng thái" data-customer-status-cell>
                            <span class="status-pill <?= e($statusClass) ?>"><?= e($statusLabels[$customer['status']] ?? $customer['status']) ?></span>
                        </td>
                        <td data-label="Ghi chú / phòng">
                            <div><?= e($customer['note'] ?: '-') ?></div>
                            <?php if ($customer['room_number']): ?>
                                <div class="text-muted small mt-1">Phòng cọc: <?= e($customer['room_number'] . ' - ' . $customer['branch_name']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-end" data-label="Thao tác" data-customer-action-cell>
                            <div class="d-grid gap-2 customer-action-stack">
                                <?php if ($isCollaborator): ?>
                                    <details class="customer-inline-card">
                                        <summary>Sửa khách</summary>
                                        <form method="POST" action="<?= e(url('/customers/update')) ?>" class="d-grid gap-2 mt-3">
                                            <input type="hidden" name="id" value="<?= e((string) $customer['id']) ?>">
                                            <input type="hidden" name="planning_scope" value="<?= e($customer['planning_scope']) ?>">
                                            <input type="text" name="customer_name" class="form-control form-control-sm" value="<?= e($customer['customer_name']) ?>" required>
                                            <input type="text" name="phone" class="form-control form-control-sm" value="<?= e($customer['phone']) ?>" required>
                                            <input type="datetime-local" name="appointment_at" class="form-control form-control-sm" value="<?= e(date('Y-m-d\TH:i', strtotime($customer['appointment_at']))) ?>" required>
                                            <textarea name="note" class="form-control form-control-sm" rows="3"><?= e($customer['note']) ?></textarea>
                                            <button type="submit" class="btn btn-sm btn-primary">Lưu chỉnh sửa</button>
                                        </form>
                                    </details>
                                    <?php if ($canCollaboratorDelete): ?>
                                        <form method="POST" action="<?= e(url('/customers/delete')) ?>" onsubmit="return confirm('Bạn chắc chắn muốn xóa khách hàng này?');">
                                            <input type="hidden" name="id" value="<?= e((string) $customer['id']) ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted small">Khách đã được phân, không thể xóa</span>
                                    <?php endif; ?>
                                <?php elseif ($isDirector): ?>
                                    <?php if ($assignmentStatus === 'accepted' && $customer['assignee_name']): ?>
                                        <div class="small text-muted">Nhân viên đang nhận</div>
                                        <div class="fw-semibold"><?= e($customer['assignee_name']) ?></div>
                                        <div class="text-muted small">Đã xác nhận nhận khách</div>
                                    <?php elseif ($assignmentStatus === 'pending' && $customer['assignee_name']): ?>
                                        <div class="small text-muted">Đang chờ phản hồi</div>
                                        <div class="fw-semibold"><?= e($customer['assignee_name']) ?></div>
                                        <form method="POST" action="<?= e(url('/customers/assign')) ?>" class="d-grid gap-2 mt-2" data-preserve-scroll data-no-ajax data-customer-assign-form>
                                            <input type="hidden" name="id" value="<?= e((string) $customer['id']) ?>">
                                            <select name="assigned_to" class="form-select form-select-sm" required>
                                                <option value="">Chọn nhân viên</option>
                                                <?php foreach ($staffUsers as $staff): ?>
                                                    <option value="<?= e((string) $staff['id']) ?>" <?= (int) $customer['assigned_to'] === (int) $staff['id'] ? 'selected' : '' ?>>
                                                        <?= e($staff['full_name'] . ' (' . $staff['username'] . ')') ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary">Cập nhật phân khách</button>
                                        </form>
                                        <div class="text-muted small">Nhân viên chưa xác nhận, giám đốc vẫn có thể đổi người phụ trách.</div>
                                    <?php else: ?>
                                        <form method="POST" action="<?= e(url('/customers/assign')) ?>" class="d-grid gap-2" data-preserve-scroll data-no-ajax data-customer-assign-form>
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
                                    <?php endif; ?>
                                <?php elseif ($isStaff): ?>
                                    <?php if ($assignmentStatus === 'pending'): ?>
                                        <form method="POST" action="<?= e(url('/customers/confirm-assignment')) ?>" class="d-grid gap-2" data-preserve-scroll>
                                            <input type="hidden" name="id" value="<?= e((string) $customer['id']) ?>">
                                            <button type="submit" class="btn btn-sm btn-primary">Xác nhận nhận khách</button>
                                        </form>
                                        <details class="customer-inline-card">
                                            <summary>Từ chối nhận khách</summary>
                                            <form method="POST" action="<?= e(url('/customers/reject-assignment')) ?>" class="d-grid gap-2 mt-3" data-preserve-scroll>
                                                <input type="hidden" name="id" value="<?= e((string) $customer['id']) ?>">
                                                <textarea name="assignment_rejection_reason" class="form-control form-control-sm" rows="3" placeholder="Nhập lý do từ chối" required></textarea>
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Gửi từ chối</button>
                                            </form>
                                        </details>
                                    <?php elseif ($assignmentStatus === 'accepted'): ?>
                                        <details class="customer-inline-card">
                                            <summary>Xử lý khách</summary>
                                            <form method="POST" action="<?= e(url('/customers/progress')) ?>" class="d-grid gap-2 mt-3" data-preserve-scroll>
                                                <input type="hidden" name="id" value="<?= e((string) $customer['id']) ?>">
                                                <select name="progress_action" class="form-select form-select-sm" required>
                                                    <option value="completed">Đã hoàn thành</option>
                                                    <option value="canceled">Đã hủy</option>
                                                    <option value="rescheduled">Dời ngày</option>
                                                    <option value="deposited">Khách cọc căn hộ dịch vụ</option>
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
                                    <?php else: ?>
                                        <span class="text-muted small">Đang chờ giám đốc phân khách</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
