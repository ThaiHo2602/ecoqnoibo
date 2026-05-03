<?php

use App\Core\Auth;
use App\Core\Session;

$currentUser = Auth::user();
$successMessage = Session::getFlash('success');
$errorMessage = Session::getFlash('error');
$pageTitle = $pageTitle ?? 'Trang chủ';
$pageDescription = $pageDescription ?? 'Nền tảng nội bộ giúp Eco-Q House quản lý hệ thống, chi nhánh, phòng, khách hàng và toàn bộ quy trình lock tập trung.';
$logoUrl = asset('assets/images/logo.png');
$faviconUrl = asset('assets/images/logo.png');
$appCssFile = public_path('assets/css/app.css');
$appCssVersion = is_file($appCssFile) ? (string) filemtime($appCssFile) : '1';
$inlineAppCss = is_file($appCssFile) ? (string) file_get_contents($appCssFile) : '';
$bootstrapCssUrl = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css';
$bootstrapCssFallbackUrl = 'https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css';
$appCssUrl = asset('assets/css/app.css?v=' . $appCssVersion);
$appCssFallbackUrl = asset('assets/css/app.css?v=' . $appCssVersion);
$roleName = Auth::resolveRoleName($currentUser);
$isManagerOrDirector = in_array($roleName, ['manager', 'director'], true);
$isDirector = $roleName === 'director';
$isManager = $roleName === 'manager';
$isCollaborator = $roleName === 'collaborator';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(config('name')) ?></title>
    <link rel="icon" type="image/png" href="<?= e($faviconUrl) ?>">
    <link rel="shortcut icon" href="<?= e($faviconUrl) ?>">
    <link rel="apple-touch-icon" href="<?= e($faviconUrl) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= e($bootstrapCssUrl) ?>" rel="stylesheet" onerror="if(!this.dataset.fallback){this.dataset.fallback='1';this.href='<?= e($bootstrapCssFallbackUrl) ?>';}">
    <link href="<?= e($appCssUrl) ?>" rel="stylesheet" onerror="if(!this.dataset.fallback){this.dataset.fallback='1';this.href='<?= e($appCssFallbackUrl) ?>';}">
    <style>
        body.app-body {
            margin: 0;
            font-family: "Be Vietnam Pro", sans-serif;
            color: #14284a;
            background:
                radial-gradient(circle at top left, rgba(23, 58, 122, 0.14), transparent 28%),
                radial-gradient(circle at top right, rgba(216, 170, 65, 0.22), transparent 24%),
                radial-gradient(circle at bottom left, rgba(57, 105, 191, 0.12), transparent 22%),
                linear-gradient(180deg, #fafdff 0%, #f4f8ff 45%, #edf2ff 100%);
        }

        .app-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 300px minmax(0, 1fr);
            gap: 24px;
            padding: 20px;
        }

        .sidebar {
            position: sticky;
            top: 20px;
            height: calc(100vh - 40px);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 24px 20px;
            background: linear-gradient(180deg, rgba(13, 35, 73, 0.9), rgba(18, 42, 87, 0.82));
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 32px;
            box-shadow: 0 34px 80px rgba(14, 33, 70, 0.16);
            color: #fff;
        }

        .brand-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.08);
            text-decoration: none;
            color: inherit;
        }

        .brand-logo-wrap {
            width: 62px;
            height: 62px;
            border-radius: 20px;
            display: grid;
            place-items: center;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.12);
        }

        .brand-logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 8px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            min-height: 48px;
            padding: 12px 14px;
            border-radius: 16px;
            color: rgba(255, 255, 255, 0.82);
            text-decoration: none;
            font-weight: 600;
        }

        .sidebar-link.active {
            color: #fff;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), rgba(216, 170, 65, 0.14));
        }

        .main-content {
            min-width: 0;
            padding: 4px 0 40px;
        }

        .toast-stack {
            position: fixed;
            right: 22px;
            bottom: 22px;
            z-index: 2200;
            display: grid;
            gap: 10px;
            width: min(360px, calc(100vw - 32px));
        }

        .app-toast {
            position: relative;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            align-items: start;
            padding: 14px 16px;
            border-radius: 16px;
            color: #fff;
            box-shadow: 0 16px 38px rgba(16, 43, 90, 0.18);
            font-weight: 700;
            line-height: 1.5;
            animation: toastIn 0.22s ease-out both;
        }

        .toast-close {
            width: 24px;
            height: 24px;
            border: 0;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
            font-size: 18px;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .toast-close:hover {
            background: rgba(255, 255, 255, 0.28);
        }

        .app-toast.is-hiding {
            animation: toastOut 0.2s ease-in both;
        }

        .toast-success {
            background: #0f7a55;
        }

        .toast-error {
            background: #b4232a;
        }

        @keyframes toastIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes toastOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(10px); }
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            margin-bottom: 22px;
            padding: 22px 24px;
            border-radius: 32px;
            background: rgba(255, 255, 255, 0.74);
            border: 1px solid rgba(255, 255, 255, 0.68);
            box-shadow: 0 14px 30px rgba(16, 43, 90, 0.08);
        }

        @media (max-width: 991.98px) {
            .app-shell {
                grid-template-columns: 1fr;
                padding: 16px;
            }

            .sidebar {
                position: static;
                height: auto;
            }
        }
    </style>
    <style><?php require __DIR__ . '/app-inline-css.php'; ?></style>
    <?php if ($inlineAppCss !== ''): ?>
        <style><?= $inlineAppCss ?></style>
    <?php endif; ?>
</head>
<body class="app-body">
    <div class="app-shell" data-app-shell>
        <div class="app-overlay" data-sidebar-overlay></div>

        <aside class="sidebar" data-app-sidebar>
            <div>
                <a href="<?= e(url('/app')) ?>" class="brand-card brand-link">
                    <div class="brand-logo-wrap">
                        <img
                            src="<?= e($logoUrl) ?>"
                            alt="Eco-Q House"
                            class="brand-logo"
                            onerror="this.style.display='none'; this.parentNode.classList.add('is-fallback'); this.parentNode.innerHTML='<span class=&quot;brand-logo-fallback&quot;>EQ</span>';"
                        >
                    </div>
                    <div>
                        <div class="brand-name"><?= e(config('name')) ?></div>
                        <div class="brand-subtitle">Quản lý căn hộ dịch vụ nội bộ thông minh</div>
                    </div>
                </a>

                <nav class="nav flex-column gap-2 mt-4 sidebar-nav">
                    <a class="nav-link sidebar-link <?= is_current_path('/app') ? 'active' : '' ?>" href="<?= e(url('/app')) ?>">Trang chủ</a>

                    <?php if ($currentUser && $isManagerOrDirector): ?>
                        <a class="nav-link sidebar-link <?= is_current_path('/dashboard') ? 'active' : '' ?>" href="<?= e(url('/dashboard')) ?>">Bảng điều khiển</a>
                    <?php endif; ?>

                    <?php if ($currentUser && $isManagerOrDirector): ?>
                        <a class="nav-link sidebar-link <?= is_current_path('/systems') ? 'active' : '' ?>" href="<?= e(url('/systems')) ?>">Hệ thống</a>
                        <a class="nav-link sidebar-link <?= is_current_path('/wards') ? 'active' : '' ?>" href="<?= e(url('/wards')) ?>">Phường</a>
                        <a class="nav-link sidebar-link <?= is_current_path('/branches') ? 'active' : '' ?>" href="<?= e(url('/branches')) ?>">Chi nhánh</a>
                    <?php endif; ?>

                    <?php if (! $isManager): ?>
                        <a class="nav-link sidebar-link <?= is_current_path('/customers') ? 'active' : '' ?>" href="<?= e(url('/customers')) ?>">Khách hàng</a>
                    <?php endif; ?>

                    <?php if (! $isCollaborator): ?>
                        <a class="nav-link sidebar-link <?= is_path_prefix('/rooms') ? 'active' : '' ?>" href="<?= e(url('/rooms')) ?>">Phòng</a>
                        <a class="nav-link sidebar-link <?= is_current_path('/lock-requests') ? 'active' : '' ?>" href="<?= e(url('/lock-requests')) ?>">
                            <?= $currentUser && $isManagerOrDirector ? 'Duyệt lock' : 'Yêu cầu lock' ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($currentUser && $isManagerOrDirector): ?>
                        <a class="nav-link sidebar-link <?= is_current_path('/reports') ? 'active' : '' ?>" href="<?= e(url('/reports')) ?>">Báo cáo</a>
                    <?php endif; ?>

                    <?php if ($currentUser && $isDirector): ?>
                        <a class="nav-link sidebar-link <?= is_current_path('/users') ? 'active' : '' ?>" href="<?= e(url('/users')) ?>">Người dùng</a>
                        <a class="nav-link sidebar-link <?= is_current_path('/activity-logs') ? 'active' : '' ?>" href="<?= e(url('/activity-logs')) ?>">Nhật ký hoạt động</a>
                    <?php endif; ?>

                    <?php if ($currentUser): ?>
                        <a class="nav-link sidebar-link <?= is_current_path('/profile/password') ? 'active' : '' ?>" href="<?= e(url('/profile/password')) ?>">Đổi mật khẩu</a>
                    <?php endif; ?>
                </nav>
            </div>

            <div class="sidebar-footer">
                <div class="small text-white-50">Đang đăng nhập với</div>
                <div class="fw-semibold"><?= e($currentUser['full_name'] ?? '') ?></div>
                <div class="text-white-50 small"><?= e($currentUser['role_display_name'] ?? '') ?></div>
            </div>
        </aside>

        <button
            type="button"
            class="sidebar-edge-toggle"
            aria-label="Thu gọn menu"
            title="Thu gọn menu"
            data-sidebar-desktop-toggle
        ></button>

        <main class="main-content" data-app-main>
            <header class="topbar">
                <div class="topbar-heading">
                    <button
                        type="button"
                        class="sidebar-toggle"
                        aria-label="Mở menu"
                        data-sidebar-toggle
                    >
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>

                    <div>
                        <div class="topbar-badge">Eco-Q House</div>
                        <h1 class="page-title mb-1"><?= e($pageTitle) ?></h1>
                        <div class="page-subtitle"><?= e($pageDescription) ?></div>
                    </div>
                </div>

                <div class="topbar-actions">
                    <div class="topbar-user-card">
                        <img
                            src="<?= e($logoUrl) ?>"
                            alt="Logo Eco-Q House"
                            class="topbar-user-logo"
                            onerror="this.style.display='none';"
                        >
                        <div>
                            <div class="fw-semibold"><?= e($currentUser['full_name'] ?? '') ?></div>
                            <div class="text-muted small"><?= e($currentUser['role_display_name'] ?? '') ?></div>
                        </div>
                    </div>

                    <form method="POST" action="<?= e(url('/logout')) ?>">
                        <button type="submit" class="btn btn-outline-danger">Đăng xuất</button>
                    </form>
                </div>
            </header>

            <?php if ($successMessage || $errorMessage): ?>
                <div class="toast-stack" data-toast-stack>
                    <?php if ($successMessage): ?>
                        <div class="app-toast toast-success" data-app-toast>
                            <span><?= e($successMessage) ?></span>
                            <button type="button" class="toast-close" data-toast-close aria-label="Đóng thông báo">&times;</button>
                        </div>
                    <?php endif; ?>
                    <?php if ($errorMessage): ?>
                        <div class="app-toast toast-error" data-app-toast>
                            <span><?= e($errorMessage) ?></span>
                            <button type="button" class="toast-close" data-toast-close aria-label="Đóng thông báo">&times;</button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const body = document.body;
            const appOrigin = window.location.origin;

            const closeSidebar = function () {
                body.classList.remove('sidebar-open');
            };

            const applyDesktopSidebarState = function (isCollapsed) {
                body.classList.toggle('sidebar-collapsed', isCollapsed);
                const desktopToggle = document.querySelector('[data-sidebar-desktop-toggle]');
                if (desktopToggle) {
                    desktopToggle.setAttribute('aria-label', isCollapsed ? 'Mở menu' : 'Thu gọn menu');
                    desktopToggle.setAttribute('title', isCollapsed ? 'Mở menu' : 'Thu gọn menu');
                }
            };

            const loadDesktopSidebarState = function () {
                applyDesktopSidebarState(window.localStorage.getItem('ecoq-sidebar-collapsed') === '1');
            };

            const setLoading = function (isLoading) {
                body.classList.toggle('app-loading', isLoading);
            };

            const isModifiedClick = function (event) {
                return event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0;
            };

            const isHtmlResponse = function (documentText) {
                return documentText.includes('<html') || documentText.includes('<body') || documentText.includes('data-app-shell');
            };

            const parseHtml = function (html) {
                return new DOMParser().parseFromString(html, 'text/html');
            };

            const loadedScriptSources = new Set(
                Array.from(document.querySelectorAll('script[src]'))
                    .map(function (script) {
                        return script.src;
                    })
                    .filter(Boolean)
            );

            const executeScripts = async function (container) {
                const scripts = Array.from(container.querySelectorAll('script'));

                for (const oldScript of scripts) {
                    const source = oldScript.getAttribute('src');

                    if (source && loadedScriptSources.has(new URL(source, appOrigin).toString())) {
                        oldScript.remove();
                        continue;
                    }

                    const newScript = document.createElement('script');

                    Array.from(oldScript.attributes).forEach(function (attribute) {
                        newScript.setAttribute(attribute.name, attribute.value);
                    });

                    if (!source) {
                        newScript.textContent = oldScript.textContent;
                        oldScript.replaceWith(newScript);
                        continue;
                    }

                    await new Promise(function (resolve) {
                        newScript.onload = function () {
                            loadedScriptSources.add(newScript.src);
                            resolve();
                        };

                        newScript.onerror = function () {
                            console.warn('Không thể tải script:', newScript.src);
                            resolve();
                        };

                        oldScript.replaceWith(newScript);
                    });
                }
            };

            const replaceShellSections = async function (nextDocument) {
                const nextSidebar = nextDocument.querySelector('[data-app-sidebar]');
                const nextMain = nextDocument.querySelector('[data-app-main]');
                const currentSidebar = document.querySelector('[data-app-sidebar]');
                const currentMain = document.querySelector('[data-app-main]');

                if (!nextSidebar || !nextMain || !currentSidebar || !currentMain) {
                    return false;
                }

                currentSidebar.replaceWith(nextSidebar);
                currentMain.replaceWith(nextMain);
                document.title = nextDocument.title || document.title;
                closeSidebar();
                await executeScripts(nextMain);
                hydrateToasts();

                return true;
            };

            const removeToast = function (toast) {
                if (!toast || toast.dataset.removing === '1') {
                    return;
                }

                toast.dataset.removing = '1';
                toast.classList.add('is-hiding');
                window.setTimeout(function () {
                    toast.remove();
                    const stack = document.querySelector('[data-toast-stack]');
                    if (stack && !stack.querySelector('[data-app-toast]')) {
                        stack.remove();
                    }
                }, 220);
            };

            const hydrateToasts = function () {
                document.querySelectorAll('[data-app-toast]').forEach(function (toast) {
                    if (toast.dataset.bound === '1') {
                        return;
                    }

                    toast.dataset.bound = '1';
                    const closeButton = toast.querySelector('[data-toast-close]');
                    if (closeButton) {
                        closeButton.addEventListener('click', function () {
                            removeToast(toast);
                        });
                    }

                    window.setTimeout(function () {
                        removeToast(toast);
                    }, 2000);
                });
            };

            window.appShowToast = function (type, message) {
                let stack = document.querySelector('[data-toast-stack]');
                if (!stack) {
                    stack = document.createElement('div');
                    stack.className = 'toast-stack';
                    stack.setAttribute('data-toast-stack', '');
                    const main = document.querySelector('[data-app-main]') || document.body;
                    main.prepend(stack);
                }

                const toast = document.createElement('div');
                toast.className = 'app-toast ' + (type === 'error' ? 'toast-error' : 'toast-success');
                toast.setAttribute('data-app-toast', '');

                const text = document.createElement('span');
                text.textContent = message || '';
                toast.appendChild(text);

                const closeButton = document.createElement('button');
                closeButton.type = 'button';
                closeButton.className = 'toast-close';
                closeButton.setAttribute('data-toast-close', '');
                closeButton.setAttribute('aria-label', 'Đóng thông báo');
                closeButton.innerHTML = '&times;';
                toast.appendChild(closeButton);

                stack.appendChild(toast);
                hydrateToasts();
            };

            const buildUrlWithFormData = function (action, formData) {
                const targetUrl = new URL(action, appOrigin);
                const searchParams = new URLSearchParams(targetUrl.search);
                const seenKeys = new Set();

                formData.forEach(function (value, key) {
                    if (typeof value !== 'string') {
                        return;
                    }

                    if (!seenKeys.has(key)) {
                        searchParams.delete(key);
                        seenKeys.add(key);
                    }

                    searchParams.append(key, value);
                });

                targetUrl.search = searchParams.toString();

                return targetUrl.toString();
            };

            const shouldHandleLink = function (link, event) {
                if (!link || isModifiedClick(event) || event.defaultPrevented) {
                    return false;
                }

                if (link.hasAttribute('download') || link.getAttribute('target') === '_blank' || link.dataset.noAjax !== undefined) {
                    return false;
                }

                const href = link.getAttribute('href');

                if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
                    return false;
                }

                const url = new URL(href, appOrigin);

                if (url.origin !== appOrigin) {
                    return false;
                }

                if (!url.pathname.startsWith('/')) {
                    return false;
                }

                if (url.pathname.startsWith('/company')) {
                    return false;
                }

                return Boolean(link.closest('[data-app-sidebar], [data-app-main]'));
            };

            const shouldHandleForm = function (form, event) {
                if (!form || event.defaultPrevented || form.dataset.noAjax !== undefined) {
                    return false;
                }

                if (form.getAttribute('target')) {
                    return false;
                }

                const action = form.getAttribute('action') || window.location.href;
                const url = new URL(action, appOrigin);

                if (url.origin !== appOrigin) {
                    return false;
                }

                return Boolean(form.closest('[data-app-sidebar], [data-app-main]'));
            };

            const visit = async function (url, options) {
                const method = options.method || 'GET';
                const pushState = options.pushState !== false;
                const formData = options.formData || null;
                const submitter = options.submitter || null;
                const preserveScroll = options.preserveScroll === true;
                const previousScrollY = window.scrollY;

                try {
                    setLoading(true);

                    const fetchOptions = {
                        method: method,
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-App-Pjax': '1',
                        },
                    };

                    let requestUrl = url;

                    if (formData) {
                        if (method === 'GET') {
                            requestUrl = buildUrlWithFormData(url, formData);
                        } else {
                            if (submitter && submitter.name && !formData.has(submitter.name)) {
                                formData.append(submitter.name, submitter.value || '');
                            }

                            fetchOptions.body = formData;
                        }
                    }

                    const response = await fetch(requestUrl, fetchOptions);
                    const html = await response.text();

                    if (!isHtmlResponse(html)) {
                        window.location.href = response.url || requestUrl;
                        return;
                    }

                    const nextDocument = parseHtml(html);

                    if (!await replaceShellSections(nextDocument)) {
                        window.location.href = response.url || requestUrl;
                        return;
                    }

                    const finalUrl = response.url || requestUrl;

                    if (pushState) {
                        window.history.pushState({}, '', finalUrl);
                    }

                    if (preserveScroll) {
                        window.scrollTo({ top: previousScrollY, behavior: 'auto' });
                    } else {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                } catch (error) {
                    console.error(error);
                    window.location.href = url;
                } finally {
                    setLoading(false);
                }
            };

            document.addEventListener('click', function (event) {
                const toggleButton = event.target.closest('[data-sidebar-toggle]');
                if (toggleButton) {
                    body.classList.toggle('sidebar-open');
                    return;
                }

                const overlay = event.target.closest('[data-sidebar-overlay]');
                if (overlay) {
                    closeSidebar();
                    return;
                }

                const desktopToggle = event.target.closest('[data-sidebar-desktop-toggle]');
                if (desktopToggle) {
                    const nextState = !body.classList.contains('sidebar-collapsed');
                    applyDesktopSidebarState(nextState);
                    window.localStorage.setItem('ecoq-sidebar-collapsed', nextState ? '1' : '0');
                    return;
                }

                const link = event.target.closest('a');
                if (!shouldHandleLink(link, event)) {
                    return;
                }

                event.preventDefault();
                visit(link.href, { method: 'GET' });
            });

            document.addEventListener('submit', function (event) {
                const form = event.target;

                if (!shouldHandleForm(form, event)) {
                    return;
                }

                event.preventDefault();

                const method = ((form.getAttribute('method') || 'GET').toUpperCase());
                const action = form.getAttribute('action') || window.location.href;
                const formData = new FormData(form);

                visit(action, {
                    method: method,
                    formData: formData,
                    submitter: event.submitter || null,
                    preserveScroll: form.dataset.preserveScroll !== undefined,
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeSidebar();
                }
            });

            window.addEventListener('popstate', function () {
                visit(window.location.href, {
                    method: 'GET',
                    pushState: false,
                });
            });

            hydrateToasts();

            loadDesktopSidebarState();

            window.addEventListener('resize', function () {
                if (window.innerWidth < 992) {
                    applyDesktopSidebarState(false);
                } else {
                    loadDesktopSidebarState();
                }
            });

            loadDesktopSidebarState();
        });
    </script>
</body>
</html>
