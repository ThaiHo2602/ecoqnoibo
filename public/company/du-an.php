<?php
require_once __DIR__ . '/includes/projects-data.php';

$rooms = fetch_public_rooms();
$roomIds = array_map(static fn (array $room): int => (int) $room['id'], $rooms);
$roomMediaMap = fetch_room_media_map($roomIds);

$pageTitle = 'Dự án - Eco-Q House';
$pageDescription = 'Danh sách các phòng và dự án đang còn trống, sẵn sàng hỗ trợ khách thuê lựa chọn nhanh chóng.';
$currentPage = 'projects';

require_once __DIR__ . '/includes/header.php';
?>
<section class="page-hero page-hero-light">
    <div class="container">
        <span class="section-kicker">Dự án</span>
        <h1>Danh sách phòng đang mở cho khách thuê tại Eco-Q House</h1>
        <p>Chỉ hiển thị những phòng đang ở trạng thái Chưa lock để khách hàng dễ theo dõi và lựa chọn nhanh.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head-inline reveal-up">
            <div>
                <span class="section-kicker">Kho phòng khả dụng</span>
                <h2><?= count($rooms) ?> phòng đang sẵn sàng tư vấn</h2>
                <p>Mỗi dự án hiển thị hình ảnh đại diện, thông tin cơ bản và đường dẫn vào trang chi tiết.</p>
            </div>
        </div>

        <?php if ($rooms === []): ?>
            <div class="project-empty-state reveal-up">
                Hiện tại chưa có phòng nào ở trạng thái Chưa lock. Vui lòng quay lại sau hoặc liên hệ Eco-Q House để được hỗ trợ.
            </div>
        <?php else: ?>
            <div class="project-collection-grid">
                <?php foreach ($rooms as $index => $room): ?>
                    <?php $cover = $roomMediaMap[$room['id']][0] ?? null; ?>
                    <article class="project-card reveal-up" data-delay="<?= ($index % 3) * 100 ?>">
                        <a class="project-card-media" href="<?= htmlspecialchars(base_url('chi-tiet-du-an.php?id=' . $room['id'])) ?>">
                            <?php if ($cover && $cover['media_type'] === 'image'): ?>
                                <img src="<?= htmlspecialchars(room_media_url($cover['file_path'])) ?>" alt="<?= htmlspecialchars('Phòng ' . $room['room_number']) ?>">
                            <?php elseif ($cover && $cover['media_type'] === 'video'): ?>
                                <video src="<?= htmlspecialchars(room_media_url($cover['file_path'])) ?>" muted preload="metadata"></video>
                            <?php else: ?>
                                <div class="project-card-placeholder">Đang cập nhật hình ảnh</div>
                            <?php endif; ?>

                            <div class="project-card-overlay">
                                
                                <span class="project-card-chip"><?= htmlspecialchars($room['district_name']) ?></span>
                            </div>
                        </a>

                        <div class="project-card-body">
                            <div class="project-card-meta">
                                <span><?= htmlspecialchars($room['system_name']) ?></span>
                                <span><?= htmlspecialchars($room['branch_name']) ?></span>
                            </div>
                            <h3>
                                <a href="<?= htmlspecialchars(base_url('chi-tiet-du-an.php?id=' . $room['id'])) ?>">
                                    <?= htmlspecialchars('Phòng ' . $room['room_number'] . ' - ' . $room['branch_name']) ?>
                                </a>
                            </h3>
                            <p class="project-card-location"><?= htmlspecialchars($room['branch_address']) ?></p>
                            <div class="project-card-description">
                                <?= htmlspecialchars($room['note'] ?: 'Phòng đang mở lịch tư vấn, có thể xem ngay nếu khách phù hợp khu vực và ngân sách.') ?>
                            </div>
                            <div class="project-card-footer">
                                <div class="project-price"><?= htmlspecialchars(number_format((float) $room['price'], 0, ',', '.')) ?>đ/tháng</div>
                                <a class="project-link" href="<?= htmlspecialchars(base_url('chi-tiet-du-an.php?id=' . $room['id'])) ?>">
                                    Xem chi tiết <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                            <div class="project-tags">
                                <span><?= htmlspecialchars(room_type_label($room['room_type'])) ?></span>
                                <span><?= htmlspecialchars(furniture_status_label($room['furniture_status'])) ?></span>
                                <span><?= (int) $room['has_balcony'] === 1 ? 'Có ban công' : 'Không ban công' ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
