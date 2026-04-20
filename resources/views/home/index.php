<?php
$statusLabels = [
    'chua_lock' => 'Chưa lock',
    'dang_giu' => 'Đang giữ',
    'da_lock' => 'Đã lock',
];
$typeLabels = [
    'co_gac' => 'Có gác',
    'khong_gac' => 'Không gác',
];
$furnitureLabels = [
    'co_noi_that' => 'Có nội thất',
    'khong_noi_that' => 'Không nội thất',
];
?>

<section class="listing-hero">
    <div>
        <div class="eyebrow">Lựa chọn chỗ ở nổi bật</div>
        <h2 class="listing-title">Kho phòng nội bộ giúp đội ngũ tư vấn chốt khách nhanh, rõ thông tin và đúng nhu cầu.</h2>
        <p class="listing-subtitle">
            Lọc theo quận, mức giá, trạng thái, nội thất, hệ thống và chi nhánh. Bấm vào từng phòng để xem
            chi tiết hình ảnh, video, thông số phòng và thao tác lock ngay trên hệ thống.
        </p>
    </div>

    <div class="listing-summary">
        <div><span>Tổng phòng</span><strong><?= e((string) $roomStats['total']) ?></strong></div>
        <div><span>Chưa lock</span><strong><?= e((string) $roomStats['chua_lock']) ?></strong></div>
        <div><span>Đang giữ</span><strong><?= e((string) $roomStats['dang_giu']) ?></strong></div>
        <div><span>Đã lock</span><strong><?= e((string) $roomStats['da_lock']) ?></strong></div>
    </div>
</section>

<section class="panel-card listing-filter-panel">
    <div class="panel-header">
        <div>
            <h3>Bộ lọc phòng</h3>
            <p class="panel-subtitle mb-0">Tối ưu cho luồng tìm phòng nhanh khi làm việc với khách hàng.</p>
        </div>
    </div>

    <form method="GET" action="<?= e(url('/')) ?>" class="listing-filters">
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

        <select name="branch_id" class="form-select">
            <option value="0">Chi nhánh</option>
            <?php foreach ($branches as $branch): ?>
                <option value="<?= e((string) $branch['id']) ?>" <?= (int) $filters['branch_id'] === (int) $branch['id'] ? 'selected' : '' ?>><?= e($branch['system_name'] . ' - ' . $branch['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="keyword" class="form-control listing-search" placeholder="Tìm theo số phòng, chi nhánh, địa chỉ..." value="<?= e($filters['keyword']) ?>">

        <button type="submit" class="btn btn-primary">Lọc phòng</button>
        <a href="<?= e(url('/')) ?>" class="btn btn-outline-secondary">Xóa lọc</a>
    </form>
</section>

<section class="listing-grid">
    <?php if (! $rooms): ?>
        <div class="panel-card empty-state-card">
            <h3 class="h5 mb-2">Không có phòng phù hợp</h3>
            <p class="text-muted mb-0">Hãy thử điều chỉnh bộ lọc để xem thêm các lựa chọn khác.</p>
        </div>
    <?php endif; ?>

    <?php foreach ($rooms as $room): ?>
        <?php $cover = $roomMediaMap[$room['id']][0] ?? null; ?>
        <a href="<?= e(url('/rooms/' . $room['id'])) ?>" class="listing-card">
            <div class="listing-card-media">
                <?php if ($cover && $cover['media_type'] === 'image'): ?>
                    <img src="<?= e(url('../' . $cover['file_path'])) ?>" alt="<?= e($room['room_number']) ?>">
                <?php elseif ($cover && $cover['media_type'] === 'video'): ?>
                    <video src="<?= e(url('../' . $cover['file_path'])) ?>" muted preload="metadata"></video>
                <?php else: ?>
                    <div class="listing-card-placeholder">Chưa có ảnh</div>
                <?php endif; ?>

                <span class="listing-hot-badge <?= $room['status'] === 'chua_lock' ? 'is-hot' : 'is-status' ?>">
                    <?= $room['status'] === 'chua_lock' ? 'HOT' : e($statusLabels[$room['status']] ?? $room['status']) ?>
                </span>
            </div>

            <div class="listing-card-body">
                <h3><?= e('Phòng ' . $room['room_number'] . ' - ' . $room['branch_name']) ?></h3>
                <div class="listing-price">Từ <?= e(number_format((float) $room['price'], 0, ',', '.')) ?>đ/tháng</div>

                <div class="listing-tags">
                    <span><?= e($typeLabels[$room['room_type']] ?? $room['room_type']) ?></span>
                    <span><?= e($furnitureLabels[$room['furniture_status']] ?? $room['furniture_status']) ?></span>
                    <span><?= (int) $room['has_balcony'] === 1 ? 'Có ban công' : 'Không ban công' ?></span>
                </div>

                <div class="listing-address"><?= e($room['district_name'] . ' - ' . $room['branch_address']) ?></div>
                <div class="listing-meta"><?= e($room['system_name'] . ' / ' . $room['branch_name']) ?></div>
            </div>
        </a>
    <?php endforeach; ?>
</section>
