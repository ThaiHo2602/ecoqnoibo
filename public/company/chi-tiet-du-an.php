<?php
require_once __DIR__ . '/includes/projects-data.php';

$roomId = (int) ($_GET['id'] ?? 0);
$room = fetch_public_room_detail($roomId);

if ($room === null) {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy dự án - Eco-Q House';
    $pageDescription = 'Dự án bạn đang tìm hiện không tồn tại hoặc đã được lock.';
    $currentPage = 'projects';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="page-hero page-hero-light">
        <div class="container">
            <span class="section-kicker">404</span>
            <h1>Dự án này hiện không còn khả dụng</h1>
            <p>Phòng có thể đã được lock hoặc liên kết bạn truy cập không còn hợp lệ.</p>
            <a href="<?= htmlspecialchars(base_url('du-an.php')) ?>" class="btn btn-brand mt-3">Quay lại trang dự án</a>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$media = fetch_public_room_media((int) $room['id']);
$relatedRooms = fetch_related_public_rooms($room);
$relatedMediaMap = fetch_room_media_map(array_map(static fn (array $item): int => (int) $item['id'], $relatedRooms));

$pageTitle = 'Chi tiết dự án - Phòng ' . $room['room_number'] . ' - Eco-Q House';
$pageDescription = $room['note'] ?: 'Thông tin chi tiết phòng đang còn trống tại Eco-Q House.';
$currentPage = 'projects';

require_once __DIR__ . '/includes/header.php';
?>
<section class="page-hero page-hero-light">
    <div class="container">
        <span class="section-kicker">Chi tiết dự án</span>
        <h1><?= htmlspecialchars('Phòng ' . $room['room_number'] . ' - ' . $room['branch_name']) ?></h1>
        <p><?= htmlspecialchars($room['district_name'] . ' • ' . $room['branch_address']) ?></p>
    </div>
</section>

<section class="section pt-0">
    <div class="container">
        <div class="project-detail-layout">
            <div class="project-gallery-panel reveal-up">
                <?php if ($media !== []): ?>
                    <?php $hero = $media[0]; ?>
                    <div class="project-gallery" data-project-gallery data-current-index="0">
                        <div class="project-gallery-stage" data-project-gallery-stage>
                            <?php if ($hero['media_type'] === 'image'): ?>
                                <img src="<?= htmlspecialchars(room_media_url($hero['file_path'])) ?>" alt="<?= htmlspecialchars('Phòng ' . $room['room_number']) ?>">
                            <?php else: ?>
                                <video src="<?= htmlspecialchars(room_media_url($hero['file_path'])) ?>" controls preload="metadata"></video>
                            <?php endif; ?>
                        </div>

                        <?php if (count($media) > 1): ?>
                            <button type="button" class="project-gallery-nav prev" data-gallery-prev aria-label="Ảnh trước">‹</button>
                            <button type="button" class="project-gallery-nav next" data-gallery-next aria-label="Ảnh tiếp theo">›</button>
                        <?php endif; ?>

                        <div class="project-gallery-thumbs">
                            <?php foreach ($media as $index => $item): ?>
                                <button
                                    type="button"
                                    class="project-gallery-thumb <?= $index === 0 ? 'is-active' : '' ?>"
                                    data-gallery-thumb
                                    data-index="<?= htmlspecialchars((string) $index) ?>"
                                    data-media-type="<?= htmlspecialchars($item['media_type']) ?>"
                                    data-src="<?= htmlspecialchars(room_media_url($item['file_path'])) ?>"
                                    data-alt="<?= htmlspecialchars($item['file_name'] ?: ('Phòng ' . $room['room_number'])) ?>"
                                >
                                    <?php if ($item['media_type'] === 'image'): ?>
                                        <img src="<?= htmlspecialchars(room_media_url($item['file_path'])) ?>" alt="<?= htmlspecialchars($item['file_name']) ?>">
                                    <?php else: ?>
                                        <video src="<?= htmlspecialchars(room_media_url($item['file_path'])) ?>" muted preload="metadata"></video>
                                        <span class="project-gallery-video-badge">Video</span>
                                    <?php endif; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="project-empty-media">Phòng này hiện chưa có hình ảnh hoặc video.</div>
                <?php endif; ?>
            </div>

            <aside class="project-summary-panel reveal-up" data-delay="100">
                <div class="project-summary-top">
                    <span class="section-kicker">Thông tin nổi bật</span>
                    <div class="project-summary-top-badges">
                        
                        <span class="project-card-chip"><?= htmlspecialchars($room['district_name']) ?></span>
                    </div>
                </div>

                <h2><?= htmlspecialchars('Phòng ' . $room['room_number'] . ' - ' . $room['branch_name']) ?></h2>
                <div class="project-summary-subtitle"><?= htmlspecialchars($room['system_name']) ?></div>
                <div class="project-summary-address"><?= htmlspecialchars($room['branch_address']) ?></div>
                <div class="project-summary-price"><?= htmlspecialchars(number_format((float) $room['price'], 0, ',', '.')) ?>đ/tháng</div>

                <div class="project-summary-quickstats">
                    <div>
                        <span>Loại phòng</span>
                        <strong><?= htmlspecialchars(room_type_label($room['room_type'])) ?></strong>
                    </div>
                    <div>
                        <span>Nội thất</span>
                        <strong><?= htmlspecialchars(furniture_status_label($room['furniture_status'])) ?></strong>
                    </div>
                    <div>
                        <span>Ban công</span>
                        <strong><?= (int) $room['has_balcony'] === 1 ? 'Có' : 'Không' ?></strong>
                    </div>
                    <div>
                        <span>Cửa sổ</span>
                        <strong><?= htmlspecialchars(window_type_label($room['window_type'])) ?></strong>
                    </div>
                </div>

                <div class="project-tags mt-3">
                    <span><?= htmlspecialchars(room_type_label($room['room_type'])) ?></span>
                    <span><?= htmlspecialchars(furniture_status_label($room['furniture_status'])) ?></span>
                    <span><?= (int) $room['has_balcony'] === 1 ? 'Có ban công' : 'Không ban công' ?></span>
                </div>

                <div class="project-summary-note">
                    <?= htmlspecialchars($room['note'] ?: 'Eco-Q House đang cập nhật thêm mô tả chi tiết cho phòng này.') ?>
                </div>

                <a
                    href="<?= htmlspecialchars($site['socials']['facebook'] ?? '#') ?>"
                    class="btn btn-brand btn-lg mt-3"
                    target="_blank"
                    rel="noopener noreferrer"
                >Liên hệ ngay qua Facebook</a>
            </aside>
        </div>
    </div>
</section>

<section class="section pt-0">
    <div class="container">
        <div class="project-detail-grid">
            <article class="project-info-card reveal-up">
                <span class="section-kicker">Thông số chi tiết</span>
                <h2>Thông tin phòng</h2>
                <div class="project-info-specs">
                    <div><span>Hệ thống</span><strong><?= htmlspecialchars($room['system_name']) ?></strong></div>
                    <div><span>Chi nhánh</span><strong><?= htmlspecialchars($room['branch_name']) ?></strong></div>
                    <div><span>Quận</span><strong><?= htmlspecialchars($room['district_name']) ?></strong></div>
                    <div><span>Số phòng</span><strong><?= htmlspecialchars($room['room_number']) ?></strong></div>
                    <div><span>Giá phòng</span><strong><?= htmlspecialchars(number_format((float) $room['price'], 0, ',', '.')) ?>đ</strong></div>
                    <div><span>Loại phòng</span><strong><?= htmlspecialchars(room_type_label($room['room_type'])) ?></strong></div>
                    <div><span>Tiền điện</span><strong><?= htmlspecialchars(number_format((float) $room['electricity_fee'], 0, ',', '.')) ?>đ</strong></div>
                    <div><span>Tiền nước</span><strong><?= htmlspecialchars(number_format((float) $room['water_fee'], 0, ',', '.')) ?>đ</strong></div>
                    <div><span>Phí dịch vụ</span><strong><?= htmlspecialchars(number_format((float) $room['service_fee'], 0, ',', '.')) ?>đ</strong></div>
                    <div><span>Phí gửi xe</span><strong><?= htmlspecialchars(number_format((float) $room['parking_fee'], 0, ',', '.')) ?>đ</strong></div>
                    <div><span>Nội thất</span><strong><?= htmlspecialchars(furniture_status_label($room['furniture_status'])) ?></strong></div>
                    <div><span>Ban công</span><strong><?= (int) $room['has_balcony'] === 1 ? 'Có' : 'Không' ?></strong></div>
                    <div><span>Loại cửa sổ</span><strong><?= htmlspecialchars(window_type_label($room['window_type'])) ?></strong></div>
                    <div><span>Trạng thái</span><strong><?= htmlspecialchars(room_status_label($room['status'])) ?></strong></div>
                </div>
            </article>

            <article class="project-info-card reveal-up" data-delay="100">
                <span class="section-kicker">Vị trí</span>
                <h2>Khu vực dự án</h2>
                <div class="project-map-placeholder">
                    <div class="project-map-pin"><i class="bi bi-geo-alt-fill"></i></div>
                    <div>
                        <strong><?= htmlspecialchars($room['branch_address']) ?></strong>
                        <p><?= htmlspecialchars('Khu vực ' . $room['district_name'] . ', thuộc ' . $room['system_name']) ?></p>
                    </div>
                </div>
            </article>
        </div>
    </div>
</section>

<?php if ($relatedRooms !== []): ?>
    <section class="section pt-0">
        <div class="container">
            <div class="section-head-inline reveal-up">
                <div>
                    <span class="section-kicker">Dự án liên quan</span>
                    <h2>Những phòng cùng khu vực bạn có thể quan tâm</h2>
                </div>
                <a href="<?= htmlspecialchars(base_url('du-an.php')) ?>" class="btn btn-outline-brand">Xem toàn bộ dự án</a>
            </div>
            <div class="project-collection-grid">
                <?php foreach ($relatedRooms as $index => $relatedRoom): ?>
                    <?php $cover = $relatedMediaMap[$relatedRoom['id']][0] ?? null; ?>
                    <article class="project-card reveal-up" data-delay="<?= ($index % 3) * 100 ?>">
                        <a class="project-card-media" href="<?= htmlspecialchars(base_url('chi-tiet-du-an.php?id=' . $relatedRoom['id'])) ?>">
                            <?php if ($cover && $cover['media_type'] === 'image'): ?>
                                <img src="<?= htmlspecialchars(room_media_url($cover['file_path'])) ?>" alt="<?= htmlspecialchars('Phòng ' . $relatedRoom['room_number']) ?>">
                            <?php elseif ($cover && $cover['media_type'] === 'video'): ?>
                                <video src="<?= htmlspecialchars(room_media_url($cover['file_path'])) ?>" muted preload="metadata"></video>
                            <?php else: ?>
                                <div class="project-card-placeholder">Đang cập nhật hình ảnh</div>
                            <?php endif; ?>
                            
                        </a>
                        <div class="project-card-body">
                            <div class="project-card-meta">
                                <span><?= htmlspecialchars($relatedRoom['system_name']) ?></span>
                                <span><?= htmlspecialchars($relatedRoom['district_name']) ?></span>
                            </div>
                            <h3>
                                <a href="<?= htmlspecialchars(base_url('chi-tiet-du-an.php?id=' . $relatedRoom['id'])) ?>">
                                    <?= htmlspecialchars('Phòng ' . $relatedRoom['room_number'] . ' - ' . $relatedRoom['branch_name']) ?>
                                </a>
                            </h3>
                            <p class="project-card-location"><?= htmlspecialchars($relatedRoom['branch_address']) ?></p>
                            <div class="project-card-footer">
                                <div class="project-price"><?= htmlspecialchars(number_format((float) $relatedRoom['price'], 0, ',', '.')) ?>đ/tháng</div>
                                <a class="project-link" href="<?= htmlspecialchars(base_url('chi-tiet-du-an.php?id=' . $relatedRoom['id'])) ?>">
                                    Xem chi tiết <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php
$landingImageMedia = array_values(array_filter(
    $media,
    static fn (array $item): bool => ($item['media_type'] ?? '') === 'image'
));
?>

<?php if ($landingImageMedia !== []): ?>
    <div class="project-lightbox" data-project-lightbox hidden>
        <button type="button" class="project-lightbox-close" data-project-lightbox-close aria-label="Đóng">×</button>
        <button type="button" class="project-lightbox-nav prev" data-project-lightbox-prev aria-label="Ảnh trước">‹</button>
        <div class="project-lightbox-dialog">
            <img src="" alt="" class="project-lightbox-image" data-project-lightbox-image>
        </div>
        <button type="button" class="project-lightbox-nav next" data-project-lightbox-next aria-label="Ảnh tiếp theo">›</button>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const gallery = document.querySelector('[data-project-gallery]');
        if (!gallery) {
            return;
        }

        const stage = gallery.querySelector('[data-project-gallery-stage]');
        const thumbs = Array.from(gallery.querySelectorAll('[data-gallery-thumb]'));
        const prevButton = gallery.querySelector('[data-gallery-prev]');
        const nextButton = gallery.querySelector('[data-gallery-next]');
        const lightbox = document.querySelector('[data-project-lightbox]');
        const lightboxImage = lightbox ? lightbox.querySelector('[data-project-lightbox-image]') : null;
        const lightboxClose = lightbox ? lightbox.querySelector('[data-project-lightbox-close]') : null;
        const lightboxPrev = lightbox ? lightbox.querySelector('[data-project-lightbox-prev]') : null;
        const lightboxNext = lightbox ? lightbox.querySelector('[data-project-lightbox-next]') : null;
        const imageThumbs = thumbs.filter((thumb) => (thumb.dataset.mediaType || 'image') === 'image');
        let activeLightboxIndex = 0;

        const renderMedia = (index) => {
            const item = thumbs[index];
            if (!item || !stage) {
                return;
            }

            const src = item.dataset.src || '';
            const type = item.dataset.mediaType || 'image';
            const alt = item.dataset.alt || '';

            stage.innerHTML = '';

            if (type === 'video') {
                const video = document.createElement('video');
                video.src = src;
                video.controls = true;
                video.preload = 'metadata';
                stage.appendChild(video);
            } else {
                const image = document.createElement('img');
                image.src = src;
                image.alt = alt;
                stage.appendChild(image);
            }

            gallery.dataset.currentIndex = String(index);
            thumbs.forEach((thumb, thumbIndex) => {
                thumb.classList.toggle('is-active', thumbIndex === index);
            });
        };

        const syncLightbox = (imageIndex) => {
            if (!lightbox || !lightboxImage || imageThumbs.length === 0) {
                return;
            }

            const safeIndex = imageIndex < 0
                ? imageThumbs.length - 1
                : imageIndex >= imageThumbs.length
                    ? 0
                    : imageIndex;

            activeLightboxIndex = safeIndex;
            const thumb = imageThumbs[safeIndex];
            lightboxImage.src = thumb.dataset.src || '';
            lightboxImage.alt = thumb.dataset.alt || '';
        };

        const openLightbox = (imageIndex) => {
            if (!lightbox || imageThumbs.length === 0) {
                return;
            }

            syncLightbox(imageIndex);
            lightbox.hidden = false;
            document.body.classList.add('lightbox-open');
        };

        const closeLightboxModal = () => {
            if (!lightbox) {
                return;
            }

            lightbox.hidden = true;
            document.body.classList.remove('lightbox-open');
        };

        thumbs.forEach((thumb, index) => {
            thumb.addEventListener('click', function () {
                renderMedia(index);
            });

            if ((thumb.dataset.mediaType || 'image') === 'image') {
                thumb.addEventListener('dblclick', function (event) {
                    event.preventDefault();
                    const imageIndex = imageThumbs.indexOf(thumb);
                    openLightbox(imageIndex >= 0 ? imageIndex : 0);
                });
            }
        });

        if (prevButton) {
            prevButton.addEventListener('click', function () {
                const currentIndex = Number(gallery.dataset.currentIndex || 0);
                const nextIndex = currentIndex === 0 ? thumbs.length - 1 : currentIndex - 1;
                renderMedia(nextIndex);
            });
        }

        if (nextButton) {
            nextButton.addEventListener('click', function () {
                const currentIndex = Number(gallery.dataset.currentIndex || 0);
                const nextIndex = currentIndex === thumbs.length - 1 ? 0 : currentIndex + 1;
                renderMedia(nextIndex);
            });
        }

        stage.addEventListener('click', function (event) {
            const target = event.target;
            if (!(target instanceof HTMLImageElement)) {
                return;
            }

            const currentSrc = target.getAttribute('src') || '';
            const imageIndex = imageThumbs.findIndex((thumb) => (thumb.dataset.src || '') === currentSrc);
            openLightbox(imageIndex >= 0 ? imageIndex : 0);
        });

        if (lightboxClose) {
            lightboxClose.addEventListener('click', closeLightboxModal);
        }

        if (lightboxPrev) {
            lightboxPrev.addEventListener('click', function () {
                syncLightbox(activeLightboxIndex - 1);
            });
        }

        if (lightboxNext) {
            lightboxNext.addEventListener('click', function () {
                syncLightbox(activeLightboxIndex + 1);
            });
        }

        if (lightbox) {
            lightbox.addEventListener('click', function (event) {
                if (event.target === lightbox) {
                    closeLightboxModal();
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (!lightbox || lightbox.hidden) {
                return;
            }

            if (event.key === 'Escape') {
                closeLightboxModal();
            }

            if (event.key === 'ArrowLeft') {
                syncLightbox(activeLightboxIndex - 1);
            }

            if (event.key === 'ArrowRight') {
                syncLightbox(activeLightboxIndex + 1);
            }
        });
    });
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
