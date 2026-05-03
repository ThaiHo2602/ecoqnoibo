<section class="content-grid two-column-balanced">
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Bộ lọc chi nhánh</h3>
                <p class="panel-subtitle mb-0">Lọc theo hệ thống, quận và từ khóa để quản lý nhanh hơn.</p>
            </div>
        </div>

        <form method="GET" action="<?= e(url('/branches')) ?>" class="d-grid gap-3">
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
                <label class="form-label">Từ khóa</label>
                <input type="text" name="keyword" class="form-control" value="<?= e($filters['keyword']) ?>" placeholder="Tên, địa chỉ hoặc số điện thoại">
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">Áp dụng bộ lọc</button>
                <a href="<?= e(url('/branches')) ?>" class="btn btn-outline-secondary">Xóa lọc</a>
            </div>
        </form>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3><?= $editBranch ? 'Cập nhật chi nhánh' : 'Thêm chi nhánh mới' ?></h3>
                <p class="panel-subtitle mb-0">Dành cho quản lý và giám đốc thao tác CRUD nhanh.</p>
            </div>
        </div>

        <form method="POST" action="<?= e(url($editBranch ? '/branches/update' : '/branches/store')) ?>" class="d-grid gap-3">
            <?php if ($editBranch): ?>
                <input type="hidden" name="id" value="<?= e((string) $editBranch['id']) ?>">
            <?php endif; ?>
            <input type="hidden" name="redirect_to" value="/branches">

            <div>
                <label class="form-label">Hệ thống</label>
                <select name="system_id" class="form-select">
                    <option value="">Chọn hệ thống</option>
                    <?php foreach ($systems as $system): ?>
                        <option value="<?= e((string) $system['id']) ?>" <?= (int) ($editBranch['system_id'] ?? $filters['system_id']) === (int) $system['id'] ? 'selected' : '' ?>>
                            <?= e($system['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label">Phường</label>
                <select name="ward_id" class="form-select" required>
                    <option value="">Chọn phường</option>
                    <?php foreach ($wards as $ward): ?>
                        <option value="<?= e((string) $ward['id']) ?>" <?= (int) ($editBranch['ward_id'] ?? $filters['ward_id']) === (int) $ward['id'] ? 'selected' : '' ?>>
                            <?= e($ward['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Chi nhánh có thể thuộc hệ thống và phường độc lập với nhau.</div>
            </div>

            <div>
                <label class="form-label">Tên chi nhánh</label>
                <input type="text" name="name" class="form-control" value="<?= e($editBranch['name'] ?? '') ?>" required>
            </div>

            <div>
                <label class="form-label">Quận</label>
                <select name="district_id" class="form-select" required>
                    <option value="">Chọn quận</option>
                    <?php foreach ($districts as $district): ?>
                        <option value="<?= e((string) $district['id']) ?>" <?= (int) ($editBranch['district_id'] ?? $filters['district_id']) === (int) $district['id'] ? 'selected' : '' ?>>
                            <?= e($district['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label">Địa chỉ</label>
                <input type="text" name="address" class="form-control" value="<?= e($editBranch['address'] ?? '') ?>" required>
            </div>

            <div>
                <label class="form-label">Số điện thoại quản lý</label>
                <input type="text" name="manager_phone" class="form-control" value="<?= e($editBranch['manager_phone'] ?? '') ?>">
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary"><?= $editBranch ? 'Lưu cập nhật' : 'Thêm chi nhánh' ?></button>
                <?php if ($editBranch): ?>
                    <a href="<?= e(url('/branches')) ?>" class="btn btn-outline-secondary">Bỏ chỉnh sửa</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>

<section class="panel-card mt-4">
    <div class="panel-header">
        <div>
            <h3>Danh sách chi nhánh</h3>
            <p class="panel-subtitle mb-0">Đang hiển thị theo bộ lọc hiện tại. Ở bước sau mình sẽ nối thêm popup danh sách phòng theo chi nhánh.</p>
        </div>
        <span class="badge text-bg-light"><?= e((string) count($branches)) ?> chi nhánh</span>
    </div>

    <div class="table-responsive">
        <form id="branchesBulkDeleteForm" method="POST" action="<?= e(url('/branches/bulk-delete')) ?>" class="mb-3" onsubmit="return confirm('Bạn chắc chắn muốn xóa các chi nhánh đã chọn?');">
            <input type="hidden" name="redirect_to" value="/branches">
            <button type="submit" class="btn btn-outline-danger btn-sm">Xóa chi nhánh đã chọn</button>
        </form>
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th></th>
                    <th>Chi nhánh</th>
                    <th>Hệ thống</th>
                    <th>Phường</th>
                    <th>Quận</th>
                    <th>Địa chỉ</th>
                    <th>SDT quản lý</th>
                    <th>Số phòng</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! $branches): ?>
                    <tr>
                        <td colspan="9">
                            <div class="empty-state my-3">Chưa có chi nhánh nào phù hợp với bộ lọc hiện tại.</div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($branches as $branch): ?>
                    <tr>
                        <td><input class="form-check-input" type="checkbox" name="ids[]" value="<?= e((string) $branch['id']) ?>" form="branchesBulkDeleteForm"></td>
                        <td><?= e($branch['name']) ?></td>
                        <td><?= e($branch['system_name']) ?></td>
                        <td><?= e($branch['ward_name'] ?: '-') ?></td>
                        <td><?= e($branch['district_name']) ?></td>
                        <td><?= e($branch['address']) ?></td>
                        <td><?= e($branch['manager_phone'] ?: '-') ?></td>
                        <td><?= e((string) $branch['room_count']) ?></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                <a href="<?= e(url('/branches?edit=' . $branch['id'])) ?>" class="btn btn-sm btn-outline-primary">Sửa</a>
                                <a href="<?= e(url('/rooms?branch_id=' . $branch['id'])) ?>" class="btn btn-sm btn-outline-secondary">Xem phòng</a>
                                <form method="POST" action="<?= e(url('/branches/delete')) ?>" onsubmit="return confirm('Bạn chắc chắn muốn xóa chi nhánh này?');">
                                    <input type="hidden" name="id" value="<?= e((string) $branch['id']) ?>">
                                    <input type="hidden" name="redirect_to" value="/branches">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
