<?php
require_once __DIR__ . '/config.php';

$pageTitle = $pageTitle ?? $site['name'];
$pageDescription = $pageDescription ?? 'Eco-Q House - Dịch vụ môi giới căn hộ dịch vụ chuyên nghiệp, nhanh chóng và minh bạch.';
$currentPage = $currentPage ?? 'home';
$bodyClass = $bodyClass ?? '';

$navItems = [
    'home' => ['label' => 'Trang chủ', 'url' => base_url('index.php')],
    'about' => ['label' => 'Giới thiệu', 'url' => base_url('gioi-thieu.php')],
    'news' => ['label' => 'Tin tức', 'url' => base_url('tin-tuc.php')],
    'jobs' => ['label' => 'Tuyển dụng', 'url' => base_url('tuyen-dung.php')],
    'contact' => ['label' => 'Liên hệ', 'url' => base_url('lien-he.php')],
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(base_url('assets/css/style.css')); ?>">
</head>
<body class="<?php echo htmlspecialchars($bodyClass); ?>">
    <header class="site-header">
        <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="siteNavbar">
            <div class="container">
                <a class="navbar-brand" href="<?php echo htmlspecialchars(base_url('index.php')); ?>">
                    <img src="<?php echo htmlspecialchars(base_url($site['logo'])); ?>" alt="<?php echo htmlspecialchars($site['name']); ?> logo">
                    <span>
                        <strong><?php echo htmlspecialchars($site['name']); ?></strong>
                        <small><?php echo htmlspecialchars($site['tagline']); ?></small>
                    </span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Chuyển đổi điều hướng">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <?php foreach ($navItems as $key => $item): ?>
                            <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage === $key ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($item['url']); ?>">
                                    <?php echo htmlspecialchars($item['label']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <li class="nav-item ms-lg-3">
                            <a class="btn btn-brand" href="<?php echo htmlspecialchars(base_url('lien-he.php')); ?>">Liên hệ ngay</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main>
