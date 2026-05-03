<?php
$statusLabels = [
    'pending' => 'Chờ duyệt',
    'approved' => 'Đã duyệt',
    'rejected' => 'Từ chối',
    'undone' => 'Hoàn tác',
];
$roomStatusLabels = [
    'chua_lock' => 'Chưa lock',
    'dang_giu' => 'Đang giữ',
    'da_lock' => 'Đã lock',
];
?>

<section class="content-grid two-column-balanced">
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Bộ lọc yêu cầu lock</h3>
                <p class="panel-subtitle mb-0">Lọc theo trạng thái, nhân viên, hệ thống và chi nhánh.</p>
            </div>
        </div>

        <form method="GET" action="<?= e(url('/lock-requests')) ?>" class="d-grid gap-3">
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
                    <label class="form-label">Hệ thống</label>
                    <select name="system_id" class="form-select">
                        <option value="0">Tất cả hệ thống</option>
                        <?php foreach ($systems as $system): ?>
                            <option value="<?= e((string) $system['id']) ?>" <?= (int) $filters['system_id'] === (int) $system['id'] ? 'selected' : '' ?>>
                                <?= e($system['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div>
                <label class="form-label">Phường</label>
                    <select name="ward_id" class="form-select">
                        <option value="0">Tất cả phường</option>
                        <?php foreach ($wards as $ward): ?>
                            <option value="<?= e((string) $ward['id']) ?>" <?= (int) $filters['ward_id'] === (int) $ward['id'] ? 'selected' : '' ?>>
                                <?= e($ward['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Chi nhánh</label>
                    <select name="branch_id" class="form-select">
                        <option value="0">Tất cả chi nhánh</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= e((string) $branch['id']) ?>" <?= (int) $filters['branch_id'] === (int) $branch['id'] ? 'selected' : '' ?>>
                                <?= e($branch['system_name'] . ' - ' . $branch['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($isManagerView): ?>
                    <div>
                        <label class="form-label">Nhân viên</label>
                        <select name="staff_id" class="form-select">
                            <option value="0">Tất cả nhân viên</option>
                            <?php foreach ($staffUsers as $staff): ?>
                                <option value="<?= e((string) $staff['id']) ?>" <?= (int) $filters['staff_id'] === (int) $staff['id'] ? 'selected' : '' ?>>
                                    <?= e($staff['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">Áp dụng bộ lọc</button>
                <a href="<?= e(url('/lock-requests')) ?>" class="btn btn-outline-secondary">Xóa lọc</a>
            </div>
        </form>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Tổng quan workflow lock</h3>
                <p class="panel-subtitle mb-0">Nhân viên gửi lock, phòng chuyển sang Đang giữ, sau đó quản lý hoặc giám đốc xét duyệt.</p>
            </div>
        </div>

        <div class="empty-state">
            <?php if ($isManagerView): ?>
                Bạn đang ở chế độ duyệt. Mọi yêu cầu `pending` có thể được duyệt hoặc từ chối ngay trong bảng bên dưới.
            <?php else: ?>
                Bạn đang ở chế độ nhân viên. Trang này hiển thị lịch sử yêu cầu lock của chính bạn.
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="panel-card mt-4">
    <div class="panel-header">
        <div>
            <h3>Danh sách yêu cầu lock</h3>
            <p class="panel-subtitle mb-0"><?= $isManagerView ? 'Quản lý và giám đốc có quyền xử lý yêu cầu đang chờ.' : 'Lịch sử yêu cầu lock của bạn.' ?></p>
        </div>
        <span class="badge text-bg-light"><?= e((string) count($requests)) ?> yêu cầu</span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Phòng</th>
                    <th>Địa chỉ phòng</th>
                    <th>Nhân viên</th>
                    <th>Trạng thái yêu cầu</th>
                    <th>Trạng thái phòng</th>
                    <th>Thời gian gửi</th>
                    <th>Ghi chú</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! $requests): ?>
                    <tr>
                        <td colspan="8">
                            <div class="empty-state my-3">Chưa có yêu cầu lock nào phù hợp với bộ lọc hiện tại.</div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($request['room_number']) ?></div>
                            <div class="text-muted small"><?= e(number_format((float) $request['price'], 0, ',', '.')) ?> đ</div>
                        </td>
                        <td>
                            <div><?= e($request['branch_address'] ?: '-') ?></div>
                            <div class="text-muted small"><?= e($request['branch_name'] . ' - ' . $request['district_name']) ?></div>
                        </td>
                        <td>
                            <div><?= e($request['requester_name']) ?></div>
                            <?php if ($request['approver_name']): ?>
                                <div class="text-muted small">Duyệt bởi: <?= e($request['approver_name']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><span class="status-pill status-<?= e($request['request_status']) ?>"><?= e($statusLabels[$request['request_status']] ?? $request['request_status']) ?></span></td>
                        <td><span class="status-pill status-<?= e($request['room_status']) ?>"><?= e($roomStatusLabels[$request['room_status']] ?? $request['room_status']) ?></span></td>
                        <td>
                            <div><?= e($request['requested_at']) ?></div>
                            <?php if ($request['decided_at']): ?>
                                <div class="text-muted small">Xử lý: <?= e($request['decided_at']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div><?= e($request['request_note'] ?: '-') ?></div>
                            <?php if ($request['decision_note']): ?>
                                <div class="text-muted small mt-1">QĐ: <?= e($request['decision_note']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if ($isManagerView && $request['request_status'] === 'pending'): ?>
                                <div class="d-grid gap-2 action-stack">
                                    <form method="POST" action="<?= e(url('/lock-requests/approve')) ?>" class="d-grid gap-2">
                                        <input type="hidden" name="id" value="<?= e((string) $request['id']) ?>">
                                        <textarea name="decision_note" class="form-control form-control-sm" rows="2" placeholder="Ghi chú duyệt (tùy chọn)"></textarea>
                                        <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
                                    </form>
                                    <form method="POST" action="<?= e(url('/lock-requests/reject')) ?>" class="d-grid gap-2">
                                        <input type="hidden" name="id" value="<?= e((string) $request['id']) ?>">
                                        <textarea name="decision_note" class="form-control form-control-sm" rows="2" placeholder="Lý do từ chối (tùy chọn)"></textarea>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Từ chối</button>
                                    </form>
                                </div>
                            <?php elseif ($isManagerView && $request['request_status'] === 'approved'): ?>
                                <form method="POST" action="<?= e(url('/lock-requests/undo')) ?>" onsubmit="return confirm('Bạn chắc chắn muốn hoàn tác lock này?');">
                                    <input type="hidden" name="id" value="<?= e((string) $request['id']) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-warning">Hoàn tác</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small">Không có thao tác</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
