<?php
$pageTitle = 'Liên hệ - Eco-Q House';
$pageDescription = 'Liên hệ Eco-Q House để được tư vấn thuê căn hộ dịch vụ hoặc hợp tác phát triển nguồn phòng.';
$currentPage = 'contact';

require_once __DIR__ . '/includes/header.php';
$contactSuccess = \App\Core\Session::getFlash('contact_success');
$contactError = \App\Core\Session::getFlash('contact_error');
?>
<section class="page-hero page-hero-light">
    <div class="container">
        <span class="section-kicker">Liên hệ</span>
        <h1>Hãy để Eco-Q House hỗ trợ bạn nhanh chóng và tận tâm</h1>
        <p>Chúng tôi luôn sẵn sàng tiếp nhận nhu cầu tìm phòng, hợp tác nguồn phòng hoặc ứng tuyển nghề nghiệp.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="contact-info reveal-up">
                    <h2>Thông tin liên hệ</h2>
                    <ul>
                        <li><i class="bi bi-telephone-fill"></i><span><?php echo htmlspecialchars($site['phone']); ?></span></li>
                        <li><i class="bi bi-envelope-fill"></i><span><?php echo htmlspecialchars($site['email']); ?></span></li>
                        <li><i class="bi bi-geo-alt-fill"></i><span><?php echo htmlspecialchars($site['address']); ?></span></li>
                        <li><i class="bi bi-clock-fill"></i><span><?php echo htmlspecialchars($site['working_hours']); ?></span></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-7">
                <form class="contact-form reveal-up" action="<?php echo htmlspecialchars(base_url('lien-he')); ?>" method="post">
                    <?php echo function_exists('csrf_field') ? csrf_field() : ''; ?>
                    <?php if ($contactSuccess): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($contactSuccess); ?></div>
                    <?php endif; ?>
                    <?php if ($contactError): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($contactError); ?></div>
                    <?php endif; ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nhập họ và tên" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Nhập số điện thoại" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email">
                        </div>
                        <div class="col-md-6">
                            <label for="topic" class="form-label">Nhu cầu</label>
                            <select class="form-select" id="topic" name="topic" required>
                                <option value="">Chọn nhu cầu</option>
                                <option value="Tìm phòng căn hộ dịch vụ">Tìm phòng căn hộ dịch vụ</option>
                                <option value="Tìm căn hộ mini">Tìm căn hộ mini</option>
                                <option value="Hợp tác nguồn phòng">Hợp tác nguồn phòng</option>
                                <option value="Ứng tuyển">Ứng tuyển</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="message" class="form-label">Nội dung</label>
                            <textarea class="form-control" id="message" name="message" rows="5" placeholder="Chia sẻ nhu cầu của bạn" required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-brand btn-lg">Gửi thông tin</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
