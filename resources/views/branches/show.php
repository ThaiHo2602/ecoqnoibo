<?php
$statusLabels = [
    'chua_lock' => 'Còn trống',
    'dang_giu' => 'Đang giữ',
    'da_lock' => 'Đã thuê',
];
$typeLabels = [
    'duplex' => 'Duplex',
    'studio' => 'Studio',
    'one_bedroom' => 'Studio',
    'two_bedroom' => 'Studio',
    'kiot' => 'Kiot',
];
$furnitureLabels = [
    'co_noi_that' => 'Đầy đủ',
    'khong_noi_that' => 'Không nội thất',
];
$windowLabels = [
    'cua_so_troi' => 'Cửa sổ trời',
    'cua_so_hanh_lang' => 'Cửa sổ hành lang',
    'cua_so_gieng_troi' => 'Cửa sổ giếng trời',
];
$formatMoney = static fn (mixed $value): string => (float) $value > 0 ? number_format((float) $value, 0, ',', '.') . 'đ' : 'Chưa cập nhật';
$safeFileName = static fn (string $value): string => trim((string) preg_replace('/[^A-Za-z0-9_-]+/', '-', $value), '-') ?: 'ecoq';
$availableRooms = count(array_filter($rooms, static fn (array $room): bool => $room['status'] === 'chua_lock'));
$roomPayloads = [];
foreach ($rooms as $room) {
    $mediaItems = [];
    foreach ($roomMediaMap[$room['id']] ?? [] as $index => $media) {
        $extension = pathinfo((string) $media['file_name'], PATHINFO_EXTENSION) ?: pathinfo((string) $media['file_path'], PATHINFO_EXTENSION);
        $downloadName = $safeFileName($branch['name'] . '_' . $room['room_number'] . '_' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) . ($extension ? '.' . $extension : '');
        $mediaItems[] = [
            'id' => (int) $media['id'],
            'media_type' => $media['media_type'],
            'file_name' => $media['file_name'],
            'url' => media_url($media['file_path']),
            'download_name' => $downloadName,
        ];
    }

    $roomPayloads[] = [
        'id' => (int) $room['id'],
        'room_number' => $room['room_number'],
        'price' => (float) $room['price'],
        'price_display' => $formatMoney($room['price']),
        'status' => $room['status'],
        'status_label' => $statusLabels[$room['status']] ?? $room['status'],
        'room_type_label' => $typeLabels[$room['room_type']] ?? $room['room_type'],
        'window_label' => $windowLabels[$room['window_type']] ?? $room['window_type'],
        'furniture_label' => $furnitureLabels[$room['furniture_status']] ?? $room['furniture_status'],
        'has_balcony' => (int) $room['has_balcony'],
        'note' => $room['note'] ?: 'Phòng hiện chưa có mô tả chi tiết.',
        'media' => $mediaItems,
    ];
}
?>

<section class="branch-detail-page branch-detail-modern" data-branch-detail data-mode="internal">
    <div class="branch-detail-topline">
        <div class="branch-breadcrumb">
            <a href="<?= e(url('/app')) ?>">Trang chủ</a>
            <span>/</span>
            <span><?= e($branch['district_name']) ?></span>
            <span>/</span>
            <strong><?= e($branch['name']) ?></strong>
        </div>
    </div>

    <header class="branch-detail-heading">
        <div>
            <div class="branch-title-row">
                <h1><?= e($branch['name']) ?></h1>
                <span class="branch-badge verified">Đã xác thực</span>
                <?php if ($availableRooms > 0): ?><span class="branch-badge hot">HOT</span><?php endif; ?>
                <?php if ($availableRooms > 0 && $availableRooms <= 2): ?><span class="branch-badge warning">Sắp hết</span><?php endif; ?>
            </div>
            <p class="branch-address-line">📍 <?= e($branch['name']) ?>, <?= e($branch['district_name']) ?></p>
        </div>
    </header>

    <section class="branch-detail-main-grid">
        <div class="branch-detail-main">
            <div class="branch-gallery-card">
                <div class="branch-gallery-stage modern" data-gallery-stage>
                    <div class="room-no-media">Chưa có ảnh</div>
                </div>
                <button type="button" class="branch-gallery-arrow prev" data-gallery-prev aria-label="Ảnh trước">‹</button>
                <button type="button" class="branch-gallery-arrow next" data-gallery-next aria-label="Ảnh tiếp theo">›</button>
                <div class="branch-gallery-counter" data-gallery-counter>0 / 0</div>
            </div>
            <div class="branch-gallery-thumbs modern" data-gallery-thumbs></div>

            <section class="branch-room-strip-card">
                <div class="branch-section-head">
                    <h2>Danh sách phòng trong chi nhánh <span>(<?= e((string) count($rooms)) ?> phòng)</span></h2>
                    <div class="status-legend">
                        <span><i class="dot green"></i>Còn trống</span>
                        <span><i class="dot yellow"></i>Đang giữ</span>
                        <span><i class="dot red"></i>Đã thuê</span>
                    </div>
                </div>
                <div class="branch-room-strip" data-room-list></div>
            </section>

            <section class="branch-info-card">
                <h2>A. Thông tin chi nhánh</h2>
                <div class="branch-info-grid">
                    <div><span>Tên chi nhánh</span><strong><?= e($branch['name']) ?></strong></div>
                    <div><span>Tổng số phòng</span><strong><?= e((string) count($rooms)) ?> phòng</strong></div>
                    <div><span>Phòng trống</span><strong><?= e((string) $availableRooms) ?> phòng</strong></div>
                    <div><span>Giờ giấc</span><strong>Tự do</strong></div>
                </div>
            </section>

            <section class="branch-info-card">
                <h2>B. Thông tin phòng đang chọn <span data-selected-room-heading></span></h2>
                <div class="selected-room-spec-grid" data-selected-room-specs></div>
                <p class="selected-room-description" data-selected-room-note></p>
            </section>

            <section class="branch-info-card">
                <h2>C. Chi phí dịch vụ thuộc chi nhánh</h2>
                <div class="branch-fee-row">
                    <div><span>Giá điện</span><strong><?= e($formatMoney($branch['electricity_price'] ?? 0)) ?></strong></div>
                    <div><span>Giá nước</span><strong><?= e($formatMoney($branch['water_price'] ?? 0)) ?></strong></div>
                    <div><span>Phí gửi xe</span><strong><?= e($formatMoney($branch['parking_price'] ?? 0)) ?></strong></div>
                    <div><span>Phí dịch vụ</span><strong><?= e($formatMoney($branch['service_price'] ?? 0)) ?></strong></div>
                </div>
                <div class="branch-fee-note">Các chi phí này áp dụng chung cho tất cả các phòng trong chi nhánh.</div>
            </section>
        </div>

        <aside class="branch-side-panel internal">
            <div class="side-label">Công cụ nội bộ</div>
            <h2 data-side-room-title>Phòng đang chọn</h2>
            <div class="side-price" data-side-room-price></div>
            <div data-side-room-status></div>
            <button type="button" class="btn btn-primary w-100 mt-3" data-download-all-room-images>Tải toàn bộ ảnh phòng đang chọn</button>
            <button type="button" class="btn btn-outline-secondary w-100 mt-2" data-open-current-image>Phóng to ảnh đang xem</button>
            <?php if ($canRequestLock): ?>
                <form method="POST" action="<?= e(url('/lock-requests/store')) ?>" class="d-grid gap-3 mt-3" data-selected-room-lock-form>
                    <input type="hidden" name="room_id" value="<?= e((string) $selectedRoomId) ?>" data-selected-room-id-input>
                    <input type="hidden" name="return_to" value="<?= e(current_path()) ?>">
                    <textarea name="request_note" class="form-control" rows="3" placeholder="Ghi chú khi gửi yêu cầu lock"></textarea>
                    <button type="submit" class="btn btn-warning" data-selected-room-lock-button>Gửi yêu cầu lock</button>
                </form>
            <?php endif; ?>
            <div class="side-verified-note">Dữ liệu chi nhánh và phòng được lấy từ hệ thống nội bộ.</div>
        </aside>
    </section>

    <div class="media-lightbox" data-branch-lightbox hidden>
        <button type="button" class="media-lightbox-close" data-lightbox-close aria-label="Đóng">×</button>
        <button type="button" class="media-lightbox-nav prev" data-lightbox-prev aria-label="Ảnh trước">‹</button>
        <div class="media-lightbox-dialog">
            <a href="#" class="media-lightbox-download-icon" data-lightbox-download download title="Tải ảnh này">↓</a>
            <img src="" alt="" class="media-lightbox-image" data-lightbox-image>
            <div class="branch-gallery-counter lightbox-count" data-lightbox-counter>0 / 0</div>
        </div>
        <button type="button" class="media-lightbox-nav next" data-lightbox-next aria-label="Ảnh tiếp theo">›</button>
    </div>
</section>

<script>
    (function initBranchDetail() {
        const rooms = <?= json_encode($roomPayloads, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        let selectedRoom = rooms.find((room) => Number(room.id) === <?= json_encode((int) $selectedRoomId) ?>) || rooms[0] || null;
        let currentImageIndex = 0;
        const list = document.querySelector('[data-room-list]');
        const stage = document.querySelector('[data-gallery-stage]');
        const thumbs = document.querySelector('[data-gallery-thumbs]');
        const counter = document.querySelector('[data-gallery-counter]');
        const sideTitle = document.querySelector('[data-side-room-title]');
        const sidePrice = document.querySelector('[data-side-room-price]');
        const sideStatus = document.querySelector('[data-side-room-status]');
        const specs = document.querySelector('[data-selected-room-specs]');
        const note = document.querySelector('[data-selected-room-note]');
        const heading = document.querySelector('[data-selected-room-heading]');
        const roomInput = document.querySelector('[data-selected-room-id-input]');
        const lockButton = document.querySelector('[data-selected-room-lock-button]');
        const downloadAll = document.querySelector('[data-download-all-room-images]');
        const openCurrent = document.querySelector('[data-open-current-image]');
        const lightbox = document.querySelector('[data-branch-lightbox]');
        const lightboxImage = document.querySelector('[data-lightbox-image]');
        const lightboxDownload = document.querySelector('[data-lightbox-download]');
        const lightboxCounter = document.querySelector('[data-lightbox-counter]');

        const escapeHtml = (value) => String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        const statusClass = (status) => status === 'chua_lock' ? 'green' : (status === 'dang_giu' ? 'yellow' : 'red');

        function renderRoomCards() {
            list.innerHTML = rooms.map((room) => `
                <button type="button" class="branch-room-card ${Number(room.id) === Number(selectedRoom?.id) ? 'is-active' : ''}" data-room-id="${room.id}">
                    <strong>${escapeHtml(room.room_number)}</strong>
                    <span class="room-status ${statusClass(room.status)}">${escapeHtml(room.status_label)}</span>
                    <em>${escapeHtml(room.price_display)}/tháng</em>
                </button>
            `).join('');
            list.querySelectorAll('[data-room-id]').forEach((button) => {
                button.addEventListener('click', () => renderSelectedRoom(Number(button.dataset.roomId)));
            });
        }

        function renderGallery() {
            const media = selectedRoom?.media || [];
            const active = media[currentImageIndex] || media[0] || null;
            counter.textContent = media.length ? `${currentImageIndex + 1} / ${media.length}` : '0 / 0';
            if (!active) {
                stage.innerHTML = '<div class="room-no-media">Chưa có ảnh</div>';
                thumbs.innerHTML = '';
                return;
            }
            stage.innerHTML = active.media_type === 'image'
                ? `<img src="${escapeHtml(active.url)}" alt="${escapeHtml(active.file_name || selectedRoom.room_number)}" data-stage-media>`
                : `<video src="${escapeHtml(active.url)}" controls preload="metadata"></video>`;
            const stageMedia = stage.querySelector('[data-stage-media]');
            if (stageMedia) stageMedia.addEventListener('click', () => openLightbox(currentImageIndex));
            thumbs.innerHTML = media.map((item, index) => {
                const preview = item.media_type === 'image'
                    ? `<img src="${escapeHtml(item.url)}" alt="${escapeHtml(item.file_name || selectedRoom.room_number)}">`
                    : `<video src="${escapeHtml(item.url)}" muted preload="metadata"></video>`;
                return `<button type="button" class="branch-gallery-thumb ${index === currentImageIndex ? 'is-active' : ''}" data-thumb-index="${index}">${preview}</button>`;
            }).join('');
            thumbs.querySelectorAll('[data-thumb-index]').forEach((button) => {
                button.addEventListener('click', () => {
                    currentImageIndex = Number(button.dataset.thumbIndex || 0);
                    renderGallery();
                });
                button.addEventListener('dblclick', () => openLightbox(Number(button.dataset.thumbIndex || 0)));
            });
        }

        function renderSelectedRoom(roomId) {
            selectedRoom = rooms.find((room) => Number(room.id) === Number(roomId)) || selectedRoom;
            currentImageIndex = 0;
            renderRoomCards();
            renderGallery();
            sideTitle.textContent = selectedRoom ? `Phòng ${selectedRoom.room_number}` : 'Chưa chọn phòng';
            sidePrice.textContent = selectedRoom ? `${selectedRoom.price_display}/tháng` : '';
            sideStatus.innerHTML = selectedRoom ? `<span class="room-status ${statusClass(selectedRoom.status)}">${escapeHtml(selectedRoom.status_label)}</span>` : '';
            heading.textContent = selectedRoom ? `(Phòng ${selectedRoom.room_number})` : '';
            note.textContent = selectedRoom?.note || '';
            specs.innerHTML = selectedRoom ? [
                ['Giá phòng', `${selectedRoom.price_display}/tháng`],
                ['Loại phòng', selectedRoom.room_type_label],
                ['Nội thất', selectedRoom.furniture_label],
                ['Ban công', Number(selectedRoom.has_balcony) === 1 ? 'Có' : 'Không'],
                ['Loại cửa sổ', selectedRoom.window_label],
                ['Tình trạng', selectedRoom.status_label],
            ].map(([label, value]) => `<div><span>${escapeHtml(label)}</span><strong>${escapeHtml(value)}</strong></div>`).join('') : '';
            if (roomInput && selectedRoom) roomInput.value = selectedRoom.id;
            if (lockButton && selectedRoom) {
                lockButton.disabled = selectedRoom.status !== 'chua_lock';
                lockButton.textContent = selectedRoom.status === 'chua_lock' ? 'Gửi yêu cầu lock' : 'Phòng này chưa thể lock';
            }
        }

        function downloadAllRoomImages() {
            const images = (selectedRoom?.media || []).filter((item) => item.media_type === 'image');
            if (!images.length) {
                alert('Phòng đang chọn chưa có ảnh để tải.');
                return;
            }

            images.forEach((item, index) => {
                window.setTimeout(() => {
                    const link = document.createElement('a');
                    link.href = item.url;
                    link.download = item.download_name || `room-image-${index + 1}.jpg`;
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                }, index * 250);
            });
        }

        function shiftImage(delta) {
            const total = selectedRoom?.media?.length || 0;
            if (!total) return;
            currentImageIndex = (currentImageIndex + delta + total) % total;
            renderGallery();
        }

        function openLightbox(index) {
            const media = selectedRoom?.media || [];
            if (!media.length) return;
            currentImageIndex = index;
            const active = media[currentImageIndex];
            if (active.media_type !== 'image') return;
            lightboxImage.src = active.url;
            lightboxImage.alt = active.file_name || selectedRoom.room_number;
            lightboxDownload.href = active.url;
            lightboxDownload.setAttribute('download', active.download_name || 'room-image.jpg');
            lightboxCounter.textContent = `${currentImageIndex + 1} / ${media.length}`;
            lightbox.hidden = false;
            document.body.classList.add('lightbox-open');
        }

        document.querySelector('[data-gallery-prev]').addEventListener('click', () => shiftImage(-1));
        document.querySelector('[data-gallery-next]').addEventListener('click', () => shiftImage(1));
        downloadAll?.addEventListener('click', downloadAllRoomImages);
        openCurrent?.addEventListener('click', () => openLightbox(currentImageIndex));
        document.querySelector('[data-lightbox-close]').addEventListener('click', () => {
            lightbox.hidden = true;
            document.body.classList.remove('lightbox-open');
        });
        document.querySelector('[data-lightbox-prev]').addEventListener('click', () => { shiftImage(-1); openLightbox(currentImageIndex); });
        document.querySelector('[data-lightbox-next]').addEventListener('click', () => { shiftImage(1); openLightbox(currentImageIndex); });

        renderSelectedRoom(selectedRoom?.id);
    })();
</script>
