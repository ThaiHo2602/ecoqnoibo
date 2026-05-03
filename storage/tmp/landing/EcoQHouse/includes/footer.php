<?php require_once __DIR__ . '/config.php'; ?>
    </main>
    <footer class="site-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <a class="footer-brand" href="<?php echo htmlspecialchars(base_url('index.php')); ?>">
                        <img src="<?php echo htmlspecialchars(base_url($site['logo'])); ?>" alt="<?php echo htmlspecialchars($site['name']); ?> logo">
                        <div>
                            <strong><?php echo htmlspecialchars($site['name']); ?></strong>
                            <p><?php echo htmlspecialchars($site['tagline']); ?></p>
                        </div>
                    </a>
                    <p class="footer-text">
                        Đồng hành cùng khách hàng trên hành trình tìm kiếm nơi ở phù hợp bằng sự minh bạch,
                        tận tâm và tốc độ phục vụ chuyên nghiệp.
                    </p>
                </div>
                <div class="col-sm-6 col-lg-2">
                    <h3 class="footer-title">Điều hướng</h3>
                    <ul class="footer-links">
                        <li><a href="<?php echo htmlspecialchars(base_url('index.php')); ?>">Trang chủ</a></li>
                        <li><a href="<?php echo htmlspecialchars(base_url('gioi-thieu.php')); ?>">Giới thiệu</a></li>
                        <li><a href="<?php echo htmlspecialchars(base_url('tin-tuc.php')); ?>">Tin tức</a></li>
                        <li><a href="<?php echo htmlspecialchars(base_url('tuyen-dung.php')); ?>">Tuyển dụng</a></li>
                        <li><a href="<?php echo htmlspecialchars(base_url('lien-he.php')); ?>">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <h3 class="footer-title">Thông tin</h3>
                    <ul class="footer-contact">
                        <li><i class="bi bi-telephone-fill"></i><span><?php echo htmlspecialchars($site['phone']); ?></span></li>
                        <li><i class="bi bi-envelope-fill"></i><span><?php echo htmlspecialchars($site['email']); ?></span></li>
                        <li><i class="bi bi-geo-alt-fill"></i><span><?php echo htmlspecialchars($site['address']); ?></span></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h3 class="footer-title">Kết nối nhanh</h3>
                    <div class="footer-socials">
                        <a href="<?php echo htmlspecialchars($site['socials']['facebook']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="<?php echo htmlspecialchars($site['socials']['tiktok']); ?>" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                        <a href="<?php echo htmlspecialchars($site['socials']['zalo']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Zalo"><span>Zalo</span></a>
                    </div>
                    <div class="footer-cta">
                        <span>Hỗ trợ tư vấn miễn phí</span>
                        <a href="<?php echo htmlspecialchars(base_url('lien-he.php')); ?>" class="btn btn-outline-light">Gửi yêu cầu</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo current_year(); ?> <?php echo htmlspecialchars($site['name']); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <div class="floating-actions" aria-label="Liên kết nhanh của Eco-Q House">
        <a class="floating-action floating-action-tiktok" href="<?php echo htmlspecialchars($site['socials']['tiktok']); ?>" target="_blank" rel="noopener noreferrer" aria-label="TikTok Eco-Q House">
            <span class="floating-action-ring"></span>
            <span class="floating-action-core"><i class="bi bi-tiktok"></i></span>
        </a>
        <a class="floating-action floating-action-zalo" href="<?php echo htmlspecialchars($site['socials']['zalo']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Zalo Eco-Q House">
            <span class="floating-action-ring"></span>
            <span class="floating-action-core floating-action-text">Zalo</span>
        </a>
        <a class="floating-action floating-action-phone" href="<?php echo htmlspecialchars($site['socials']['phone_link']); ?>" aria-label="Gọi điện cho Eco-Q House">
            <span class="floating-action-ring"></span>
            <span class="floating-action-core"><i class="bi bi-telephone-fill"></i></span>
        </a>
        <a class="floating-action floating-action-facebook" href="<?php echo htmlspecialchars($site['socials']['facebook']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook Eco-Q House">
            <span class="floating-action-ring"></span>
            <span class="floating-action-core"><i class="bi bi-facebook"></i></span>
        </a>
        <button class="floating-action floating-action-top" id="backToTop" type="button" aria-label="Quay lại đầu trang">
            <span class="floating-action-ring"></span>
            <span class="floating-action-core"><i class="bi bi-arrow-up"></i></span>
        </button>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo htmlspecialchars(base_url('assets/js/main.js')); ?>"></script>
</body>
</html>
