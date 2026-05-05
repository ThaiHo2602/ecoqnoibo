<?php
require_once __DIR__ . '/data/jobs.php';

$pageTitle = 'Tuyển dụng - Eco-Q House';
$pageDescription = 'Khám phá các vị trí tuyển dụng mới nhất tại Eco-Q House.';
$currentPage = 'jobs';

require_once __DIR__ . '/includes/header.php';
?>
<section class="page-hero page-hero-light">
    <div class="container">
        <span class="section-kicker">Tuyển dụng</span>
        <h1>Gia nhập đội ngũ Eco-Q House</h1>
        <p>
            Chúng tôi tìm kiếm những cộng sự yêu thích dịch vụ, chủ động phát triển và sẵn sàng tạo ra giá trị bền vững.
        </p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading text-center reveal-up">
            <span class="section-kicker">Tuyển dụng</span>
            <h2>Những vị trí đang mở tại Eco-Q House</h2>
            <p>
                Theo dõi các cơ hội nghề nghiệp mới nhất với thông tin rõ ràng, dễ xem và có thể truy cập ngay vào trang chi tiết từng vị trí.
            </p>
        </div>
        <div class="jobs-overview reveal-up">
            <div class="jobs-overview-copy">
                <span class="section-kicker">Môi trường làm việc</span>
                <h2>Cơ hội dành cho những người muốn phát triển nhanh trong lĩnh vực nhà ở và dịch vụ khách hàng</h2>
                <p>
                    Eco-Q House xây dựng một môi trường đề cao sự tử tế, chủ động và tinh thần đồng hành.
                    Chúng tôi không chỉ tuyển người phù hợp với công việc, mà còn tìm những cộng sự muốn đi đường dài cùng thương hiệu.
                </p>
            </div>
            <div class="jobs-overview-stats">
                <div>
                    <strong><?php echo count($jobItems); ?></strong>
                    <span>Vị trí đang tuyển</span>
                </div>
                <div>
                    <strong>3</strong>
                    <span>Phòng ban mở rộng</span>
                </div>
                <div>
                    <strong>100%</strong>
                    <span>Được đào tạo nội bộ</span>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach ($jobItems as $index => $job): ?>
                <div class="col-md-6 col-lg-4">
                    <article class="job-card job-card-dashboard reveal-up" data-delay="<?php echo ($index % 3) * 100; ?>">
                        <a class="job-card-cover" href="<?php echo htmlspecialchars(base_url('viec-lam.php?slug=' . urlencode($job['slug']))); ?>" aria-label="<?php echo htmlspecialchars($job['title']); ?>">
                            <img src="<?php echo htmlspecialchars($job['image']); ?>" alt="<?php echo htmlspecialchars($job['title']); ?>">
                        </a>
                        <div class="job-card-body">
                            <div class="job-card-meta">
                                <span><?php echo format_vn_date($job['date']); ?></span>
                                <span>Tuyển dụng</span>
                            </div>
                            <span class="job-badge"><?php echo htmlspecialchars($job['type']); ?></span>
                            <h3>
                                <a href="<?php echo htmlspecialchars(base_url('viec-lam.php?slug=' . urlencode($job['slug']))); ?>">
                                    <?php echo htmlspecialchars($job['title']); ?>
                                </a>
                            </h3>
                            <p><?php echo htmlspecialchars($job['excerpt']); ?></p>
                            <ul class="job-card-info">
                                <li><i class="bi bi-geo-alt"></i><?php echo htmlspecialchars($job['location']); ?></li>
                                <li><i class="bi bi-cash-stack"></i><?php echo htmlspecialchars($job['salary']); ?></li>
                                <li><i class="bi bi-calendar-check"></i>Hạn nộp: <?php echo htmlspecialchars($job['deadline']); ?></li>
                            </ul>
                            <div class="job-card-actions">
                                <a class="job-link" href="<?php echo htmlspecialchars(base_url('viec-lam.php?slug=' . urlencode($job['slug']))); ?>">
                                    Xem chi tiết <i class="bi bi-arrow-right"></i>
                                </a>
                                <a class="job-link-secondary" href="<?php echo htmlspecialchars(base_url('viec-lam.php?slug=' . urlencode($job['slug']))); ?>">
                                    Đọc bài đăng
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
