<?php $userRole = $user['role_display_name'] ?? 'Người dùng'; ?>

<section class="hero-panel">
    <div>
        <div class="eyebrow">Tổng quan nội bộ</div>
        <h2>Chào <?= e($user['full_name'] ?? '') ?>, đây là bảng điều khiển để theo dõi toàn bộ hoạt động quản trị.</h2>
        <p class="mb-0 text-muted">
            Trang chủ sau đăng nhập đã được chuyển sang listing phòng kiểu card. Màn này giữ vai trò bảng điều khiển riêng cho quản lý và giám đốc.
        </p>
    </div>
    <div class="hero-chip">
        <span class="d-block small text-uppercase text-muted">Vai trò hiện tại</span>
        <strong><?= e($userRole) ?></strong>
    </div>
</section>

<section class="stats-grid">
    <article class="stat-card">
        <span class="stat-label">Hệ thống</span>
        <strong><?= e((string) $stats['systems']) ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Chi nhánh</span>
        <strong><?= e((string) $stats['branches']) ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Phòng</span>
        <strong><?= e((string) $stats['rooms']) ?></strong>
    </article>
    <article class="stat-card highlight">
        <span class="stat-label">Yêu cầu lock chờ duyệt</span>
        <strong><?= e((string) $stats['pendingLocks']) ?></strong>
    </article>
</section>

<section class="content-grid">
    <div class="panel-card">
        <div class="panel-header">
            <h3>Phòng cập nhật gần đây</h3>
            <span class="badge text-bg-light">Dữ liệu mới</span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Phong</th>
                        <th>Chi nhánh</th>
                        <th>Quận</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentRooms as $room): ?>
                        <tr>
                            <td><?= e($room['room_number']) ?></td>
                            <td><?= e($room['branch_name']) ?></td>
                            <td><?= e($room['district_name']) ?></td>
                            <td><?= e(number_format((float) $room['price'], 0, ',', '.')) ?> VND</td>
                            <td><span class="status-pill status-<?= e($room['status']) ?>"><?= e($room['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <h3>Yêu cầu lock gần đây</h3>
            <span class="badge text-bg-light">Workflow</span>
        </div>
        <div class="list-group list-group-flush">
            <?php foreach ($recentLockRequests as $request): ?>
                <div class="list-group-item px-0">
                    <div class="d-flex justify-content-between gap-3">
                        <div>
                            <div class="fw-semibold"><?= e($request['full_name']) ?> gửi lock phòng <?= e($request['room_number']) ?></div>
                            <div class="text-muted small"><?= e($request['requested_at']) ?></div>
                        </div>
                        <span class="status-pill status-<?= e($request['request_status']) ?>"><?= e($request['request_status']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
