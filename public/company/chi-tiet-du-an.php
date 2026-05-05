<?php
require_once __DIR__ . '/includes/projects-data.php';

$roomId = (int) ($_GET['id'] ?? 0);
$room = fetch_public_room_detail($roomId);

if ($room === null) {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy phòng - Eco-Q House';
    $pageDescription = 'Phòng bạn đang tìm hiện không tồn tại hoặc chưa được bật hiển thị.';
    $currentPage = 'projects';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="page-hero page-hero-light">
        <div class="container">
            <span class="section-kicker">404</span>
            <h1>Phòng này hiện không còn khả dụng</h1>
            <p>Phòng có thể đã được lock hoặc liên kết bạn truy cập không còn hợp lệ.</p>
            <a href="<?= htmlspecialchars(base_url('du-an.php')) ?>" class="btn btn-brand mt-3">Quay lại trang dự án</a>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$media = fetch_public_room_media((int) $room['id']);
$imageMedia = array_values(array_filter($media, static fn (array $item): bool => ($item['media_type'] ?? '') === 'image'));
$relatedRooms = fetch_related_public_rooms($room);
$relatedMediaMap = fetch_room_media_map(array_map(static fn (array $item): int => (int) $item['id'], $relatedRooms));
$availableStatus = room_status_label((string) $room['status']);
$priceLabel = public_room_price_label($room);
$galleryImages = array_map(
    static fn (array $item): array => [
        'url' => room_media_url($item['file_path']),
        'alt' => $item['file_name'] ?: ('Phòng ' . $room['room_number']),
    ],
    $imageMedia
);

$pageTitle = 'Phòng ' . $room['room_number'] . ' - ' . $room['branch_name'] . ' - Eco-Q House';
$pageDescription = $room['note'] ?: 'Thông tin chi tiết phòng đang được hiển thị tại Eco-Q House.';
$currentPage = 'projects';

require_once __DIR__ . '/includes/header.php';
?>
<section class="branch-public-page">
    <div class="branch-public-shell">
        <nav class="branch-public-crumb" aria-label="Breadcrumb">
            <a href="<?= htmlspecialchars(base_url('du-an.php')) ?>">Cho thuê</a>
            <span>›</span>
            <span>Tp. Hồ Chí Minh</span>
            <span>›</span>
            <span><?= htmlspecialchars($room['district_name']) ?></span>
            <span>›</span>
            <strong><?= htmlspecialchars('Phòng ' . $room['room_number']) ?></strong>
        </nav>

        <header class="branch-public-header">
            <div>
                <div class="branch-public-title-row">
                    <h1><?= htmlspecialchars('Phòng ' . $room['room_number'] . ' - ' . $room['branch_name']) ?></h1>
                    <span class="branch-public-badge verified"><i class="bi bi-patch-check-fill"></i> Đã xác thực</span>
                    <span class="branch-public-badge hot">HOT</span>
                    <span class="branch-public-badge warning">Sắp hết</span>
                </div>
                <p><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($room['branch_name'] . ', ' . $room['district_name']) ?></p>
            </div>
            <div class="branch-public-actions">
                <button type="button" class="btn btn-light"><i class="bi bi-heart"></i> Yêu thích</button>
                <button type="button" class="btn btn-light"><i class="bi bi-share"></i> Chia sẻ</button>
                <a href="<?= htmlspecialchars(base_url('lien-he.php')) ?>" class="btn btn-brand">Đặt lịch xem phòng</a>
            </div>
        </header>

        <div class="branch-public-gallery-wrap">
            <div class="branch-public-stage hero" data-room-gallery-stage>
                <?php if ($galleryImages !== []): ?>
                    <img src="<?= htmlspecialchars($galleryImages[0]['url']) ?>" alt="<?= htmlspecialchars($galleryImages[0]['alt']) ?>">
                <?php else: ?>
                    <div class="project-card-placeholder">Chưa có ảnh</div>
                <?php endif; ?>
            </div>
            <?php if (count($galleryImages) > 1): ?>
                <button type="button" class="branch-public-arrow prev" data-gallery-prev aria-label="Ảnh trước">‹</button>
                <button type="button" class="branch-public-arrow next" data-gallery-next aria-label="Ảnh tiếp theo">›</button>
            <?php endif; ?>
            <div class="branch-public-counter" data-gallery-counter><?= $galleryImages === [] ? '0 / 0' : '1 / ' . count($galleryImages) ?></div>
        </div>

        <?php if ($galleryImages !== []): ?>
            <div class="branch-public-thumbs modern" data-gallery-thumbs>
                <?php foreach ($galleryImages as $index => $image): ?>
                    <button type="button" class="branch-public-thumb <?= $index === 0 ? 'is-active' : '' ?>" data-thumb-index="<?= $index ?>">
                        <img src="<?= htmlspecialchars($image['url']) ?>" alt="<?= htmlspecialchars($image['alt']) ?>">
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <section class="public-room-list-card">
            <div class="branch-section-head">
                <h2>Phòng đang xem</h2>
                <div class="status-legend">
                    <span><i class="dot green"></i>Còn trống</span>
                    <span><i class="dot yellow"></i>Đang giữ</span>
                    <span><i class="dot red"></i>Đã thuê</span>
                </div>
            </div>
            <div class="branch-public-room-strip">
                <button type="button" class="public-room-card is-active">
                    <strong><?= htmlspecialchars($room['room_number']) ?></strong>
                    <span class="room-status green"><?= htmlspecialchars($availableStatus) ?></span>
                    <em><?= htmlspecialchars($priceLabel) ?></em>
                </button>
            </div>
        </section>

        <div class="branch-public-content-grid">
            <main class="branch-public-content-main">
                <section class="public-info-card">
                    <h2>A. Thông tin chi nhánh</h2>
                    <div class="public-info-grid">
                        <div><span>Tên chi nhánh</span><strong><?= htmlspecialchars($room['branch_name']) ?></strong></div>
                        <div><span>Hệ thống</span><strong><?= htmlspecialchars($room['system_name']) ?></strong></div>
                        <div><span>Chi nhánh</span><strong><?= htmlspecialchars($room['branch_name']) ?></strong></div>
                        <div><span>Khu vực</span><strong><?= htmlspecialchars($room['district_name']) ?></strong></div>
                        <div><span>Giờ giấc</span><strong>Tự do</strong></div>
                    </div>
                </section>

                <section class="public-info-card">
                    <h2>B. Thông tin phòng đang chọn <span>(Phòng <?= htmlspecialchars($room['room_number']) ?>)</span></h2>
                    <div class="public-info-grid">
                        <div><span>Giá phòng</span><strong><?= htmlspecialchars($priceLabel) ?></strong></div>
                        <div><span>Loại phòng</span><strong><?= htmlspecialchars(room_type_label((string) $room['room_type'])) ?></strong></div>
                        <div><span>Nội thất</span><strong><?= htmlspecialchars(furniture_status_label((string) $room['furniture_status'])) ?></strong></div>
                        <div><span>Ban công</span><strong><?= (int) $room['has_balcony'] === 1 ? 'Có' : 'Không' ?></strong></div>
                        <div><span>Loại cửa sổ</span><strong><?= htmlspecialchars(window_type_label((string) $room['window_type'])) ?></strong></div>
                        <div><span>Tình trạng</span><strong><?= htmlspecialchars($availableStatus) ?></strong></div>
                        <div class="wide"><span>Mô tả phòng</span><strong><?= htmlspecialchars($room['note'] ?: 'Phòng hiện chưa có mô tả chi tiết.') ?></strong></div>
                    </div>
                </section>

                <section class="public-info-card">
                    <h2>C. Chi phí dịch vụ thuộc chi nhánh</h2>
                    <div class="branch-public-fees">
                        <div><span>Giá điện</span><strong><?= htmlspecialchars(number_format((float) $room['electricity_fee'], 0, ',', '.')) ?>đ</strong></div>
                        <div><span>Giá nước</span><strong><?= htmlspecialchars(number_format((float) $room['water_fee'], 0, ',', '.')) ?>đ</strong></div>
                        <div><span>Phí gửi xe</span><strong><?= htmlspecialchars(number_format((float) $room['parking_fee'], 0, ',', '.')) ?>đ</strong></div>
                        <div><span>Phí dịch vụ</span><strong><?= htmlspecialchars(number_format((float) $room['service_fee'], 0, ',', '.')) ?>đ</strong></div>
                    </div>
                    <div class="branch-public-fee-note">Các chi phí này áp dụng chung cho tất cả các phòng trong chi nhánh.</div>
                </section>
            </main>

            <aside class="public-cta-card">
                <span>Phòng đang chọn</span>
                <h2><?= htmlspecialchars('Phòng ' . $room['room_number']) ?></h2>
                <div class="room-status green"><?= htmlspecialchars($availableStatus) ?></div>
                <p>Giá phòng</p>
                <strong><?= htmlspecialchars($priceLabel) ?></strong>
                <a href="<?= htmlspecialchars(base_url('lien-he.php')) ?>" class="btn btn-brand w-100 mt-3">Đặt lịch xem phòng</a>
                <div class="public-cta-actions">
                    <a href="<?= htmlspecialchars($site['socials']['facebook'] ?? '#') ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-chat-dots"></i> Chat ngay</a>
                    <a href="tel:<?= htmlspecialchars(preg_replace('/\s+/', '', (string) ($room['manager_phone'] ?: ''))) ?>"><i class="bi bi-telephone"></i> Gọi hỗ trợ</a>
                </div>
                <div class="public-verified-note"><i class="bi bi-shield-check"></i> Thông tin đã được xác thực bởi đội ngũ hệ thống</div>
            </aside>
        </div>
    </div>
</section>

<?php if ($relatedRooms !== []): ?>
    <section class="section pt-0">
        <div class="container">
            <div class="section-head-inline reveal-up">
                <div>
                    <span class="section-kicker">Phòng liên quan</span>
                    <h2>Những phòng cùng khu vực bạn có thể quan tâm</h2>
                </div>
                <a href="<?= htmlspecialchars(base_url('du-an.php')) ?>" class="btn btn-outline-brand">Xem toàn bộ dự án</a>
            </div>
            <div class="project-collection-grid">
                <?php foreach ($relatedRooms as $index => $relatedRoom): ?>
                    <?php
                    $cover = null;
                    foreach ($relatedMediaMap[$relatedRoom['id']] ?? [] as $mediaItem) {
                        if (($mediaItem['media_type'] ?? '') === 'image') {
                            $cover = $mediaItem;
                            break;
                        }
                    }
                    ?>
                    <article class="project-card reveal-up" data-delay="<?= ($index % 3) * 100 ?>">
                        <a class="project-card-media" href="<?= htmlspecialchars(base_url('chi-tiet-du-an.php?id=' . $relatedRoom['id'])) ?>">
                            <?php if ($cover): ?>
                                <img src="<?= htmlspecialchars(room_media_url($cover['file_path'])) ?>" alt="<?= htmlspecialchars('Phòng ' . $relatedRoom['room_number']) ?>">
                            <?php else: ?>
                                <div class="project-card-placeholder">Chưa có ảnh</div>
                            <?php endif; ?>
                        </a>
                        <div class="project-card-body">
                            <div class="project-card-meta">
                                <span><?= htmlspecialchars($relatedRoom['branch_name']) ?></span>
                                <span><?= htmlspecialchars($relatedRoom['district_name']) ?></span>
                            </div>
                            <h3><a href="<?= htmlspecialchars(base_url('chi-tiet-du-an.php?id=' . $relatedRoom['id'])) ?>"><?= htmlspecialchars('Phòng ' . $relatedRoom['room_number']) ?></a></h3>
                            <p class="project-card-location"><?= htmlspecialchars($relatedRoom['branch_name']) ?></p>
                            <div class="project-card-footer">
                                <div class="project-price"><?= htmlspecialchars(public_room_price_label($relatedRoom)) ?></div>
                                <a class="project-link" href="<?= htmlspecialchars(base_url('chi-tiet-du-an.php?id=' . $relatedRoom['id'])) ?>">Xem chi tiết <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const images = <?= json_encode($galleryImages, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const stage = document.querySelector('[data-room-gallery-stage]');
        const thumbs = Array.from(document.querySelectorAll('[data-thumb-index]'));
        const counter = document.querySelector('[data-gallery-counter]');
        const prev = document.querySelector('[data-gallery-prev]');
        const next = document.querySelector('[data-gallery-next]');
        let current = 0;

        function escapeHtml(value) {
            return String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }

        function renderGallery(index) {
            if (!stage || images.length === 0) {
                return;
            }

            current = (index + images.length) % images.length;
            const image = images[current];
            stage.innerHTML = `<img src="${escapeHtml(image.url)}" alt="${escapeHtml(image.alt)}">`;
            if (counter) {
                counter.textContent = `${current + 1} / ${images.length}`;
            }
            thumbs.forEach((thumb) => {
                thumb.classList.toggle('is-active', Number(thumb.dataset.thumbIndex) === current);
            });
        }

        thumbs.forEach((thumb) => {
            thumb.addEventListener('click', () => renderGallery(Number(thumb.dataset.thumbIndex || 0)));
        });
        prev?.addEventListener('click', () => renderGallery(current - 1));
        next?.addEventListener('click', () => renderGallery(current + 1));
    });
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
