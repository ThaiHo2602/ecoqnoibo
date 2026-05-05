<?php
$roomTypeLabels = [
    'duplex' => 'Duplex',
    'studio' => 'Studio',
    'one_bedroom' => 'Studio',
    'two_bedroom' => 'Studio',
    'kiot' => 'Kiot',
];
$statusLabels = [
    'chua_lock' => 'Chưa lock',
    'dang_giu' => 'Đang giữ',
    'da_lock' => 'Đã lock',
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
$branchesBySystem = [];
foreach ($systems as $system) {
    $branchesBySystem[$system['id']] = [];
}
foreach ($branches as $branch) {
    $branchesBySystem[$branch['system_id']][] = $branch;
}
$lockRequestLabels = [
    'pending' => 'Chờ duyệt',
    'approved' => 'Đã duyệt',
    'rejected' => 'Từ chối',
];
?>

<section class="stats-grid room-stats-grid">
    <article class="stat-card">
        <span class="stat-label">Tổng phòng đang hiển thị</span>
        <strong><?= e((string) $roomStats['total']) ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Chưa lock</span>
        <strong><?= e((string) $roomStats['chua_lock']) ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Đang giữ</span>
        <strong><?= e((string) $roomStats['dang_giu']) ?></strong>
    </article>
    <article class="stat-card highlight">
        <span class="stat-label">Đã lock</span>
        <strong><?= e((string) $roomStats['da_lock']) ?></strong>
    </article>
</section>

<section class="room-filter-section" style="display:block;width:100%;max-width:none;">
    <div class="panel-card listing-filter-panel room-filter-card" style="width:100%;max-width:none;">
        <div class="panel-header">
            <div>
                <h3>Bộ lọc phòng</h3>
                <p class="panel-subtitle mb-0">Lọc theo quận, giá, trạng thái, nội thất, hệ thống và chi nhánh.</p>
            </div>
        </div>

        <form method="GET" action="<?= e(url('/rooms')) ?>" class="d-grid gap-3 room-filter-form" style="width:100%;max-width:none;">
            <div class="room-filter-stack">
                <div>
                    <label class="form-label">Hệ thống</label>
                    <select name="system_id" class="form-select">
                        <option value="0">Tất cả hệ thống</option>
                        <?php foreach ($systems as $system): ?>
                            <option value="<?= e((string) $system['id']) ?>" <?= (int) $filters['system_id'] === (int) $system['id'] ? 'selected' : '' ?>>
                                <?= e($system['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                <label class="form-label">Phường</label>
                    <select name="ward_id" class="form-select">
                        <option value="0">Tất cả phường</option>
                        <?php foreach ($wards as $ward): ?>
                            <option value="<?= e((string) $ward['id']) ?>" <?= (int) $filters['ward_id'] === (int) $ward['id'] ? 'selected' : '' ?>>
                                <?= e($ward['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Chi nhánh</label>
                    <select name="branch_id" class="form-select">
                        <option value="0">Tất cả chi nhánh</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= e((string) $branch['id']) ?>" <?= (int) $filters['branch_id'] === (int) $branch['id'] ? 'selected' : '' ?>>
                                <?= e($branch['system_name'] . ' - ' . $branch['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="room-filter-stack">
                <div>
                    <label class="form-label">Quận</label>
                    <select name="district_id" class="form-select">
                        <option value="0">Tất cả quận</option>
                        <?php foreach ($districts as $district): ?>
                            <option value="<?= e((string) $district['id']) ?>" <?= (int) $filters['district_id'] === (int) $district['id'] ? 'selected' : '' ?>>
                                <?= e($district['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <?php foreach ($statusLabels as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= $filters['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Loại phòng</label>
                    <select name="room_type" class="form-select">
                        <option value="">Tất cả loại phòng</option>
                        <?php foreach ($roomTypeLabels as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= $filters['room_type'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="room-filter-stack">
                <div>
                    <label class="form-label">Nội thất</label>
                    <select name="furniture_status" class="form-select">
                        <option value="">Tất cả</option>
                        <?php foreach ($furnitureLabels as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= $filters['furniture_status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Từ khóa</label>
                    <input type="text" name="keyword" class="form-control" value="<?= e($filters['keyword']) ?>" placeholder="Số phòng, chi nhánh, địa chỉ, ghi chú">
                </div>
            </div>

            <div class="room-filter-stack">
                <div>
                    <label class="form-label">Giá từ</label>
                    <input type="number" name="price_min" class="form-control" value="<?= e($filters['price_min']) ?>" min="0" step="1000">
                </div>
                <div>
                    <label class="form-label">Giá đến</label>
                    <input type="number" name="price_max" class="form-control" value="<?= e($filters['price_max']) ?>" min="0" step="1000">
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap room-filter-actions">
                <button type="submit" class="btn btn-primary">Áp dụng bộ lọc</button>
                <a href="<?= e(url('/rooms')) ?>" class="btn btn-outline-secondary">Xóa lọc</a>
            </div>
        </form>
    </div>

</section>

    <?php if ($canManage): ?>
        <div class="room-form-modal <?= $editRoom ? 'is-open' : '' ?>" data-room-form-modal>
            <button type="button" class="room-form-backdrop" data-room-form-close aria-label="Đóng form phòng"></button>
            <div class="room-form-dialog" role="dialog" aria-modal="true" aria-labelledby="roomFormModalTitle">
            <div class="room-form-header">
                <div>
                    <div class="eyebrow">Quản lý phòng</div>
                    <h3 id="roomFormModalTitle" class="mb-1" data-room-form-title><?= $editRoom ? 'Cập nhật phòng' : 'Thêm phòng mới' ?></h3>
                    <p class="panel-subtitle mb-0">Điền đầy đủ thông tin phòng, upload nhiều ảnh hoặc video trong cùng một lần lưu.</p>
                </div>
                <button type="button" class="btn btn-outline-secondary" data-room-form-close>Đóng</button>
            </div>

            <div class="room-form-content">
            <div id="roomAjaxMessage" class="d-none mb-3"></div>

            <form
                method="POST"
                action="<?= e(url($editRoom ? '/rooms/update' : '/rooms/store')) ?>"
                enctype="multipart/form-data"
                class="d-grid gap-3"
                data-room-form
                data-store-url="<?= e(url('/rooms/store')) ?>"
                data-update-url="<?= e(url('/rooms/update')) ?>"
                data-manage-url-base="<?= e(url('/rooms')) ?>"
            >
                <input type="hidden" name="id" value="<?= e((string) ($editRoom['id'] ?? '')) ?>" data-room-id-input>

                <div>
                    <label class="form-label">Chi nhánh</label>
                    <select name="branch_id" class="form-select" required>
                        <option value="">Chọn chi nhánh</option>
                        <?php foreach ($systems as $system): ?>
                            <?php if (empty($branchesBySystem[$system['id']])) continue; ?>
                            <optgroup label="<?= e($system['name']) ?>">
                                <?php foreach ($branchesBySystem[$system['id']] as $branch): ?>
                                    <option value="<?= e((string) $branch['id']) ?>" <?= (int) ($editRoom['branch_id'] ?? $filters['branch_id']) === (int) $branch['id'] ? 'selected' : '' ?>>
                                        <?= e($branch['name'] . ' - ' . ($branch['ward_name'] ?: 'Chưa có phường')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid-2">
                    <div>
                        <label class="form-label">Số phòng</label>
                        <input type="text" name="room_number" class="form-control" value="<?= e($editRoom['room_number'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label class="form-label">Giá phòng</label>
                        <input type="number" name="price" class="form-control" value="<?= e((string) ($editRoom['price'] ?? '')) ?>" min="0" step="1000" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label class="form-label">Loại phòng</label>
                        <select name="room_type" class="form-select" required>
                            <option value="">Chọn loại phòng</option>
                            <?php foreach ($roomTypeLabels as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= ($editRoom['room_type'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select" required>
                            <?php foreach ($statusLabels as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= ($editRoom['status'] ?? 'chua_lock') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label class="form-label">Nội thất</label>
                        <select name="furniture_status" class="form-select" required>
                            <option value="">Chọn nội thất</option>
                            <?php foreach ($furnitureLabels as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= ($editRoom['furniture_status'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Loại cửa sổ</label>
                        <select name="window_type" class="form-select" required>
                            <option value="">Chọn loại cửa sổ</option>
                            <?php foreach ($windowLabels as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= ($editRoom['window_type'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="hasBalcony" name="has_balcony" <?= (int) ($editRoom['has_balcony'] ?? 0) === 1 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="hasBalcony">Có ban công</label>
                </div>

                <?php if ($canFeaturePublic): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="isPublicVisible" name="is_public_visible" <?= (int) ($editRoom['is_public_visible'] ?? 0) === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isPublicVisible">Hiển thị ngoài landing page và trang dự án</label>
                    </div>
                <?php elseif ($editRoom): ?>
                    <div class="small text-muted">
                        Trạng thái hiển thị public: <?= (int) ($editRoom['is_public_visible'] ?? 0) === 1 ? 'Đang hiển thị' : 'Chưa hiển thị' ?>. Chỉ giám đốc mới thay đổi được mục này.
                    </div>
                <?php endif; ?>

                <div>
                    <label class="form-label">Ảnh và video</label>
                    <input type="file" name="media_files[]" class="form-control" multiple accept="image/*,video/*">
                    <div class="form-text">Chấp nhận upload nhiều file cùng lúc. Ảnh: JPG, PNG, WEBP, GIF. Video: MP4, WEBM, OGG, MOV.</div>
                </div>

                <div data-existing-media-wrapper <?= (! $editRoom || ! $editRoomMedia) ? 'class="d-none"' : '' ?>>
                    <label class="form-label">Media hiện có</label>
                    <div class="media-grid" data-existing-media-list>
                        <?php foreach ($editRoomMedia as $media): ?>
                            <label class="media-card">
                                <?php if ($media['media_type'] === 'image'): ?>
                                    <img src="<?= e(media_url($media['file_path'])) ?>" alt="<?= e($media['file_name']) ?>">
                                <?php else: ?>
                                    <video src="<?= e(media_url($media['file_path'])) ?>" controls preload="metadata"></video>
                                <?php endif; ?>
                                <span><?= e($media['file_name']) ?></span>
                                <span class="small text-muted"><?= e(strtoupper($media['media_type'])) ?></span>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="delete_media_ids[]" value="<?= e((string) $media['id']) ?>" id="mediaDelete<?= e((string) $media['id']) ?>">
                                    <label class="form-check-label" for="mediaDelete<?= e((string) $media['id']) ?>">Xóa media này</label>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <label class="form-label">Ghi chú tổng</label>
                    <textarea name="note" class="form-control" rows="4" placeholder="Nhập ghi chú tổng cho phòng"><?= e($editRoom['note'] ?? '') ?></textarea>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary" data-room-submit-button><?= $editRoom ? 'Lưu cập nhật phòng' : 'Thêm phòng' ?></button>
                    <button type="button" class="btn btn-outline-secondary <?= $editRoom ? '' : 'd-none' ?>" data-room-form-reset>Bỏ chỉnh sửa</button>
                </div>
            </form>
            </div>
            </div>
        </div>
    <?php else: ?>
        <div class="panel-card">
            <div class="panel-header">
                <div>
                    <h3>Góc thao tác nhanh cho nhân viên</h3>
                    <p class="panel-subtitle mb-0">Bấm `Xem chi tiết` trên từng phòng để mở popup đầy đủ hình ảnh, video, chi phí và gửi yêu cầu lock.</p>
                </div>
            </div>
            <div class="empty-state">Quy trình hiện tại: `Xem chi tiết` -> `Gửi yêu cầu lock` -> phòng sang `Đang giữ` -> quản lý/giám đốc vào trang `Yêu cầu lock` để duyệt.</div>
        </div>
    <?php endif; ?>

<section class="panel-card mt-4">
    <div class="panel-header">
        <div>
            <h3>Danh sách phòng</h3>
            <p class="panel-subtitle mb-0">Hiển thị dạng list cho khu vực quản trị, chưa show hình ở list để quản lý thao tác nhanh.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <?php if ($canManage): ?>
                <button type="button" class="btn btn-primary" data-room-open-create>Thêm phòng</button>
            <?php endif; ?>
            <span class="badge text-bg-light" data-room-count-badge><?= e((string) count($rooms)) ?> phòng</span>
        </div>
    </div>

    <div class="table-responsive">
        <?php if ($canManage): ?>
            <form id="roomsBulkDeleteForm" method="POST" action="<?= e(url('/rooms/bulk-delete')) ?>" class="mb-3" onsubmit="return confirm('Bạn chắc chắn muốn xóa các phòng đã chọn?');">
                <button type="submit" class="btn btn-outline-danger btn-sm">Xóa phòng đã chọn</button>
            </form>
        <?php endif; ?>
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <?php if ($canManage): ?>
                        <th></th>
                    <?php endif; ?>
                    <th>Số phòng</th>
                    <th>Chi nhánh</th>
                    <th>Quận</th>
                    <th>Giá</th>
                    <th>Loại phòng</th>
                    <th>Trạng thái</th>
                    <th>Nội thất</th>
                    <th>Public</th>
                    <th>Media</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody data-room-table-body>
                <?php if (! $rooms): ?>
                    <tr data-room-empty-row>
                        <td colspan="<?= $canManage ? '11' : '10' ?>">
                            <div class="empty-state my-3">Chưa có phòng nào phù hợp với bộ lọc hiện tại.</div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($rooms as $room): ?>
                    <tr data-room-row-id="<?= e((string) $room['id']) ?>">
                        <?php if ($canManage): ?>
                            <td><input class="form-check-input" type="checkbox" name="ids[]" value="<?= e((string) $room['id']) ?>" form="roomsBulkDeleteForm"></td>
                        <?php endif; ?>
                        <td>
                            <div class="fw-semibold"><?= e($room['room_number']) ?></div>
                            <div class="text-muted small"><?= e($windowLabels[$room['window_type']] ?? $room['window_type']) ?></div>
                        </td>
                        <td>
                            <div><?= e($room['branch_name']) ?></div>
                            <div class="text-muted small"><?= e($room['system_name']) ?></div>
                        </td>
                        <td><?= e($room['district_name']) ?></td>
                        <td><?= e(number_format((float) $room['price'], 0, ',', '.')) ?> đ</td>
                        <td><?= e($roomTypeLabels[$room['room_type']] ?? $room['room_type']) ?></td>
                        <td><span class="status-pill status-<?= e($room['status']) ?>"><?= e($statusLabels[$room['status']] ?? $room['status']) ?></span></td>
                        <td><?= e($furnitureLabels[$room['furniture_status']] ?? $room['furniture_status']) ?></td>
                        <td>
                            <?php if ((int) ($room['is_public_visible'] ?? 0) === 1): ?>
                                <span class="badge rounded-pill text-bg-success">Đang hiển thị</span>
                            <?php else: ?>
                                <span class="badge rounded-pill text-bg-light">Ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e((string) $room['media_count']) ?></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#roomDetailModal<?= e((string) $room['id']) ?>"
                                >
                                    Xem chi tiết
                                </button>
                                <?php if ($canManage): ?>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-room-edit-button
                                        data-room-id="<?= e((string) $room['id']) ?>"
                                    >
                                        Sửa
                                    </button>
                                    <form method="POST" action="<?= e(url('/rooms/delete')) ?>" data-room-delete-form>
                                        <input type="hidden" name="id" value="<?= e((string) $room['id']) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted small">Không có thao tác</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php foreach ($rooms as $room): ?>
    <?php
    $roomMedia = $roomMediaMap[$room['id']] ?? [];
    $latestLock = $latestLockMap[$room['id']] ?? null;
    ?>
    <div class="modal fade" id="roomDetailModal<?= e((string) $room['id']) ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content room-modal">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <div class="eyebrow">Chi tiết phòng</div>
                        <h2 class="modal-title h4 mt-2 mb-1">Phòng <?= e($room['room_number']) ?> - <?= e($room['branch_name']) ?></h2>
                        <div class="text-muted"><?= e($room['system_name'] . ' - ' . $room['district_name']) ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="room-modal-grid">
                        <div>
                            <div class="modal-media-panel">
                                <div class="panel-header mb-3">
                                    <div>
                                        <h3>Hình ảnh và video</h3>
                                        <p class="panel-subtitle mb-0">Media của phòng này được hiển thị ngay trong popup.</p>
                                    </div>
                                </div>

                                <?php if ($roomMedia): ?>
                                    <div class="modal-media-grid">
                                        <?php foreach ($roomMedia as $media): ?>
                                            <div class="modal-media-card">
                                                <?php if ($media['media_type'] === 'image'): ?>
                                                    <img src="<?= e(media_url($media['file_path'])) ?>" alt="<?= e($media['file_name']) ?>">
                                                <?php else: ?>
                                                    <video src="<?= e(media_url($media['file_path'])) ?>" controls preload="metadata"></video>
                                                <?php endif; ?>
                                                <div class="small text-muted mt-2"><?= e($media['file_name']) ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-state">Phòng này chưa có media nào.</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div>
                            <div class="detail-card">
                                <div class="detail-price"><?= e(number_format((float) $room['price'], 0, ',', '.')) ?> VND</div>
                                <div class="d-flex gap-2 flex-wrap mt-3">
                                    <span class="status-pill status-<?= e($room['status']) ?>"><?= e($statusLabels[$room['status']] ?? $room['status']) ?></span>
                                    <span class="detail-tag"><?= e($roomTypeLabels[$room['room_type']] ?? $room['room_type']) ?></span>
                                    <span class="detail-tag"><?= e($furnitureLabels[$room['furniture_status']] ?? $room['furniture_status']) ?></span>
                                    <span class="detail-tag"><?= e($windowLabels[$room['window_type']] ?? $room['window_type']) ?></span>
                                    <span class="detail-tag"><?= (int) $room['has_balcony'] === 1 ? 'Có ban công' : 'Không ban công' ?></span>
                                </div>
                            </div>

                            <div class="detail-card mt-3">
                                <div class="panel-header mb-3">
                                    <div>
                                        <h3>Thông tin chi tiết</h3>
                                        <p class="panel-subtitle mb-0">Tổng hợp đầy đủ các chi phí và đặc điểm phòng.</p>
                                    </div>
                                </div>

                                <div class="detail-list">
                                    <div><span>Hệ thống</span><strong><?= e($room['system_name']) ?></strong></div>
                                    <div><span>Chi nhánh</span><strong><?= e($room['branch_name']) ?></strong></div>
                                    <div><span>Quận</span><strong><?= e($room['district_name']) ?></strong></div>
                                    <div><span>Tiền điện</span><strong><?= e(number_format((float) ($room['electricity_price'] ?? 0), 0, ',', '.')) ?></strong></div>
                                    <div><span>Tiền nước</span><strong><?= e(number_format((float) ($room['water_price'] ?? 0), 0, ',', '.')) ?></strong></div>
                                    <div><span>Phí dịch vụ</span><strong><?= e(number_format((float) ($room['service_price'] ?? 0), 0, ',', '.')) ?></strong></div>
                                    <div><span>Phí gửi xe</span><strong><?= e(number_format((float) ($room['parking_price'] ?? 0), 0, ',', '.')) ?></strong></div>
                                </div>

                                <div class="detail-note mt-3">
                                    <div class="small text-uppercase text-muted fw-semibold mb-2">Ghi chú tổng</div>
                                    <div><?= e($room['note'] ?: 'Chưa có ghi chú cho phòng này.') ?></div>
                                </div>
                            </div>

                            <?php if ($latestLock): ?>
                                <div class="detail-card mt-3">
                                    <div class="panel-header mb-3">
                                        <div>
                                            <h3>Thông tin lock gần nhất</h3>
                                            <p class="panel-subtitle mb-0">Giúp nhân viên và quản lý nhìn nhanh tình hình xử lý.</p>
                                        </div>
                                    </div>

                                    <div class="detail-list">
                                        <div><span>Trạng thái</span><strong><?= e($lockRequestLabels[$latestLock['request_status']] ?? $latestLock['request_status']) ?></strong></div>
                                        <div><span>Người gửi</span><strong><?= e($latestLock['requester_name']) ?></strong></div>
                                        <div><span>Thời gian gửi</span><strong><?= e($latestLock['requested_at']) ?></strong></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($canManage): ?>
                                <div class="d-flex gap-2 flex-wrap mt-3">
                                    <button type="button" class="btn btn-primary" data-room-edit-button data-room-id="<?= e((string) $room['id']) ?>" data-bs-dismiss="modal">Chỉnh sửa phòng này</button>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng popup</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php if ($canManage): ?>
    <script>
        (function initRoomFormPage() {
            const roomForm = document.querySelector('[data-room-form]');
            const formTitle = document.querySelector('[data-room-form-title]');
            const roomIdInput = document.querySelector('[data-room-id-input]');
            const resetButton = document.querySelector('[data-room-form-reset]');
            const submitButton = document.querySelector('[data-room-submit-button]');
            const formModal = document.querySelector('[data-room-form-modal]');
            const openCreateButton = document.querySelector('[data-room-open-create]');
            const closeFormButtons = document.querySelectorAll('[data-room-form-close]');
            const messageBox = document.getElementById('roomAjaxMessage');
            const tableBody = document.querySelector('[data-room-table-body]');
            const countBadge = document.querySelector('[data-room-count-badge]');
            const existingMediaWrapper = document.querySelector('[data-existing-media-wrapper]');
            const existingMediaList = document.querySelector('[data-existing-media-list]');

            if (!roomForm || !tableBody || !roomIdInput) {
                return;
            }

            const storeUrl = roomForm.dataset.storeUrl;
            const updateUrl = roomForm.dataset.updateUrl;
            const manageUrlBase = roomForm.dataset.manageUrlBase;
            const mediaBaseUrl = <?= json_encode(rtrim(url('/'), '/') . '/') ?>;

            const labels = {
                roomType: <?= json_encode($roomTypeLabels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
                status: <?= json_encode($statusLabels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
                furniture: <?= json_encode($furnitureLabels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
                window: <?= json_encode($windowLabels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
            };

            const escapeHtml = function (value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            };

            const formatMoney = function (value) {
                return new Intl.NumberFormat('vi-VN').format(Number(value || 0));
            };

            const mediaUrl = function (path) {
                return mediaBaseUrl + String(path || '').replace(/^\/+/, '');
            };

            const openFormModal = function () {
                if (!formModal) {
                    return;
                }

                formModal.classList.add('is-open');
                document.body.classList.add('room-form-modal-open');
            };

            const closeFormModal = function () {
                if (!formModal) {
                    return;
                }

                formModal.classList.remove('is-open');
                document.body.classList.remove('room-form-modal-open');
            };

            const showMessage = function (type, text) {
                if (!messageBox) {
                    return;
                }
                messageBox.className = 'alert ' + (type === 'success' ? 'alert-success' : 'alert-danger') + ' mb-3';
                messageBox.textContent = text;
            };

            const clearMessage = function () {
                if (!messageBox) {
                    return;
                }
                messageBox.className = 'd-none mb-3';
                messageBox.textContent = '';
            };

            const updateCountBadge = function () {
                if (!countBadge) {
                    return;
                }
                const totalRows = tableBody.querySelectorAll('tr[data-room-row-id]').length;
                countBadge.textContent = totalRows + ' phòng';
            };

            const renderMediaEditor = function (mediaItems) {
                if (!existingMediaWrapper || !existingMediaList) {
                    return;
                }

                if (!Array.isArray(mediaItems) || mediaItems.length === 0) {
                    existingMediaWrapper.classList.add('d-none');
                    existingMediaList.innerHTML = '';
                    return;
                }

                existingMediaWrapper.classList.remove('d-none');
                existingMediaList.innerHTML = mediaItems.map(function (media) {
                    const preview = media.media_type === 'image'
                        ? '<img src="' + escapeHtml(mediaUrl(media.file_path)) + '" alt="' + escapeHtml(media.file_name) + '">'
                        : '<video src="' + escapeHtml(mediaUrl(media.file_path)) + '" controls preload="metadata"></video>';

                    return '<label class="media-card">'
                        + preview
                        + '<span>' + escapeHtml(media.file_name) + '</span>'
                        + '<span class="small text-muted">' + escapeHtml(String(media.media_type || '').toUpperCase()) + '</span>'
                        + '<div class="form-check mt-2">'
                        + '<input class="form-check-input" type="checkbox" name="delete_media_ids[]" value="' + escapeHtml(media.id) + '" id="mediaDeleteDynamic' + escapeHtml(media.id) + '">'
                        + '<label class="form-check-label" for="mediaDeleteDynamic' + escapeHtml(media.id) + '">Xóa media này</label>'
                        + '</div>'
                        + '</label>';
                }).join('');
            };

            const setFormMode = function (mode) {
                if (mode === 'edit') {
                    roomForm.action = updateUrl;
                    if (submitButton) {
                        submitButton.textContent = 'Lưu cập nhật phòng';
                    }
                    if (formTitle) {
                        formTitle.textContent = 'Cập nhật phòng';
                    }
                    if (resetButton) {
                        resetButton.classList.remove('d-none');
                    }
                    openFormModal();
                    return;
                }

                roomForm.action = storeUrl;
                if (submitButton) {
                    submitButton.textContent = 'Thêm phòng';
                }
                if (formTitle) {
                    formTitle.textContent = 'Thêm phòng mới';
                }
                if (resetButton) {
                    resetButton.classList.add('d-none');
                }
            };

            const resetForm = function () {
                roomForm.reset();
                roomIdInput.value = '';
                setFormMode('create');
                renderMediaEditor([]);
                closeFormModal();
            };

            const renderRoomRow = function (room) {
                const publicBadge = Number(room.is_public_visible) === 1
                    ? '<span class="badge rounded-pill text-bg-success">Đang hiển thị</span>'
                    : '<span class="badge rounded-pill text-bg-light">Ẩn</span>';
                const detailButton = '<button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#roomDetailModal' + escapeHtml(room.id) + '">Xem chi tiết</button>';

                return '<tr data-room-row-id="' + escapeHtml(room.id) + '">'
                    + '<td><input class="form-check-input" type="checkbox" name="ids[]" value="' + escapeHtml(room.id) + '" form="roomsBulkDeleteForm"></td>'
                    + '<td><div class="fw-semibold">' + escapeHtml(room.room_number) + '</div><div class="text-muted small">' + escapeHtml(labels.window[room.window_type] || room.window_type) + '</div></td>'
                    + '<td><div>' + escapeHtml(room.branch_name) + '</div><div class="text-muted small">' + escapeHtml(room.system_name) + '</div></td>'
                    + '<td>' + escapeHtml(room.district_name) + '</td>'
                    + '<td>' + escapeHtml(formatMoney(room.price)) + ' đ</td>'
                    + '<td>' + escapeHtml(labels.roomType[room.room_type] || room.room_type) + '</td>'
                    + '<td><span class="status-pill status-' + escapeHtml(room.status) + '">' + escapeHtml(labels.status[room.status] || room.status) + '</span></td>'
                    + '<td>' + escapeHtml(labels.furniture[room.furniture_status] || room.furniture_status) + '</td>'
                    + '<td>' + publicBadge + '</td>'
                    + '<td>' + escapeHtml(room.media_count) + '</td>'
                    + '<td class="text-end"><div class="d-inline-flex gap-2 flex-wrap justify-content-end">'
                    + detailButton
                    + '<button type="button" class="btn btn-sm btn-outline-primary" data-room-edit-button data-room-id="' + escapeHtml(room.id) + '">Sửa</button>'
                    + '<form method="POST" action="<?= e(url('/rooms/delete')) ?>" data-room-delete-form>'
                    + '<input type="hidden" name="_csrf_token" value="' + escapeHtml(window.appCsrfToken || '') + '">'
                    + '<input type="hidden" name="id" value="' + escapeHtml(room.id) + '">'
                    + '<button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>'
                    + '</form>'
                    + '</div></td>'
                    + '</tr>';
            };

            const attachDeleteHandlers = function () {
                document.querySelectorAll('[data-room-delete-form]').forEach(function (form) {
                    if (form.dataset.bound === '1') {
                        return;
                    }

                    form.dataset.bound = '1';
                    form.addEventListener('submit', function (event) {
                        event.preventDefault();

                        if (!window.confirm('Bạn chắc chắn muốn xóa phòng này?')) {
                            return;
                        }

                        const formData = new FormData(form);
                        if (window.appCsrfToken && !formData.has('_csrf_token')) {
                            formData.append('_csrf_token', window.appCsrfToken);
                        }
                        fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-Token': window.appCsrfToken || ''
                            },
                            body: formData
                        })
                            .then(function (response) { return response.json(); })
                            .then(function (data) {
                                if (!data.success) {
                                    throw new Error(data.message || 'Xóa phòng thất bại.');
                                }

                                const row = form.closest('tr[data-room-row-id]');
                                if (row) {
                                    row.remove();
                                }

                                if (!tableBody.querySelector('[data-room-row-id]')) {
                                    tableBody.innerHTML = '<tr data-room-empty-row><td colspan="11"><div class="empty-state my-3">Chưa có phòng nào phù hợp với bộ lọc hiện tại.</div></td></tr>';
                                }

                                updateCountBadge();
                                showMessage('success', data.message || 'Đã xóa phòng thành công.');

                                if (String(roomIdInput.value) === String(data.room_id)) {
                                    resetForm();
                                }
                            })
                            .catch(function (error) {
                                showMessage('error', error.message || 'Xóa phòng thất bại.');
                            });
                    });
                });
            };

            const attachEditHandlers = function () {
                document.querySelectorAll('[data-room-edit-button]').forEach(function (button) {
                    if (button.dataset.bound === '1') {
                        return;
                    }

                    button.dataset.bound = '1';
                    button.addEventListener('click', function () {
                        clearMessage();

                        fetch(manageUrlBase + '/' + button.dataset.roomId + '/manage-data', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                            .then(function (response) { return response.json(); })
                            .then(function (data) {
                                if (!data.success || !data.room) {
                                    throw new Error(data.message || 'Không thể nạp dữ liệu phòng.');
                                }

                                const room = data.room;
                                roomIdInput.value = room.id;
                                roomForm.querySelector('[name="branch_id"]').value = room.branch_id;
                                roomForm.querySelector('[name="room_number"]').value = room.room_number;
                                roomForm.querySelector('[name="price"]').value = room.price;
                                roomForm.querySelector('[name="room_type"]').value = room.room_type;
                                roomForm.querySelector('[name="status"]').value = room.status;
                                roomForm.querySelector('[name="furniture_status"]').value = room.furniture_status;
                                roomForm.querySelector('[name="window_type"]').value = room.window_type;
                                roomForm.querySelector('[name="note"]').value = room.note || '';
                                roomForm.querySelector('[name="has_balcony"]').checked = Number(room.has_balcony) === 1;

                                const publicCheckbox = roomForm.querySelector('[name="is_public_visible"]');
                                if (publicCheckbox) {
                                    publicCheckbox.checked = Number(room.is_public_visible) === 1;
                                }

                                renderMediaEditor(data.room.media || []);
                                setFormMode('edit');
                            })
                            .catch(function (error) {
                                showMessage('error', error.message || 'Không thể nạp dữ liệu phòng.');
                            });
                    });
                });
            };

            roomForm.addEventListener('submit', function (event) {
                event.preventDefault();
                clearMessage();

                const formData = new FormData(roomForm);
                if (window.appCsrfToken && !formData.has('_csrf_token')) {
                    formData.append('_csrf_token', window.appCsrfToken);
                }
                fetch(roomForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-Token': window.appCsrfToken || ''
                    },
                    body: formData
                })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        if (!data.success || !data.room) {
                            throw new Error(data.message || 'Lưu phòng thất bại.');
                        }

                        const existingRow = tableBody.querySelector('[data-room-row-id="' + data.room.id + '"]');
                        const emptyRow = tableBody.querySelector('[data-room-empty-row]');
                        if (emptyRow) {
                            emptyRow.remove();
                        }

                        if (existingRow) {
                            existingRow.outerHTML = renderRoomRow(data.room);
                        } else {
                            tableBody.insertAdjacentHTML('afterbegin', renderRoomRow(data.room));
                        }

                        updateCountBadge();
                        attachEditHandlers();
                        attachDeleteHandlers();
                        showMessage('success', data.message || 'Đã lưu phòng thành công.');
                        resetForm();
                    })
                    .catch(function (error) {
                        showMessage('error', error.message || 'Lưu phòng thất bại.');
                    });
            });

            if (resetButton) {
                resetButton.addEventListener('click', function () {
                    clearMessage();
                    resetForm();
                });
            }

            if (openCreateButton) {
                openCreateButton.addEventListener('click', function () {
                    clearMessage();
                    roomForm.reset();
                    roomIdInput.value = '';
                    setFormMode('create');
                    renderMediaEditor([]);
                    openFormModal();
                });
            }

            closeFormButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    clearMessage();
                    closeFormModal();
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeFormModal();
                }
            });

            if (formModal && formModal.classList.contains('is-open')) {
                document.body.classList.add('room-form-modal-open');
            }

            attachEditHandlers();
            attachDeleteHandlers();
        })();
    </script>
<?php endif; ?>
