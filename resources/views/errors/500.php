<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? '500') ?> | <?= e(config('name')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(asset('assets/css/app.css')) ?>" rel="stylesheet">
</head>
<body class="error-body">
    <div class="error-card">
        <div class="display-4 fw-bold">500</div>
        <h1 class="h3 mt-3">He thong dang gap loi</h1>
        <p class="text-muted"><?= e($message ?: 'Co loi xay ra trong qua trinh xu ly yeu cau.') ?></p>
        <a href="<?= e(url('/')) ?>" class="btn btn-primary">Thu tai trang chu</a>
    </div>
</body>
</html>
