<section class="content-grid two-column-balanced">
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3><?= $editSystem ? 'Cập nhật hệ thống' : 'Thêm hệ thống mới' ?></h3>
                <p class="panel-subtitle mb-0">Quản lý danh sách hệ thống và tình trạng hoạt động.</p>
            </div>
        </div>

        <form method="POST" action="<?= e(url($editSystem ? '/systems/update' : '/systems/store')) ?>" class="d-grid gap-3">
            <?php if ($editSystem): ?>
                <input type="hidden" name="id" value="<?= e((string) $editSystem['id']) ?>">
            <?php endif; ?>

            <div>
                <label class="form-label">Tên hệ thống</label>
                <input
                    type="text"
                    name="name"
                    class="form-control"
                    value="<?= e($editSystem['name'] ?? '') ?>"
                    placeholder="Ví dụ: Long Thịnh"
                    required
                >
            </div>

            <div>
                <label class="form-label">Mô tả ngắn</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Mô tả thêm nếu cần"><?= e($editSystem['description'] ?? '') ?></textarea>
            </div>

            <div class="form-check">
                <input
                    class="form-check-input"
                    type="checkbox"
                    value="1"
                    id="systemIsActive"
                    name="is_active"
                    <?= ! isset($editSystem['is_active']) || (int) $editSystem['is_active'] === 1 ? 'checked' : '' ?>
                >
                <label class="form-check-label" for="systemIsActive">Hệ thống đang hoạt động</label>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary"><?= $editSystem ? 'Lưu cập nhật' : 'Thêm hệ thống' ?></button>
                <?php if ($editSystem): ?>
                    <a href="<?= e(url('/systems')) ?>" class="btn btn-outline-secondary">Bỏ chỉnh sửa</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3><?= $editBranch ? 'Sửa nhanh chi nhánh' : 'Thêm nhanh chi nhánh' ?></h3>
                <p class="panel-subtitle mb-0">Tạo chi nhánh ngay trong trang hệ thống để đúng với flow nghiệp vụ của bạn.</p>
            </div>
        </div>

        <form method="POST" action="<?= e(url($editBranch ? '/branches/update' : '/branches/store')) ?>" class="d-grid gap-3">
            <?php if ($editBranch): ?>
                <input type="hidden" name="id" value="<?= e((string) $editBranch['id']) ?>">
            <?php endif; ?>
            <input type="hidden" name="redirect_to" value="/systems">

            <div>
                <label class="form-label">Hệ thống</label>
                <select name="system_id" class="form-select" required>
                    <option value="">Chọn hệ thống</option>
                    <?php foreach ($systems as $system): ?>
                        <option value="<?= e((string) $system['id']) ?>" <?= (int) ($editBranch['system_id'] ?? 0) === (int) $system['id'] ? 'selected' : '' ?>>
                            <?= e($system['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label">Tên chi nhánh</label>
                <input type="text" name="name" class="form-control" value="<?= e($editBranch['name'] ?? '') ?>" placeholder="Ví dụ: Long Thịnh 1" required>
            </div>

            <div>
                <label class="form-label">Quận</label>
                <select name="district_id" class="form-select" required>
                    <option value="">Chọn quận</option>
                    <?php foreach ($districts as $district): ?>
                        <option value="<?= e((string) $district['id']) ?>" <?= (int) ($editBranch['district_id'] ?? 0) === (int) $district['id'] ? 'selected' : '' ?>>
                            <?= e($district['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label">Địa chỉ</label>
                <input type="text" name="address" class="form-control" value="<?= e($editBranch['address'] ?? '') ?>" placeholder="Nhập địa chỉ chi nhánh" required>
            </div>

            <div>
                <label class="form-label">Số điện thoại quản lý</label>
                <input type="text" name="manager_phone" class="form-control" value="<?= e($editBranch['manager_phone'] ?? '') ?>" placeholder="Không bắt buộc">
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary"><?= $editBranch ? 'Lưu chi nhánh' : 'Thêm chi nhánh' ?></button>
                <?php if ($editBranch): ?>
                    <a href="<?= e(url('/systems')) ?>" class="btn btn-outline-secondary">Bỏ chỉnh sửa</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>

<section class="panel-card mt-4">
    <div class="panel-header">
        <div>
            <h3>Danh sách hệ thống</h3>
            <p class="panel-subtitle mb-0">Bấm vào từng hệ thống để xem các chi nhánh bên trong và thao tác nhanh.</p>
        </div>
        <span class="badge text-bg-light"><?= e((string) count($systems)) ?> hệ thống</span>
    </div>

    <div class="accordion-stack">
        <?php foreach ($systems as $system): ?>
            <?php $systemBranches = $branchesBySystem[$system['id']] ?? []; ?>
            <article class="accordion-item-custom">
                <div class="accordion-summary">
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h4 class="mb-0"><?= e($system['name']) ?></h4>
                            <span class="status-pill <?= (int) $system['is_active'] === 1 ? 'status-approved' : 'status-rejected' ?>">
                                <?= (int) $system['is_active'] === 1 ? 'Đang hoạt động' : 'Tạm dừng' ?>
                            </span>
                        </div>
                        <p class="text-muted mb-0 mt-2"><?= e($system['description'] ?: 'Chưa có mô tả cho hệ thống này.') ?></p>
                    </div>

                    <div class="accordion-meta">
                        <div><strong><?= e((string) $system['branch_count']) ?></strong><span>Chi nhánh</span></div>
                        <div><strong><?= e((string) $system['room_count']) ?></strong><span>Phòng</span></div>
                    </div>
                </div>

                <div class="accordion-actions">
                    <a href="<?= e(url('/systems?edit_system=' . $system['id'])) ?>" class="btn btn-sm btn-outline-primary">Sửa hệ thống</a>
                    <a href="<?= e(url('/branches?system_id=' . $system['id'])) ?>" class="btn btn-sm btn-outline-secondary">Xem ở trang chi nhánh</a>
                    <form method="POST" action="<?= e(url('/systems/delete')) ?>" onsubmit="return confirm('Bạn chắc chắn muốn xóa hệ thống này?');">
                        <input type="hidden" name="id" value="<?= e((string) $system['id']) ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                    </form>
                </div>

                <div class="accordion-body-custom">
                    <?php if ($systemBranches): ?>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Chi nhánh</th>
                                        <th>Quận</th>
                                        <th>Địa chỉ</th>
                                        <th>SDT quản lý</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($systemBranches as $branch): ?>
                                        <tr>
                                            <td><?= e($branch['name']) ?></td>
                                            <td><?= e($branch['district_name']) ?></td>
                                            <td><?= e($branch['address']) ?></td>
                                            <td><?= e($branch['manager_phone'] ?: '-') ?></td>
                                            <td class="text-end">
                                                <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                                    <a href="<?= e(url('/systems?edit_branch=' . $branch['id'])) ?>" class="btn btn-sm btn-outline-primary">Sửa</a>
                                                    <form method="POST" action="<?= e(url('/branches/delete')) ?>" onsubmit="return confirm('Bạn chắc chắn muốn xóa chi nhánh này?');">
                                                        <input type="hidden" name="id" value="<?= e((string) $branch['id']) ?>">
                                                        <input type="hidden" name="redirect_to" value="/systems">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            Hệ thống này chưa có chi nhánh nào. Bạn có thể thêm nhanh ở form bên trên.
                        </div>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
