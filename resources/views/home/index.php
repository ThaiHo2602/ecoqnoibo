<?php
$statusLabels = [
    'chua_lock' => 'Chưa khóa',
    'dang_giu' => 'Đang giữ',
    'da_lock' => 'Đã khóa',
];
$typeLabels = [
    'duplex' => 'Duplex',
    'studio' => 'Studio',
    'one_bedroom' => 'Studio',
    'two_bedroom' => 'Studio',
    'kiot' => 'Kiot',
];
$furnitureLabels = [
    'co_noi_that' => 'Có nội thất',
    'khong_noi_that' => 'Không nội thất',
];
$branchCards = $branchCards ?? [];
$pagination = $pagination ?? [
    'current_page' => 1,
    'per_page' => 30,
    'total_items' => count($branchCards),
    'total_pages' => 1,
];
$currentPage = (int) $pagination['current_page'];
$totalPages = (int) $pagination['total_pages'];
$totalItems = (int) $pagination['total_items'];
$perPage = (int) $pagination['per_page'];
$fromItem = $totalItems > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
$toItem = $totalItems > 0 ? min($totalItems, $currentPage * $perPage) : 0;
$pageUrl = static function (int $page): string {
    $params = $_GET;
    $params['page'] = $page;

    return url('/app') . '?' . http_build_query($params);
};
$branchUrl = static function (int $branchId): string {
    $params = $_GET;
    unset($params['page']);
    $query = http_build_query($params);

    return url('/branches/' . $branchId . '/detail') . ($query !== '' ? '?' . $query : '');
};
$formatMillion = static function (mixed $value): string {
    $price = (float) $value;
    if ($price <= 0) {
        return 'Chưa cập nhật';
    }

    $million = $price / 1000000;
    $formatted = rtrim(rtrim(number_format($million, 1, '.', ''), '0'), '.');

    return $formatted . ' triệu';
};
$formatBranchPrice = static function (array $branch) use ($formatMillion): string {
    $min = (float) ($branch['min_price'] ?? 0);
    $max = (float) ($branch['max_price'] ?? 0);
    $totalRooms = (int) ($branch['total_rooms'] ?? 0);

    if ($totalRooms <= 0 || ($min <= 0 && $max <= 0)) {
        return 'Liên hệ';
    }

    if ($max <= 0 || $min === $max || $totalRooms === 1) {
        return 'Giá: ' . $formatMillion($min > 0 ? $min : $max);
    }

    return 'Giá từ: ' . $formatMillion($min) . ' đến ' . $formatMillion($max);
};
$visiblePages = array_unique(array_filter([
    1,
    2,
    $currentPage - 2,
    $currentPage - 1,
    $currentPage,
    $currentPage + 1,
    $currentPage + 2,
    $totalPages - 1,
    $totalPages,
], static fn (int $page): bool => $page >= 1 && $page <= $totalPages));
sort($visiblePages);
?>

<div class="mobile-branch-app">
<section class="listing-hero">
    <div class="mobile-listing-brand">
        <div class="mobile-brand-mark">
            <img src="<?= e(asset('assets/images/logo.png')) ?>" alt="Eco-Q House">
        </div>
        <div class="mobile-header-actions">
            <button type="button" class="mobile-header-icon" aria-label="Mở menu" data-sidebar-toggle>☰</button>
            <form method="POST" action="<?= e(url('/logout')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="mobile-header-icon is-danger" aria-label="Đăng xuất">↪</button>
            </form>
        </div>
    </div>
    <div>
        <div class="eyebrow">DANH SÁCH CHI NHÁNH</div>
        <h2 class="listing-title">ECO-Q HOUSE</h2>
        <p class="listing-subtitle">
            Mỗi thẻ đại diện cho một chi nhánh hoặc tòa nhà. Bấm vào chi nhánh để xem danh sách phòng bên trong và chọn đúng phòng cần tư vấn.
        </p>
    </div>

    <div class="listing-summary">
        <div><i>▥</i><span>Chi nhánh phù hợp</span><strong><?= e((string) $totalItems) ?></strong></div>
        <div><i>▱</i><span>Tổng phòng lọc được</span><strong><?= e((string) $roomStats['total']) ?></strong></div>
        <div><i>✓</i><span>Còn trống</span><strong><?= e((string) $roomStats['chua_lock']) ?></strong></div>
        <div><i>⌂</i><span>Đang giữ / đã khóa</span><strong><?= e((string) ((int) $roomStats['dang_giu'] + (int) $roomStats['da_lock'])) ?></strong></div>
    </div>
</section>

<section class="panel-card listing-filter-panel">
    <div class="panel-header">
        <div>
            <h3>Bộ lọc phòng</h3>
            <p class="panel-subtitle mb-0">Bộ lọc vẫn dựa trên phòng, kết quả được gom lại theo chi nhánh.</p>
        </div>
        <span class="mobile-filter-icon">⌯</span>
    </div>

    <form method="GET" action="<?= e(url('/app')) ?>" class="listing-filters">
        <select name="district_id" class="form-select">
            <option value="0">Chọn quận</option>
            <?php foreach ($districts as $district): ?>
                <option value="<?= e((string) $district['id']) ?>" <?= (int) $filters['district_id'] === (int) $district['id'] ? 'selected' : '' ?>><?= e($district['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <input type="number" name="price_min" class="form-control" placeholder="Giá từ" value="<?= e($filters['price_min']) ?>" min="0" step="1000">
        <input type="number" name="price_max" class="form-control" placeholder="Giá đến" value="<?= e($filters['price_max']) ?>" min="0" step="1000">

        <select name="status" class="form-select">
            <option value="">Trạng thái</option>
            <?php foreach ($statusLabels as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= $filters['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="room_type" class="form-select">
            <option value="">Loại phòng</option>
            <?php foreach ($typeLabels as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= $filters['room_type'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="furniture_status" class="form-select">
            <option value="">Nội thất</option>
            <?php foreach ($furnitureLabels as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= $filters['furniture_status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="system_id" class="form-select">
            <option value="0">Hệ thống</option>
            <?php foreach ($systems as $system): ?>
                <option value="<?= e((string) $system['id']) ?>" <?= (int) $filters['system_id'] === (int) $system['id'] ? 'selected' : '' ?>><?= e($system['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="ward_id" class="form-select">
            <option value="0">Phường</option>
            <?php foreach ($wards as $ward): ?>
                <option value="<?= e((string) $ward['id']) ?>" <?= (int) $filters['ward_id'] === (int) $ward['id'] ? 'selected' : '' ?>><?= e($ward['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="branch_id" class="form-select">
            <option value="0">Chi nhánh</option>
            <?php foreach ($branches as $branch): ?>
                <option value="<?= e((string) $branch['id']) ?>" <?= (int) $filters['branch_id'] === (int) $branch['id'] ? 'selected' : '' ?>><?= e($branch['system_name'] . ' - ' . $branch['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="keyword" class="form-control listing-search" placeholder="Tìm theo số phòng, tên chi nhánh hoặc ghi chú..." value="<?= e($filters['keyword']) ?>">

        <a href="<?= e(url('/app')) ?>" class="btn btn-outline-secondary">Xóa lọc</a>
        <button type="submit" class="btn btn-primary">Lọc phòng</button>
    </form>
</section>

<section class="mobile-branch-list-head">
    <h3>Danh sách chi nhánh</h3>
    <a href="<?= e(url('/app')) ?>">Xem tất cả →</a>
</section>

<section class="listing-grid branch-listing-grid">
    <?php if (! $branchCards): ?>
        <div class="panel-card empty-state-card">
            <h3 class="h5 mb-2">Không có chi nhánh phù hợp</h3>
            <p class="text-muted mb-0">Hãy thử điều chỉnh bộ lọc để xem thêm các tòa nhà khác.</p>
        </div>
    <?php endif; ?>

    <?php foreach ($branchCards as $branch): ?>
        <?php
        $availableRooms = (int) ($branch['available_rooms'] ?? 0);
        $totalRooms = (int) ($branch['total_rooms'] ?? 0);
        $heldRooms = (int) ($branch['held_rooms'] ?? 0) + (int) ($branch['locked_rooms'] ?? 0);
        $badges = [];
        if ($availableRooms > 0) {
            $badges[] = 'Hot';
        }
        if ($availableRooms > 0 && $availableRooms <= 2) {
            $badges[] = 'Sắp hết';
        }
        if ((int) ($branch['verified_badge'] ?? 0) === 1) {
            $badges[] = 'Đã xác thực';
        }
        ?>
        <a href="<?= e($branchUrl((int) $branch['branch_id'])) ?>" class="listing-card branch-card">
            <div class="listing-card-media">
                <?php if (! empty($branch['cover_image_path'])): ?>
                    <img src="<?= e(media_url($branch['cover_image_path'])) ?>" alt="<?= e($branch['branch_name']) ?>">
                <?php else: ?>
                    <div class="listing-card-placeholder">Chưa có ảnh</div>
                <?php endif; ?>

                <?php if ($badges): ?>
                    <div class="branch-card-badges">
                        <?php foreach ($badges as $badge): ?>
                            <span><?= e($badge) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="branch-available-bar">Còn <?= e((string) $availableRooms) ?> phòng trống</div>
            </div>

            <div class="listing-card-body branch-card-body">
                <h3><?= e($branch['branch_name']) ?></h3>
                <div class="listing-address"><?= e($branch['branch_name']) ?></div>
                <div class="listing-meta">
                    <?= e(trim(($branch['ward_name'] ?: 'Chưa có phường') . ' - ' . $branch['district_name'], ' -')) ?>
                </div>
                <div class="listing-price"><?= e($formatBranchPrice($branch)) ?></div>
                <div class="listing-tags">
                    <span><?= e((string) $totalRooms) ?> phòng</span>
                    <span><?= e($branch['system_name']) ?></span>
                </div>
                <div class="mobile-branch-stats">
                    <span><b>Phòng</b><strong><?= e((string) $totalRooms) ?></strong></span>
                    <span><b>Trống</b><strong><?= e((string) $availableRooms) ?></strong></span>
                    <span><b>Đang giữ</b><strong><?= e((string) $heldRooms) ?></strong></span>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</section>

<?php if ($totalPages > 1): ?>
    <nav class="listing-pagination" aria-label="Phân trang danh sách chi nhánh">
        <div class="listing-pagination-summary">
            Hiển thị <?= e((string) $fromItem) ?>-<?= e((string) $toItem) ?> trong <?= e((string) $totalItems) ?> chi nhánh
        </div>
        <div class="listing-pagination-pages">
            <a class="pagination-button <?= $currentPage <= 1 ? 'is-disabled' : '' ?>" href="<?= e($currentPage > 1 ? $pageUrl($currentPage - 1) : '#') ?>" aria-disabled="<?= $currentPage <= 1 ? 'true' : 'false' ?>">Trước</a>

            <?php $previousPage = 0; ?>
            <?php foreach ($visiblePages as $page): ?>
                <?php if ($previousPage > 0 && $page > $previousPage + 1): ?>
                    <span class="pagination-ellipsis">...</span>
                <?php endif; ?>

                <a class="pagination-button <?= $page === $currentPage ? 'is-active' : '' ?>" href="<?= e($pageUrl($page)) ?>" aria-current="<?= $page === $currentPage ? 'page' : 'false' ?>">
                    <?= e((string) $page) ?>
                </a>
                <?php $previousPage = $page; ?>
            <?php endforeach; ?>

            <a class="pagination-button <?= $currentPage >= $totalPages ? 'is-disabled' : '' ?>" href="<?= e($currentPage < $totalPages ? $pageUrl($currentPage + 1) : '#') ?>" aria-disabled="<?= $currentPage >= $totalPages ? 'true' : 'false' ?>">Sau</a>
        </div>
    </nav>
<?php endif; ?>

</div>
