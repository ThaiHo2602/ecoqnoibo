<?php

use App\Core\Auth;
use App\Core\Session;

$currentUser = Auth::user();
$successMessage = Session::getFlash('success');
$errorMessage = Session::getFlash('error');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> | <?= e(config('name')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(asset('assets/css/app.css')) ?>" rel="stylesheet">
</head>
<body class="app-body">
    <div class="app-shell">
        <aside class="sidebar">
            <div>
                <div class="brand-card">
                    <div class="brand-mark">E</div>
                    <div>
                        <div class="brand-name"><?= e(config('name')) ?></div>
                        <div class="brand-subtitle">Hệ thống quản lý trọ nội bộ</div>
                    </div>
                </div>

                <nav class="nav flex-column gap-2 mt-4">
                    <a class="nav-link sidebar-link <?= is_current_path('/') ? 'active' : '' ?>" href="<?= e(url('/')) ?>">Trang chủ</a>
                    <?php if ($currentUser && in_array($currentUser['role_name'], ['manager', 'director'], true)): ?>
                        <a class="nav-link sidebar-link <?= is_current_path('/dashboard') ? 'active' : '' ?>" href="<?= e(url('/dashboard')) ?>">Bảng điều khiển</a>
                    <?php endif; ?>
                    <?php if ($currentUser && in_array($currentUser['role_name'], ['manager', 'director'], true)): ?>
                        <a class="nav-link sidebar-link <?= is_current_path('/systems') ? 'active' : '' ?>" href="<?= e(url('/systems')) ?>">Hệ thống</a>
                        <a class="nav-link sidebar-link <?= is_current_path('/branches') ? 'active' : '' ?>" href="<?= e(url('/branches')) ?>">Chi nhánh</a>
                    <?php endif; ?>
                    <a class="nav-link sidebar-link <?= is_path_prefix('/rooms') ? 'active' : '' ?>" href="<?= e(url('/rooms')) ?>">Phòng</a>
                    <a class="nav-link sidebar-link <?= is_current_path('/lock-requests') ? 'active' : '' ?>" href="<?= e(url('/lock-requests')) ?>">
                        <?= $currentUser && in_array($currentUser['role_name'], ['manager', 'director'], true) ? 'Duyệt lock' : 'Yêu cầu lock' ?>
                    </a>
                    <?php if ($currentUser && in_array($currentUser['role_name'], ['manager', 'director'], true)): ?>
                        <a class="nav-link sidebar-link" href="javascript:void(0)">Báo cáo</a>
                    <?php endif; ?>
                    <?php if ($currentUser && $currentUser['role_name'] === 'director'): ?>
                        <a class="nav-link sidebar-link <?= is_current_path('/users') ? 'active' : '' ?>" href="<?= e(url('/users')) ?>">Người dùng</a>
                        <a class="nav-link sidebar-link" href="javascript:void(0)">Nhật ký</a>
                    <?php endif; ?>
                    <?php if ($currentUser): ?>
                        <a class="nav-link sidebar-link <?= is_current_path('/profile/password') ? 'active' : '' ?>" href="<?= e(url('/profile/password')) ?>">Đổi mật khẩu</a>
                    <?php endif; ?>
                </nav>
            </div>

            <div class="sidebar-footer">
                <div class="small text-white-50">Đăng nhập với</div>
                <div class="fw-semibold"><?= e($currentUser['full_name'] ?? '') ?></div>
                <div class="text-white-50 small"><?= e($currentUser['role_display_name'] ?? '') ?></div>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div>
                    <h1 class="page-title mb-1"><?= e($pageTitle ?? 'Trang chủ') ?></h1>
                    <div class="text-muted">Hệ thống đang được hoàn thiện thành nền tảng quản lý trọ nội bộ đầy đủ cho công ty môi giới.</div>
                </div>
                <form method="POST" action="<?= e(url('/logout')) ?>">
                    <button type="submit" class="btn btn-outline-danger">Đăng xuất</button>
                </form>
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
</body>
</html>
