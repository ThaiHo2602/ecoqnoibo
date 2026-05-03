<?php

require dirname(__DIR__) . '/bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\ActivityLogController;
use App\Controllers\BranchController;
use App\Controllers\CustomerController;
use App\Controllers\DashboardController;
use App\Controllers\ContactController;
use App\Controllers\LockRequestController;
use App\Controllers\ProfileController;
use App\Controllers\ReportController;
use App\Controllers\RoomController;
use App\Controllers\SystemController;
use App\Controllers\UserController;
use App\Controllers\WardController;
use App\Core\Auth;

$path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/', '/');
$basePath = trim(parse_url(app_base_url(), PHP_URL_PATH) ?? '', '/');

if ($basePath !== '' && str_starts_with($path, $basePath)) {
    $path = trim(substr($path, strlen($basePath)), '/');
}

$legacyLandingRedirects = [
    'index.php' => '/',
    'gioi-thieu.php' => '/gioi-thieu',
    'du-an.php' => '/du-an',
    'chi-tiet-du-an.php' => '/chi-tiet-du-an',
    'tin-tuc.php' => '/tin-tuc',
    'bai-viet.php' => '/bai-viet',
    'tuyen-dung.php' => '/tuyen-dung',
    'viec-lam.php' => '/viec-lam',
    'lien-he.php' => '/lien-he',
];

if (request_method() === 'GET' && array_key_exists($path, $legacyLandingRedirects)) {
    $queryString = $_SERVER['QUERY_STRING'] ?? '';
    $target = $legacyLandingRedirects[$path];

    if ($queryString !== '') {
        $target .= '?' . $queryString;
    }

    redirect($target);
}

$authController = new AuthController();
$activityLogController = new ActivityLogController();
$branchController = new BranchController();
$contactController = new ContactController();
$customerController = new CustomerController();
$dashboardController = new DashboardController();
$lockRequestController = new LockRequestController();
$profileController = new ProfileController();
$reportController = new ReportController();
$roomController = new RoomController();
$systemController = new SystemController();
$userController = new UserController();
$wardController = new WardController();

$renderCompanyPage = static function (string $file): void {
    require public_path('company/' . ltrim($file, '/'));
};

if (request_method() === 'GET' && preg_match('#^rooms/(\d+)$#', $path, $matches) === 1) {
    $roomController->show((int) $matches[1]);
    exit;
}

if (request_method() === 'GET' && preg_match('#^rooms/(\d+)/manage-data$#', $path, $matches) === 1) {
    $roomController->manageData((int) $matches[1]);
    exit;
}

if (request_method() === 'GET' && preg_match('#^rooms/(\d+)/download-images$#', $path, $matches) === 1) {
    $roomController->downloadImages((int) $matches[1]);
    exit;
}

$routeKey = request_method() . ' /' . $path;
if ($path === '') {
    $routeKey = request_method() . ' /';
}

$routes = [
    'GET /' => fn () => $renderCompanyPage('index.php'),
    'GET /index.php' => fn () => redirect('/'),
    'GET /gioi-thieu' => fn () => $renderCompanyPage('gioi-thieu.php'),
    'GET /gioi-thieu.php' => fn () => $renderCompanyPage('gioi-thieu.php'),
    'GET /du-an' => fn () => $renderCompanyPage('du-an.php'),
    'GET /du-an.php' => fn () => $renderCompanyPage('du-an.php'),
    'GET /chi-tiet-du-an' => fn () => $renderCompanyPage('chi-tiet-du-an.php'),
    'GET /chi-tiet-du-an.php' => fn () => $renderCompanyPage('chi-tiet-du-an.php'),
    'GET /tin-tuc' => fn () => $renderCompanyPage('tin-tuc.php'),
    'GET /tin-tuc.php' => fn () => $renderCompanyPage('tin-tuc.php'),
    'GET /bai-viet' => fn () => $renderCompanyPage('bai-viet.php'),
    'GET /bai-viet.php' => fn () => $renderCompanyPage('bai-viet.php'),
    'GET /tuyen-dung' => fn () => $renderCompanyPage('tuyen-dung.php'),
    'GET /tuyen-dung.php' => fn () => $renderCompanyPage('tuyen-dung.php'),
    'GET /viec-lam' => fn () => $renderCompanyPage('viec-lam.php'),
    'GET /viec-lam.php' => fn () => $renderCompanyPage('viec-lam.php'),
    'GET /lien-he' => fn () => $renderCompanyPage('lien-he.php'),
    'GET /lien-he.php' => fn () => $renderCompanyPage('lien-he.php'),
    'POST /lien-he' => fn () => $contactController->send(),
    'POST /lien-he.php' => fn () => $contactController->send(),
    'GET /company' => fn () => redirect('/'),
    'GET /app' => fn () => $roomController->home(),
    'GET /dashboard' => fn () => $dashboardController->index(),
    'GET /login' => fn () => $authController->showLogin(),
    'POST /login' => fn () => $authController->login(),
    'POST /logout' => fn () => $authController->logout(),
    'GET /systems' => fn () => $systemController->index(),
    'POST /systems/store' => fn () => $systemController->store(),
    'POST /systems/update' => fn () => $systemController->update(),
    'POST /systems/delete' => fn () => $systemController->delete(),
    'POST /systems/bulk-delete' => fn () => $systemController->bulkDelete(),
    'GET /wards' => fn () => $wardController->index(),
    'POST /wards/store' => fn () => $wardController->store(),
    'POST /wards/update' => fn () => $wardController->update(),
    'POST /wards/delete' => fn () => $wardController->delete(),
    'POST /wards/bulk-delete' => fn () => $wardController->bulkDelete(),
    'GET /branches' => fn () => $branchController->index(),
    'POST /branches/store' => fn () => $branchController->store(),
    'POST /branches/update' => fn () => $branchController->update(),
    'POST /branches/delete' => fn () => $branchController->delete(),
    'POST /branches/bulk-delete' => fn () => $branchController->bulkDelete(),
    'GET /rooms' => fn () => $roomController->index(),
    'POST /rooms/store' => fn () => $roomController->store(),
    'POST /rooms/update' => fn () => $roomController->update(),
    'POST /rooms/delete' => fn () => $roomController->delete(),
    'POST /rooms/bulk-delete' => fn () => $roomController->bulkDelete(),
    'GET /customers' => fn () => $customerController->index(),
    'POST /customers' => fn () => $customerController->store(),
    'GET /customers/store' => fn () => $customerController->storeFromQuery(),
    'GET /customers/update' => fn () => redirect('/customers'),
    'GET /customers/delete' => fn () => redirect('/customers'),
    'GET /customers/assign' => fn () => redirect('/customers'),
    'GET /customers/confirm-assignment' => fn () => redirect('/customers'),
    'GET /customers/reject-assignment' => fn () => redirect('/customers'),
    'GET /customers/progress' => fn () => redirect('/customers'),
    'POST /customers/store' => fn () => $customerController->store(),
    'POST /customers/update' => fn () => $customerController->update(),
    'POST /customers/delete' => fn () => $customerController->delete(),
    'POST /customers/assign' => fn () => $customerController->assign(),
    'POST /customers/confirm-assignment' => fn () => $customerController->confirmAssignment(),
    'POST /customers/reject-assignment' => fn () => $customerController->rejectAssignment(),
    'POST /customers/progress' => fn () => $customerController->progress(),
    'GET /reports' => fn () => $reportController->index(),
    'GET /lock-requests' => fn () => $lockRequestController->index(),
    'POST /lock-requests/store' => fn () => $lockRequestController->store(),
    'POST /lock-requests/approve' => fn () => $lockRequestController->approve(),
    'POST /lock-requests/reject' => fn () => $lockRequestController->reject(),
    'POST /lock-requests/undo' => fn () => $lockRequestController->undo(),
    'GET /users' => fn () => $userController->index(),
    'GET /activity-logs' => fn () => $activityLogController->index(),
    'POST /users/store' => fn () => $userController->store(),
    'POST /users/update' => fn () => $userController->update(),
    'POST /users/unlock' => fn () => $userController->unlock(),
    'POST /users/delete' => fn () => $userController->delete(),
    'GET /profile/password' => fn () => $profileController->password(),
    'POST /profile/password' => fn () => $profileController->updatePassword(),
];

if (! array_key_exists($routeKey, $routes)) {
    abort(404, 'Tuyến đường không tồn tại.');
}

$routes[$routeKey]();
