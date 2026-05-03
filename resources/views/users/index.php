<section class="content-grid two-column-balanced">
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Bộ lọc người dùng</h3>
                <p class="panel-subtitle mb-0">Lọc theo vai trò, trạng thái và từ khóa.</p>
            </div>
        </div>

        <form method="GET" action="<?= e(url('/users')) ?>" class="d-grid gap-3">
            <div>
                <label class="form-label">Vai trò</label>
                <select name="role" class="form-select">
                    <option value="">Tất cả vai trò</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= e($role['name']) ?>" <?= $filters['role'] === $role['name'] ? 'selected' : '' ?>><?= e($role['display_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="locked" <?= $filters['status'] === 'locked' ? 'selected' : '' ?>>Bị khóa</option>
                </select>
            </div>

            <div>
                <label class="form-label">Từ khóa</label>
                <input type="text" name="keyword" class="form-control" value="<?= e($filters['keyword']) ?>" placeholder="Họ tên, tài khoản, số điện thoại, email">
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">Áp dụng bộ lọc</button>
                <a href="<?= e(url('/users')) ?>" class="btn btn-outline-secondary">Xóa lọc</a>
            </div>
        </form>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3><?= $editUser ? 'Cập nhật người dùng' : 'Thêm người dùng mới' ?></h3>
                <p class="panel-subtitle mb-0">Giám đốc quản lý tài khoản nội bộ theo vai trò, không cần nhập thêm chức vụ riêng.</p>
            </div>
        </div>

        <form method="POST" action="<?= e(url($editUser ? '/users/update' : '/users/store')) ?>" class="d-grid gap-3">
            <?php if ($editUser): ?>
                <input type="hidden" name="id" value="<?= e((string) $editUser['id']) ?>">
            <?php endif; ?>

            <div>
                <label class="form-label">Họ tên</label>
                <input type="text" name="full_name" class="form-control" value="<?= e($editUser['full_name'] ?? '') ?>" required>
            </div>

            <div class="grid-2">
                <div>
                    <label class="form-label">Vai trò</label>
                    <select name="role_id" class="form-select" required>
                        <option value="">Chọn vai trò</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= e((string) $role['id']) ?>" <?= (int) ($editUser['role_id'] ?? 0) === (int) $role['id'] ? 'selected' : '' ?>><?= e($role['display_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Trạng thái tài khoản</label>
                    <select name="account_status" class="form-select">
                        <option value="active" <?= ($editUser['account_status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="locked" <?= ($editUser['account_status'] ?? '') === 'locked' ? 'selected' : '' ?>>Bị khóa</option>
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div>
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" value="<?= e($editUser['phone'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= e($editUser['email'] ?? '') ?>">
                </div>
            </div>

            <div class="grid-2">
                <div>
                    <label class="form-label">Tài khoản</label>
                    <input type="text" name="username" class="form-control" value="<?= e($editUser['username'] ?? '') ?>" required>
                </div>
                <div>
                    <label class="form-label"><?= $editUser ? 'Mật khẩu mới (nếu đổi)' : 'Mật khẩu' ?></label>
                    <input type="password" name="password" class="form-control" <?= $editUser ? '' : 'required' ?>>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary"><?= $editUser ? 'Lưu cập nhật' : 'Thêm người dùng' ?></button>
                <?php if ($editUser): ?>
                    <a href="<?= e(url('/users')) ?>" class="btn btn-outline-secondary">Bỏ chỉnh sửa</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>

<section class="panel-card mt-4">
    <div class="panel-header">
        <div>
            <h3>Danh sách người dùng</h3>
            <p class="panel-subtitle mb-0">Danh sách nhân sự nội bộ với vai trò, trạng thái và thông tin đăng nhập.</p>
        </div>
        <span class="badge text-bg-light"><?= e((string) count($users)) ?> người dùng</span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Họ tên</th>
                    <th>Vai trò</th>
                    <th>Tài khoản</th>
                    <th>Thông tin liên hệ</th>
                    <th>Đăng nhập cuối</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! $users): ?>
                    <tr>
                        <td colspan="7"><div class="empty-state my-3">Chưa có người dùng nào phù hợp với bộ lọc hiện tại.</div></td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><div class="fw-semibold"><?= e($user['full_name']) ?></div></td>
                        <td><?= e($user['role_display_name']) ?></td>
                        <td><?= e($user['username']) ?></td>
                        <td>
                            <div><?= e($user['phone'] ?: '-') ?></div>
                            <div class="text-muted small"><?= e($user['email'] ?: '-') ?></div>
                        </td>
                        <td><?= e($user['last_login_at'] ?: 'Chưa đăng nhập') ?></td>
                        <td><span class="status-pill <?= $user['account_status'] === 'active' ? 'status-approved' : 'status-rejected' ?>"><?= e($user['account_status'] === 'active' ? 'Hoạt động' : 'Bị khóa') ?></span></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                <a href="<?= e(url('/users?edit=' . $user['id'])) ?>" class="btn btn-sm btn-outline-primary">Sửa</a>
                                <?php if ($user['account_status'] === 'locked'): ?>
                                    <form method="POST" action="<?= e(url('/users/unlock')) ?>">
                                        <input type="hidden" name="id" value="<?= e((string) $user['id']) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-success">Mở khóa</button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" action="<?= e(url('/users/delete')) ?>" onsubmit="return confirm('Bạn chắc chắn muốn xóa người dùng này?');">
                                    <input type="hidden" name="id" value="<?= e((string) $user['id']) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
