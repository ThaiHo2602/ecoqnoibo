<?php
$rankingModeLabel = $rankingMode === 'lowest' ? 'Thấp nhất' : 'Cao nhất';
$chartStaffLabels = array_map(static fn (array $item): string => $item['full_name'], array_slice($topStaff, 0, min(10, count($topStaff))));
$chartStaffValues = array_map(static fn (array $item): int => (int) $item['approved_count'], array_slice($topStaff, 0, min(10, count($topStaff))));
$chartSystemLabels = array_map(static fn (array $item): string => $item['name'], array_slice($topSystems, 0, min(10, count($topSystems))));
$chartSystemValues = array_map(static fn (array $item): int => (int) $item['approved_count'], array_slice($topSystems, 0, min(10, count($topSystems))));
$chartBranchLabels = array_map(static fn (array $item): string => $item['name'], array_slice($topBranches, 0, min(10, count($topBranches))));
$chartBranchValues = array_map(static fn (array $item): int => (int) $item['approved_count'], array_slice($topBranches, 0, min(10, count($topBranches))));
?>

<section class="report-hero">
    <div class="report-hero-copy">
        <div class="eyebrow">Báo cáo nâng cao</div>
        <h2 class="report-title">Theo dõi hiệu quả lock phòng bằng số liệu trực quan và bảng xếp hạng linh hoạt</h2>
        <p class="report-subtitle">Bạn có thể đổi giữa thống kê cao nhất hoặc thấp nhất, đồng thời chọn quy mô hiển thị top 10, top 50 hoặc top 100 theo đúng nhu cầu quản trị.</p>
    </div>

    <form method="GET" action="<?= e(url('/reports')) ?>" class="report-filter-card">
        <div>
            <label class="form-label">Kiểu xếp hạng</label>
            <select name="ranking_mode" class="form-select">
                <option value="highest" <?= $rankingMode === 'highest' ? 'selected' : '' ?>>Cao nhất</option>
                <option value="lowest" <?= $rankingMode === 'lowest' ? 'selected' : '' ?>>Thấp nhất</option>
            </select>
        </div>
        <div>
            <label class="form-label">Quy mô top</label>
            <select name="top_limit" class="form-select">
                <option value="10" <?= $topLimit === 10 ? 'selected' : '' ?>>Top 10</option>
                <option value="50" <?= $topLimit === 50 ? 'selected' : '' ?>>Top 50</option>
                <option value="100" <?= $topLimit === 100 ? 'selected' : '' ?>>Top 100</option>
            </select>
        </div>
        <div class="report-filter-actions">
            <button type="submit" class="btn btn-primary">Cập nhật báo cáo</button>
            <a href="<?= e(url('/reports')) ?>" class="btn btn-outline-secondary">Đặt lại</a>
        </div>
        <div class="report-filter-note">Đang xem: <strong><?= e($rankingModeLabel) ?></strong> - <strong><?= e((string) $topLimit) ?></strong> dòng dữ liệu</div>
    </form>
</section>

<section class="stats-grid">
    <article class="stat-card">
        <span class="stat-label">Lock hôm nay</span>
        <strong><?= e((string) $summary['day']) ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Lock tuần này</span>
        <strong><?= e((string) $summary['week']) ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Lock tháng này</span>
        <strong><?= e((string) $summary['month']) ?></strong>
    </article>
    <article class="stat-card highlight">
        <span class="stat-label">Lock quý này</span>
        <strong><?= e((string) $summary['quarter']) ?></strong>
    </article>
</section>

<section class="stats-grid report-secondary-stats">
    <article class="stat-card">
        <span class="stat-label">Yêu cầu chờ duyệt</span>
        <strong><?= e((string) $summary['pending']) ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Tổng lock đã duyệt</span>
        <strong><?= e((string) $summary['totalApproved']) ?></strong>
    </article>
    <article class="stat-card">
        <span class="stat-label">Tổng hoàn tác lock</span>
        <strong><?= e((string) $summary['totalUndone']) ?></strong>
    </article>
</section>

<section class="content-grid report-chart-grid mt-4">
    <div class="panel-card report-chart-card">
        <div class="panel-header">
            <div>
                <h3>Xếp hạng nhân viên</h3>
                <p class="panel-subtitle mb-0">Biểu đồ top nhân viên theo số lock đã duyệt.</p>
            </div>
        </div>
        <div class="report-chart-frame report-chart-frame-md">
            <canvas id="staffReportChart"></canvas>
        </div>
    </div>

    <div class="panel-card report-chart-card">
        <div class="panel-header">
            <div>
                <h3>Xếp hạng hệ thống</h3>
                <p class="panel-subtitle mb-0">Biểu đồ so sánh số lock đã duyệt theo hệ thống.</p>
            </div>
        </div>
        <div class="report-chart-frame report-chart-frame-md">
            <canvas id="systemReportChart"></canvas>
        </div>
    </div>
</section>

<section class="panel-card report-chart-card mt-4">
    <div class="panel-header">
        <div>
            <h3>Xếp hạng chi nhánh</h3>
            <p class="panel-subtitle mb-0">Biểu đồ top chi nhánh theo số lock đã duyệt.</p>
        </div>
    </div>
    <div class="report-chart-frame report-chart-frame-lg">
        <canvas id="branchReportChart"></canvas>
    </div>
</section>

<section class="content-grid mt-4">
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3><?= e($rankingModeLabel) ?> - Nhân viên lock căn hộ dịch vụ</h3>
                <p class="panel-subtitle mb-0">Hiển thị theo bộ lọc top <?= e((string) $topLimit) ?> mà bạn đã chọn.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nhân viên</th>
                        <th>Tài khoản</th>
                        <th class="text-end">Số lock duyệt</th>
                        <th class="text-end">Hoàn tác lock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (! $topStaff): ?>
                        <tr><td colspan="5"><div class="empty-state my-3">Chưa có dữ liệu nhân viên để thống kê.</div></td></tr>
                    <?php endif; ?>
                    <?php foreach ($topStaff as $index => $item): ?>
                        <tr>
                            <td><?= e((string) ($index + 1)) ?></td>
                            <td><?= e($item['full_name']) ?></td>
                            <td><?= e($item['username']) ?></td>
                            <td class="text-end fw-semibold"><?= e((string) $item['approved_count']) ?></td>
                            <td class="text-end fw-semibold"><?= e((string) ($item['undone_count'] ?? 0)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3><?= e($rankingModeLabel) ?> - Hệ thống duy trì</h3>
                <p class="panel-subtitle mb-0">Bảng xếp hạng hệ thống theo số yêu cầu lock đã duyệt.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Hệ thống</th>
                        <th class="text-end">Số lock duyệt</th>
                        <th class="text-end">Hoàn tác lock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (! $topSystems): ?>
                        <tr><td colspan="4"><div class="empty-state my-3">Chưa có dữ liệu hệ thống.</div></td></tr>
                    <?php endif; ?>
                    <?php foreach ($topSystems as $index => $item): ?>
                        <tr>
                            <td><?= e((string) ($index + 1)) ?></td>
                            <td><?= e($item['name']) ?></td>
                            <td class="text-end fw-semibold"><?= e((string) $item['approved_count']) ?></td>
                            <td class="text-end fw-semibold"><?= e((string) ($item['undone_count'] ?? 0)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="panel-card mt-4">
    <div class="panel-header">
        <div>
            <h3><?= e($rankingModeLabel) ?> - Chi nhánh duy trì</h3>
            <p class="panel-subtitle mb-0">Danh sách chi nhánh theo bộ lọc top <?= e((string) $topLimit) ?>, có kèm hệ thống để đối chiếu nhanh.</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Chi nhánh</th>
                    <th>Hệ thống</th>
                    <th class="text-end">Số lock duyệt</th>
                    <th class="text-end">Hoàn tác lock</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! $topBranches): ?>
                    <tr><td colspan="5"><div class="empty-state my-3">Chưa có dữ liệu chi nhánh.</div></td></tr>
                <?php endif; ?>
                <?php foreach ($topBranches as $index => $item): ?>
                    <tr>
                        <td><?= e((string) ($index + 1)) ?></td>
                        <td><?= e($item['name']) ?></td>
                        <td><?= e($item['system_name']) ?></td>
                        <td class="text-end fw-semibold"><?= e((string) $item['approved_count']) ?></td>
                        <td class="text-end fw-semibold"><?= e((string) ($item['undone_count'] ?? 0)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    (() => {
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            layout: {
                padding: {
                    top: 8,
                    right: 8,
                    bottom: 0,
                    left: 0
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        };

        const buildBarChart = (elementId, labels, data, color) => {
            const element = document.getElementById(elementId);
            if (!element || labels.length === 0) {
                return;
            }

            new Chart(element, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        data,
                        backgroundColor: color,
                        borderRadius: 10,
                        maxBarThickness: 42
                    }]
                },
                options: commonOptions
            });
        };

        buildBarChart(
            'staffReportChart',
            <?= json_encode($chartStaffLabels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
            <?= json_encode($chartStaffValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
            'rgba(166, 61, 64, 0.82)'
        );

        buildBarChart(
            'systemReportChart',
            <?= json_encode($chartSystemLabels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
            <?= json_encode($chartSystemValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
            'rgba(18, 75, 158, 0.8)'
        );

        buildBarChart(
            'branchReportChart',
            <?= json_encode($chartBranchLabels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
            <?= json_encode($chartBranchValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
            'rgba(245, 107, 0, 0.78)'
        );
    })();
</script>
