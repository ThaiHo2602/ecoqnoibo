<?php
$roomTypeLabels = [
    'co_gac' => 'Có gác',
    'khong_gac' => 'Không gác',
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

<section class="content-grid room-layout">
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Bộ lọc phòng</h3>
                <p class="panel-subtitle mb-0">Lọc theo quận, giá, trạng thái, nội thất, hệ thống và chi nhánh.</p>
            </div>
        </div>

        <form method="GET" action="<?= e(url('/rooms')) ?>" class="d-grid gap-3">
            <div class="grid-2">
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

            <div class="grid-2">
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
            </div>

            <div class="grid-2">
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

            <div class="grid-2">
                <div>
                    <label class="form-label">Giá từ</label>
                    <input type="number" name="price_min" class="form-control" value="<?= e($filters['price_min']) ?>" min="0" step="1000">
                </div>
                <div>
                    <label class="form-label">Giá đến</label>
                    <input type="number" name="price_max" class="form-control" value="<?= e($filters['price_max']) ?>" min="0" step="1000">
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">Áp dụng bộ lọc</button>
                <a href="<?= e(url('/rooms')) ?>" class="btn btn-outline-secondary">Xóa lọc</a>
            </div>
        </form>
    </div>

    <?php if ($canManage): ?>
        <div class="panel-card">
            <div class="panel-header">
                <div>
                    <h3><?= $editRoom ? 'Cap nhat phong' : 'Them phong moi' ?></h3>
                    <p class="panel-subtitle mb-0">Form chi tiet phong va upload nhieu anh, video.</p>
                </div>
            </div>

            <form method="POST" action="<?= e(url($editRoom ? '/rooms/update' : '/rooms/store')) ?>" enctype="multipart/form-data" class="d-grid gap-3">
                <?php if ($editRoom): ?>
                    <input type="hidden" name="id" value="<?= e((string) $editRoom['id']) ?>">
                <?php endif; ?>

                <div>
                    <label class="form-label">Chi nhanh</label>
                    <select name="branch_id" class="form-select" required>
                        <option value="">Chon chi nhanh</option>
                        <?php foreach ($systems as $system): ?>
                            <?php if (empty($branchesBySystem[$system['id']])) continue; ?>
                            <optgroup label="<?= e($system['name']) ?>">
                                <?php foreach ($branchesBySystem[$system['id']] as $branch): ?>
                                    <option value="<?= e((string) $branch['id']) ?>" <?= (int) ($editRoom['branch_id'] ?? $filters['branch_id']) === (int) $branch['id'] ? 'selected' : '' ?>>
                                        <?= e($branch['name'] . ' - ' . $branch['district_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid-2">
                    <div>
                        <label class="form-label">So phong</label>
                        <input type="text" name="room_number" class="form-control" value="<?= e($editRoom['room_number'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label class="form-label">Gia phong</label>
                        <input type="number" name="price" class="form-control" value="<?= e((string) ($editRoom['price'] ?? '')) ?>" min="0" step="1000" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label class="form-label">Loai phong</label>
                        <select name="room_type" class="form-select" required>
                            <option value="">Chon loai phong</option>
                            <?php foreach ($roomTypeLabels as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= ($editRoom['room_type'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Trang thai</label>
                        <select name="status" class="form-select" required>
                            <?php foreach ($statusLabels as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= ($editRoom['status'] ?? 'chua_lock') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label class="form-label">Noi that</label>
                        <select name="furniture_status" class="form-select" required>
                            <option value="">Chon noi that</option>
                            <?php foreach ($furnitureLabels as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= ($editRoom['furniture_status'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Loai cua so</label>
                        <select name="window_type" class="form-select" required>
                            <option value="">Chon loai cua so</option>
                            <?php foreach ($windowLabels as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= ($editRoom['window_type'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label class="form-label">Tien dien</label>
                        <input type="number" name="electricity_fee" class="form-control" value="<?= e((string) ($editRoom['electricity_fee'] ?? '0')) ?>" min="0" step="500">
                    </div>
                    <div>
                        <label class="form-label">Tien nuoc</label>
                        <input type="number" name="water_fee" class="form-control" value="<?= e((string) ($editRoom['water_fee'] ?? '0')) ?>" min="0" step="1000">
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label class="form-label">Phi dich vu</label>
                        <input type="number" name="service_fee" class="form-control" value="<?= e((string) ($editRoom['service_fee'] ?? '0')) ?>" min="0" step="1000">
                    </div>
                    <div>
                        <label class="form-label">Phi gui xe</label>
                        <input type="number" name="parking_fee" class="form-control" value="<?= e((string) ($editRoom['parking_fee'] ?? '0')) ?>" min="0" step="1000">
                    </div>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="hasBalcony" name="has_balcony" <?= (int) ($editRoom['has_balcony'] ?? 0) === 1 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="hasBalcony">Co ban cong</label>
                </div>

                <div>
                    <label class="form-label">Anh va video</label>
                    <input type="file" name="media_files[]" class="form-control" multiple accept="image/*,video/*">
                    <div class="form-text">Chap nhan upload nhieu file cung luc. Anh: JPG, PNG, WEBP, GIF. Video: MP4, WEBM, OGG, MOV.</div>
                </div>

                <?php if ($editRoom && $editRoomMedia): ?>
                    <div>
                        <label class="form-label">Media hien co</label>
                        <div class="media-grid">
                            <?php foreach ($editRoomMedia as $media): ?>
                                <label class="media-card">
                                    <?php if ($media['media_type'] === 'image'): ?>
                                        <img src="<?= e(url('../' . $media['file_path'])) ?>" alt="<?= e($media['file_name']) ?>">
                                    <?php else: ?>
                                        <video src="<?= e(url('../' . $media['file_path'])) ?>" controls preload="metadata"></video>
                                    <?php endif; ?>
                                    <span><?= e($media['file_name']) ?></span>
                                    <span class="small text-muted"><?= e(strtoupper($media['media_type'])) ?></span>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="delete_media_ids[]" value="<?= e((string) $media['id']) ?>" id="mediaDelete<?= e((string) $media['id']) ?>">
                                        <label class="form-check-label" for="mediaDelete<?= e((string) $media['id']) ?>">Xoa media nay</label>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div>
                    <label class="form-label">Ghi chu tong</label>
                    <textarea name="note" class="form-control" rows="4" placeholder="Nhap ghi chu tong cho phong"><?= e($editRoom['note'] ?? '') ?></textarea>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary"><?= $editRoom ? 'Luu cap nhat phong' : 'Them phong' ?></button>
                    <?php if ($editRoom): ?>
                        <a href="<?= e(url('/rooms')) ?>" class="btn btn-outline-secondary">Bo chinh sua</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="panel-card">
            <div class="panel-header">
                <div>
                    <h3>Goc thao tac nhanh cho nhan vien</h3>
                    <p class="panel-subtitle mb-0">Bam `Xem chi tiet` tren tung phong de mo popup day du hinh anh, video, chi phi va gui yeu cau lock.</p>
                </div>
            </div>
            <div class="empty-state">Quy trinh hien tai: `Xem chi tiet` -> `Gui yeu cau lock` -> phong sang `dang giu` -> quan ly/giam doc vao trang `Yeu cau lock` de duyet.</div>
        </div>
    <?php endif; ?>
</section>

<section class="panel-card mt-4">
    <div class="panel-header">
        <div>
            <h3>Danh sach phong</h3>
            <p class="panel-subtitle mb-0">Hien theo dang list, chua show hinh o list, dung dung voi mong muon hien tai.</p>
        </div>
        <span class="badge text-bg-light"><?= e((string) count($rooms)) ?> phong</span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>So phong</th>
                    <th>Chi nhanh</th>
                    <th>Quan</th>
                    <th>Gia</th>
                    <th>Loai phong</th>
                    <th>Trang thai</th>
                    <th>Noi that</th>
                    <th>Media</th>
                    <th class="text-end">Thao tac</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! $rooms): ?>
                    <tr>
                        <td colspan="9">
                            <div class="empty-state my-3">Chua co phong nao phu hop voi bo loc hien tai.</div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($rooms as $room): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($room['room_number']) ?></div>
                            <div class="text-muted small"><?= e($windowLabels[$room['window_type']] ?? $room['window_type']) ?></div>
                        </td>
                        <td>
                            <div><?= e($room['branch_name']) ?></div>
                            <div class="text-muted small"><?= e($room['system_name']) ?></div>
                        </td>
                        <td><?= e($room['district_name']) ?></td>
                        <td><?= e(number_format((float) $room['price'], 0, ',', '.')) ?> VND</td>
                        <td><?= e($roomTypeLabels[$room['room_type']] ?? $room['room_type']) ?></td>
                        <td><span class="status-pill status-<?= e($room['status']) ?>"><?= e($statusLabels[$room['status']] ?? $room['status']) ?></span></td>
                        <td><?= e($furnitureLabels[$room['furniture_status']] ?? $room['furniture_status']) ?></td>
                        <td><?= e((string) $room['media_count']) ?></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#roomDetailModal<?= e((string) $room['id']) ?>"
                                >
                                    Xem chi tiet
                                </button>
                                <?php if ($canManage): ?>
                                    <a href="<?= e(url('/rooms?edit=' . $room['id'])) ?>" class="btn btn-sm btn-outline-primary">Sua</a>
                                    <form method="POST" action="<?= e(url('/rooms/delete')) ?>" onsubmit="return confirm('Ban chac chan muon xoa phong nay?');">
                                        <input type="hidden" name="id" value="<?= e((string) $room['id']) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Xoa</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted small">Khong co thao tac</span>
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
                        <div class="eyebrow">Chi tiet phong</div>
                        <h2 class="modal-title h4 mt-2 mb-1">Phong <?= e($room['room_number']) ?> - <?= e($room['branch_name']) ?></h2>
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
                                        <h3>Hinh anh va video</h3>
                                        <p class="panel-subtitle mb-0">Media cua phong nay duoc hien ngay trong popup.</p>
                                    </div>
                                </div>

                                <?php if ($roomMedia): ?>
                                    <div class="modal-media-grid">
                                        <?php foreach ($roomMedia as $media): ?>
                                            <div class="modal-media-card">
                                                <?php if ($media['media_type'] === 'image'): ?>
                                                    <img src="<?= e(url('../' . $media['file_path'])) ?>" alt="<?= e($media['file_name']) ?>">
                                                <?php else: ?>
                                                    <video src="<?= e(url('../' . $media['file_path'])) ?>" controls preload="metadata"></video>
                                                <?php endif; ?>
                                                <div class="small text-muted mt-2"><?= e($media['file_name']) ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-state">Phong nay chua co media nao.</div>
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
                                    <span class="detail-tag"><?= (int) $room['has_balcony'] === 1 ? 'Co ban cong' : 'Khong ban cong' ?></span>
                                </div>
                            </div>

                            <div class="detail-card mt-3">
                                <div class="panel-header mb-3">
                                    <div>
                                        <h3>Thong tin chi tiet</h3>
                                        <p class="panel-subtitle mb-0">Tong hop day du cac chi phi va dac diem phong.</p>
                                    </div>
                                </div>

                                <div class="detail-list">
                                    <div><span>He thong</span><strong><?= e($room['system_name']) ?></strong></div>
                                    <div><span>Chi nhanh</span><strong><?= e($room['branch_name']) ?></strong></div>
                                    <div><span>Quan</span><strong><?= e($room['district_name']) ?></strong></div>
                                    <div><span>Tien dien</span><strong><?= e(number_format((float) $room['electricity_fee'], 0, ',', '.')) ?></strong></div>
                                    <div><span>Tien nuoc</span><strong><?= e(number_format((float) $room['water_fee'], 0, ',', '.')) ?></strong></div>
                                    <div><span>Phi dich vu</span><strong><?= e(number_format((float) $room['service_fee'], 0, ',', '.')) ?></strong></div>
                                    <div><span>Phi gui xe</span><strong><?= e(number_format((float) $room['parking_fee'], 0, ',', '.')) ?></strong></div>
                                </div>

                                <div class="detail-note mt-3">
                                    <div class="small text-uppercase text-muted fw-semibold mb-2">Ghi chu tong</div>
                                    <div><?= e($room['note'] ?: 'Chua co ghi chu cho phong nay.') ?></div>
                                </div>
                            </div>

                            <?php if ($latestLock): ?>
                                <div class="detail-card mt-3">
                                    <div class="panel-header mb-3">
                                        <div>
                                            <h3>Thong tin lock gan nhat</h3>
                                            <p class="panel-subtitle mb-0">Giup nhan vien va quan ly nhin nhanh tinh hinh xu ly.</p>
                                        </div>
                                    </div>

                                    <div class="detail-list">
                                        <div><span>Trang thai</span><strong><?= e($lockRequestLabels[$latestLock['request_status']] ?? $latestLock['request_status']) ?></strong></div>
                                        <div><span>Nguoi gui</span><strong><?= e($latestLock['requester_name']) ?></strong></div>
                                        <div><span>Thoi gian gui</span><strong><?= e($latestLock['requested_at']) ?></strong></div>
                                    </div>
                                    <?php if ($latestLock['request_note']): ?>
                                        <div class="detail-note mt-3">
                                            <div class="small text-uppercase text-muted fw-semibold mb-2">Ghi chu yeu cau</div>
                                            <div><?= e($latestLock['request_note']) ?></div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($latestLock['decision_note']): ?>
                                        <div class="detail-note mt-3">
                                            <div class="small text-uppercase text-muted fw-semibold mb-2">Ghi chu xu ly</div>
                                            <div><?= e($latestLock['decision_note']) ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($canRequestLock): ?>
                                <div class="detail-card mt-3">
                                    <div class="panel-header mb-3">
                                        <div>
                                            <h3>Thao tac nhan vien</h3>
                                            <p class="panel-subtitle mb-0">Gui yeu cau lock ngay trong popup nay.</p>
                                        </div>
                                    </div>

                                    <?php if ($room['status'] === 'chua_lock'): ?>
                                        <form method="POST" action="<?= e(url('/lock-requests/store')) ?>" class="d-grid gap-3">
                                            <input type="hidden" name="room_id" value="<?= e((string) $room['id']) ?>">
                                            <div>
                                                <label class="form-label">Ghi chu lock</label>
                                                <textarea name="request_note" class="form-control" rows="3" placeholder="Vi du: Khach da hen xem phong, du kien dat coc toi nay"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-warning">Gui yeu cau lock</button>
                                        </form>
                                    <?php else: ?>
                                        <div class="empty-state">Phong nay hien dang `<?= e(strtolower($statusLabels[$room['status']] ?? $room['status'])) ?>`, nen chua the gui them mot yeu cau lock moi.</div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($canManage): ?>
                                <div class="d-flex gap-2 flex-wrap mt-3">
                                    <a href="<?= e(url('/rooms?edit=' . $room['id'])) ?>" class="btn btn-primary">Chinh sua phong nay</a>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Dong popup</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
