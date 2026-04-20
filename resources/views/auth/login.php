<?php

use App\Core\Session;

$errorMessage = Session::getFlash('error');
$successMessage = Session::getFlash('success');
?>
<div class="auth-wrapper">
    <div class="auth-panel">
        <div class="auth-hero">
            <div class="eyebrow">EcoQ Noi Bo</div>
            <h1>Hệ thống quản lý trọ cho công ty môi giới</h1>
            <p>Đăng nhập để quản lý hệ thống, chi nhánh, phòng, duyệt lock và theo dõi hoạt động nội bộ.</p>

            <div class="auth-points">
                <div class="auth-point">
                    <strong>Phân quyền rõ ràng</strong>
                    <span>Giám đốc, quản lý, nhân viên với luồng công việc tách biệt.</span>
                </div>
                <div class="auth-point">
                    <strong>Bộ lọc mạnh</strong>
                    <span>Sẵn sàng cho tìm kiếm theo quận, giá, trạng thái, nội thất.</span>
                </div>
                <div class="auth-point">
                    <strong>Quy trình lock phòng</strong>
                    <span>Nhân viên gửi lock, quản lý xét duyệt minh bạch.</span>
                </div>
            </div>
        </div>

        <div class="auth-card">
            <div class="mb-4">
                <div class="eyebrow">Đăng nhập</div>
                <h2>Chào mừng quay lại</h2>
                <p class="text-muted mb-0">Tài khoản mẫu: `director`, `manager`, `staff` - mật khẩu chung: `123456`</p>
            </div>

            <?php if ($errorMessage): ?>
                <div class="alert alert-danger"><?= e($errorMessage) ?></div>
            <?php endif; ?>

            <?php if ($successMessage): ?>
                <div class="alert alert-success"><?= e($successMessage) ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= e(url('/login')) ?>" class="d-grid gap-3">
                <div>
                    <label for="username" class="form-label">Tài khoản</label>
                    <input
                        id="username"
                        type="text"
                        name="username"
                        class="form-control form-control-lg"
                        value="<?= e(old('username')) ?>"
                        placeholder="Nhập tài khoản"
                        required
                    >
                </div>

                <div>
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-control form-control-lg"
                        placeholder="Nhập mật khẩu"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary btn-lg">Đăng nhập vào hệ thống</button>
            </form>
        </div>
    </div>
</div>
