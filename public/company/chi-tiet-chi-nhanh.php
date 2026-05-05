<?php
require_once __DIR__ . '/includes/projects-data.php';

$branchId = (int) ($_GET['id'] ?? 0);
$branch = fetch_public_branch_detail($branchId);

if (! $branch) {
    $pageTitle = 'Không tìm thấy chi nhánh - Eco-Q House';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="page-hero page-hero-light">
        <div class="container">
            <h1>Không tìm thấy chi nhánh</h1>
            <p>Chi nhánh này không tồn tại hoặc chưa sẵn sàng hiển thị.</p>
            <a href="<?= htmlspecialchars(base_url('du-an.php')) ?>" class="btn btn-brand mt-3">Quay lại trang dự án</a>
        </div>
    </section>
    <?php require_once __DIR__ . '/includes/footer.php'; return; ?>
<?php
}

$rooms = fetch_public_branch_rooms((int) $branch['id']);
$roomIds = array_map(static fn (array $room): int => (int) $room['id'], $rooms);
$roomMediaMap = fetch_room_media_map($roomIds);
$selectedRoomId = (int) ($rooms[0]['id'] ?? 0);
$availableRooms = count($rooms);
$formatMoney = static fn (mixed $value): string => (float) $value > 0 ? number_format((float) $value, 0, ',', '.') . 'đ' : 'Chưa cập nhật';
$roomPayloads = [];
foreach ($rooms as $room) {
    $mediaItems = [];
    foreach ($roomMediaMap[$room['id']] ?? [] as $media) {
        $mediaItems[] = [
            'media_type' => $media['media_type'],
            'file_name' => $media['file_name'],
            'url' => room_media_url($media['file_path']),
        ];
    }

    $roomPayloads[] = [
        'id' => (int) $room['id'],
        'room_number' => $room['room_number'],
        'price_display' => $formatMoney($room['price']),
        'status' => $room['status'],
        'status_label' => 'Còn trống',
        'room_type_label' => room_type_label($room['room_type']),
        'area_label' => 'Chưa cập nhật',
        'furniture_label' => furniture_status_label($room['furniture_status']),
        'direction_label' => window_type_label($room['window_type']),
        'has_balcony' => (int) $room['has_balcony'],
        'note' => $room['note'] ?: 'Phòng rộng rãi, thoáng mát, phù hợp khách cần không gian sống tiện nghi và d�
 di chuyển.',
        'media' => $mediaItems,
    ];
}

$pageTitle = $branch['name'] . ' - Eco-Q House';
$pageDescription = 'Chi tiết chi nhánh ' . $branch['name'] . ' và danh sách phòng còn trống.';
$currentPage = 'projects';

require_once __DIR__ . '/includes/header.php';
?>

<main class="branch-public-page">
    <section class="branch-public-shell">
        <div class="branch-public-crumb">
            <a href="<?= htmlspecialchars(base_url('du-an.php')) ?>">Cho thuê</a>
            <span>›</span>
            <span>Tp. Hồ Chí Minh</span>
            <span>›</span>
            <span><?= htmlspecialchars($branch['district_name']) ?></span>
            <span>›</span>
            <strong><?= htmlspecialchars($branch['name']) ?></strong>
        </div>

        <header class="branch-public-header">
            <div>
                <div class="branch-public-title-row">
                    <h1><?= htmlspecialchars($branch['name']) ?></h1>
                    <span class="public-badge verified">Đã xác thực</span>
                    <?php if ($availableRooms > 0): ?><span class="public-badge hot">HOT</span><?php endif; ?>
                    <?php if ($availableRooms > 0 && $availableRooms <= 2): ?><span class="public-badge warning">Sắp hết</span><?php endif; ?>
                </div>
                <p>📍 <?= htmlspecialchars($branch['name']) ?>, <?= htmlspecialchars($branch['district_name']) ?>, Thành phố Hồ Chí Minh</p>
            </div>
            <div class="branch-public-actions">
                <button type="button" class="btn btn-outline-brand">♡ Yêu thích</button>
                <button type="button" class="btn btn-outline-brand">⌯ Chia sẻ</button>
                <a href="<?= htmlspecialchars(base_url('lien-he.php')) ?>" class="btn btn-brand">Đặt lịch xem phòng</a>
            </div>
        </header>

        <section class="branch-public-gallery-wrap">
            <div class="branch-public-stage hero" data-public-gallery-stage>
                <div class="project-card-placeholder">Chưa có ảnh</div>
            </div>
            <button type="button" class="branch-public-arrow prev" data-public-gallery-prev aria-label="Ảnh trước">‹</button>
            <button type="button" class="branch-public-arrow next" data-public-gallery-next aria-label="Ảnh tiếp theo">›</button>
            <div class="branch-public-counter" data-public-gallery-counter>0 / 0</div>
        </section>
        <div class="branch-public-thumbs modern" data-public-gallery-thumbs></div>

        <section class="public-room-list-card">
            <div class="public-room-list-head">
                <h2>Danh sách phòng trong chi nhánh <span>(<?= htmlspecialchars((string) count($rooms)) ?> phòng)</span></h2>
                <div class="public-status-legend">
                    <span><i class="dot green"></i>Còn trống</span>
                    <span><i class="dot yellow"></i>Đang giữ</span>
                    <span><i class="dot red"></i>Đã thuê</span>
                </div>
            </div>
            <div class="branch-public-room-strip" data-public-room-list></div>
        </section>

        <section class="branch-public-content-grid">
            <div class="branch-public-content-main">
                <section class="public-info-card">
                    <h2>A. Thông tin chi nhánh</h2>
                    <div class="public-info-grid">
                        <div><span>Tên chi nhánh</span><strong><?= htmlspecialchars($branch['name']) ?></strong></div>
                        <div><span>Tổng số phòng</span><strong><?= htmlspecialchars((string) count($rooms)) ?> phòng</strong></div>
                        <div><span>Phòng trống</span><strong><?= htmlspecialchars((string) $availableRooms) ?> phòng</strong></div>
                        <div><span>Giờ giấc</span><strong>Tự do</strong></div>
                        <div class="wide"><span>Mô tả chi nhánh</span><strong>Tòa nhà mới, không gian sạch sẽ, thuận tiện di chuyển và phù hợp khách cần xem nhiều lựa chọn trong cùng một chi nhánh.</strong></div>
                    </div>
                </section>

                <section class="public-info-card">
                    <h2>B. Thông tin phòng đang chọn <span data-public-selected-heading></span></h2>
                    <div class="public-selected-specs" data-public-room-specs></div>
                    <p class="public-selected-note" data-public-room-note></p>
                </section>

                <section class="public-info-card">
                    <h2>C. Chi phí dịch vụ thuộc chi nhánh</h2>
                    <div class="public-fee-row">
                        <div><span>Giá điện</span><strong><?= htmlspecialchars($formatMoney($branch['electricity_price'] ?? 0)) ?></strong></div>
                        <div><span>Giá nước</span><strong><?= htmlspecialchars($formatMoney($branch['water_price'] ?? 0)) ?></strong></div>
                        <div><span>Phí gửi xe</span><strong><?= htmlspecialchars($formatMoney($branch['parking_price'] ?? 0)) ?></strong></div>
                        <div><span>Phí dịch vụ</span><strong><?= htmlspecialchars($formatMoney($branch['service_price'] ?? 0)) ?></strong></div>
                    </div>
                    <div class="public-fee-note">Các chi phí này áp dụng chung cho tất cả các phòng trong chi nhánh.</div>
                </section>
            </div>

            <aside class="public-cta-card">
                <div class="side-label">Phòng đang chọn</div>
                <h2 data-public-side-title>Phòng đang chọn</h2>
                <div class="public-side-status" data-public-side-status></div>
                <div class="public-side-price" data-public-side-price></div>
                <a href="<?= htmlspecialchars(base_url('lien-he.php')) ?>" class="btn btn-brand w-100">Đặt lịch xem phòng</a>
                <div class="public-cta-split">
                    <a href="<?= htmlspecialchars(base_url('lien-he.php')) ?>" class="btn btn-outline-brand">Chat ngay</a>
                    <a href="tel:<?= htmlspecialchars(preg_replace('/\s+/', '', (string) ($branch['manager_phone'] ?: ''))) ?>" class="btn btn-outline-brand">Gọi hỗ trợ</a>
                </div>
                <div class="public-verified-note">Thông tin đã được xác thực bởi đội ngũ hệ thống.</div>
            </aside>
        </section>
    </section>
</main>

<script>
    (function initPublicBranchDetail() {
        const rooms = <?= json_encode($roomPayloads, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        let selectedRoom = rooms.find((room) => Number(room.id) === <?= json_encode($selectedRoomId) ?>) || rooms[0] || null;
        let currentImageIndex = 0;
        const stage = document.querySelector('[data-public-gallery-stage]');
        const thumbs = document.querySelector('[data-public-gallery-thumbs]');
        const counter = document.querySelector('[data-public-gallery-counter]');
        const list = document.querySelector('[data-public-room-list]');
        const sideTitle = document.querySelector('[data-public-side-title]');
        const sidePrice = document.querySelector('[data-public-side-price]');
        const sideStatus = document.querySelector('[data-public-side-status]');
        const heading = document.querySelector('[data-public-selected-heading]');
        const specs = document.querySelector('[data-public-room-specs]');
        const note = document.querySelector('[data-public-room-note]');
        const escapeHtml = (value) => String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');

        function renderRoomCards() {
            list.innerHTML = rooms.map((room) => `
                <button type="button" class="public-room-card ${Number(room.id) === Number(selectedRoom?.id) ? 'is-active' : ''}" data-room-id="${room.id}">
                    <strong>${escapeHtml(room.room_number)}</strong>
                    <span class="public-status green">${escapeHtml(room.status_label)}</span>
                    <em>${escapeHtml(room.price_display)}/tháng</em>
                </button>
            `).join('');
            list.querySelectorAll('[data-room-id]').forEach((button) => button.addEventListener('click', () => renderSelectedRoom(Number(button.dataset.roomId))));
        }

        function renderGallery() {
            const media = selectedRoom?.media || [];
            const active = media[currentImageIndex] || media[0] || null;
            counter.textContent = media.length ? `${currentImageIndex + 1} / ${media.length}` : '0 / 0';
            if (!active) {
                stage.innerHTML = '<div class="project-card-placeholder">Chưa có ảnh</div>';
                thumbs.innerHTML = '';
                return;
            }
            stage.innerHTML = active.media_type === 'image'
                ? `<img src="${escapeHtml(active.url)}" alt="${escapeHtml(active.file_name || selectedRoom.room_number)}">`
                : `<video src="${escapeHtml(active.url)}" controls preload="metadata"></video>`;
            thumbs.innerHTML = media.map((item, index) => {
                const preview = item.media_type === 'image'
                    ? `<img src="${escapeHtml(item.url)}" alt="${escapeHtml(item.file_name || selectedRoom.room_number)}">`
                    : `<video src="${escapeHtml(item.url)}" muted preload="metadata"></video>`;
                return `<button type="button" class="branch-public-thumb ${index === currentImageIndex ? 'is-active' : ''}" data-thumb-index="${index}">${preview}</button>`;
            }).join('');
            thumbs.querySelectorAll('[data-thumb-index]').forEach((button) => {
                button.addEventListener('click', () => {
                    currentImageIndex = Number(button.dataset.thumbIndex || 0);
                    renderGallery();
                });
            });
        }

        function renderSelectedRoom(roomId) {
            selectedRoom = rooms.find((room) => Number(room.id) === Number(roomId)) || selectedRoom;
            currentImageIndex = 0;
            renderRoomCards();
            renderGallery();
            sideTitle.textContent = selectedRoom ? `Phòng ${selectedRoom.room_number}` : 'Chưa chọn phòng';
            sidePrice.textContent = selectedRoom ? `${selectedRoom.price_display}/tháng` : '';
            sideStatus.innerHTML = selectedRoom ? `<span class="public-status green">${escapeHtml(selectedRoom.status_label)}</span>` : '';
            heading.textContent = selectedRoom ? `(Phòng ${selectedRoom.room_number})` : '';
            note.textContent = selectedRoom?.note || '';
            specs.innerHTML = selectedRoom ? [
                ['Giá phòng', `${selectedRoom.price_display}/tháng`],
                ['Diện tích', selectedRoom.area_label],
                ['Loại phòng', selectedRoom.room_type_label],
                ['Nội thất', selectedRoom.furniture_label],
                ['Ban công', Number(selectedRoom.has_balcony) === 1 ? 'Có' : 'Không'],
                ['Hướng phòng', selectedRoom.direction_label],
                ['Tình trạng', selectedRoom.status_label],
            ].map(([label, value]) => `<div><span>${escapeHtml(label)}</span><strong>${escapeHtml(value)}</strong></div>`).join('') : '';
        }

        function shiftImage(delta) {
            const total = selectedRoom?.media?.length || 0;
            if (!total) return;
            currentImageIndex = (currentImageIndex + delta + total) % total;
            renderGallery();
        }

        document.querySelector('[data-public-gallery-prev]').addEventListener('click', () => shiftImage(-1));
        document.querySelector('[data-public-gallery-next]').addEventListener('click', () => shiftImage(1));
        renderSelectedRoom(selectedRoom?.id);
    })();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
