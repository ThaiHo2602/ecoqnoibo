<?php
$statusLabels = [
    'pending' => 'Cho duyet',
    'approved' => 'Da duyet',
    'rejected' => 'Tu choi',
];
$roomStatusLabels = [
    'chua_lock' => 'Chua lock',
    'dang_giu' => 'Dang giu',
    'da_lock' => 'Da lock',
];
?>

<section class="content-grid two-column-balanced">
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Bo loc yeu cau lock</h3>
                <p class="panel-subtitle mb-0">Loc theo trang thai, nhan vien, he thong va chi nhanh.</p>
            </div>
        </div>

        <form method="GET" action="<?= e(url('/lock-requests')) ?>" class="d-grid gap-3">
            <div class="grid-2">
                <div>
                    <label class="form-label">Trang thai</label>
                    <select name="status" class="form-select">
                        <option value="">Tat ca</option>
                        <?php foreach ($statusLabels as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= $filters['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">He thong</label>
                    <select name="system_id" class="form-select">
                        <option value="0">Tat ca he thong</option>
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
                    <label class="form-label">Chi nhanh</label>
                    <select name="branch_id" class="form-select">
                        <option value="0">Tat ca chi nhanh</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= e((string) $branch['id']) ?>" <?= (int) $filters['branch_id'] === (int) $branch['id'] ? 'selected' : '' ?>>
                                <?= e($branch['system_name'] . ' - ' . $branch['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($isManagerView): ?>
                    <div>
                        <label class="form-label">Nhan vien</label>
                        <select name="staff_id" class="form-select">
                            <option value="0">Tat ca nhan vien</option>
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
                <button type="submit" class="btn btn-primary">Ap dung bo loc</button>
                <a href="<?= e(url('/lock-requests')) ?>" class="btn btn-outline-secondary">Xoa loc</a>
            </div>
        </form>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Tong quan workflow lock</h3>
                <p class="panel-subtitle mb-0">Nhan vien gui lock, phong chuyen dang giu, sau do quan ly hoac giam doc xet duyet.</p>
            </div>
        </div>

        <div class="empty-state">
            <?php if ($isManagerView): ?>
                Ban dang o che do duyet. Moi yeu cau `pending` co the duoc duyet hoac tu choi ngay trong bang ben duoi.
            <?php else: ?>
                Ban dang o che do nhan vien. Trang nay hien lich su yeu cau lock cua chinh ban.
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="panel-card mt-4">
    <div class="panel-header">
        <div>
            <h3>Danh sach yeu cau lock</h3>
            <p class="panel-subtitle mb-0"><?= $isManagerView ? 'Quan ly va giam doc co quyen xu ly yeu cau pending.' : 'Lich su yeu cau lock cua ban.' ?></p>
        </div>
        <span class="badge text-bg-light"><?= e((string) count($requests)) ?> yeu cau</span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Phong</th>
                    <th>Chi nhanh</th>
                    <th>Nhan vien</th>
                    <th>Trang thai yeu cau</th>
                    <th>Trang thai phong</th>
                    <th>Thoi gian gui</th>
                    <th>Ghi chu</th>
                    <th class="text-end">Thao tac</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! $requests): ?>
                    <tr>
                        <td colspan="8">
                            <div class="empty-state my-3">Chua co yeu cau lock nao phu hop voi bo loc hien tai.</div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($request['room_number']) ?></div>
                            <div class="text-muted small"><?= e(number_format((float) $request['price'], 0, ',', '.')) ?> VND</div>
                        </td>
                        <td>
                            <div><?= e($request['branch_name']) ?></div>
                            <div class="text-muted small"><?= e($request['system_name'] . ' - ' . $request['district_name']) ?></div>
                        </td>
                        <td>
                            <div><?= e($request['requester_name']) ?></div>
                            <?php if ($request['approver_name']): ?>
                                <div class="text-muted small">Duyet boi: <?= e($request['approver_name']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><span class="status-pill status-<?= e($request['request_status']) ?>"><?= e($statusLabels[$request['request_status']] ?? $request['request_status']) ?></span></td>
                        <td><span class="status-pill status-<?= e($request['room_status']) ?>"><?= e($roomStatusLabels[$request['room_status']] ?? $request['room_status']) ?></span></td>
                        <td>
                            <div><?= e($request['requested_at']) ?></div>
                            <?php if ($request['decided_at']): ?>
                                <div class="text-muted small">Xu ly: <?= e($request['decided_at']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div><?= e($request['request_note'] ?: '-') ?></div>
                            <?php if ($request['decision_note']): ?>
                                <div class="text-muted small mt-1">QD: <?= e($request['decision_note']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if ($isManagerView && $request['request_status'] === 'pending'): ?>
                                <div class="d-grid gap-2 action-stack">
                                    <form method="POST" action="<?= e(url('/lock-requests/approve')) ?>" class="d-grid gap-2">
                                        <input type="hidden" name="id" value="<?= e((string) $request['id']) ?>">
                                        <textarea name="decision_note" class="form-control form-control-sm" rows="2" placeholder="Ghi chu duyet (tuy chon)"></textarea>
                                        <button type="submit" class="btn btn-sm btn-success">Duyet</button>
                                    </form>
                                    <form method="POST" action="<?= e(url('/lock-requests/reject')) ?>" class="d-grid gap-2">
                                        <input type="hidden" name="id" value="<?= e((string) $request['id']) ?>">
                                        <textarea name="decision_note" class="form-control form-control-sm" rows="2" placeholder="Ly do tu choi (tuy chon)"></textarea>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Tu choi</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">Khong co thao tac</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
