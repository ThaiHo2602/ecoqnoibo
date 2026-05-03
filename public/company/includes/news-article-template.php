<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../data/news-content.php';

if (! isset($articleSlug) || ! is_string($articleSlug) || $articleSlug === '') {
    throw new RuntimeException('Thiếu slug bài viết để render trang chi tiết tin tức.');
}

$newsItems = ecoq_news_items();
$article = ecoq_find_news_by_slug($articleSlug);

if ($article === null) {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy bài viết - Eco-Q House';
    $pageDescription = 'Bài viết bạn đang tìm hiện không tồn tại hoặc đã được cập nhật đường dẫn.';
    $currentPage = 'news';
    require_once __DIR__ . '/header.php';
    ?>
    <section class="page-hero page-hero-light">
        <div class="container">
            <span class="section-kicker">404</span>
            <h1>Bài viết không tồn tại</h1>
            <p>Liên kết bạn truy cập hiện không còn khả dụng hoặc bài viết chưa được cập nhật.</p>
            <a href="<?php echo htmlspecialchars(base_url('tin-tuc')); ?>" class="btn btn-brand mt-3">Quay lại trang tin tức</a>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/footer.php';
    return;
}

$pageTitle = $article['title'] . ' - Eco-Q House';
$pageDescription = $article['meta_description'] ?? $article['excerpt'];
$currentPage = 'news';
$bodyClass = 'news-detail-body';

$articleIndex = $article['_index'] ?? 0;
$publishedDate = format_vn_date($article['date']);
$canonicalUrl = base_url($article['url']);
$prevArticle = $newsItems[$articleIndex - 1] ?? null;
$nextArticle = $newsItems[$articleIndex + 1] ?? null;
$relatedArticles = array_values(array_filter(
    $newsItems,
    static fn (array $item): bool => ($item['slug'] ?? '') !== $article['slug']
));
$relatedArticles = array_slice($relatedArticles, 0, 3);
$topics = $article['topics'] ?? [];

require_once __DIR__ . '/header.php';
?>

<div class="news-reading-progress" data-reading-progress></div>

<section class="news-detail-shell">
    <div class="container">
        <div class="news-detail-breadcrumb reveal-up">
            <a href="<?php echo htmlspecialchars(base_url()); ?>">Trang chủ</a>
            <span><i class="bi bi-chevron-right"></i></span>
            <a href="<?php echo htmlspecialchars(base_url('tin-tuc')); ?>">Tin tức</a>
            <span><i class="bi bi-chevron-right"></i></span>
            <span><?php echo htmlspecialchars($article['title']); ?></span>
        </div>

        <section class="news-hero-glass reveal-up">
            <div class="news-hero-media">
                <img src="<?php echo htmlspecialchars($article['hero_image'] ?? $article['image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
            </div>

            <div class="news-hero-overlay">
                <div class="news-hero-content-card">
                    <span class="news-category-pill"><?php echo htmlspecialchars($article['category']); ?></span>
                    <h1><?php echo htmlspecialchars($article['title']); ?></h1>
                    <p><?php echo htmlspecialchars($article['excerpt']); ?></p>

                    <div class="news-meta-row">
                        <span><i class="bi bi-calendar3"></i> <?php echo htmlspecialchars($publishedDate); ?></span>
                        <span><i class="bi bi-clock-history"></i> <?php echo htmlspecialchars((string) ($article['reading_minutes'] ?? 5)); ?> phút đọc</span>
                        <span><i class="bi bi-house-heart"></i> Bởi <?php echo htmlspecialchars($article['author'] ?? 'Eco-Q House'); ?></span>
                    </div>
                </div>

                <div class="news-share-card">
                    <div class="news-share-title">Chia sẻ bài viết</div>
                    <div class="news-share-actions">
                        <a href="<?php echo htmlspecialchars('https://www.facebook.com/sharer/sharer.php?u=' . urlencode($canonicalUrl)); ?>" target="_blank" rel="noopener noreferrer" aria-label="Chia sẻ Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="<?php echo htmlspecialchars('https://zalo.me/share?url=' . urlencode($canonicalUrl)); ?>" target="_blank" rel="noopener noreferrer" aria-label="Chia sẻ Zalo">
                            <span>Zalo</span>
                        </a>
                        <button type="button" class="news-copy-link-btn" data-copy-link="<?php echo htmlspecialchars($canonicalUrl); ?>" aria-label="Sao chép liên kết">
                            <i class="bi bi-link-45deg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="news-detail-layout">
            <article class="news-article-card reveal-up">
                <div class="news-article-intro">
                    <?php foreach ($article['intro'] as $introParagraph): ?>
                        <p><?php echo htmlspecialchars($introParagraph); ?></p>
                    <?php endforeach; ?>
                </div>

                <?php foreach ($article['sections'] as $section): ?>
                    <section class="news-article-section" id="<?php echo htmlspecialchars($section['id']); ?>">
                        <h2><?php echo htmlspecialchars($section['title']); ?></h2>

                        <?php foreach ($section['paragraphs'] as $paragraph): ?>
                            <p><?php echo htmlspecialchars($paragraph); ?></p>
                        <?php endforeach; ?>

                        <?php if (! empty($section['quote'])): ?>
                            <blockquote class="news-quote-glass">
                                <div class="news-quote-icon"><i class="bi bi-quote"></i></div>
                                <p><?php echo htmlspecialchars($section['quote']); ?></p>
                            </blockquote>
                        <?php endif; ?>

                        <?php if (! empty($section['image'])): ?>
                            <figure class="news-inline-figure">
                                <img src="<?php echo htmlspecialchars($section['image']); ?>" alt="<?php echo htmlspecialchars($section['title']); ?>">
                            </figure>
                        <?php endif; ?>

                        <?php if (! empty($section['gallery'])): ?>
                            <div class="news-inline-gallery">
                                <?php foreach ($section['gallery'] as $galleryImage): ?>
                                    <figure class="news-inline-gallery-item">
                                        <img src="<?php echo htmlspecialchars($galleryImage); ?>" alt="<?php echo htmlspecialchars($section['title']); ?>">
                                    </figure>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (! empty($section['checklist'])): ?>
                            <div class="news-checklist-card">
                                <?php foreach ($section['checklist'] as $item): ?>
                                    <div class="news-checklist-item">
                                        <span class="news-check-icon"><i class="bi bi-check2"></i></span>
                                        <span><?php echo htmlspecialchars($item); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </section>
                <?php endforeach; ?>
            </article>

            <aside class="news-sidebar reveal-up">
                <div class="news-sidebar-card">
                    <h3>Mục lục bài viết</h3>
                    <ul class="news-toc-list">
                        <?php foreach ($article['sections'] as $section): ?>
                            <li><a href="#<?php echo htmlspecialchars($section['id']); ?>"><?php echo htmlspecialchars($section['title']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="news-sidebar-card">
                    <h3>Bài viết liên quan</h3>
                    <div class="news-related-list">
                        <?php foreach ($relatedArticles as $related): ?>
                            <a class="news-related-item" href="<?php echo htmlspecialchars(base_url($related['url'])); ?>">
                                <div class="news-related-thumb">
                                    <img src="<?php echo htmlspecialchars($related['image']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                                </div>
                                <div class="news-related-body">
                                    <span class="news-related-badge"><?php echo htmlspecialchars($related['category']); ?></span>
                                    <strong><?php echo htmlspecialchars($related['title']); ?></strong>
                                    <small><?php echo htmlspecialchars(format_vn_date($related['date'])); ?> · <?php echo htmlspecialchars((string) ($related['reading_minutes'] ?? 5)); ?> phút đọc</small>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if ($topics !== []): ?>
                    <div class="news-sidebar-card">
                        <h3>Chủ đề phổ biến</h3>
                        <div class="news-topic-tags">
                            <?php foreach ($topics as $tag): ?>
                                <span><?php echo htmlspecialchars($tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </aside>
        </section>

        <section class="news-cta-panel reveal-up">
            <div>
                <span class="news-mini-kicker">Eco-Q House</span>
                <h2><?php echo htmlspecialchars($article['cta_title'] ?? 'Bạn đang tìm căn hộ phù hợp?'); ?></h2>
                <p><?php echo htmlspecialchars($article['cta_text'] ?? 'Đội ngũ Eco-Q House sẽ giúp bạn chọn nhanh căn hộ dịch vụ đúng nhu cầu, minh bạch và tiết kiệm thời gian.'); ?></p>
            </div>
            <div class="news-cta-actions">
                <a href="<?php echo htmlspecialchars(base_url('du-an')); ?>" class="btn btn-news-gradient">Tìm căn hộ ngay</a>
                <a href="<?php echo htmlspecialchars(base_url('lien-he')); ?>" class="btn btn-news-ghost">Liên hệ tư vấn</a>
            </div>
        </section>

        <section class="news-post-navigation reveal-up">
            <?php if ($prevArticle): ?>
                <a class="news-nav-card" href="<?php echo htmlspecialchars(base_url($prevArticle['url'])); ?>">
                    <span class="news-nav-icon"><i class="bi bi-arrow-left"></i></span>
                    <div>
                        <small>Bài viết trước</small>
                        <strong><?php echo htmlspecialchars($prevArticle['title']); ?></strong>
                    </div>
                </a>
            <?php else: ?>
                <div class="news-nav-card news-nav-card-disabled">
                    <span class="news-nav-icon"><i class="bi bi-arrow-left"></i></span>
                    <div>
                        <small>Bài viết trước</small>
                        <strong>Đây là bài đầu tiên</strong>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($nextArticle): ?>
                <a class="news-nav-card news-nav-card-next" href="<?php echo htmlspecialchars(base_url($nextArticle['url'])); ?>">
                    <div>
                        <small>Bài viết tiếp theo</small>
                        <strong><?php echo htmlspecialchars($nextArticle['title']); ?></strong>
                    </div>
                    <span class="news-nav-icon"><i class="bi bi-arrow-right"></i></span>
                </a>
            <?php else: ?>
                <div class="news-nav-card news-nav-card-disabled news-nav-card-next">
                    <div>
                        <small>Bài viết tiếp theo</small>
                        <strong>Đây là bài mới nhất</strong>
                    </div>
                    <span class="news-nav-icon"><i class="bi bi-arrow-right"></i></span>
                </div>
            <?php endif; ?>
        </section>

        <section class="news-newsletter-card reveal-up">
            <div class="news-newsletter-brand">
                <span class="news-newsletter-logo"><i class="bi bi-house-heart"></i></span>
                <div>
                    <h3>Đăng ký nhận tin</h3>
                    <p>Nhận ngay bài viết mới nhất, mẹo thuê nhà và xu hướng căn hộ dịch vụ từ Eco-Q House.</p>
                </div>
            </div>
            <form class="news-newsletter-form" action="<?php echo htmlspecialchars(base_url('lien-he')); ?>" method="get">
                <input type="email" name="email" placeholder="Nhập email của bạn" aria-label="Email nhận tin">
                <button type="submit" aria-label="Gửi đăng ký">
                    <i class="bi bi-arrow-up-right"></i>
                </button>
            </form>
        </section>
    </div>
</section>

<script>
    (function () {
        const progressBar = document.querySelector('[data-reading-progress]');
        const copyButton = document.querySelector('[data-copy-link]');

        const updateProgress = () => {
            if (!progressBar) {
                return;
            }

            const documentHeight = document.documentElement.scrollHeight - window.innerHeight;
            const progress = documentHeight > 0 ? Math.min(100, Math.max(0, (window.scrollY / documentHeight) * 100)) : 0;
            progressBar.style.width = progress + '%';
        };

        window.addEventListener('scroll', updateProgress, { passive: true });
        updateProgress();

        if (copyButton) {
            copyButton.addEventListener('click', async function () {
                const url = copyButton.getAttribute('data-copy-link') || window.location.href;

                try {
                    await navigator.clipboard.writeText(url);
                    copyButton.classList.add('is-copied');
                    copyButton.innerHTML = '<i class="bi bi-check2"></i>';

                    window.setTimeout(function () {
                        copyButton.classList.remove('is-copied');
                        copyButton.innerHTML = '<i class="bi bi-link-45deg"></i>';
                    }, 1600);
                } catch (error) {
                    window.prompt('Sao chép liên kết bài viết:', url);
                }
            });
        }
    })();
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
