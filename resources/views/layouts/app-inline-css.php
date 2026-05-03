<?php /* Inline fallback CSS for internal app */ ?>
:root {
    --bg: #f5f8ff;
    --panel: rgba(255, 255, 255, 0.78);
    --ink: #14284a;
    --muted: #6d7a92;
    --brand: #173a7a;
    --brand-strong: #102b5a;
    --shadow-sm: 0 14px 30px rgba(16, 43, 90, 0.08);
    --shadow-md: 0 24px 60px rgba(17, 42, 90, 0.12);
    --shadow-lg: 0 34px 80px rgba(14, 33, 70, 0.16);
    --radius-lg: 32px;
    --radius-md: 24px;
    --scrollbar-size: 8px;
    --scrollbar-thumb: rgba(255, 255, 255, 0.34);
    --scrollbar-thumb-hover: rgba(255, 255, 255, 0.62);
}

html,
* {
    scrollbar-width: thin;
    scrollbar-color: var(--scrollbar-thumb) transparent;
}

::-webkit-scrollbar {
    width: var(--scrollbar-size);
    height: var(--scrollbar-size);
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    min-height: 48px;
    border-radius: 999px;
    background-color: var(--scrollbar-thumb);
    border: 2px solid transparent;
    background-clip: padding-box;
    -webkit-backdrop-filter: blur(14px);
    backdrop-filter: blur(14px);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.18),
        0 8px 24px rgba(16, 43, 90, 0.08);
    transition: background-color 220ms cubic-bezier(.22, 1, .36, 1), box-shadow 220ms cubic-bezier(.22, 1, .36, 1);
}

::-webkit-scrollbar-thumb:hover {
    background-color: var(--scrollbar-thumb-hover);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.28),
        0 10px 28px rgba(16, 43, 90, 0.12);
}

::-webkit-scrollbar-corner {
    background: transparent;
}

body.app-body {
    margin: 0;
    font-family: "Be Vietnam Pro", sans-serif;
    color: var(--ink);
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
    box-shadow: var(--shadow-lg);
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

.brand-name {
    font-weight: 800;
    font-size: 1.05rem;
    text-transform: uppercase;
}

.brand-subtitle {
    font-size: 0.88rem;
    color: rgba(255, 255, 255, 0.72);
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

.sidebar-footer {
    padding: 18px;
    border-radius: 22px;
    background: rgba(255, 255, 255, 0.08);
}

.main-content {
    min-width: 0;
    padding: 4px 0 40px;
}

body.sidebar-collapsed .app-shell {
    grid-template-columns: 0 minmax(0, 1fr);
}

body.sidebar-collapsed .sidebar {
    transform: translateX(calc(-100% - 36px));
    opacity: 0;
    pointer-events: none;
}

.sidebar-edge-toggle {
    position: fixed;
    left: 306px;
    top: 50%;
    z-index: 45;
    width: 34px;
    height: 64px;
    border: 1px solid rgba(255, 255, 255, 0.72);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.92);
    box-shadow: 0 14px 34px rgba(16, 43, 90, 0.16);
    transform: translateY(-50%);
}

.sidebar-edge-toggle::before {
    content: "";
    display: block;
    width: 11px;
    height: 11px;
    margin: auto;
    border-top: 2px solid var(--brand-strong);
    border-right: 2px solid var(--brand-strong);
    transform: rotate(-135deg);
}

body.sidebar-collapsed .sidebar-edge-toggle {
    left: 18px;
}

body.sidebar-collapsed .sidebar-edge-toggle::before {
    transform: rotate(45deg);
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 18px;
    margin-bottom: 22px;
    padding: 22px 24px;
    border-radius: 32px;
    background: rgba(255, 255, 255, 0.78);
    border: 1px solid rgba(255, 255, 255, 0.68);
    box-shadow: var(--shadow-sm);
}

.topbar-heading {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    min-width: 0;
}

.topbar-badge,
.eyebrow {
    display: inline-flex;
    align-items: center;
    padding: 8px 14px;
    margin-bottom: 10px;
    border-radius: 999px;
    background: linear-gradient(135deg, rgba(23, 58, 122, 0.08), rgba(216, 170, 65, 0.2));
    color: var(--brand);
    font-size: 0.8rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.12em;
}

.page-title {
    margin: 0;
    font-size: clamp(1.8rem, 2.8vw, 2.8rem);
    font-weight: 800;
    color: var(--brand-strong);
}

.page-subtitle {
    max-width: 760px;
    color: var(--muted);
    line-height: 1.7;
}

.topbar-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.topbar-user-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.7);
}

.topbar-user-logo {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    object-fit: contain;
    background: #fff;
    padding: 4px;
}

.panel-card,
.hero-panel,
.listing-hero,
.listing-filter-panel,
.listing-card,
.empty-state-card {
    border-radius: 32px;
    background: var(--panel);
    border: 1px solid rgba(255, 255, 255, 0.72);
    box-shadow: var(--shadow-sm);
    backdrop-filter: blur(18px);
}

.hero-panel,
.listing-hero,
.listing-filter-panel {
    padding: 26px 28px;
    margin-bottom: 22px;
}

.hero-panel h2,
.listing-title {
    margin: 0 0 10px;
    font-size: clamp(1.85rem, 2.8vw, 2.7rem);
    line-height: 1.18;
    color: var(--brand-strong);
    font-weight: 800;
}

.listing-subtitle,
.report-subtitle {
    margin: 0;
    color: var(--muted);
    line-height: 1.78;
}

.listing-hero {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(260px, 0.8fr);
    gap: 20px;
}

.listing-summary {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}

.listing-summary div {
    padding: 18px;
    border-radius: 22px;
    background: rgba(255, 255, 255, 0.7);
}

.listing-summary span {
    display: block;
    font-size: 0.85rem;
    color: var(--muted);
}

.listing-summary strong {
    display: block;
    margin-top: 10px;
    font-size: 1.6rem;
    color: var(--brand);
}

.listing-filters {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
}

.listing-search {
    grid-column: span 2;
}

.listing-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 20px;
}

.listing-pagination {
    margin-top: 24px;
    padding: 16px;
    border-radius: 24px;
    background: rgba(255, 255, 255, 0.72);
    border: 1px solid rgba(255, 255, 255, 0.78);
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}

.listing-pagination-summary {
    color: var(--muted);
    font-weight: 700;
    font-size: 0.92rem;
}

.listing-pagination-pages {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.pagination-button,
.pagination-ellipsis {
    min-width: 40px;
    height: 40px;
    padding: 0 14px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
}

.pagination-button {
    color: var(--brand);
    background: rgba(255, 255, 255, 0.68);
    border: 1px solid rgba(23, 58, 122, 0.12);
    text-decoration: none;
    transition: transform var(--transition), background-color var(--transition), color var(--transition), box-shadow var(--transition);
}

.pagination-button:hover {
    color: #fff;
    background: var(--brand);
    box-shadow: 0 12px 24px rgba(23, 58, 122, 0.18);
    transform: translateY(-2px);
}

.pagination-button.is-active {
    color: #fff;
    background: linear-gradient(135deg, var(--brand), var(--brand-2));
    box-shadow: 0 14px 26px rgba(23, 58, 122, 0.22);
}

.pagination-button.is-disabled {
    color: rgba(23, 58, 122, 0.35);
    pointer-events: none;
    background: rgba(255, 255, 255, 0.38);
    box-shadow: none;
}

.pagination-ellipsis {
    color: var(--muted);
}

.listing-card {
    overflow: hidden;
    text-decoration: none;
    color: inherit;
}

.listing-card-media {
    position: relative;
    aspect-ratio: 1 / 0.78;
    overflow: hidden;
    background: linear-gradient(135deg, rgba(23, 58, 122, 0.08), rgba(216, 170, 65, 0.14));
}

.listing-card-media img,
.listing-card-media video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.listing-card-body {
    padding: 20px;
}

.listing-card-body h3 {
    margin: 0 0 10px;
    font-size: 1.08rem;
    line-height: 1.45;
    font-weight: 800;
    color: var(--ink);
}

.listing-hot-badge {
    position: absolute;
    top: 16px;
    left: 16px;
    padding: 9px 16px;
    border-radius: 999px;
    color: #fff;
    font-size: 0.84rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ff4a5d, #da2338);
}

.listing-price {
    margin-bottom: 12px;
    font-size: 1.42rem;
    color: #ec7300;
    font-weight: 800;
}

.listing-tags {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}

.listing-tags span {
    padding: 7px 12px;
    border-radius: 999px;
    background: rgba(23, 58, 122, 0.08);
    color: var(--brand);
    font-size: 0.82rem;
    font-weight: 700;
}

.listing-address,
.listing-meta {
    color: var(--muted);
    line-height: 1.6;
}

.listing-meta {
    margin-top: 6px;
    font-size: 0.92rem;
}

.form-control,
.form-select {
    width: 100%;
    min-height: 48px;
    padding: 12px 14px;
    border-radius: 16px;
    border: 1px solid rgba(20, 40, 74, 0.12);
    background: rgba(255, 255, 255, 0.9);
    color: var(--ink);
    font: inherit;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 46px;
    padding: 12px 18px;
    border-radius: 16px;
    border: 1px solid transparent;
    text-decoration: none;
    font: inherit;
    font-weight: 700;
    cursor: pointer;
}

.btn-primary {
    color: #fff;
    background: linear-gradient(135deg, #173a7a, #3c67bc);
}

.btn-outline-secondary {
    color: var(--brand-strong);
    border-color: rgba(20, 40, 74, 0.12);
    background: rgba(255, 255, 255, 0.68);
}

.alert {
    border-radius: 20px;
    padding: 16px 18px;
    margin-bottom: 18px;
}

.alert-success {
    background: rgba(34, 139, 90, 0.12);
    color: #196947;
}

.alert-danger {
    background: rgba(214, 77, 95, 0.12);
    color: #9f293c;
}

@media (max-width: 1199.98px) {
    .listing-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
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

    .topbar,
    .listing-hero {
        grid-template-columns: 1fr;
        flex-direction: column;
    }

    .listing-filters,
    .listing-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767.98px) {
    .listing-filters,
    .listing-grid,
    .listing-summary {
        grid-template-columns: 1fr;
    }

    .listing-search {
        grid-column: auto;
    }

    .hero-panel,
    .listing-hero,
    .listing-filter-panel,
    .topbar {
        padding: 18px;
        border-radius: 24px;
    }
}
