<?php
require_once __DIR__ . '/includes/projects-data.php';

$rooms = fetch_public_rooms();
$roomIds = array_map(static fn (array $room): int => (int) $room['id'], $rooms);
$roomMediaMap = fetch_room_media_map($roomIds);

$pageTitle = 'Dự án - Eco-Q House';
$pageDescription = 'Danh sách các phòng đang được chọn xuất hiện ngoài landing page của Eco-Q House.';
$currentPage = 'projects';

require_once __DIR__ . '/includes/header.php';
?>
<section class="page-hero page-hero-light">
    <div class="container">
        <span class="section-kicker">Dự án</span>
        <h1>Danh sách phòng đang sẵn sàng tư vấn tại Eco-Q House</h1>
        <p>Những phòng hiển thị ở đây là các phòng được bật xuất hiện ngoài landing page trong hệ thống nội bộ.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head-inline reveal-up">
            <div>
                <span class="section-kicker">Kho phòng khả dụng</span>
                <h2><?= count($rooms) ?> phòng đang được hiển thị</h2>
                <p>Bấm vào từng phòng để xem gallery, thông tin phòng và các chi phí áp dụng theo chi nhánh.</p>
            </div>
        </div>

        <?php if ($rooms === []): ?>
            <div class="project-empty-state reveal-up">
                Hiện tại chưa có phòng nào được bật hiển thị ngoài landing page. Vui lòng quay lại sau hoặc liên hệ Eco-Q House để được hỗ trợ.
            </div>
        <?php else: ?>
            <div class="project-collection-grid">
                <?php foreach ($rooms as $index => $room): ?>
                    <?php
                    $cover = null;
                    foreach ($roomMediaMap[$room['id']] ?? [] as $media) {
                        if (($media['media_type'] ?? '') === 'image') {
                            $cover = $media;
                            break;
                        }
                    }
                    $detailUrl = base_url('chi-tiet-du-an.php?id=' . $room['id']);
                    ?>
                    <article class="project-card reveal-up" data-delay="<?= ($index % 3) * 100 ?>">
                        <a class="project-card-media" href="<?= htmlspecialchars($detailUrl) ?>">
                            <?php if ($cover): ?>
                                <img src="<?= htmlspecialchars(room_media_url($cover['file_path'])) ?>" alt="<?= htmlspecialchars('Phòng ' . $room['room_number']) ?>">
                            <?php else: ?>
                                <div class="project-card-placeholder">Chưa có ảnh</div>
                            <?php endif; ?>

                            <div class="project-card-overlay">
                                <span class="project-card-chip"><?= htmlspecialchars(room_status_label($room['status'])) ?></span>
                                <span class="project-card-chip"><?= htmlspecialchars($room['district_name']) ?></span>
                            </div>
                        </a>

                        <div class="project-card-body">
                            <div class="project-card-meta">
                                <span><?= htmlspecialchars($room['system_name']) ?></span>
                                <span><?= htmlspecialchars($room['branch_name']) ?></span>
                            </div>
                            <h3>
                                <a href="<?= htmlspecialchars($detailUrl) ?>">
                                    <?= htmlspecialchars('Phòng ' . $room['room_number']) ?>
                                </a>
                            </h3>
                            <p class="project-card-location"><?= htmlspecialchars($room['branch_name']) ?></p>
                            <div class="project-card-description">
                                <?= htmlspecialchars(room_type_label((string) $room['room_type'])) ?> · <?= htmlspecialchars(furniture_status_label((string) $room['furniture_status'])) ?> · <?= (int) $room['has_balcony'] === 1 ? 'Có ban công' : 'Không ban công' ?>
                            </div>
                            <div class="project-card-footer">
                                <div class="project-price"><?= htmlspecialchars(public_room_price_label($room)) ?></div>
                                <a class="project-link" href="<?= htmlspecialchars($detailUrl) ?>">
                                    Xem chi tiết <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                            <div class="project-tags">
                                <span>Đã xác thực</span>
                                <span><?= htmlspecialchars(room_type_label((string) $room['room_type'])) ?></span>
                                <span><?= htmlspecialchars(window_type_label((string) $room['window_type'])) ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
