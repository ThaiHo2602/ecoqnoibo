<?php
require_once __DIR__ . '/data/jobs.php';

$slug = $_GET['slug'] ?? '';
$jobDetail = null;

foreach ($jobItems as $item) {
    if ($item['slug'] === $slug) {
        $jobDetail = $item;
        break;
    }
}

$relatedJobs = array_values(array_filter($jobItems, static function ($item) use ($slug) {
    return $item['slug'] !== $slug;
}));

if ($jobDetail === null) {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy vị trí - Eco-Q House';
    $currentPage = 'jobs';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="page-hero page-hero-light">
        <div class="container">
            <span class="section-kicker">404</span>
            <h1>Vị trí tuyển dụng không tồn tại</h1>
            <p>Liên kết bạn truy cập hiện không còn khả dụng hoặc vị trí đã được cập nhật.</p>
            <a href="<?php echo htmlspecialchars(base_url('tuyen-dung.php')); ?>" class="btn btn-brand mt-3">Quay lại trang tuyển dụng</a>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $jobDetail['title'] . ' - Eco-Q House';
$pageDescription = $jobDetail['excerpt'];
$currentPage = 'jobs';

require_once __DIR__ . '/includes/header.php';
?>
<section class="job-detail-hero" style="background-image: linear-gradient(90deg, rgba(13, 28, 69, 0.82), rgba(13, 28, 69, 0.48)), url('<?php echo htmlspecialchars($jobDetail['image']); ?>');">
    <div class="container">
        <div class="job-detail-hero-content reveal-up">
            <span class="section-kicker text-light">Tuyển dụng</span>
            <h1><?php echo htmlspecialchars($jobDetail['title']); ?></h1>
            <p><?php echo htmlspecialchars($jobDetail['location']); ?> • <?php echo htmlspecialchars($jobDetail['salary']); ?> • <?php echo htmlspecialchars($jobDetail['type']); ?></p>
            <a href="<?php echo htmlspecialchars(base_url('lien-he.php')); ?>" class="btn btn-brand">Ứng tuyển ngay</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <article class="job-detail-content reveal-up">
                    <section class="job-detail-section">
                        <h2>Mô tả công việc</h2>
                        <p class="lead"><?php echo htmlspecialchars($jobDetail['excerpt']); ?></p>
                        <?php foreach ($jobDetail['summary'] as $paragraph): ?>
                            <p><?php echo htmlspecialchars($paragraph); ?></p>
                        <?php endforeach; ?>
                    </section>

                    <section class="job-detail-section">
                        <h2>Trách nhiệm công việc</h2>
                        <ul class="job-detail-list">
                            <?php foreach ($jobDetail['responsibilities'] as $item): ?>
                                <li><?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>

                    <section class="job-detail-section">
                        <h2>Yêu cầu ứng viên</h2>
                        <ul class="job-detail-list">
                            <?php foreach ($jobDetail['requirements'] as $item): ?>
                                <li><?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>

                    <section class="job-detail-section">
                        <h2>Quyền lợi</h2>
                        <ul class="job-detail-list">
                            <?php foreach ($jobDetail['benefits'] as $item): ?>
                                <li><?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>

                    <section class="job-detail-section">
                        <h2>Lịch làm việc</h2>
                        <div class="job-schedule-grid">
                            <?php foreach ($jobDetail['schedule'] as $item): ?>
                                <article class="job-schedule-card">
                                    <span><?php echo htmlspecialchars($item['label']); ?></span>
                                    <strong><?php echo htmlspecialchars($item['value']); ?></strong>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                </article>
            </div>
            <div class="col-lg-4">
                <aside class="job-sidebar reveal-up" data-delay="100">
                    <div class="job-sidebar-card">
                        <h3>Thông tin chung</h3>
                        <ul class="job-sidebar-list">
                            <li><span>Mức lương</span><strong><?php echo htmlspecialchars($jobDetail['salary']); ?></strong></li>
                            <li><span>Địa điểm</span><strong><?php echo htmlspecialchars($jobDetail['location']); ?></strong></li>
                            <li><span>Hình thức</span><strong><?php echo htmlspecialchars($jobDetail['type']); ?></strong></li>
                            <li><span>Kinh nghiệm</span><strong><?php echo htmlspecialchars($jobDetail['experience']); ?></strong></li>
                            <li><span>Phòng ban</span><strong><?php echo htmlspecialchars($jobDetail['department']); ?></strong></li>
                            <li><span>Số lượng</span><strong><?php echo htmlspecialchars($jobDetail['openings']); ?></strong></li>
                            <li><span>Hạn nộp</span><strong><?php echo htmlspecialchars($jobDetail['deadline']); ?></strong></li>
                        </ul>
                        <a href="<?php echo htmlspecialchars(base_url('lien-he.php')); ?>" class="btn btn-brand w-100">Ứng tuyển ngay</a>
                        <a href="<?php echo htmlspecialchars(base_url('tuyen-dung.php')); ?>" class="btn btn-outline-brand w-100 mt-3">Xem các vị trí khác</a>
                    </div>

                    <div class="job-sidebar-card">
                        <h3>Ứng tuyển tại Eco-Q House</h3>
                        <p>Gửi CV của bạn về email hoặc để lại thông tin liên hệ, đội ngũ tuyển dụng sẽ phản hồi trong thời gian sớm nhất.</p>
                        <div class="job-sidebar-contact">
                            <a href="tel:0909123456"><i class="bi bi-telephone-fill"></i> 0909 123 456</a>
                            <a href="mailto:hello@ecoqhouse.vn"><i class="bi bi-envelope-fill"></i> hello@ecoqhouse.vn</a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>

<section class="section section-process">
    <div class="container">
        <div class="section-heading text-center reveal-up">
            <span class="section-kicker">Quy trình ứng tuyển</span>
            <h2>Ứng tuyển đơn giản và rõ ràng</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <article class="feature-card reveal-up">
                    <i class="bi bi-send-check"></i>
                    <h3>Gửi thông tin ứng tuyển</h3>
                    <p>Ứng viên gửi CV hoặc thông tin cá nhân cơ bản qua form liên hệ, email hoặc hotline của Eco-Q House.</p>
                </article>
            </div>
            <div class="col-md-4">
                <article class="feature-card reveal-up" data-delay="100">
                    <i class="bi bi-person-check"></i>
                    <h3>Phỏng vấn phù hợp</h3>
                    <p>Đội ngũ tuyển dụng sàng lọc hồ sơ và mời phỏng vấn những ứng viên đáp ứng tiêu chí của vị trí.</p>
                </article>
            </div>
            <div class="col-md-4">
                <article class="feature-card reveal-up" data-delay="200">
                    <i class="bi bi-stars"></i>
                    <h3>Nhận phản hồi nhanh</h3>
                    <p>Kết quả được phản hồi sớm cùng lộ trình onboarding rõ ràng để ứng viên bắt nhịp công việc thuận lợi.</p>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="cta-panel reveal-up">
            <div>
                <span class="section-kicker text-light">Sẵn sàng gia nhập đội ngũ?</span>
                <h2>Eco-Q House luôn chào đón những cộng sự tử tế, chủ động và muốn phát triển dài hạn</h2>
                <p>Nếu bạn đang tìm một môi trường làm việc thực chiến nhưng vẫn nhiều hỗ trợ, đây có thể là cơ hội phù hợp để bắt đầu.</p>
            </div>
            <div class="cta-actions">
                <a href="<?php echo htmlspecialchars(base_url('lien-he.php')); ?>" class="btn btn-brand btn-lg">Nộp hồ sơ ngay</a>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head-inline reveal-up">
            <div>
                <span class="section-kicker">Các vị trí đang tuyển</span>
                <h2>Khám phá thêm cơ hội nghề nghiệp khác</h2>
            </div>
            <a href="<?php echo htmlspecialchars(base_url('tuyen-dung.php')); ?>" class="btn btn-outline-brand">Xem tất cả</a>
        </div>
        <div class="row g-4">
            <?php foreach (array_slice($relatedJobs, 0, 3) as $index => $job): ?>
                <div class="col-md-6 col-lg-4">
                    <article class="job-card job-card-dashboard reveal-up" data-delay="<?php echo $index * 100; ?>">
                        <a class="job-card-cover" href="<?php echo htmlspecialchars(base_url('viec-lam.php?slug=' . urlencode($job['slug']))); ?>" aria-label="<?php echo htmlspecialchars($job['title']); ?>">
                            <img src="<?php echo htmlspecialchars($job['image']); ?>" alt="<?php echo htmlspecialchars($job['title']); ?>">
                        </a>
                        <div class="job-card-body">
                            <div class="job-card-meta">
                                <span><?php echo format_vn_date($job['date']); ?></span>
                                <span>Tuyển dụng</span>
                            </div>
                            <span class="job-badge"><?php echo htmlspecialchars($job['type']); ?></span>
                            <h3><a href="<?php echo htmlspecialchars(base_url('viec-lam.php?slug=' . urlencode($job['slug']))); ?>"><?php echo htmlspecialchars($job['title']); ?></a></h3>
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
