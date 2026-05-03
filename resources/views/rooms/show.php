<?php
$statusLabels = [
    'chua_lock' => 'Chưa lock',
    'dang_giu' => 'Đang giữ',
    'da_lock' => 'Đã lock',
];
$typeLabels = [
    'duplet' => 'Duplet',
    'studio' => 'Studio',
    'one_bedroom' => '1 phòng ngủ',
    'two_bedroom' => '2 phòng ngủ',
    'kiot' => 'Kiot',
];
$furnitureLabels = [
    'co_noi_that' => 'Có nội thất',
    'khong_noi_that' => 'Không nội thất',
];
$windowLabels = [
    'cua_so_troi' => 'Cửa sổ trời',
    'cua_so_hanh_lang' => 'Cửa sổ hành lang',
    'cua_so_gieng_troi' => 'Cửa sổ giếng trời',
];
$lockLabels = [
    'pending' => 'Chờ duyệt',
    'approved' => 'Đã duyệt',
    'rejected' => 'Từ chối',
];
$imageMedia = array_values(array_filter(
    $media,
    static fn (array $item): bool => ($item['media_type'] ?? '') === 'image'
));
?>

<section class="room-detail-page">
    <div class="breadcrumb-line">
        <a href="<?= e(url('/app')) ?>">Trang chủ</a>
        <span>/</span>
        <span><?= e('Phòng ' . $room['room_number']) ?></span>
    </div>

    <div class="room-detail-top">
        <div class="room-gallery-main">
            <?php if ($media): ?>
                <?php $hero = $media[0]; ?>
                <div class="room-gallery-shell" data-room-gallery data-current-index="0">
                    <div class="room-hero-media">
                        <div class="room-hero-stage" data-room-gallery-stage>
                            <?php if (($hero['media_type'] ?? '') === 'image'): ?>
                                <img
                                    src="<?= e(media_url($hero['file_path'])) ?>"
                                    alt="<?= e($hero['file_name'] ?: ('Phòng ' . $room['room_number'])) ?>"
                                    data-room-gallery-display
                                    data-media-type="image"
                                    class="room-lightbox-trigger"
                                >
                            <?php else: ?>
                                <video
                                    src="<?= e(media_url($hero['file_path'])) ?>"
                                    controls
                                    preload="metadata"
                                    data-room-gallery-display
                                    data-media-type="video"
                                ></video>
                            <?php endif; ?>
                        </div>

                        <?php if (count($media) > 1): ?>
                            <button type="button" class="room-gallery-nav prev" data-gallery-prev aria-label="Ảnh trước">‹</button>
                            <button type="button" class="room-gallery-nav next" data-gallery-next aria-label="Ảnh tiếp theo">›</button>
                        <?php endif; ?>
                    </div>

                    <div class="room-gallery-grid">
                        <?php foreach ($media as $index => $item): ?>
                            <?php
                            $sourceUrl = media_url($item['file_path']);
                            $fallbackName = 'phong-' . $room['room_number'] . '-' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
                            $downloadName = trim((string) ($item['file_name'] ?? '')) !== '' ? (string) $item['file_name'] : $fallbackName;
                            ?>
                            <button
                                type="button"
                                class="room-gallery-thumb <?= $index === 0 ? 'is-active' : '' ?>"
                                data-gallery-thumb
                                data-index="<?= e((string) $index) ?>"
                                data-media-type="<?= e($item['media_type']) ?>"
                                data-src="<?= e($sourceUrl) ?>"
                                data-alt="<?= e($item['file_name'] ?: ('Phòng ' . $room['room_number'])) ?>"
                                data-download-name="<?= e($downloadName) ?>"
                                aria-label="<?= e('Chuyển tới media ' . ($index + 1)) ?>"
                            >
                                <?php if (($item['media_type'] ?? '') === 'image'): ?>
                                    <img src="<?= e($sourceUrl) ?>" alt="<?= e($item['file_name']) ?>">
                                <?php else: ?>
                                    <video src="<?= e($sourceUrl) ?>" muted preload="metadata"></video>
                                    <span class="room-gallery-video-badge">Video</span>
                                <?php endif; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="room-no-media">Phòng này chưa có hình ảnh hoặc video.</div>
            <?php endif; ?>
        </div>

        <aside class="room-summary-card">
            <div class="eyebrow">Chi tiết phòng</div>
            <h1><?= e('Phòng ' . $room['room_number'] . ' - ' . $room['branch_name']) ?></h1>
            <p class="room-summary-address"><?= e($room['branch_address']) ?></p>
            <div class="room-summary-price"><?= e(number_format((float) $room['price'], 0, ',', '.')) ?>đ/tháng</div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <span class="status-pill status-<?= e($room['status']) ?>"><?= e($statusLabels[$room['status']] ?? $room['status']) ?></span>
                <span class="detail-tag"><?= e($typeLabels[$room['room_type']] ?? $room['room_type']) ?></span>
                <span class="detail-tag"><?= e($furnitureLabels[$room['furniture_status']] ?? $room['furniture_status']) ?></span>
            </div>

            <div class="room-cta-stack">
                <?php if ($imageMedia): ?>
                    <button type="button" class="btn btn-outline-primary btn-lg" data-download-all-room-images>
                        Tải toàn bộ ảnh phòng
                    </button>
                <?php endif; ?>

                <?php if ($canRequestLock && $room['status'] === 'chua_lock'): ?>
                    <form method="POST" action="<?= e(url('/lock-requests/store')) ?>" class="d-grid gap-3">
                        <input type="hidden" name="room_id" value="<?= e((string) $room['id']) ?>">
                        <input type="hidden" name="return_to" value="<?= e(current_path()) ?>">
                        <textarea name="request_note" class="form-control" rows="3" placeholder="Ghi chú cho quản lý khi gửi yêu cầu lock"></textarea>
                        <button type="submit" class="btn btn-warning btn-lg">Gửi yêu cầu lock</button>
                    </form>
                <?php elseif ($canRequestLock): ?>
                    <div class="empty-state">Phòng này hiện đang <?= e(mb_strtolower($statusLabels[$room['status']] ?? $room['status'])) ?> nên chưa thể gửi yêu cầu lock mới.</div>
                <?php endif; ?>

                <?php if ($canManage): ?>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= e(url('/rooms?edit=' . $room['id'])) ?>" class="btn btn-primary">Chỉnh sửa phòng</a>
                        <form method="POST" action="<?= e(url('/rooms/delete')) ?>" onsubmit="return confirm('Bạn chắc chắn muốn xóa phòng này?');">
                            <input type="hidden" name="id" value="<?= e((string) $room['id']) ?>">
                            <button type="submit" class="btn btn-outline-danger">Xóa phòng</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </aside>
    </div>

    <section class="room-section-grid mt-4">
        <div class="detail-card">
            <div class="panel-header mb-3">
                <div>
                    <h3>Mô tả phòng</h3>
                    <p class="panel-subtitle mb-0">Thông tin tổng quát để nhân viên tư vấn khách nhanh và chính xác.</p>
                </div>
            </div>
            <p class="mb-0"><?= e($room['note'] ?: 'Phòng hiện chưa có mô tả chi tiết.') ?></p>
        </div>

        <div class="detail-card">
            <div class="panel-header mb-3">
                <div>
                    <h3>Thông tin lock gần nhất</h3>
                    <p class="panel-subtitle mb-0">Hiển thị trạng thái xử lý mới nhất của phòng này.</p>
                </div>
            </div>
            <?php if ($latestLock): ?>
                <div class="detail-list">
                    <div><span>Trạng thái yêu cầu</span><strong><?= e($lockLabels[$latestLock['request_status']] ?? $latestLock['request_status']) ?></strong></div>
                    <div><span>Người gửi</span><strong><?= e($latestLock['requester_name']) ?></strong></div>
                    <div><span>Thời gian gửi</span><strong><?= e($latestLock['requested_at']) ?></strong></div>
                </div>
                <?php if ($latestLock['request_note']): ?>
                    <div class="detail-note mt-3"><?= e($latestLock['request_note']) ?></div>
                <?php endif; ?>
            <?php else: ?>
                <div class="empty-state">Phòng này chưa có lịch sử lock.</div>
            <?php endif; ?>
        </div>
    </section>

    <section class="detail-card mt-4">
        <div class="panel-header mb-3">
            <div>
                <h3>Thông tin chi tiết</h3>
                <p class="panel-subtitle mb-0">Tổng hợp chi phí và đặc điểm phòng theo đúng dữ liệu nội bộ.</p>
            </div>
        </div>

        <div class="detail-spec-grid">
            <div><span>Hệ thống</span><strong><?= e($room['system_name']) ?></strong></div>
            <div><span>Chi nhánh</span><strong><?= e($room['branch_name']) ?></strong></div>
            <div><span>Quận</span><strong><?= e($room['district_name']) ?></strong></div>
            <div><span>Số phòng</span><strong><?= e($room['room_number']) ?></strong></div>
            <div><span>Giá phòng</span><strong><?= e(number_format((float) $room['price'], 0, ',', '.')) ?>đ</strong></div>
            <div><span>Loại phòng</span><strong><?= e($typeLabels[$room['room_type']] ?? $room['room_type']) ?></strong></div>
            <div><span>Tiền điện</span><strong><?= e(number_format((float) $room['electricity_fee'], 0, ',', '.')) ?>đ</strong></div>
            <div><span>Tiền nước</span><strong><?= e(number_format((float) $room['water_fee'], 0, ',', '.')) ?>đ</strong></div>
            <div><span>Phí dịch vụ</span><strong><?= e(number_format((float) $room['service_fee'], 0, ',', '.')) ?>đ</strong></div>
            <div><span>Phí gửi xe</span><strong><?= e(number_format((float) $room['parking_fee'], 0, ',', '.')) ?>đ</strong></div>
            <div><span>Nội thất</span><strong><?= e($furnitureLabels[$room['furniture_status']] ?? $room['furniture_status']) ?></strong></div>
            <div><span>Ban công</span><strong><?= (int) $room['has_balcony'] === 1 ? 'Có' : 'Không' ?></strong></div>
            <div><span>Loại cửa sổ</span><strong><?= e($windowLabels[$room['window_type']] ?? $room['window_type']) ?></strong></div>
            <div><span>Trạng thái</span><strong><?= e($statusLabels[$room['status']] ?? $room['status']) ?></strong></div>
        </div>
    </section>

    <section class="room-section-grid mt-4">
        <div class="detail-card">
            <div class="panel-header mb-3">
                <div>
                    <h3>Vị trí</h3>
                    <p class="panel-subtitle mb-0">Khu vực hiển thị nhanh vị trí chi nhánh, sẵn sàng để tích hợp bản đồ thật sau này.</p>
                </div>
            </div>
            <div class="map-placeholder">
                <div class="map-pin">📍</div>
                <div>
                    <div class="fw-semibold"><?= e($room['branch_address']) ?></div>
                    <div class="text-muted">Khu vực: <?= e($room['district_name']) ?></div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="panel-header mb-3">
                <div>
                    <h3>Liên hệ chi nhánh</h3>
                    <p class="panel-subtitle mb-0">Dùng khi cần chốt lịch xem phòng hoặc xác nhận thông tin thực tế.</p>
                </div>
            </div>
            <div class="detail-list">
                <div><span>Địa chỉ</span><strong><?= e($room['branch_address']) ?></strong></div>
                <div><span>Số điện thoại quản lý</span><strong><?= e($room['manager_phone'] ?: 'Chưa cập nhật') ?></strong></div>
            </div>
        </div>
    </section>

    <section class="related-section mt-4">
        <div class="related-header">
            <div>
                <div class="eyebrow">Gợi ý thêm</div>
                <h3>Phòng liên quan cùng khu vực hoặc cùng chi nhánh</h3>
            </div>
        </div>

        <div class="listing-grid related-grid">
            <?php foreach ($relatedRooms as $relatedRoom): ?>
                <?php $cover = $relatedMediaMap[$relatedRoom['id']][0] ?? null; ?>
                <a href="<?= e(url('/rooms/' . $relatedRoom['id'])) ?>" class="listing-card compact-card">
                    <div class="listing-card-media">
                        <?php if ($cover && ($cover['media_type'] ?? '') === 'image'): ?>
                            <img src="<?= e(media_url($cover['file_path'])) ?>" alt="<?= e($relatedRoom['room_number']) ?>">
                        <?php elseif ($cover && ($cover['media_type'] ?? '') === 'video'): ?>
                            <video src="<?= e(media_url($cover['file_path'])) ?>" muted preload="metadata"></video>
                        <?php else: ?>
                            <div class="listing-card-placeholder">Chưa có ảnh</div>
                        <?php endif; ?>
                    </div>
                    <div class="listing-card-body">
                        <h3><?= e('Phòng ' . $relatedRoom['room_number'] . ' - ' . $relatedRoom['branch_name']) ?></h3>
                        <div class="listing-price">Từ <?= e(number_format((float) $relatedRoom['price'], 0, ',', '.')) ?>đ/tháng</div>
                        <div class="listing-address"><?= e($relatedRoom['district_name']) ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</section>

<?php if ($media): ?>
    <div class="media-lightbox" data-media-lightbox hidden>
        <button type="button" class="media-lightbox-close" data-lightbox-close aria-label="Đóng">×</button>
        <button type="button" class="media-lightbox-nav prev" data-lightbox-prev aria-label="Ảnh trước">‹</button>
        <div class="media-lightbox-dialog">
            <a href="#" class="media-lightbox-download-icon" data-lightbox-download download aria-label="Tải ảnh hiện tại" title="Tải ảnh hiện tại">
                <span aria-hidden="true">⭳</span>
            </a>
            <img src="" alt="" class="media-lightbox-image" data-lightbox-image>
        </div>
        <button type="button" class="media-lightbox-nav next" data-lightbox-next aria-label="Ảnh tiếp theo">›</button>
    </div>

    <script>
        (function () {
            const gallery = document.querySelector('[data-room-gallery]');
            if (!gallery || gallery.dataset.initialized === '1') {
                return;
            }

            gallery.dataset.initialized = '1';

            const stage = gallery.querySelector('[data-room-gallery-stage]');
            const thumbs = Array.from(gallery.querySelectorAll('[data-gallery-thumb]'));
            const prevButton = gallery.querySelector('[data-gallery-prev]');
            const nextButton = gallery.querySelector('[data-gallery-next]');
            const downloadAllButton = document.querySelector('[data-download-all-room-images]');
            const lightbox = document.querySelector('[data-media-lightbox]');
            const lightboxImage = lightbox ? lightbox.querySelector('[data-lightbox-image]') : null;
            const lightboxDownload = lightbox ? lightbox.querySelector('[data-lightbox-download]') : null;
            const lightboxClose = lightbox ? lightbox.querySelector('[data-lightbox-close]') : null;
            const lightboxPrev = lightbox ? lightbox.querySelector('[data-lightbox-prev]') : null;
            const lightboxNext = lightbox ? lightbox.querySelector('[data-lightbox-next]') : null;
            const imageThumbs = thumbs.filter((thumb) => (thumb.dataset.mediaType || 'image') === 'image');
            let activeLightboxIndex = 0;

            const wait = (duration) => new Promise((resolve) => window.setTimeout(resolve, duration));

            const triggerDownload = async (url, filename) => {
                const response = await fetch(url, {
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('download_failed');
                }

                const blob = await response.blob();
                const objectUrl = window.URL.createObjectURL(blob);
                const anchor = document.createElement('a');
                anchor.href = objectUrl;
                anchor.download = filename || 'room-image';
                document.body.appendChild(anchor);
                anchor.click();
                anchor.remove();

                window.setTimeout(function () {
                    window.URL.revokeObjectURL(objectUrl);
                }, 1000);
            };

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
                    video.setAttribute('data-room-gallery-display', '');
                    video.setAttribute('data-media-type', 'video');
                    stage.appendChild(video);
                } else {
                    const image = document.createElement('img');
                    image.src = src;
                    image.alt = alt;
                    image.setAttribute('data-room-gallery-display', '');
                    image.setAttribute('data-media-type', 'image');
                    image.classList.add('room-lightbox-trigger');
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
                const src = thumb.dataset.src || '';
                const alt = thumb.dataset.alt || '';
                const downloadName = thumb.dataset.downloadName || alt || 'room-image';

                lightboxImage.src = src;
                lightboxImage.alt = alt;

                if (lightboxDownload) {
                    lightboxDownload.href = src;
                    lightboxDownload.setAttribute('download', downloadName);
                    lightboxDownload.dataset.filename = downloadName;
                }
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

            if (stage) {
                stage.addEventListener('click', function (event) {
                    const target = event.target;
                    if (!(target instanceof HTMLImageElement)) {
                        return;
                    }

                    const currentSrc = target.getAttribute('src') || '';
                    const imageIndex = imageThumbs.findIndex((thumb) => (thumb.dataset.src || '') === currentSrc);
                    openLightbox(imageIndex >= 0 ? imageIndex : 0);
                });
            }

            if (downloadAllButton) {
                downloadAllButton.addEventListener('click', async function () {
                    if (imageThumbs.length === 0) {
                        return;
                    }

                    const originalLabel = downloadAllButton.textContent;
                    downloadAllButton.disabled = true;
                    downloadAllButton.textContent = 'Đang tải ảnh...';

                    try {
                        for (const thumb of imageThumbs) {
                            await triggerDownload(
                                thumb.dataset.src || '',
                                thumb.dataset.downloadName || thumb.dataset.alt || 'room-image'
                            );
                            await wait(180);
                        }
                    } catch (error) {
                        window.alert('Không thể tải toàn bộ ảnh. Vui lòng thử lại.');
                    } finally {
                        downloadAllButton.disabled = false;
                        downloadAllButton.textContent = originalLabel;
                    }
                });
            }

            if (lightboxDownload) {
                lightboxDownload.addEventListener('click', async function (event) {
                    event.preventDefault();

                    const src = lightboxDownload.getAttribute('href') || '';
                    const filename = lightboxDownload.dataset.filename || 'room-image';
                    if (src === '') {
                        return;
                    }

                    try {
                        await triggerDownload(src, filename);
                    } catch (error) {
                        window.alert('Không thể tải ảnh này. Vui lòng thử lại.');
                    }
                });
            }

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
        })();
    </script>
<?php endif; ?>
