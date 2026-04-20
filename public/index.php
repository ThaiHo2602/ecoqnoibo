<?php

require dirname(__DIR__) . '/bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\ActivityLogController;
use App\Controllers\BranchController;
use App\Controllers\CustomerController;
use App\Controllers\DashboardController;
use App\Controllers\LockRequestController;
use App\Controllers\ProfileController;
use App\Controllers\ReportController;
use App\Controllers\RoomController;
use App\Controllers\SystemController;
use App\Controllers\UserController;

$path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/', '/');
$basePath = trim(parse_url(config('base_url'), PHP_URL_PATH) ?? '', '/');

if ($basePath !== '' && str_starts_with($path, $basePath)) {
    $path = trim(substr($path, strlen($basePath)), '/');
}

$authController = new AuthController();
$activityLogController = new ActivityLogController();
$branchController = new BranchController();
$customerController = new CustomerController();
$dashboardController = new DashboardController();
$lockRequestController = new LockRequestController();
$profileController = new ProfileController();
$reportController = new ReportController();
$roomController = new RoomController();
$systemController = new SystemController();
$userController = new UserController();

if (request_method() === 'GET' && preg_match('#^rooms/(\d+)$#', $path, $matches) === 1) {
    $roomController->show((int) $matches[1]);
    exit;
}

$routeKey = request_method() . ' /' . $path;
if ($path === '') {
    $routeKey = request_method() . ' /';
}

$routes = [
    'GET /' => fn () => $roomController->home(),
    'GET /dashboard' => fn () => $dashboardController->index(),
    'GET /login' => fn () => $authController->showLogin(),
    'POST /login' => fn () => $authController->login(),
    'POST /logout' => fn () => $authController->logout(),
    'GET /systems' => fn () => $systemController->index(),
    'POST /systems/store' => fn () => $systemController->store(),
    'POST /systems/update' => fn () => $systemController->update(),
    'POST /systems/delete' => fn () => $systemController->delete(),
    'GET /branches' => fn () => $branchController->index(),
    'POST /branches/store' => fn () => $branchController->store(),
    'POST /branches/update' => fn () => $branchController->update(),
    'POST /branches/delete' => fn () => $branchController->delete(),
    'GET /rooms' => fn () => $roomController->index(),
    'POST /rooms/store' => fn () => $roomController->store(),
    'POST /rooms/update' => fn () => $roomController->update(),
    'POST /rooms/delete' => fn () => $roomController->delete(),
    'GET /customers' => fn () => $customerController->index(),
    'POST /customers/store' => fn () => $customerController->store(),
    'POST /customers/update' => fn () => $customerController->update(),
    'POST /customers/delete' => fn () => $customerController->delete(),
    'POST /customers/assign' => fn () => $customerController->assign(),
    'POST /customers/progress' => fn () => $customerController->progress(),
    'GET /reports' => fn () => $reportController->index(),
    'GET /lock-requests' => fn () => $lockRequestController->index(),
    'POST /lock-requests/store' => fn () => $lockRequestController->store(),
    'POST /lock-requests/approve' => fn () => $lockRequestController->approve(),
    'POST /lock-requests/reject' => fn () => $lockRequestController->reject(),
    'GET /users' => fn () => $userController->index(),
    'GET /activity-logs' => fn () => $activityLogController->index(),
    'POST /users/store' => fn () => $userController->store(),
    'POST /users/update' => fn () => $userController->update(),
    'POST /users/delete' => fn () => $userController->delete(),
    'GET /profile/password' => fn () => $profileController->password(),
    'POST /profile/password' => fn () => $profileController->updatePassword(),
];

if (! array_key_exists($routeKey, $routes)) {
    abort(404, 'Tuyến đường không tồn tại.');
}

$routes[$routeKey]();
