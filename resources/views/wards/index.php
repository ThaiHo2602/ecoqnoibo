<section class="content-grid two-column-balanced">
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Bộ lọc phường</h3>
                <p class="panel-subtitle mb-0">Lọc phường theo từ khóa để quản lý nhanh hơn.</p>
            </div>
        </div>

        <form method="GET" action="<?= e(url('/wards')) ?>" class="d-grid gap-3" accept-charset="UTF-8">
            <div>
                <label class="form-label">Từ khóa</label>
                <input type="text" name="keyword" class="form-control" value="<?= e($filters['keyword']) ?>" placeholder="Tên phường hoặc ghi chú">
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">Áp dụng bộ lọc</button>
                <a href="<?= e(url('/wards')) ?>" class="btn btn-outline-secondary">Xóa lọc</a>
            </div>
        </form>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3><?= $editWard ? 'Cập nhật phường' : 'Thêm phường mới' ?></h3>
                <p class="panel-subtitle mb-0">Phường là danh mục độc lập, dùng để gom các chi nhánh/tên đường theo khu vực.</p>
            </div>
        </div>

        <form method="POST" action="<?= e(url($editWard ? '/wards/update' : '/wards/store')) ?>" class="d-grid gap-3" accept-charset="UTF-8">
            <?php if ($editWard): ?>
                <input type="hidden" name="id" value="<?= e((string) $editWard['id']) ?>">
            <?php endif; ?>

            <div>
                <label class="form-label">Tên phường</label>
                <input type="text" name="name" class="form-control" value="<?= e($editWard['name'] ?? '') ?>" placeholder="Ví dụ: Phường Tân Phong" required>
            </div>

            <div>
                <label class="form-label">Ghi chú</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Không bắt buộc"><?= e($editWard['description'] ?? '') ?></textarea>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary"><?= $editWard ? 'Lưu cập nhật' : 'Thêm phường' ?></button>
                <?php if ($editWard): ?>
                    <a href="<?= e(url('/wards')) ?>" class="btn btn-outline-secondary">Bỏ chỉnh sửa</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>

<section class="panel-card mt-4">
    <div class="panel-header">
        <div>
            <h3>Danh sách phường</h3>
            <p class="panel-subtitle mb-0">Một phường có thể chứa nhiều chi nhánh, trong đó chi nhánh đại diện cho tên đường.</p>
        </div>
        <span class="badge text-bg-light"><?= e((string) count($wards)) ?> phường</span>
    </div>

    <div class="table-responsive">
        <form id="wardsBulkDeleteForm" method="POST" action="<?= e(url('/wards/bulk-delete')) ?>" class="mb-3" onsubmit="return confirm('Bạn chắc chắn muốn xóa các phường đã chọn?');">
            <button type="submit" class="btn btn-outline-danger btn-sm">Xóa phường đã chọn</button>
        </form>
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th></th>
                    <th>Phường</th>
                    <th>Ghi chú</th>
                    <th>Số chi nhánh</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! $wards): ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state my-3">Chưa có phường nào phù hợp với bộ lọc hiện tại.</div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($wards as $ward): ?>
                    <tr>
                        <td><input class="form-check-input" type="checkbox" name="ids[]" value="<?= e((string) $ward['id']) ?>" form="wardsBulkDeleteForm"></td>
                        <td><?= e($ward['name']) ?></td>
                        <td><?= e($ward['description'] ?: '-') ?></td>
                        <td><?= e((string) $ward['branch_count']) ?></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                <a href="<?= e(url('/wards?edit=' . $ward['id'])) ?>" class="btn btn-sm btn-outline-primary">Sửa</a>
                                <a href="<?= e(url('/branches?ward_id=' . $ward['id'])) ?>" class="btn btn-sm btn-outline-secondary">Xem chi nhánh</a>
                                <form method="POST" action="<?= e(url('/wards/delete')) ?>" onsubmit="return confirm('Bạn chắc chắn muốn xóa phường này?');">
                                    <input type="hidden" name="id" value="<?= e((string) $ward['id']) ?>">
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
