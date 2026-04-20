<?php

use App\Core\Auth;
use App\Core\Session;

$currentUser = Auth::user();
$successMessage = Session::getFlash('success');
$errorMessage = Session::getFlash('error');
$pageTitle = $pageTitle ?? 'Trang chủ';
$pageDescription = $pageDescription ?? 'Nền tảng nội bộ giúp Eco-Q House quản lý hệ thống, chi nhánh, phòng, khách hàng và toàn bộ quy trình lock tập trung.';
$logoUrl = asset('assets/images/logo.png');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(config('name')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(asset('assets/css/app.css')) ?>" rel="stylesheet">
</head>
<body class="app-body">
    <div class="app-shell">
        <div class="app-overlay" data-sidebar-overlay></div>

        <aside class="sidebar" data-app-sidebar>
            <div>
                <a href="<?= e(url('/')) ?>" class="brand-card brand-link">
                    <div class="brand-logo-wrap">
                        <img
                            src="<?= e($logoUrl) ?>"
                            alt="Eco-Q House"
                            class="brand-logo"
                            onerror="this.style.display='none'; this.parentNode.classList.add('is-fallback'); this.parentNode.innerHTML='<span class=&quot;brand-logo-fallback&quot;>EQ</span>';"
                        >
                    </div>
                    <div>
                        <div class="brand-name"><?= e(config('name')) ?></div>
                        <div class="brand-subtitle">Quản lý trọ nội bộ thông minh</div>
                    </div>
                </a>

                <nav class="nav flex-column gap-2 mt-4">
                    <a class="nav-link sidebar-link <?= is_current_path('/') ? 'active' : '' ?>" href="<?= e(url('/')) ?>">Trang chủ</a>
                    <?php if ($currentUser && in_array($currentUser['role_name'], ['manager', 'director'], true)): ?>
                        <a class="nav-link sidebar-link <?= is_current_path('/dashboard') ? 'active' : '' ?>" href="<?= e(url('/dashboard')) ?>">Bảng điều khiển</a>
                    <?php endif; ?>
                    <?php if ($currentUser && in_array($currentUser['role_name'], ['manager', 'director'], true)): ?>
                        <a class="nav-link sidebar-link <?= is_current_path('/systems') ? 'active' : '' ?>" href="<?= e(url('/systems')) ?>">Hệ thống</a>
                        <a class="nav-link sidebar-link <?= is_current_path('/branches') ? 'active' : '' ?>" href="<?= e(url('/branches')) ?>">Chi nhánh</a>
                    <?php endif; ?>
                    <a class="nav-link sidebar-link <?= is_current_path('/customers') ? 'active' : '' ?>" href="<?= e(url('/customers')) ?>">Khách hàng</a>
                    <a class="nav-link sidebar-link <?= is_path_prefix('/rooms') ? 'active' : '' ?>" href="<?= e(url('/rooms')) ?>">Phòng</a>
                    <a class="nav-link sidebar-link <?= is_current_path('/lock-requests') ? 'active' : '' ?>" href="<?= e(url('/lock-requests')) ?>">
                        <?= $currentUser && in_array($currentUser['role_name'], ['manager', 'director'], true) ? 'Duyệt lock' : 'Yêu cầu lock' ?>
                    </a>
                    <?php if ($currentUser && in_array($currentUser['role_name'], ['manager', 'director'], true)): ?>
                        <a class="nav-link sidebar-link <?= is_current_path('/reports') ? 'active' : '' ?>" href="<?= e(url('/reports')) ?>">Báo cáo</a>
                    <?php endif; ?>
                    <?php if ($currentUser && $currentUser['role_name'] === 'director'): ?>
                        <a class="nav-link sidebar-link <?= is_current_path('/users') ? 'active' : '' ?>" href="<?= e(url('/users')) ?>">Người dùng</a>
                        <a class="nav-link sidebar-link <?= is_current_path('/activity-logs') ? 'active' : '' ?>" href="<?= e(url('/activity-logs')) ?>">Nhật ký hoạt động</a>
                    <?php endif; ?>
                    <?php if ($currentUser): ?>
                        <a class="nav-link sidebar-link <?= is_current_path('/profile/password') ? 'active' : '' ?>" href="<?= e(url('/profile/password')) ?>">Đổi mật khẩu</a>
                    <?php endif; ?>
                </nav>
            </div>

            <div class="sidebar-footer">
                <div class="small text-white-50">Đang đăng nhập với</div>
                <div class="fw-semibold"><?= e($currentUser['full_name'] ?? '') ?></div>
                <div class="text-white-50 small"><?= e($currentUser['role_display_name'] ?? '') ?></div>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="topbar-heading">
                    <button
                        type="button"
                        class="sidebar-toggle"
                        aria-label="Mở menu"
                        data-sidebar-toggle
                    >
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>

                    <div>
                        <div class="topbar-badge">Eco-Q House</div>
                        <h1 class="page-title mb-1"><?= e($pageTitle) ?></h1>
                        <div class="page-subtitle"><?= e($pageDescription) ?></div>
                    </div>
                </div>

                <div class="topbar-actions">
                    <div class="topbar-user-card">
                        <img
                            src="<?= e($logoUrl) ?>"
                            alt="Logo Eco-Q House"
                            class="topbar-user-logo"
                            onerror="this.style.display='none';"
                        >
                        <div>
                            <div class="fw-semibold"><?= e($currentUser['full_name'] ?? '') ?></div>
                            <div class="text-muted small"><?= e($currentUser['role_display_name'] ?? '') ?></div>
                        </div>
                    </div>

                    <form method="POST" action="<?= e(url('/logout')) ?>">
                        <button type="submit" class="btn btn-outline-danger">Đăng xuất</button>
                    </form>
                </div>
            </header>

            <?php if ($successMessage): ?>
                <div class="alert alert-success border-0 shadow-sm"><?= e($successMessage) ?></div>
            <?php endif; ?>

            <?php if ($errorMessage): ?>
                <div class="alert alert-danger border-0 shadow-sm"><?= e($errorMessage) ?></div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const body = document.body;
            const sidebar = document.querySelector('[data-app-sidebar]');
            const overlay = document.querySelector('[data-sidebar-overlay]');
            const toggle = document.querySelector('[data-sidebar-toggle]');

            if (!sidebar || !overlay || !toggle) {
                return;
            }

            const closeSidebar = function () {
                body.classList.remove('sidebar-open');
            };

            toggle.addEventListener('click', function () {
                body.classList.toggle('sidebar-open');
            });

            overlay.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeSidebar();
                }
            });
        });
    </script>
</body>
</html>
