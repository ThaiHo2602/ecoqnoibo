<?php
require_once __DIR__ . '/data/news.php';

$slug = $_GET['slug'] ?? '';
$article = null;

foreach ($newsItems as $item) {
    if ($item['slug'] === $slug) {
        $article = $item;
        break;
    }
}

if ($article === null) {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy bài viết - Eco-Q House';
    $currentPage = 'news';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="page-hero page-hero-light">
        <div class="container">
            <span class="section-kicker">404</span>
            <h1>Bài viết không tồn tại</h1>
            <p>Liên kết bạn truy cập hiện không còn khả dụng hoặc bài viết chưa được cập nhật.</p>
            <a href="<?php echo htmlspecialchars(base_url('tin-tuc.php')); ?>" class="btn btn-brand mt-3">Quay lại trang tin tức</a>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $article['title'] . ' - Eco-Q House';
$pageDescription = $article['excerpt'];
$currentPage = 'news';

require_once __DIR__ . '/includes/header.php';
?>
<section class="article-hero">
    <div class="container">
        <span class="section-kicker"><?php echo htmlspecialchars($article['category']); ?></span>
        <h1><?php echo htmlspecialchars($article['title']); ?></h1>
        <p><?php echo format_vn_date($article['date']); ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="article-cover reveal-up">
            <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
        </div>
        <article class="article-content reveal-up">
            <p class="lead"><?php echo htmlspecialchars($article['excerpt']); ?></p>
            <?php foreach ($article['content'] as $paragraph): ?>
                <p><?php echo htmlspecialchars($paragraph); ?></p>
            <?php endforeach; ?>
            <a href="<?php echo htmlspecialchars(base_url('tin-tuc.php')); ?>" class="btn btn-outline-brand mt-4">Quay lại danh sách tin tức</a>
        </article>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
