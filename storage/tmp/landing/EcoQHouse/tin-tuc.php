<?php
require_once __DIR__ . '/data/news.php';

$pageTitle = 'Tin tức - Eco-Q House';
$pageDescription = 'Tin tức mới nhất từ Eco-Q House về thị trường thuê căn hộ dịch vụ và kinh nghiệm sống tiện nghi.';
$currentPage = 'news';

require_once __DIR__ . '/includes/header.php';
?>
<section class="page-hero page-hero-light">
    <div class="container">
        <span class="section-kicker">Tin tức</span>
        <h1>Những bài viết mới nhất từ Eco-Q House</h1>
        <p>Cập nhật kiến thức thuê căn hộ dịch vụ, thị trường phòng ở và các mẹo hữu ích cho cuộc sống thuận tiện hơn.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($newsItems as $index => $item): ?>
                <div class="col-md-6 col-lg-4">
                    <article class="content-card reveal-up" data-delay="<?php echo ($index % 3) * 100; ?>">
                        <a class="content-card-image" href="<?php echo htmlspecialchars(base_url('bai-viet.php?slug=' . urlencode($item['slug']))); ?>">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        </a>
                        <div class="content-card-body">
                            <span class="content-meta"><?php echo htmlspecialchars($item['category']); ?> • <?php echo format_vn_date($item['date']); ?></span>
                            <h3><a href="<?php echo htmlspecialchars(base_url('bai-viet.php?slug=' . urlencode($item['slug']))); ?>"><?php echo htmlspecialchars($item['title']); ?></a></h3>
                            <p><?php echo htmlspecialchars($item['excerpt']); ?></p>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
