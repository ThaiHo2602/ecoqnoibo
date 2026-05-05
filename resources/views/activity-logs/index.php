<section class="content-grid two-column-balanced">
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Bộ lọc nhật ký</h3>
                <p class="panel-subtitle mb-0">Lọc theo người thao tác, module, hành động hoặc từ khóa mô tả.</p>
            </div>
        </div>

        <form method="GET" action="<?= e(url('/activity-logs')) ?>" class="d-grid gap-3">
            <div class="grid-2">
                <div>
                    <label class="form-label">Người dùng</label>
                    <select name="user_id" class="form-select">
                        <option value="0">Tất cả</option>
                        <?php foreach ($users as $item): ?>
                            <option value="<?= e((string) $item['id']) ?>" <?= (int) $filters['user_id'] === (int) $item['id'] ? 'selected' : '' ?>>
                                <?= e($item['full_name'] . ' (' . $item['username'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Module</label>
                    <select name="module" class="form-select">
                        <option value="">Tất cả module</option>
                        <?php foreach ($modules as $item): ?>
                            <option value="<?= e($item['module']) ?>" <?= $filters['module'] === $item['module'] ? 'selected' : '' ?>>
                                <?= e($item['module']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div>
                    <label class="form-label">Hành động</label>
                    <select name="action" class="form-select">
                        <option value="">Tất cả hành động</option>
                        <?php foreach ($actions as $item): ?>
                            <option value="<?= e($item['action']) ?>" <?= $filters['action'] === $item['action'] ? 'selected' : '' ?>>
                                <?= e($item['action']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Từ khóa</label>
                    <input type="text" name="keyword" class="form-control" value="<?= e($filters['keyword']) ?>" placeholder="Mô tả, IP, tên người dùng...">
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">Lọc nhật ký</button>
                <a href="<?= e(url('/activity-logs')) ?>" class="btn btn-outline-secondary">Xóa lọc</a>
            </div>
        </form>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Ghi chú màn hình</h3>
                <p class="panel-subtitle mb-0">Màn này chỉ dành cho giám đốc, giúp theo dõi ai đã tạo, sửa, xóa, gửi lock, duyệt lock hoặc đăng nhập.</p>
            </div>
        </div>

        <div class="detail-list">
            <div><span>Tổng bản ghi đang hiển thị</span><strong><?= e((string) count($logs)) ?></strong></div>
            <div><span>Sắp xếp</span><strong>Mới nhất lên đầu</strong></div>
            <div><span>Giới hạn</span><strong>300 bản ghi gần nhất</strong></div>
        </div>
    </div>
</section>

<section class="panel-card mt-4">
    <div class="panel-header">
        <div>
            <h3>Danh sách nhật ký hoạt động</h3>
            <p class="panel-subtitle mb-0">Theo dõi đầy đủ thao tác CRUD, yêu cầu lock và hoạt động đăng nhập trong hệ thống.</p>
        </div>
        <span class="badge text-bg-light"><?= e((string) count($logs)) ?> bản ghi</span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Thời gian</th>
                    <th>Người thao tác</th>
                    <th>Vai trò</th>
                    <th>Module</th>
                    <th>Hành động</th>
                    <th>Mô tả</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! $logs): ?>
                    <tr>
                        <td colspan="7"><div class="empty-state my-3">Chưa có nhật ký nào phù hợp với bộ lọc hiện tại.</div></td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= e($log['created_at']) ?></td>
                        <td><?= e($log['full_name'] ?: 'Hệ thống') ?></td>
                        <td><?= e($log['role_display_name'] ?: '-') ?></td>
                        <td><span class="detail-tag"><?= e($log['module']) ?></span></td>
                        <td><span class="status-pill status-approved"><?= e($log['action']) ?></span></td>
                        <td><?= e($log['description']) ?></td>
                        <td><?= e($log['ip_address'] ?: '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
