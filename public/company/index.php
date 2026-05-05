<?php
require_once __DIR__ . '/data/news-content.php';
require_once __DIR__ . '/data/jobs.php';

$newsItems = ecoq_news_items();

$pageTitle = 'Trang chủ - Eco-Q House';
$pageDescription = 'Landing page Eco-Q House - môi giới căn hộ dịch vụ chuyên nghiệp, tin tức hữu ích và cơ hội nghề nghiệp nổi bật.';
$currentPage = 'home';

require_once __DIR__ . '/includes/header.php';
?>
<section class="hero-section" style="background-image: linear-gradient(90deg, rgba(10, 25, 69, 0.80), rgba(10, 25, 69, 0.35)), url('<?php echo htmlspecialchars($site['hero_image']); ?>');">
    <div class="container">
        <div class="hero-content reveal-up">
            <span class="eyebrow">Eco-Q House</span>
            <h1>Cho thuê căn hộ dịch vụ dễ dàng<br>với Eco-Q House</h1>
            <p>
                Kết nối nhanh chóng giữa khách thuê và nguồn phòng chất lượng với quy trình minh bạch,
                tận tâm và tối ưu trải nghiệm tìm nơi ở phù hợp.
            </p>
            <div class="hero-actions">
                <a href="<?php echo htmlspecialchars(base_url('lien-he.php')); ?>" class="btn btn-brand btn-lg">Xem phòng ngay</a>
                <a href="<?php echo htmlspecialchars(base_url('gioi-thieu.php')); ?>" class="btn btn-outline-light btn-lg">Khám phá thêm</a>
            </div>
        </div>
    </div>
</section>

<section class="section section-intro">
    <div class="container">
        <div class="section-heading reveal-up">
            <span class="section-kicker">Về Eco-Q House</span>
            <h2>Giải pháp thuê căn hộ dịch vụ chuyên nghiệp, đồng hành từ nhu cầu đến quyết định</h2>
            <p>
                Eco-Q House xây dựng trải nghiệm môi giới căn hộ dịch vụ hiện đại, minh bạch và chỉnh chu hơn.
                Chúng tôi tập trung kết nối đúng nhu cầu, đúng khu vực và đúng ngân sách để khách hàng
                tiết kiệm thời gian nhưng vẫn có lựa chọn sống phù hợp.
            </p>
        </div>
        <div class="stats-grid">
            <article class="stat-card reveal-up">
                <strong>5+</strong>
                <span>Năm kinh nghiệm</span>
            </article>
            <article class="stat-card reveal-up" data-delay="100">
                <strong>10000+</strong>
                <span>Lượt tư vấn hỗ trợ</span>
            </article>
            <article class="stat-card reveal-up" data-delay="200">
                <strong>5000+</strong>
                <span>Nguồn phòng kết nối</span>
            </article>
            <article class="stat-card reveal-up" data-delay="300">
                <strong>98%</strong>
                <span>Khách hàng hài lòng</span>
            </article>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head-inline reveal-up">
            <div>
                <span class="section-kicker">Dự án nổi bật</span>
                <h2>Khám phá những phòng đang còn trống và sẵn sàng cho khách thuê</h2>
                <p>Các dự án căn hộ nổi bật của Eco-Q House</p>
            </div>
            <a href="<?php echo htmlspecialchars(base_url('du-an.php')); ?>" class="btn btn-outline-brand">Xem trang dự án</a>
        </div>

        <div class="project-home-prompt reveal-up">
            <div>
                <span class="section-kicker">Kho phong Eco-Q House</span>
                <h3>Danh sach phong dang hien thi duoc cap nhat tai trang du an.</h3>
                <p>Trang chu duoc giu gon va on dinh, ban co the xem toan bo phong kha dung, hinh anh va thong tin chi tiet o trang du an.</p>
            </div>
            <div class="project-home-actions">
                <a href="<?php echo htmlspecialchars(base_url('du-an.php')); ?>" class="btn btn-brand">Xem danh sach phong</a>
                <a href="<?php echo htmlspecialchars(base_url('lien-he.php')); ?>" class="btn btn-outline-brand">Nhan tu van</a>
            </div>
        </div>
        <?php if (false): ?>
            <div class="project-showcase-grid">
                <?php foreach ([] as $index => $room): ?>
                    <?php $cover = null; ?>
                    <article class="project-card project-card-featured reveal-up" data-delay="<?php echo $index * 100; ?>">
                        <a class="project-card-media" href="<?php echo htmlspecialchars(base_url('chi-tiet-du-an.php?id=' . $room['id'])); ?>">
                            <?php if ($cover && $cover['media_type'] === 'image'): ?>
                                <img src="<?php echo htmlspecialchars(room_media_url($cover['file_path'])); ?>" alt="<?php echo htmlspecialchars('Phòng ' . $room['room_number']); ?>">
                            <?php elseif ($cover && $cover['media_type'] === 'video'): ?>
                                <video src="<?php echo htmlspecialchars(room_media_url($cover['file_path'])); ?>" muted preload="metadata"></video>
                            <?php else: ?>
                                <div class="project-card-placeholder">Đang cập nhật hình ảnh</div>
                            <?php endif; ?>

                            <div class="project-card-overlay">
                                <span class="project-status-badge"><?php echo htmlspecialchars(room_status_label($room['status'])); ?></span>
                                <span class="project-card-chip">Dự án nổi bật</span>
                            </div>
                        </a>

                        <div class="project-card-body">
                            <div class="project-card-meta">
                                <span><?php echo htmlspecialchars($room['system_name']); ?></span>
                                <span><?php echo htmlspecialchars($room['district_name']); ?></span>
                            </div>
                            <h3>
                                <a href="<?php echo htmlspecialchars(base_url('chi-tiet-du-an.php?id=' . $room['id'])); ?>">
                                    <?php echo htmlspecialchars('Phòng ' . $room['room_number'] . ' - ' . $room['branch_name']); ?>
                                </a>
                            </h3>
                            <p class="project-card-location"><?php echo htmlspecialchars($room['branch_name']); ?></p>
                            <div class="project-card-description">
                                <?php echo htmlspecialchars($room['note'] ?: 'Phòng đang sẵn sàng tư vấn, phù hợp cho khách cần chốt nhanh theo khu vực.'); ?>
                            </div>
                            <div class="project-card-footer">
                                <div class="project-price"><?php echo htmlspecialchars(number_format((float) $room['price'], 0, ',', '.')); ?>đ/tháng</div>
                                <a class="project-link" href="<?php echo htmlspecialchars(base_url('chi-tiet-du-an.php?id=' . $room['id'])); ?>">
                                    Xem chi tiết <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                            <div class="project-tags">
                                <span><?php echo htmlspecialchars(room_type_label($room['room_type'])); ?></span>
                                <span><?php echo htmlspecialchars(furniture_status_label($room['furniture_status'])); ?></span>
                                <span><?php echo (int) $room['has_balcony'] === 1 ? 'Có ban công' : 'Không ban công'; ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head-inline reveal-up">
            <div>
                <span class="section-kicker">Tin tức</span>
                <h2>Tin tức mới nhất</h2>
                <p>Cập nhật những góc nhìn hữu ích về thị trường thuê căn hộ dịch vụ và kinh nghiệm sống tiện nghi.</p>
            </div>
            <a href="<?php echo htmlspecialchars(base_url('tin-tuc.php')); ?>" class="btn btn-outline-brand">Xem tất cả</a>
        </div>
        <div class="row g-4">
            <?php foreach ($newsItems as $index => $item): ?>
                <?php if ($index < 3): ?>
                    <div class="col-md-6 col-lg-4">
                        <article class="content-card reveal-up" data-delay="<?php echo $index * 100; ?>">
                            <a class="content-card-image" href="<?php echo htmlspecialchars(base_url($item['url'])); ?>">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                            </a>
                            <div class="content-card-body">
                                <span class="content-meta"><?php echo htmlspecialchars($item['category']); ?> • <?php echo format_vn_date($item['date']); ?></span>
                                <h3>
                                    <a href="<?php echo htmlspecialchars(base_url($item['url'])); ?>">
                                        <?php echo htmlspecialchars($item['title']); ?>
                                    </a>
                                </h3>
                                <p><?php echo htmlspecialchars($item['excerpt']); ?></p>
                            </div>
                        </article>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-process">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="process-visual reveal-up">
                    <div class="visual-card">
                        <span class="section-kicker">Cách thức hoạt động</span>
                        <h2>Tối ưu quy trình tìm căn hộ dịch vụ để khách hàng an tâm hơn trong từng quyết định</h2>
                        <p>
                            Chúng tôi rút gọn những bước phức tạp thành một hành trình đơn giản, minh bạch
                            và bám sát nhu cầu thực tế.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="process-steps">
                    <article class="step-item reveal-up">
                        <span>01</span>
                        <div>
                            <h3>Tiếp nhận nhu cầu thuê căn hộ dịch vụ</h3>
                            <p>Ghi nhận rõ ngân sách, khu vực, loại hình phòng và thời gian dọn vào ở.</p>
                        </div>
                    </article>
                    <article class="step-item reveal-up" data-delay="100">
                        <span>02</span>
                        <div>
                            <h3>Tư vấn lựa chọn phù hợp</h3>
                            <p>Đề xuất danh sách phòng căn hộ dịch vụ, căn hộ mini hoặc mô hình ở ghép phù hợp nhất.</p>
                        </div>
                    </article>
                    <article class="step-item reveal-up" data-delay="200">
                        <span>03</span>
                        <div>
                            <h3>Hỗ trợ xem phòng thực tế</h3>
                            <p>Đồng hành trong quá trình đánh giá vị trí, tiện ích, điều kiện sống và hợp đồng.</p>
                        </div>
                    </article>
                    <article class="step-item reveal-up" data-delay="300">
                        <span>04</span>
                        <div>
                            <h3>Chốt thuê nhanh chóng</h3>
                            <p>Hoàn thiện kết nối giữa khách thuê và chủ nhà với tinh thần minh bạch, rõ ràng.</p>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section-jobs">
    <div class="container">
        <div class="section-head-inline reveal-up">
            <div>
                <span class="section-kicker">Cơ hội nghề nghiệp</span>
                <h2>Môi trường làm việc năng động và nhiều dư địa phát triển</h2>
                <p>Chúng tôi tìm kiếm những cộng sự tử tế, chủ động và sẵn sàng đồng hành lâu dài cùng thương hiệu.</p>
            </div>
            <a href="<?php echo htmlspecialchars(base_url('tuyen-dung.php')); ?>" class="btn btn-outline-brand">Xem tuyển dụng</a>
        </div>
        <div class="row g-4">
            <?php foreach ($jobItems as $index => $job): ?>
                <?php if ($index < 3): ?>
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
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="cta-panel reveal-up">
            <div>
                <span class="section-kicker text-light">Kết nối cùng chúng tôi</span>
                <h2>Hãy để Eco-Q House đồng hành cùng bạn tìm nơi ở phù hợp</h2>
                <p>
                    Từ phòng căn hộ dịch vụ, căn hộ mini đến nhu cầu tuyển dụng, đội ngũ của chúng tôi luôn sẵn sàng
                    hỗ trợ nhanh chóng và tận tâm.
                </p>
            </div>
            <div class="cta-actions">
                <a href="<?php echo htmlspecialchars(base_url('lien-he.php')); ?>" class="btn btn-brand btn-lg">Liên hệ tư vấn</a>
                <a href="tel:0909123456" class="btn btn-outline-light btn-lg">Hotline: 0909 123 456</a>
            </div>
        </div>
    </div>
</section>

<section class="section section-testimonials">
    <div class="container">
        <div class="section-heading text-center reveal-up">
            <span class="section-kicker">Cảm nhận khách hàng</span>
            <h2>Những phản hồi tích cực tạo nên giá trị bền vững cho Eco-Q House</h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-4">
                <article class="testimonial-card reveal-up">
                    <div class="testimonial-quote">“</div>
                    <p>Nhờ Eco-Q House mà mình tìm được phòng gần công ty chỉ sau vài ngày. Cách tư vấn rất rõ ràng và cực kỳ có tâm.</p>
                    <strong>Anh Phương</strong>
                    <span>Nhân viên văn phòng</span>
                </article>
            </div>
            <div class="col-lg-4">
                <article class="testimonial-card featured reveal-up" data-delay="100">
                    <div class="testimonial-quote">99</div>
                    <p>Dịch vụ chuyên nghiệp, tác phong nhanh và hỗ trợ mình rất kỹ phần hợp đồng. Đây là trải nghiệm tìm căn hộ dịch vụ tốt nhất từ trước đến nay.</p>
                    <strong>Chị Như Quỳnh</strong>
                    <span>Khách thuê căn hộ mini</span>
                </article>
            </div>
            <div class="col-lg-4">
                <article class="testimonial-card reveal-up" data-delay="200">
                    <div class="testimonial-quote">“</div>
                    <p>Mình ấn tượng nhất là cách đội ngũ theo sát nhu cầu và chỉ gửi những lựa chọn thực sự phù hợp. Tiết kiệm rất nhiều thời gian.</p>
                    <strong>Bạn Minh Khoa</strong>
                    <span>Sinh viên</span>
                </article>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
