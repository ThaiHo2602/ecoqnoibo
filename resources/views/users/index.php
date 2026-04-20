<section class="content-grid two-column-balanced">
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Bo loc nguoi dung</h3>
                <p class="panel-subtitle mb-0">Loc theo vai tro, trang thai va tu khoa.</p>
            </div>
        </div>

        <form method="GET" action="<?= e(url('/users')) ?>" class="d-grid gap-3">
            <div>
                <label class="form-label">Vai tro</label>
                <select name="role" class="form-select">
                    <option value="">Tat ca vai tro</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= e($role['name']) ?>" <?= $filters['role'] === $role['name'] ? 'selected' : '' ?>><?= e($role['display_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label">Trang thai</label>
                <select name="status" class="form-select">
                    <option value="">Tat ca trang thai</option>
                    <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Hoat dong</option>
                    <option value="locked" <?= $filters['status'] === 'locked' ? 'selected' : '' ?>>Bi khoa</option>
                </select>
            </div>

            <div>
                <label class="form-label">Tu khoa</label>
                <input type="text" name="keyword" class="form-control" value="<?= e($filters['keyword']) ?>" placeholder="Ho ten, username, sdt, email">
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">Ap dung bo loc</button>
                <a href="<?= e(url('/users')) ?>" class="btn btn-outline-secondary">Xoa loc</a>
            </div>
        </form>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3><?= $editUser ? 'Cap nhat nguoi dung' : 'Them nguoi dung moi' ?></h3>
                <p class="panel-subtitle mb-0">Chi giam doc moi co quyen quan ly nhan su.</p>
            </div>
        </div>

        <form method="POST" action="<?= e(url($editUser ? '/users/update' : '/users/store')) ?>" class="d-grid gap-3">
            <?php if ($editUser): ?>
                <input type="hidden" name="id" value="<?= e((string) $editUser['id']) ?>">
            <?php endif; ?>

            <div class="grid-2">
                <div>
                    <label class="form-label">Ho ten</label>
                    <input type="text" name="full_name" class="form-control" value="<?= e($editUser['full_name'] ?? '') ?>" required>
                </div>
                <div>
                    <label class="form-label">Chuc vu</label>
                    <input type="text" name="job_title" class="form-control" value="<?= e($editUser['job_title'] ?? '') ?>" required>
                </div>
            </div>

            <div class="grid-2">
                <div>
                    <label class="form-label">Vai tro</label>
                    <select name="role_id" class="form-select" required>
                        <option value="">Chon vai tro</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= e((string) $role['id']) ?>" <?= (int) ($editUser['role_id'] ?? 0) === (int) $role['id'] ? 'selected' : '' ?>><?= e($role['display_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Trang thai tai khoan</label>
                    <select name="account_status" class="form-select">
                        <option value="active" <?= ($editUser['account_status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Hoat dong</option>
                        <option value="locked" <?= ($editUser['account_status'] ?? '') === 'locked' ? 'selected' : '' ?>>Bi khoa</option>
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div>
                    <label class="form-label">So dien thoai</label>
                    <input type="text" name="phone" class="form-control" value="<?= e($editUser['phone'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= e($editUser['email'] ?? '') ?>">
                </div>
            </div>

            <div class="grid-2">
                <div>
                    <label class="form-label">Tai khoan</label>
                    <input type="text" name="username" class="form-control" value="<?= e($editUser['username'] ?? '') ?>" required>
                </div>
                <div>
                    <label class="form-label"><?= $editUser ? 'Mat khau moi (neu doi)' : 'Mat khau' ?></label>
                    <input type="password" name="password" class="form-control" <?= $editUser ? '' : 'required' ?>>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary"><?= $editUser ? 'Luu cap nhat' : 'Them nguoi dung' ?></button>
                <?php if ($editUser): ?>
                    <a href="<?= e(url('/users')) ?>" class="btn btn-outline-secondary">Bo chinh sua</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>

<section class="panel-card mt-4">
    <div class="panel-header">
        <div>
            <h3>Danh sach nguoi dung</h3>
            <p class="panel-subtitle mb-0">Danh sach nhan su noi bo voi vai tro, trang thai va thong tin dang nhap.</p>
        </div>
        <span class="badge text-bg-light"><?= e((string) count($users)) ?> nguoi dung</span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Ho ten</th>
                    <th>Vai tro</th>
                    <th>Tai khoan</th>
                    <th>Thong tin lien he</th>
                    <th>Dang nhap cuoi</th>
                    <th>Trang thai</th>
                    <th class="text-end">Thao tac</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! $users): ?>
                    <tr>
                        <td colspan="7"><div class="empty-state my-3">Chua co nguoi dung nao phu hop voi bo loc hien tai.</div></td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($user['full_name']) ?></div>
                            <div class="text-muted small"><?= e($user['job_title']) ?></div>
                        </td>
                        <td><?= e($user['role_display_name']) ?></td>
                        <td><?= e($user['username']) ?></td>
                        <td>
                            <div><?= e($user['phone'] ?: '-') ?></div>
                            <div class="text-muted small"><?= e($user['email'] ?: '-') ?></div>
                        </td>
                        <td><?= e($user['last_login_at'] ?: 'Chua dang nhap') ?></td>
                        <td><span class="status-pill <?= $user['account_status'] === 'active' ? 'status-approved' : 'status-rejected' ?>"><?= e($user['account_status'] === 'active' ? 'Hoat dong' : 'Bi khoa') ?></span></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                <a href="<?= e(url('/users?edit=' . $user['id'])) ?>" class="btn btn-sm btn-outline-primary">Sua</a>
                                <form method="POST" action="<?= e(url('/users/delete')) ?>" onsubmit="return confirm('Ban chac chan muon xoa nguoi dung nay?');">
                                    <input type="hidden" name="id" value="<?= e((string) $user['id']) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Xoa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
