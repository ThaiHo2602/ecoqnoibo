<section class="content-grid two-column-balanced">
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3><?= $editSystem ? 'Cap nhat he thong' : 'Them he thong moi' ?></h3>
                <p class="panel-subtitle mb-0">Quan ly danh sach he thong va tinh trang hoat dong.</p>
            </div>
        </div>

        <form method="POST" action="<?= e(url($editSystem ? '/systems/update' : '/systems/store')) ?>" class="d-grid gap-3">
            <?php if ($editSystem): ?>
                <input type="hidden" name="id" value="<?= e((string) $editSystem['id']) ?>">
            <?php endif; ?>

            <div>
                <label class="form-label">Ten he thong</label>
                <input
                    type="text"
                    name="name"
                    class="form-control"
                    value="<?= e($editSystem['name'] ?? '') ?>"
                    placeholder="Vi du: Long Thinh"
                    required
                >
            </div>

            <div>
                <label class="form-label">Mo ta ngan</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Mo ta them neu can"><?= e($editSystem['description'] ?? '') ?></textarea>
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
                <label class="form-check-label" for="systemIsActive">He thong dang hoat dong</label>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary"><?= $editSystem ? 'Luu cap nhat' : 'Them he thong' ?></button>
                <?php if ($editSystem): ?>
                    <a href="<?= e(url('/systems')) ?>" class="btn btn-outline-secondary">Bo chinh sua</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3><?= $editBranch ? 'Sua nhanh chi nhanh' : 'Them nhanh chi nhanh' ?></h3>
                <p class="panel-subtitle mb-0">Tao chi nhanh ngay trong trang he thong de dung voi flow nghiep vu cua ban.</p>
            </div>
        </div>

        <form method="POST" action="<?= e(url($editBranch ? '/branches/update' : '/branches/store')) ?>" class="d-grid gap-3">
            <?php if ($editBranch): ?>
                <input type="hidden" name="id" value="<?= e((string) $editBranch['id']) ?>">
            <?php endif; ?>
            <input type="hidden" name="redirect_to" value="/systems">

            <div>
                <label class="form-label">He thong</label>
                <select name="system_id" class="form-select" required>
                    <option value="">Chon he thong</option>
                    <?php foreach ($systems as $system): ?>
                        <option value="<?= e((string) $system['id']) ?>" <?= (int) ($editBranch['system_id'] ?? 0) === (int) $system['id'] ? 'selected' : '' ?>>
                            <?= e($system['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label">Ten chi nhanh</label>
                <input type="text" name="name" class="form-control" value="<?= e($editBranch['name'] ?? '') ?>" placeholder="Vi du: Long Thinh 1" required>
            </div>

            <div>
                <label class="form-label">Quan</label>
                <select name="district_id" class="form-select" required>
                    <option value="">Chon quan</option>
                    <?php foreach ($districts as $district): ?>
                        <option value="<?= e((string) $district['id']) ?>" <?= (int) ($editBranch['district_id'] ?? 0) === (int) $district['id'] ? 'selected' : '' ?>>
                            <?= e($district['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label">Dia chi</label>
                <input type="text" name="address" class="form-control" value="<?= e($editBranch['address'] ?? '') ?>" placeholder="Nhap dia chi chi nhanh" required>
            </div>

            <div>
                <label class="form-label">So dien thoai quan ly</label>
                <input type="text" name="manager_phone" class="form-control" value="<?= e($editBranch['manager_phone'] ?? '') ?>" placeholder="Khong bat buoc">
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary"><?= $editBranch ? 'Luu chi nhanh' : 'Them chi nhanh' ?></button>
                <?php if ($editBranch): ?>
                    <a href="<?= e(url('/systems')) ?>" class="btn btn-outline-secondary">Bo chinh sua</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>

<section class="panel-card mt-4">
    <div class="panel-header">
        <div>
            <h3>Danh sach he thong</h3>
            <p class="panel-subtitle mb-0">Bam vao tung he thong de xem cac chi nhanh ben trong va thao tac nhanh.</p>
        </div>
        <span class="badge text-bg-light"><?= e((string) count($systems)) ?> he thong</span>
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
                                <?= (int) $system['is_active'] === 1 ? 'dang hoat dong' : 'tam dung' ?>
                            </span>
                        </div>
                        <p class="text-muted mb-0 mt-2"><?= e($system['description'] ?: 'Chua co mo ta cho he thong nay.') ?></p>
                    </div>

                    <div class="accordion-meta">
                        <div><strong><?= e((string) $system['branch_count']) ?></strong><span>Chi nhanh</span></div>
                        <div><strong><?= e((string) $system['room_count']) ?></strong><span>Phong</span></div>
                    </div>
                </div>

                <div class="accordion-actions">
                    <a href="<?= e(url('/systems?edit_system=' . $system['id'])) ?>" class="btn btn-sm btn-outline-primary">Sua he thong</a>
                    <a href="<?= e(url('/branches?system_id=' . $system['id'])) ?>" class="btn btn-sm btn-outline-secondary">Xem o trang chi nhanh</a>
                    <form method="POST" action="<?= e(url('/systems/delete')) ?>" onsubmit="return confirm('Ban chac chan muon xoa he thong nay?');">
                        <input type="hidden" name="id" value="<?= e((string) $system['id']) ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Xoa</button>
                    </form>
                </div>

                <div class="accordion-body-custom">
                    <?php if ($systemBranches): ?>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Chi nhanh</th>
                                        <th>Quan</th>
                                        <th>Dia chi</th>
                                        <th>SDT quan ly</th>
                                        <th class="text-end">Thao tac</th>
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
                                                    <a href="<?= e(url('/systems?edit_branch=' . $branch['id'])) ?>" class="btn btn-sm btn-outline-primary">Sua</a>
                                                    <form method="POST" action="<?= e(url('/branches/delete')) ?>" onsubmit="return confirm('Ban chac chan muon xoa chi nhanh nay?');">
                                                        <input type="hidden" name="id" value="<?= e((string) $branch['id']) ?>">
                                                        <input type="hidden" name="redirect_to" value="/systems">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Xoa</button>
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
                            He thong nay chua co chi nhanh nao. Ban co the them nhanh o form ben tren.
                        </div>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
