<section class="content-grid two-column-balanced">
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Thông tin tài khoản</h3>
                <p class="panel-subtitle mb-0">Trang này cho phép bạn tự đổi mật khẩu mà không cần vào phpMyAdmin.</p>
            </div>
        </div>

        <div class="detail-list">
            <div><span>Họ tên</span><strong><?= e($user['full_name']) ?></strong></div>
            <div><span>Tài khoản</span><strong><?= e($user['username']) ?></strong></div>
            <div><span>Vai trò</span><strong><?= e($user['role_display_name']) ?></strong></div>
            <div><span>Đăng nhập cuối</span><strong><?= e($user['last_login_at'] ?: 'Chưa xác định') ?></strong></div>
        </div>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Đổi mật khẩu</h3>
                <p class="panel-subtitle mb-0">Mật khẩu mới nên từ 6 ký tự trở lên để hệ thống xử lý ổn định.</p>
            </div>
        </div>

        <form method="POST" action="<?= e(url('/profile/password')) ?>" class="d-grid gap-3">
            <div>
                <label class="form-label">Mật khẩu hiện tại</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div>
                <label class="form-label">Mật khẩu mới</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div>
                <label class="form-label">Xác nhận mật khẩu mới</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
        </form>
    </div>
</section>
