document.addEventListener('DOMContentLoaded', function () {
    const navbar = document.getElementById('siteNavbar');
    const revealItems = document.querySelectorAll('.reveal-up');
    const backToTopButton = document.getElementById('backToTop');

    const updateNavbarState = () => {
        if (!navbar) {
            return;
        }

        if (window.scrollY > 24) {
            navbar.classList.add('is-scrolled');
        } else {
            navbar.classList.remove('is-scrolled');
        }

        if (backToTopButton) {
            if (window.scrollY > 360) {
                backToTopButton.classList.add('is-visible');
            } else {
                backToTopButton.classList.remove('is-visible');
            }
        }
    };

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const delay = entry.target.dataset.delay || 0;
                entry.target.style.setProperty('--reveal-delay', `${delay}ms`);
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.15,
    });

    revealItems.forEach((item) => revealObserver.observe(item));

    if (backToTopButton) {
        backToTopButton.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth',
            });
        });
    }

    updateNavbarState();
    window.addEventListener('scroll', updateNavbarState, { passive: true });
});
