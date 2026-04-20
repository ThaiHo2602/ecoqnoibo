<section class="content-grid two-column-balanced">
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Bo loc chi nhanh</h3>
                <p class="panel-subtitle mb-0">Loc theo he thong, quan va tu khoa de quan ly nhanh hon.</p>
            </div>
        </div>

        <form method="GET" action="<?= e(url('/branches')) ?>" class="d-grid gap-3">
            <div>
                <label class="form-label">He thong</label>
                <select name="system_id" class="form-select">
                    <option value="0">Tat ca he thong</option>
                    <?php foreach ($systems as $system): ?>
                        <option value="<?= e((string) $system['id']) ?>" <?= (int) $filters['system_id'] === (int) $system['id'] ? 'selected' : '' ?>>
                            <?= e($system['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label">Quan</label>
                <select name="district_id" class="form-select">
                    <option value="0">Tat ca quan</option>
                    <?php foreach ($districts as $district): ?>
                        <option value="<?= e((string) $district['id']) ?>" <?= (int) $filters['district_id'] === (int) $district['id'] ? 'selected' : '' ?>>
                            <?= e($district['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label">Tu khoa</label>
                <input type="text" name="keyword" class="form-control" value="<?= e($filters['keyword']) ?>" placeholder="Ten, dia chi hoac so dien thoai">
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">Ap dung bo loc</button>
                <a href="<?= e(url('/branches')) ?>" class="btn btn-outline-secondary">Xoa loc</a>
            </div>
        </form>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3><?= $editBranch ? 'Cap nhat chi nhanh' : 'Them chi nhanh moi' ?></h3>
                <p class="panel-subtitle mb-0">Danh cho quan ly va giam doc thao tac CRUD nhanh.</p>
            </div>
        </div>

        <form method="POST" action="<?= e(url($editBranch ? '/branches/update' : '/branches/store')) ?>" class="d-grid gap-3">
            <?php if ($editBranch): ?>
                <input type="hidden" name="id" value="<?= e((string) $editBranch['id']) ?>">
            <?php endif; ?>
            <input type="hidden" name="redirect_to" value="/branches">

            <div>
                <label class="form-label">He thong</label>
                <select name="system_id" class="form-select" required>
                    <option value="">Chon he thong</option>
                    <?php foreach ($systems as $system): ?>
                        <option value="<?= e((string) $system['id']) ?>" <?= (int) ($editBranch['system_id'] ?? $filters['system_id']) === (int) $system['id'] ? 'selected' : '' ?>>
                            <?= e($system['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label">Ten chi nhanh</label>
                <input type="text" name="name" class="form-control" value="<?= e($editBranch['name'] ?? '') ?>" required>
            </div>

            <div>
                <label class="form-label">Quan</label>
                <select name="district_id" class="form-select" required>
                    <option value="">Chon quan</option>
                    <?php foreach ($districts as $district): ?>
                        <option value="<?= e((string) $district['id']) ?>" <?= (int) ($editBranch['district_id'] ?? $filters['district_id']) === (int) $district['id'] ? 'selected' : '' ?>>
                            <?= e($district['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label">Dia chi</label>
                <input type="text" name="address" class="form-control" value="<?= e($editBranch['address'] ?? '') ?>" required>
            </div>

            <div>
                <label class="form-label">So dien thoai quan ly</label>
                <input type="text" name="manager_phone" class="form-control" value="<?= e($editBranch['manager_phone'] ?? '') ?>">
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary"><?= $editBranch ? 'Luu cap nhat' : 'Them chi nhanh' ?></button>
                <?php if ($editBranch): ?>
                    <a href="<?= e(url('/branches')) ?>" class="btn btn-outline-secondary">Bo chinh sua</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>

<section class="panel-card mt-4">
    <div class="panel-header">
        <div>
            <h3>Danh sach chi nhanh</h3>
            <p class="panel-subtitle mb-0">Dang hien thi theo bo loc hien tai. O buoc sau minh se noi them popup danh sach phong theo chi nhanh.</p>
        </div>
        <span class="badge text-bg-light"><?= e((string) count($branches)) ?> chi nhanh</span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Chi nhanh</th>
                    <th>He thong</th>
                    <th>Quan</th>
                    <th>Dia chi</th>
                    <th>SDT quan ly</th>
                    <th>So phong</th>
                    <th class="text-end">Thao tac</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! $branches): ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state my-3">Chua co chi nhanh nao phu hop voi bo loc hien tai.</div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($branches as $branch): ?>
                    <tr>
                        <td><?= e($branch['name']) ?></td>
                        <td><?= e($branch['system_name']) ?></td>
                        <td><?= e($branch['district_name']) ?></td>
                        <td><?= e($branch['address']) ?></td>
                        <td><?= e($branch['manager_phone'] ?: '-') ?></td>
                        <td><?= e((string) $branch['room_count']) ?></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                <a href="<?= e(url('/branches?edit=' . $branch['id'])) ?>" class="btn btn-sm btn-outline-primary">Sua</a>
                                <a href="<?= e(url('/rooms?branch_id=' . $branch['id'])) ?>" class="btn btn-sm btn-outline-secondary">Xem phong</a>
                                <form method="POST" action="<?= e(url('/branches/delete')) ?>" onsubmit="return confirm('Ban chac chan muon xoa chi nhanh nay?');">
                                    <input type="hidden" name="id" value="<?= e((string) $branch['id']) ?>">
                                    <input type="hidden" name="redirect_to" value="/branches">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Xoa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
