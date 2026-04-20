<?php

use App\Core\Session;

$errorMessage = Session::getFlash('error');
$successMessage = Session::getFlash('success');
$logoUrl = asset('assets/images/logo.png');
?>
<div class="auth-wrapper">
    <div class="auth-panel">
        <div class="auth-hero">
            <div class="auth-brand-row">
                <img
                    src="<?= e($logoUrl) ?>"
                    alt="Eco-Q House"
                    class="auth-logo"
                    onerror="this.style.display='none';"
                >
                <div>
                    <div class="eyebrow">Eco-Q House nội bộ</div>
                    <h1>Hệ thống quản lý trọ dành cho đội ngũ môi giới chuyên nghiệp.</h1>
                </div>
            </div>

            <p>
                Theo dõi hệ thống, chi nhánh, phòng, duyệt lock, khách hàng và toàn bộ lịch trình làm việc
                trên một giao diện hiện đại, rõ ràng và dễ thao tác.
            </p>

            <div class="auth-points">
                <div class="auth-point">
                    <strong>Phân quyền rõ ràng</strong>
                    <span>Giám đốc, quản lý, nhân viên và cộng tác viên đều có luồng làm việc riêng.</span>
                </div>
                <div class="auth-point">
                    <strong>Tìm phòng thật nhanh</strong>
                    <span>Lọc mạnh theo quận, giá, nội thất, trạng thái và chi nhánh để tư vấn khách hiệu quả.</span>
                </div>
                <div class="auth-point">
                    <strong>Quy trình lock minh bạch</strong>
                    <span>Nhân viên gửi yêu cầu, quản lý duyệt nhanh, lịch sử thao tác được ghi nhận đầy đủ.</span>
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
