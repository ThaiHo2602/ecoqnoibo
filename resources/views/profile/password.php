<section class="content-grid two-column-balanced">
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Thong tin tai khoan</h3>
                <p class="panel-subtitle mb-0">Trang nay cho phep ban tu doi mat khau ma khong can vao phpMyAdmin.</p>
            </div>
        </div>

        <div class="detail-list">
            <div><span>Ho ten</span><strong><?= e($user['full_name']) ?></strong></div>
            <div><span>Tai khoan</span><strong><?= e($user['username']) ?></strong></div>
            <div><span>Vai tro</span><strong><?= e($user['role_display_name']) ?></strong></div>
            <div><span>Dang nhap cuoi</span><strong><?= e($user['last_login_at'] ?: 'Chua xac dinh') ?></strong></div>
        </div>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Doi mat khau</h3>
                <p class="panel-subtitle mb-0">Mat khau moi nen tu 6 ky tu tro len de he thong xu ly on dinh.</p>
            </div>
        </div>

        <form method="POST" action="<?= e(url('/profile/password')) ?>" class="d-grid gap-3">
            <div>
                <label class="form-label">Mat khau hien tai</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div>
                <label class="form-label">Mat khau moi</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div>
                <label class="form-label">Xac nhan mat khau moi</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Cap nhat mat khau</button>
        </form>
    </div>
</section>
